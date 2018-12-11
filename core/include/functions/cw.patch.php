<?php
function cw_patch_prepare_list ($patch_lines) {
    $list = "";

    $diff_data = "";
    $orig_file = "";
    $index_found = false;

    if (empty($patch_lines) || !is_array($patch_lines))
        return false;

    foreach($patch_lines as $patch_line) {
        if(preg_match('/(^Index: (.+))|(^diff)|(^((---)|(\+\+\+)|(\*\*\*)) ([^\t:]+))/S',$patch_line, $m)) {
            if (!empty($m[2]) || !empty($m[3]) && !$index_found) {
                if (!empty($orig_file)) {
                    $diff_file = cw_patch_store_in_tmp(join("",$diff_data), false);
                    $list[] = $orig_file.",".$diff_file.",";
                    $orig_file = "";
                    $index_found = false;
                    $diff_data = "";
                }
            }
            # from Index field
            if (!empty($m[2])) {
                $index_found = true;
                if (empty($orig_file) || strlen($orig_file) > strlen($m[2]))
                    $orig_file = $m[2];
            }
            # from ---/***/+++ field
            elseif (!empty($m[9])) {
                if (empty($orig_file) || strlen($orig_file) > strlen($m[9]))
                    $orig_file = $m[9];
            }
        }
        $diff_data[] = $patch_line;
    }
    if (!empty($orig_file) && !empty($diff_data)) {
        $diff_file = cw_patch_store_in_tmp(join("",$diff_data), false);
        $list[] = $orig_file.",".$diff_file.",";
    }

    return $list;
}

function cw_patch_test($patch_lines, $patch_dirs) {
    global $ready_to_patch, $could_not_patch, $patch_cmd, $patch_rcmd;
    global $customer_files;
    global $patch_reverse;

    $could_not_patch = 0;

    echo "<script language='javascript'> loaded = false; function refresh() { window.scroll(0, 100000); if (loaded == false) setTimeout('refresh()', 1000); } setTimeout('refresh()', 1000); </script>";
    echo cw_get_langvar_by_name("txt_testing_patch_applicability", null, false, true, true)."<hr />\n";
    flush();

    $__patch_files = cw_patch_prepare_list($patch_lines);

    if (empty($__patch_files) || !is_array($__patch_files))
        return false;

    foreach($__patch_files as $patch_file_info) {
        $patch_file_info = trim($patch_file_info);
        if ($patch_file_info == "" || $patch_file_info[0] == "#") continue;

        $parsed_info = preg_split( "/[ ,\t]/S", $patch_file_info);

        list($orig_file, $diff_file, $md5_sum) = $parsed_info;

        $files_to_patch = array();
        $tmp_incl_files_to_patch = array(
            "orig_file" => $orig_file,
            "diff_file" => $diff_file,
            "real_diff" => $diff_file,
            "md5_sum"   => $md5_sum,
            "status"    => "OK");

        $real_file = array();
        $file_info = pathinfo(trim($orig_file));
        if ($file_info['extension'] == 'tpl' || $orig_file == 'index.php') {
            foreach($patch_dirs['tpl'] as $tpl_dir) {
                $tmp_incl_files_to_patch['real_file'] = $tpl_dir.'/'.$orig_file;
                $files_to_patch[] = $tmp_incl_files_to_patch;
            }
        }
        else {
            $tmp_incl_files_to_patch['real_file'] = $patch_dirs['php'].'/'.$orig_file;
            $files_to_patch[] = $tmp_incl_files_to_patch;
        }

        foreach($files_to_patch as $patch_file) {

            echo $orig_file.' ('.$patch_file['real_file'].')... '; flush();

            if (!file_exists($patch_file['real_file']) && cw_patch_is_create_new($patch_file['real_diff'], $patch_reverse)) {
                $dir = dirname($patch_file['real_file']);

                if ($patch_file['status'] == "OK" && (!file_exists($dir) || !is_dir($dir)))
                    $patch_file['status'] = "directory not found";

                if ($patch_file['status'] == "OK" && !is_writable($dir))
                    $patch_file['status'] = "directory non-writable";
            }
            else {
                if ($patch_file['status'] == "OK" && !file_exists($patch_file['real_file'])) 
                    $patch_file['status'] = "not found";

                if ($patch_file['status'] == "OK" && !is_file($patch_file['real_file']))
                    $patch_file['status'] = "not a file";

                if ($patch_file['status'] == "OK" && !is_writable($patch_file['real_file']))
                    $patch_file['status'] = "non-writable";
            }

            if ($patch_file['status'] != "OK")
                $ready_to_patch = false;

            if ($patch_file['status'] == "OK") {
                $patch_result_ = array();
                $rejects_ = false;
                $patch_errorcode_ = !cw_patch_apply($patch_file['real_file'], $patch_file['real_diff'], false, false, $patch_result_, $rejects_, true, $patch_reverse);
                if ($patch_errorcode_ != 0) {
                    $patch_result_ = array();
                    $rejects_ = false;
                    $patch_errorcode_ = !cw_patch_apply($patch_file['real_file'], $patch_file['real_diff'], false, false, $patch_result_, $rejects_, true, !$patch_reverse);
                    if ($patch_errorcode_ == 0)
                        $patch_file['status'] = "<font color=blue>already patched</font>";
                    else {
                        $patch_file['status'] = "<font color=red>could not patch</font>";
                        $could_not_patch ++;
                        $patch_file['testapply_failed'] = 1;
                    }
                }
            }

            $patch_files[] = $patch_file;
            echo $patch_file['status']."<br />\n"; flush();
        }
    }
    return $patch_files;
}

