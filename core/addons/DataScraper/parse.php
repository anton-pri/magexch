<?php
error_reporting(0);
//ini_set('user_agent', 'Mozilla/4.0 (compatible; MSIE 6.0)');
ini_set('max_execution_time', '0');
$time_start = time();

function cw_datascraper_print_r() {
    static $count = 0;
/*
    static $log_f_name;

    if (!isset($log_f_name)) {
        $log_f_name = "log/".date('Y-M-d-H-i').".html";
    }
*/
    $log_end = false;

    $args = func_get_args();

    $msg = "<div align=\"left\"><pre><font>";
    if (!empty($args)) {
        foreach ($args as $index=>$variable_content) {
            $msg .= "<b>Debug [".$index."/".$count."]:</b> ";
            ob_start();

            if ($variable_content == "log_end")
                $log_end = true;
            else
                print_r($variable_content);

            $data = ob_get_contents(); ob_end_clean();
            $msg .= htmlspecialchars($data)."\n";
        }
    } else {
        $msg .= '<b>Debug notice:</b> try to use func_print_r($varname1,$varname2); '."\n";
    }

    $msg .= "</font></pre></div>";
    echo $msg;

    cw_log_add('datascraper_parse', $msg);
/*
    file_put_contents($log_f_name, $msg, FILE_APPEND);
    if ($log_end) {
        rename($log_f_name, str_replace(".html", "_fin.html",$log_f_name));
    }
*/
    cw_ds_func_flush();

    $count++;
}


function cw_datascraper_counter($cntr_line,$to_show=false) {
    static $all_counters;

    if ($to_show) {
        cw_datascraper_print_r($all_counters);

        return;
    }

    $cnt_arr_def = explode("#%$#", $cntr_line);

    if (count($cnt_arr_def) == 1) {
        if (!isset($all_counters[$cnt_arr_def[0]]))
            $all_counters[$cnt_arr_def[0]] = 1;
        else
            $all_counters[$cnt_arr_def[0]]++;
    }

    if (count($cnt_arr_def) == 2) {
        if (!isset($all_counters[$cnt_arr_def[0]])) {
            $all_counters[$cnt_arr_def[0]] = array();
            $all_counters[$cnt_arr_def[0]][$cnt_arr_def[1]] = 1;
        } else {
           if (!isset($all_counters[$cnt_arr_def[0]][$cnt_arr_def[1]]))
               $all_counters[$cnt_arr_def[0]][$cnt_arr_def[1]] = 1;
           else
               $all_counters[$cnt_arr_def[0]][$cnt_arr_def[1]]++;
        }
    }

    if (count($cnt_arr_def) == 3)  {
        if (!isset($all_counters[$cnt_arr_def[0]])) {
            $all_counters[$cnt_arr_def[0]] = array();
            $all_counters[$cnt_arr_def[0]][$cnt_arr_def[1]] = array();
            $all_counters[$cnt_arr_def[0]][$cnt_arr_def[1]][$cnt_arr_def[2]] = 1;
        } else {
            if (!isset($all_counters[$cnt_arr_def[0]][$cnt_arr_def[1]])) {
                $all_counters[$cnt_arr_def[0]][$cnt_arr_def[1]] = array();
                $all_counters[$cnt_arr_def[0]][$cnt_arr_def[1]][$cnt_arr_def[2]] = 1;
            } else {
                if (!isset($all_counters[$cnt_arr_def[0]][$cnt_arr_def[1]][$cnt_arr_def[2]])) {
                    $all_counters[$cnt_arr_def[0]][$cnt_arr_def[1]][$cnt_arr_def[2]] = 1;
                } else {
                    $all_counters[$cnt_arr_def[0]][$cnt_arr_def[1]][$cnt_arr_def[2]]++;
                }
            }
        }
    }
}

function cw_datascraper_check_site_active($site_id, $parsing = '') {
    global $tables;
         
    $result = cw_query_first_cell("SELECT ".$parsing."active FROM $tables[datascraper_sites_config] WHERE siteid = '$site_id'");
    return $result; 
}

// get site attributes and extra patterns fromdb
function cw_datascraper_get_site_data_fromdb($id) {
    global $tables;
    
    $data = array();
    $data['attributes'] = cw_query("select ds_attribute_id, name, pattern, type, mandatory from $tables[datascraper_attributes] where site_id = ".$id." order by ds_attribute_id");

/*
                // Exec
                $ndb->Exec("select patterns, allow from patterns where site_id = ".$id);
                if ($ndb->RecCount()) {
                        $data_attributes                                = $ndb->GetAllFields();
                        $data['extra_patterns']                 = $data_attributes[0]['patterns'];
                        $data['extra_pattern_allow']    = $data_attributes[0]['allow'];
                }
                $ndb->Close();
*/

    return $data;
}