function cw_patch_store_phase_result() {
    global $patch_phase_results_file, $phase_result;
  
    $patch_phase_results_file = &cw_session_register("patch_phase_results_file");
    $patch_phase_results_file = cw_patch_store_in_tmp($phase_result);

    if ($patch_phase_results_file !== false) {
        cw_session_save();
        cw_html_location("index.php?target=patch&mode=result", 0);
    }
    else {
        cw_session_unregister("patch_phase_results_file");
        die("Upgrade/patch process cannot continue:<br />There is a problem saving temporaly data at your server. Please check permissions and/or amount of free space in your TEMP directory.<br /><br /><a href=\"patch.php\">Click here to return</a>");
    }
}

function cw_patch_restore_phase_result($remove_files = false) {
    global $phase_result, $patch_phase_results_file, $patch_files;

    $patch_phase_results_file = &cw_session_register("patch_phase_results_file");
    $phase_result = false;

    if ($patch_phase_results_file !== false) {
        $phase_result = unserialize(file_get_contents($patch_phase_results_file));
        if ($remove_files)
            @unlink($patch_phase_results_file);
    }

    if ($remove_files)
        cw_session_unregister("patch_phase_results_file");
}   

function cw_patch_store_in_tmp($data, $serialize = true) {
    global $var_dirs;

    $file = tempnam($var_dirs['tmp'], "patch_tmp");
    if ($serialize) $data = serialize($data);
    if ($file)
        file_put_contents($file, $data);
    return $file;
}

function cw_patch_is_create_new($patchfile, $reverse) {
    if (!file_exists($patchfile))
        return false;

    $patch = file($patchfile);

    $started = false;
    $regexp = '!^'.($reverse?'-':'\+').'!S';
    foreach ($patch as $line) {
        if (!$started) {
            if (!strncmp($line, '@@', 2))
                $started = true;

            continue;
        }

        if (!preg_match($regexp, $line))
            return false;
    }

    return true;
}