function cw_ds_no_cwd($str, $make_href = false) {
    $res_str = str_replace(getcwd(),'',$str);

    if ($make_href)
        $res_str = "<a target='blank' href='".ltrim($res_str,'/')."'>$res_str</a>";

    return $res_str;
}


function cw_ds_make_dirlist_file($site_name) {
    global $WGET_DOWNLOADS_PATH;

    //$site_name = str_replace('http://', '', $site_name);
    $site_data_name_parts = parse_url($site_name);
    $site_name = $site_data_name_parts['host'];

    $return = array('code'=>0);
    if (file_exists($WGET_DOWNLOADS_PATH.'/'.$site_name)) {
        $curr_pwd = shell_exec('pwd');
        $dirlist_file =  str_replace(array('www.','.com','@', '/', '%20'),'',$site_name).".txt";
        @unlink($WGET_DOWNLOADS_PATH.'/'.$dirlist_file);
        $make_dir_file = "cd ".$WGET_DOWNLOADS_PATH." && find $site_name -type f > $dirlist_file && cd $curr_pwd";
        $return['msg'] = shell_exec($make_dir_file);
        if (file_exists($WGET_DOWNLOADS_PATH.'/'.$dirlist_file)) {
            $return['code'] = 1;
            $return['msg'] = 'Created dir list file: '.$dirlist_file. " ($site_name)";
        } else {
            $return['msg'] = "Cant execute: ".$make_dir_file;
        }
    } else {
        $return['msg'] = "Site copy does not exist, please activate site (".implode(", ",$site_data_name_parts).")";
    }
    return $return;
}


function cw_datascraper_get_site_service_info($site_data) {

    global $WGET_DOWNLOADS_PATH;

    $site_data = $site_data[0];
    $info_str = array();

    $site_is_on_parsing = false;

    //display here: if copied site dir exists, last access time of the log file, percentage of parsed files 
    //$site_data['name'] = str_replace('http://', '', $site_data['name']);
    $site_data_name_parts = parse_url($site_data['name']);
    $site_copy_path = $WGET_DOWNLOADS_PATH.'/'.$site_data_name_parts['host'];
    if (file_exists($site_copy_path)) {
        $info_str[] = "<b>Site copy directory</b>: ".cw_ds_no_cwd($site_copy_path)." <b>dir size</b>: ".str_replace($site_copy_path,'',shell_exec("du -sh $site_copy_path"));
        //check if download is completed

        $site_copy_log = $WGET_DOWNLOADS_PATH.'/'.str_replace(array('http:', 'www.', '.com', '@', '/', '%20'), '', $site_data['name']).'_wget_log.txt';
        if (file_exists($site_copy_log)) {
            $site_copy_log_data = filemtime($site_copy_log);
            $info_str[] = "<b>Site download copy log:</b> ".cw_ds_no_cwd($site_copy_log,1)." <b>last access time:</b> ".date('Y-M-d G:i T',$site_copy_log_data)." (current server time: ".date('Y-M-d G:i T').")";

            $test_fin_word = 'FINISHED';
            $grep_finished = shell_exec("grep '$test_fin_word' $site_copy_log");
            if (strpos($grep_finished, $test_fin_word) === false) {
                $test_fin_word = 'ЗАВЕРШЕНО';
                $grep_finished = shell_exec("grep '$test_fin_word' $site_copy_log");
            }
            if (strpos($grep_finished, $test_fin_word) !== false) {
                $info_str[] = "Site copying is ".$grep_finished;

                $dir_list_file_name = str_replace(array('http:','www.','.com','@', '/', '%20'),'',$site_data_name_parts['host']).".txt";

                $dir_list_file = $WGET_DOWNLOADS_PATH.'/'.$dir_list_file_name;
                if (file_exists($dir_list_file))
                    $dir_list_file_data = filemtime($dir_list_file);


                if ($dir_list_file_data > $site_copy_log_data) {
                    $info_str[] = "<b>Found directory listing file</b> ".cw_ds_no_cwd($dir_list_file,1)." <b>number of lines: </b>".str_replace($dir_list_file,'',shell_exec("wc -l $dir_list_file"));
                    $parse_pos_file = $dir_list_file.'.pos';
//cw_datascraper_print_r(array('parse_pos_file'=>$parse_pos_file));
                    if (file_exists($parse_pos_file)) {
                        $last_pos = file_get_contents($parse_pos_file);
                        $last_pos = trim($last_pos);
                        if ($last_pos != 'final') {
                            $info_str[] = "<b>Parsing is in progress</b>: ".number_format(100*intval($last_pos)/filesize($dir_list_file),2)."% <b>done, last update</b>: ".date('Y-M-d G:i T',filemtime($parse_pos_file)).". You can reset parse using appropriate command";
                            $site_is_on_parsing = true;
                        } else {
//cw_datascraper_print_r(array("$parse_pos_file content"=>$last_pos));
                            $info_str[] = "<b>Parsing is completed</b>: ".date('Y-M-d G:i T',filemtime($parse_pos_file)).". You can reset parse using appropriate command";
                       }
                   } else {
                       $info_str[] = "<b>Parsing will start at index.php?target=datascraper_parse script run</b>";
                       $site_is_on_parsing = true;
                   }
                } else {
                    //create new list file
                    //@unlink($dir_list_file);
                    $res = cw_ds_make_dirlist_file($site_data['name']);
                    if ($res['code'] != 4) $info_str[] = $res['msg'];
                    $info_str[] = "<b>Directory listing file</b> ".cw_ds_no_cwd($dir_list_file,1).($res['code']?" <b>created successfully, number of lines: </b>".str_replace($dir_list_file,'',shell_exec("wc -l $dir_list_file")):"<b> could not create ($dir_list_file)</b>");
                    if ($res['code']) {
                        $site_is_on_parsing = true;
                        $info_str[] = "<b>Parsing will start at index.php?target=datascraper_parse script run</b>";
                    } else {
                        $info_str[] = "<b>Problem: could not create the directory listing file</b>";
                    }

                }
            } else {
                $info_str[] = "<b>Site is being downloaded yet, parsing can start only after it finishes (no FINISHED word in $site_copy_log)</b>";
            }
        } else {
            $info_str[] = "<b>Problem: Site copy log file $site_copy_log is not found, parsing cant start</b>";
//cw_datascraper_print_r($site_data);
        }

    } else {
        $info_str[] = 'Site copy '.$site_copy_path.' does not exist, please activate site then select day of the week and hour when site copy will run';
    }
    return array(implode('<br />', $info_str), $site_is_on_parsing, $dir_list_file_name);
}

function cw_datascraper_file_passed_with_pattern($parsed_filename, $site_id) {
    $res = true;
/*
    $patterns_string = db_exec_query_field("select patterns from patterns where site_id = '$site_id'", 'patterns');
    if ($patterns_string != '') {
        $patterns_flag = db_exec_query_field("select allow from patterns where site_id = '$site_id'", 'allow');
        $patterns_list = explode(",", $patterns_string);
        $test_name = basename($parsed_filename);
        $pat_match = false;
        foreach ($patterns_list as $pat_str) {
            if (strpos($test_name, $pat_str) !== false) {
                $pat_match = true; break;
            }
        }
        $res = (($pat_match && $patterns_flag) || (!$pat_match && !$patterns_flag));

        if ($res) {
            cw_datascraper_print_r("Allowed file: ".$parsed_filename." ($test_name)");
        } else {
            cw_datascraper_print_r("Refused file: ".$parsed_filename." ($test_name)");
        }
    }
*/
    return $res;
}

// get pattern to find
function cw_datascraper_get_pattern($pattern) {
    $pattern_to_find = '';

    if ($pattern) {
        $pattern_to_find = $pattern;
        // ^$*+?.
        $pattern_to_find = str_replace(array('^','$','*','+','?','.','/'), array('\^','\$','\*','\+','\?','\.','\/'), $pattern_to_find);
        $pattern_to_find = str_replace('@@@', '.*', $pattern_to_find);
        $pattern_to_find = str_replace('###', '(.+)', $pattern_to_find);
        $pattern_to_find = preg_replace('/\s+/', '.*', $pattern_to_find);
        $pattern_to_find = preg_replace('/>\s?</', '>.*<', $pattern_to_find);
        $pattern_to_find = '|'.$pattern_to_find.'|Uis';
    }
    return $pattern_to_find;
}