function cw_patch_apply($origfile, $patchfile, $rejfile, $backupfile, &$log, &$rejects, $check=false, $reverse=false) {
    static $masks = array (
        1 => '!^(\s*)(\@\@ -(\d+)(?:,(\d+))? \+(\d+)(?:,(\d+))? \@\@)!S' 
    );
    $type = 1;

    if (empty($origfile) || empty($patchfile) || !file_exists($patchfile))
        return false;

    $orig = array();
    if (file_exists($origfile))
        $orig = file($origfile);

    $empty_orig = empty($orig);

    $diff = file($patchfile);

    $outdata = $orig;
    $log[] = "Patching file $origfile ...";

    $idx = 0;
    $hunk_number = 1;
    $i_offset = 0;
    $rejected = array();
    $rejects = false;
    $changed = false;
    $is_new_file = false;

    while ($idx < count($diff)) {
        $line = $diff[$idx];
        if (preg_match($masks[1], $line, $m)) {
            if ($reverse) {
                list(,$space, $range, $o_start, $o_lines, $i_start, $i_lines) = $m;
            }
            else {
                list(,$space, $range, $i_start, $i_lines, $o_start, $o_lines) = $m;
            }

            $is_new_file = $is_new_file
                || ($m[3] === '0' && $m[4] === '0');

            if (empty($i_lines)) $i_lines = 1;
            if (empty($o_lines)) $o_lines = 1;
            $hunk = array();
            $idx ++;

            $can_apply = true;
            $additions = false;

            for (;$idx < count($diff) && !preg_match($masks[1], $diff[$idx]); $idx++) {
                $diff_line = $diff[$idx];

                if (!preg_match('!^(.)(.*)$!sS', $diff_line, $parsed_line)) continue;

                if ($parsed_line[1] == '\\') continue;

                if ($reverse) {
                    switch ($parsed_line[1]) {
                    case '+':
                        $parsed_line[1] = '-'; break;
                    case '-':
                        $parsed_line[1] = '+'; break;
                    }

                    $diff_line = $parsed_line[1].$parsed_line[2];
                }

                $hunk[] = $diff_line;

                # Cannot apply 'new file' hunk(s) against non empty files
                $additions = $additions
                    || $parsed_line[1] == '+';
            }

            $can_apply = !$is_new_file
                || $empty_orig
                || ($is_new_file && !$empty_orig && !$additions);

            if ($can_apply) {
                $data = cw_patch_apply_ins($hunk_number, $outdata, $hunk, $space, $i_start+$i_offset, $i_lines, $o_start, $o_lines);
            }
            else {
                $data = array (
                    'pos' => false,
                    'success' => false
                );
            }

            if (is_array($data)) {
                if ($data['pos'] === false) {
                    $hunk_pos = $i_start + $i_offset;
                } else {
                    $hunk_pos = $i_start + $i_offset + $data['pos'];
                }
                if (!$data['success']) {
                    $log[] = sprintf("Hunk #%d failed at %d.", $hunk_number, $hunk_pos);
                    $rejected[] = array (
                        'start' => $hunk_pos,
                        'hunk' => $hunk
                    );
                    $i_offset += $o_lines - $i_lines;
                } else {
                    $log[] = sprintf("Hunk #%d succeeded at %d.", $hunk_number, $hunk_pos);
                    array_splice($outdata, $hunk_pos-1, $i_lines, $data['replace']);
                    $changed = true;
                    # correct offset for next hunks
                    $i_offset += $o_lines - $i_lines + $data['pos'];
                }
            }
            $hunk_number++;
        }
        else $idx++;
    }

    if (!empty($rejected)) {
        if (empty($rejfile)) {
            $log[] = sprintf("%d out of %d hunks ignored", count($rejected), $hunk_number-1);
        }
        else {
            $log[] = sprintf("%d out of %d hunks ignored--saving rejects to %s", count($rejected), $hunk_number-1, $rejfile);

            $rejects = true;
            if (!$check) {
                if (is_writeable(dirname($rejfile))) {
                    $r = cw_pch_write_rejfile($rejfile, $rejected);
                    if (!$r) {
                        $log[] = "Write to $rejfile is failed!";
                    }
                } else {
                    $log[] = "No permissions to write $rejfile!";
                }
            }
        }
    }

    if (!$check) {
        if (!empty($backupfile) && $changed) {
            if (file_exists($backupfile))
                @unlink($backupfile);

            if (!file_exists($origfile))
                $r = touch($backupfile);
            else
                $r = copy($origfile, $backupfile);

            if (!$r) {
                $log[] = "Cannot backup file ``$origfile'' to ``$backupfile''!";
            }
        }
        $r = cw_patch_write_ins($origfile, $outdata);
        if (!$r) {
            $log[] = "Write to $origfile is failed!";
        }
    }
    $log[] = "done";

    return empty($rejected);
}

function cw_patch_apply_ins($num, &$outdata, &$hunk, $space, $i_start, $i_lines, $o_start, $o_lines) {
    $offset = cw_patch_locate_ins($outdata, $hunk, $i_start, $i_lines);

    $result = array (
        'pos' => $offset,
        'success' => false
    );

    if ($offset === false) {
        return $result;
    }

    $work_copy = array_slice($outdata,$i_start-1+$offset,$i_lines);
    $pos = 0;
    foreach ($hunk as $line) {
        if (strlen($line)>0) {
            $cmd = $line[0];
            $line = substr($line,1);
        }
        else $cmd = '';

        switch ($cmd) {
            case '-':
                if (trim($line) != trim($work_copy[$pos])) {
                    # FAILED
                    return $result;
                }
                array_splice($work_copy,$pos,1);
                break;
            case '+':
                cw_patch_array_insert_ins($work_copy,$line,$pos);
                $pos++;
                break;
            default :
                # skip ...
                $pos++;
        }
    }

    $result['success'] = true;
    $result['replace'] = $work_copy;

    return $result;
}

function cw_patch_locate_ins(&$data, &$hunk, $start, $lines) {
    $data_len = count($data);

    $max_after = $data_len - $start - $lines;
    for ($offset = 0; ; $offset++) {
        $check_after = ($offset <= $max_after);
        $check_before = ($offset <= $start);

        if ($check_after && cw_patch_match_ins($data, $hunk, $start+$offset)) {
            return $offset;
        }
        else
        if ($check_before && cw_patch_match_ins($data, $hunk, $start-$offset)) {
            return -$offset;
        }
        else
        if (!$check_after && !$check_before) {
            return false;
        }
    }

    return false;
}

function cw_patch_match_ins(&$data, &$hunk, $pos) {
    $len = count($hunk);
    $data_len = count($data);

    for ($i=0, $hunk_pos=0; $hunk_pos<$len && $pos+$i < $data_len; ) {
        if (!preg_match('!^(.)(.*)$!sS', $hunk[$hunk_pos], $matched)) {
            return false;
        }

        if ($matched[1] == '+') {
            $hunk_pos++;
            continue;
        }

        if (trim($data[$pos+$i-1]) != trim($matched[2])) {
            return false;
        }

        $i++; $hunk_pos++;
    }

    return true;
}

function cw_patch_array_insert_ins(&$array, $value, $pos) {
    if (!is_array($array)) return FALSE;

    $last = array_splice($array, $pos);
    array_push($array, $value);
    $array = array_merge($array, $last);
    return $pos;
}

function cw_patch_auto_scroll($title) {
    echo "<script language='javascript'> loaded = false; function refresh() { window.scroll(0, 100000); if (loaded == false) setTimeout('refresh()', 1000); } setTimeout('refresh()', 1000); </script>".$title;
    flush();
}

function cw_patch_write_ins($filename, $data) {
    cw_mkdir(dirname($filename));
    $fp = fopen($filename, "wb");
    if (!$fp) return false;

    fwrite($fp,implode("",$data));
    fclose($fp);

    return true;
}