function cw_datascraper_get_data($find_elements, $name, $type, $pattern, $attr_id, $test_mode=FALSE) {

    $data = array();
    if (count($find_elements)) {
        $element = $find_elements[0];
        //$num = substr_count($pattern, '[\s\S]+');
        $value = str_replace(array('</p>'),array('</p><br>'), $element[1]);
        $value = strip_tags($value, '<br>');
        $value = trim($value);

        switch ($type) {
            case 'decimal':
                $value = floatval(str_replace(array('$',','),array('',''), $value));
            break;
            case 'integer':
                $value = intval(str_replace(array('$',','),array('',''), $value));
            break;
            case 'image':
                $aHeaders = @get_headers($value);

                if ($test_mode) {
                    // Is file exist
                    if (preg_match("|200|", $aHeaders[0])) {
                        $value = "<a href='" . $value . "' target='_blank'><img width='70px' src='" . $value . "'></a>";
                    } else {
                        $value = "No image";
                    }
                } else {
                    // Is file not exist
                    if (!preg_match("|200|", $aHeaders[0])) {
                        $value = "";
                    }
                }
            break;
            default:
                $value = strval($value);
            break;
        }

        if ($value != '') {
            $data[] = array($name, $value, $attr_id, $type);
        }
    }
    return $data;
}


function cw_datascraper_check_product_is_added($site_id, $result) {
    global $tables; 

    $FIELD_NAME_FOR_CHECK = "sku";

    $res = "";

    $field_value_for_check = "";

    $field_name_for_check = strtolower($FIELD_NAME_FOR_CHECK);

    foreach ($result as $res_data) {
        foreach ($res_data as $r) {
            if (strtolower($r[0]) == $field_name_for_check) {
                $field_value_for_check = $r[1];
            }
        }
    }

    if (empty($field_value_for_check))
        return $res;

    $res = cw_query_first_cell("SELECT url FROM $tables[datascraper_result_values]$site_id WHERE `$field_name_for_check`='".addslashes($field_value_for_check)."'");

    return $res;
}

// save values
function cw_datascraper_save_values($values, $result_values_url = "") {
    global $tables;

    if (!count($values)) {
        return;
    }

    if (!empty($result_values_url))
        $values[2] = $result_values_url;

    $site_results_tbl = "$tables[datascraper_result_values]$values[0]";
    $res_tbl_exists = cw_query_first("SELECT * FROM information_schema.tables WHERE table_name = '$site_results_tbl' LIMIT 1");
    if (!$res_tbl_exists) {
        cw_datascraper_print_r("Result table $site_results_tbl does not exists."); 
        return;
    }

    $res_values_id = cw_query_first_cell("SELECT result_id FROM $site_results_tbl WHERE url='$values[2]'");
    if (!$res_values_id) {

        cw_datascraper_print_r("save_values to new");
        cw_datascraper_counter("$values[0]#%$#added_items#%$#$values[2]");

        db_query("INSERT INTO $site_results_tbl (url, `date_scraped`, `$values[3]`) VALUES ('$values[2]', ".time().", '".addslashes($values[4])."')");
    } else {

        cw_datascraper_print_r("save_values to existing $res_values_id");
        cw_datascraper_counter("$values[0]#%$#updated_items#%$#$values[2]");

        db_query("UPDATE $site_results_tbl SET `$values[3]`='".addslashes($values[4])."' WHERE result_id='$res_values_id'"); 
    } 
/*
        try {
                // Connect
                $ndb = new NDB(DB_HOSTNAME, DB_NAME, DB_LOGIN, DB_PASS);

                $res_values_id = 0;

                $ndb->Exec("SELECT id FROM result_values WHERE site_id = '$values[0]' AND site = '$values[1]' AND url = '$values[2]' AND name = '$values[3]' AND attribute_id = '$values[5]' LIMIT 1");

                if ($ndb->RecCount()) {
                        $res_values_id = $ndb->GetField('id');
                }
                if (!$res_values_id) {
cw_datascraper_print_r("save_values to new");
cw_datascraper_counter("$values[0]#%$#added_items#%$#$values[2]");
                    // Data
                    $ndb->bind['types'] = array("i", "s", "s", "s", "s", "i", "i", "i");
                    $ndb->bind['params'] = array($values[0], $values[1], $values[2], $values[3], $values[4], $values[5], $values[6], time());
                    // Exec
                    $ndb->Exec("insert into result_values (site_id, site, url, name, value, attribute_id, mandatory, last_scrap_date) values (?, ?, ?, ?, ?, ?, ?, ?)");
                } else {
                    $ndb->bind['types'] = array("s", "i", "i");
                    $ndb->bind['params'] = array($values[4], time(), $res_values_id);
                    $ndb->Exec("update result_values set value = ? , last_scrap_date = ?  where id = ?");
cw_datascraper_print_r("save_values to existing $res_values_id");
cw_datascraper_counter("$values[0]#%$#updated_items#%$#$values[2]");
                }

                $ndb->Close();
        }
        catch (Exception $e) {
                exit($e->getMessage());
        }
*/

}
 