function cw_patch_split_sql_file(&$ret, $sql, $release) {
    $sql = trim($sql);
    $sql_len = strlen($sql);
    $char = '';
    $string_start = '';
    $in_string = FALSE;
    
    for ($i = 0; $i < $sql_len; ++$i) {
        $char = $sql[$i];

        // We are in a string, check for not escaped end of strings except for
        // backquotes that can't be escaped
        if ($in_string) {
            for (;;) {
                $i = strpos($sql, $string_start, $i);
                // No end of string found -> add the current substring to the
                // returned array
                if (!$i) {
                    $ret[] = $sql;
                    return TRUE;
                }
                elseif ($string_start == '`' || $sql[$i-1] != '\\') {
                    // Backquotes or no backslashes before quotes: it's indeed the
                    // end of the string -> exit the loop
                    $string_start = '';
                    $in_string = FALSE;
                    break;
                }
                else {
                    // one or more Backslashes before the presumed end of string...
                    // ... first checks for escaped backslashes
                    $j = 2;
                    $escaped_backslash = FALSE;
                    while ($i-$j > 0 && $sql[$i-$j] == '\\') {
                        $escaped_backslash = !$escaped_backslash;
                        $j++;
                    }

                    // ... if escaped backslashes: it's really the end of the
                    // string -> exit the loop
                    if ($escaped_backslash) {
                        $string_start = ''; 
                        $in_string = FALSE;
                        break;
                    }
                    else {
                        // ... else loop
                        $i++;
                    }
                }
            }
        }
        elseif ($char == ';') {
            // We are not in a string, first check for delimiter...
            // if delimiter found, add the parsed part to the returned array
            $ret[] = substr($sql, 0, $i);
            $sql = ltrim(substr($sql, min($i + 1, $sql_len)));
            $sql_len = strlen($sql);
            if ($sql_len) {
                $i = -1;
            }
            else {
                // The submited statement(s) end(s) here
                return TRUE;
            }
        }
        elseif (($char == '"') || ($char == '\'') || ($char == '`')) {
            // ... then check for start of a string,...
            $in_string    = TRUE;
            $string_start = $char;
        }
        elseif ($char == '#' || ($i >= 1 && $sql[$i-1] . $sql[$i] == '--')) {
            // ... for start of a comment (and remove this comment if found)...
            // starting position of the comment depends on the comment type
            $start_of_comment = (($sql[$i] == '#') ? $i : $i-1);
            // if no "\n" exits in the remaining string, checks for "\r"
            // (Mac eol style)
            $end_of_comment  = (strpos(' ' . $sql, "\012", $i+1))
                ? strpos(' ' . $sql, "\012", $i+1)
                : strpos(' ' . $sql, "\015", $i+1);

            if (!$end_of_comment) {
                // no eol found after '#', add the parsed part to the returned
                // array if required and exit
                if ($start_of_comment > 0) {
                    $ret[] = trim(substr($sql, 0, $start_of_comment));
                }
                return TRUE;
            }
            else {
                $sql = substr($sql, 0, $start_of_comment).ltrim(substr($sql, $end_of_comment));
                $sql_len = strlen($sql);
                $i--;
            }
        }
        elseif ($release < 32270 && ($char == '!' && $i > 1  && $sql[$i-2] . $sql[$i-1] == '/*')) {
            // ... and finally disactivate the "/*!...*/" syntax if MySQL < 3.22.07
            $sql[$i] = ' ';
        }
    }

    // add any rest to the returned array
    if (!empty($sql) && ereg('[^[:space:]]+', $sql)) {
        $ret[] = $sql;
    }

    return TRUE;
}

function cw_patch_execute_sql_query($sql_query, $databases) {
    global $mysql_connection_id;

    $pieces = array();
    cw_patch_split_sql_file($pieces, $sql_query, PMA_MYSQL_INT_VERSION);
    $pieces_count = count($pieces);

    if ($sql_file != 'none' && $pieces_count > 10)
        $sql_query_cpy = $sql_query = '';
    else
        $sql_query_cpy = implode(";\n", $pieces) . ';';

    // Runs multiple queries
    if(is_array($databases)) {
        $current_connection = $mysql_connection_id;
        foreach($databases as $params) {
            $ret = @db_connect($params['host'], $params['user'], $params['password']) ;
            if ($ret) $ret &= db_select_db($params['db']);
           
            for ($i = 0; $i < $pieces_count; $i++) {
                $a_sql_query = $pieces[$i];
                $result = db_query($a_sql_query);
                if ($result == FALSE) {
                    $my_die = $params['db'].'@'.$params['host'].' '.$a_sql_query;
                    break;
                }
            }
            if ($result == FALSE)
                break;
        }
        $mysql_connection_id = $current_connection;
    }

    unset($pieces);
    return $my_die;
}