function cw_ds_func_flush($s = NULL) {   
    if (!is_null($s))
        echo $s;

    if (preg_match("/Apache(.*)Win/S", getenv('SERVER_SOFTWARE'))) {
        echo str_repeat(" ", 2500);
    } elseif (preg_match("/(.*)MSIE(.*)\)$/S", getenv('HTTP_USER_AGENT'))) {
        echo str_repeat(" ", 256);
    }

    if (function_exists('ob_flush')) {
        // for PHP >= 4.2.0
        @ob_flush();
    } else {
        // for PHP < 4.2.0
        if (ob_get_length() !== FALSE) {
            ob_end_flush();
        }
    }

    flush();
}



//====================================================================
// Get saved sites
$saved_sites = cw_query("select * from $tables[datascraper_sites_config] where parsed=0");

$ignoretypes = array('jpg', 'css', 'js', 'png', 'gif','jpeg','tiff','bmp');

cw_log_add('datascraper_parse', 'Parse script run');

$max_scraped_file_size = 700000;
$max_lines_per_pass = 100;
$one_run_max_time = 10; //mins
$pass_num = 0;

global $WGET_DOWNLOADS_PATH;

    $WGET_DOWNLOADS_PATH = $app_dir."/files/DataScraper/wget_rec";
    if (!file_exists($WGET_DOWNLOADS_PATH)) {
        if (!mkdir($WGET_DOWNLOADS_PATH, 0777, true))
            die("Cant create directory $WGET_DOWNLOADS_PATH");
    }


$running_proc_file = $WGET_DOWNLOADS_PATH."/parser.lock";
$process_locked = false;
$no_lock_yet = false;
if (file_exists($running_proc_file)) {
    cw_datascraper_print_r('Found parser.lock file');
    $running_proc_file_date = filemtime($running_proc_file);
    if (intval((time() - $running_proc_file_date)/60) > $one_run_max_time) {
        cw_datascraper_print_r('Lock file is too old, removing it, restarting process');
        unlink($running_proc_file);
    } else {
        $process_locked = true;
        cw_datascraper_print_r("Concurrent DataScraper parse process is run, exiting");
    }
} else {
    $no_lock_yet = true;
/*
    file_put_contents($running_proc_file, "parse process started at ".date('Y-M-d-H-i'));
    cw_datascraper_print_r('Created new parser.lock file');
*/
}

if ($process_locked)  {
    cw_datascraper_print_r("log_end");
    exit;
}

$processed_sites = 0;
if (count($saved_sites)) {
    foreach ($saved_sites as $site) {
        if (!cw_datascraper_check_site_active($site['siteid'], 'parsing_')) {
            continue;
        }
        $processed_sites++;

        $site_data = cw_datascraper_get_site_data_fromdb($site['siteid']);

        $site_data['site_url'] = $site['name'];
        $site_data['name'] = $site['name'];
        $site_data['wget_path'] = $WGET_DOWNLOADS_PATH."/";

        list($site_service_info, $is_on_parse, $wget_path_list) = cw_datascraper_get_site_service_info(array($site_data));
        if (!$is_on_parse) {
            cw_datascraper_print_r("Skipped site $site[name]");
            cw_datascraper_print_r($site_service_info);
            continue;
        }

        if ($no_lock_yet) {
            file_put_contents($running_proc_file, "parse process started at ".date('Y-M-d-H-i'));
            cw_datascraper_print_r('Created new parser.lock file');
            $no_lock_yet = false; 
        }      

        $site_data['wget_path_list'] = $wget_path_list;

        $fp_list = fopen($site_data['wget_path'].$site_data['wget_path_list'], "r");

        if (!$fp_list) {
            cw_datascraper_print_r("Cant read list file ".$site_data['wget_path'].$site_data['wget_path_list']);
            continue;
        }

        $last_pos = file_get_contents($site_data['wget_path'].$site_data['wget_path_list'].'.pos');

        if ($last_pos == 'final')
            continue;

        cw_datascraper_print_r(array('Processing site:'=>$site['name'], 'list file position:'=>$last_pos));

        if ($last_pos) {
            fseek($fp_list, $last_pos);
        }

        while (($parsed_filename = fgets($fp_list)) && $max_lines_per_pass > 0) {

            $max_lines_per_pass--;
            $parsed_filename = trim($parsed_filename);
            $curr_pos = ftell($fp_list);
            file_put_contents($site_data['wget_path'].$site_data['wget_path_list'].'.pos', $curr_pos);
            $parsed_filename = $site_data['wget_path'].$parsed_filename;
            $path_parts = pathinfo($parsed_filename);
            $fsize = filesize($parsed_filename);

            cw_datascraper_print_r("file: ".$parsed_filename . ' - ' . $fsize . ' bytes');

            if (in_array(strtolower($path_parts['extension']), $ignoretypes) || $fsize > $max_scraped_file_size || !cw_datascraper_file_passed_with_pattern($parsed_filename, $site['siteid'])) continue;

            //cw_datascraper_print_r("file: ".$parsed_filename . ' - ' . $fsize . ' bytes');
            cw_datascraper_print_r($path_parts);

            $html = file_get_contents($parsed_filename);

            if (!strlen($html)) {
                cw_datascraper_print_r('empty content');
                continue;
            }

            $visit_url = str_replace($site_data['wget_path'],'',$parsed_filename);

            $result = array();
            $mandatory_flag = 1;

            foreach ($site_data['attributes'] as $attribute) {
                $data   = array();
                $attr_id= $attribute['ds_attribute_id'];
                $name   = $attribute['name'];
                $type   = $attribute['type'];
                $mandatory  = $attribute['mandatory'];
                $pattern= cw_datascraper_get_pattern($attribute['pattern']);

                preg_match_all($pattern, $html, $matches, PREG_SET_ORDER);

                $data = cw_datascraper_get_data($matches, $name, $type, $pattern, $attr_id);

                if (count($data)) {
                    $result[] = $data;
                } elseif ($mandatory) {
                    $mandatory_flag = 0;
                }
            }

            if (count($result) && $mandatory_flag) {

                cw_datascraper_print_r("parse result:");
                cw_datascraper_print_r($result);
                cw_datascraper_counter("$site[siteid]#%$#parsed_pages_with_all_mandatory_fields");

                $res_values_url = cw_datascraper_check_product_is_added($site['siteid'], $result);

                $debug_values_url = (empty($res_values_url)?'NEW':"$res_values_url");
                cw_datascraper_counter("$site[siteid]#%$#values_pages#%$#$debug_values_url");
                cw_datascraper_print_r("defined res_values_url $debug_values_url");

                $image = FALSE;
                foreach ($result as $res) {
                    foreach ($res as $r) {
                        // Check image is exist
                        if (!$image && $r[3] == 'image' && !empty($r[1])) {
                           $image = $r[1];
                        }

                        cw_datascraper_print_r("save_values:");
                        cw_datascraper_print_r(array($site['siteid'], $site['name'], $visit_url, $r[0], $r[1], $r[2], $mandatory_flag));

                        cw_datascraper_save_values(array($site['siteid'], $site['name'], $visit_url, $r[0], $r[1], $r[2], $mandatory_flag), $res_values_url);
                    }
                }
/*
                if ($image && DO_SAVE_IMAGE_TO_FOLDER) {
                    save_image($site['siteid'], $visit_url, $image);
                }
*/
            }
        }

        $curr_pos = ftell($fp_list);

        fclose($fp_list);

        if ($curr_pos > (filesize($site_data['wget_path'].$site_data['wget_path_list'])-2)) {
            cw_datascraper_print_r("finalizing reading list");

            file_put_contents($site_data['wget_path'].$site_data['wget_path_list'].'.pos', 'final');
        } else {
            if (!$max_lines_per_pass) {
                cw_datascraper_counter("", true);
                cw_datascraper_print_r("restarting new pass, time spent: ".intval((time()-$time_start)/60)." min, ".((time()-$time_start)%60)." sec");
                cw_datascraper_print_r($curr_pos);
                @unlink($running_proc_file);

                if ($_GET['webrestart'] == 'Y') {
                    $location = "http://dev2.saratogawine.com/index.php?target=datascraper_parse&filepos=$curr_pos&webrestart=Y";

                    header("Location: $location");

                    echo "<meta http-equiv=\"Refresh\" content=\"0;URL=" . $location ."\" />";

                    cw_ds_func_flush();
                }
                exit;
            }
        }
    }
}

exit;
