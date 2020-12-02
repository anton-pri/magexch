<?php

function cw_logs_size_format($bytes, $precision = 3) {

    $units = array('B', 'KB', 'MB', 'GB', 'TB');
/*
    $bytes = max($bytes, 0); 
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
    $pow = min($pow, count($units)-1); 

    // Uncomment one of the following alternatives
    // $bytes /= pow(1024, $pow);
    // $bytes /= (1 << (10 * $pow)); 

    return round($bytes, $precision) . ' ' . $units[$pow]; 
*/
    return round($bytes/(1024*1024), $precision) . ' MB';
}


function cw_parse_log_file_name($filename) {
    $result = array();

    $result = pathinfo($filename);

    if (strtolower($result['extension'])!='php') {
        return array();
    }

    $f_parts_name = explode('.',$result['filename']);
    $result['filename'] = reset($f_parts_name);

    $result['date'] = substr($result['filename'],-6,6);
    if (intval($result['date'])==0) {
        return array();
    }

    $result['log_name'] = rtrim(str_replace($result['date'],'',$result['filename']),'-');

    return $result;
}

function cw_scan_logs_recursive($log_path, &$registered_logs, &$logs_files_summary_size) {
    $log_files = scandir($log_path);
    foreach ($log_files as $l_file) {
        $l_file_full = $log_path.'/'.$l_file;
        if (is_dir($l_file_full)) {
            if ($l_file != '.' && $l_file != '..')  
                cw_scan_logs_recursive($l_file_full, $registered_logs, $logs_files_summary_size);
            continue;
        }

        $log_file_data = cw_parse_log_file_name($l_file);

        if (empty($log_file_data)) continue;

        if (!isset($registered_logs[$log_file_data['log_name']])) {
            $registered_logs[$log_file_data['log_name']] = array(
                'size' => filesize($l_file_full),
                'dates' => array($log_file_data['date']),
                'filenames' => array($l_file_full),
//                'filemtime' => array(filemtime($l_file_full))
            );
        } else {
            $registered_logs[$log_file_data['log_name']]['size'] += filesize($l_file_full);
            $registered_logs[$log_file_data['log_name']]['dates'][] = $log_file_data['date'];
            $registered_logs[$log_file_data['log_name']]['filenames'][] = $l_file_full;
//            $registered_logs[$log_file_data['log_name']]['filemtime'][] = filemtime($l_file_full);
        }
        $logs_files_summary_size += filesize($l_file_full);
    }
}

function cw_scan_logs_directory() {
    global $var_dirs;
    $log_path = $var_dirs['log'];

    $registered_logs = array();
    $logs_files_summary_size = 0;

    cw_scan_logs_recursive($log_path, $registered_logs, $logs_files_summary_size);

    return array($registered_logs, $logs_files_summary_size);
}

function cw_delete_logs_files($log_name, $registered_logs) {
    global $var_dirs;

    $result = array();
    if (isset($registered_logs[$log_name])) {
        foreach($registered_logs[$log_name]['filenames'] as $fname) {
            if (file_exists($fname)) {
                print("Deleting file ".$fname.'<br>');
                unlink($fname);
                $result[] = $fname;
            }  
        }
    }
    return $result;
}

function cw_action_old_logs($registered_logs, $logs_settings) {
    global $var_dirs;

    $result = array('deleted'=>array(), 'archived'=>array());

    foreach ($logs_settings as $log_name => $log_info) {
        if (!$log_info['max_days']) continue;

        if (!isset($registered_logs[$log_name])) continue;

        foreach ($registered_logs[$log_name]['filenames'] as $fname) {
            $days_passed = floor((time() - filemtime($fname))/(3600*24));

            if ($days_passed >= $log_info['max_days']) {
                print(filemtime($fname)." $fname is $days_passed days old ");
                switch ($log_info['action_after_max_days']) {
                    case 'A':
                    print(" and to be archived<br>");
                    if (!file_exists($var_dirs['log'].'/archived_logs'))
                          mkdir($var_dirs['log'].'/archived_logs');

                    $pinfo = pathinfo($fname); 
                    $tar_cmd = "tar -czf $var_dirs[log]/archived_logs/$pinfo[filename].tgz $fname";
                    print("Running command: ".$tar_cmd."<br>");
                    system($tar_cmd); unlink($fname);
                    $result['archived'][] = $fname;
                    break;
                    case 'D':
                    print(" and to be deleted<br>");
                    unlink($fname);
                    $result['deleted'][] = $fname;
                    break;
                }
            }
        }
    }
    return $result;
}


function cw_get_logs_files_and_settings() {
    global $tables, $current_language;

    $logs_settings = cw_query_hash("select * from $tables[logs_settings] order by log_name asc", 'log_name', 0, 0);
    list($registered_logs, $summary_size) = cw_scan_logs_directory();
    
    //print_r(compact('registered_logs', 'logs_settings'));

    foreach ($registered_logs as $log_name => $log_info) {
        if (!isset($logs_settings[$log_name])) {
            cw_array2insert('logs_settings', array('log_name'=>addslashes($log_name)), 1);
            $logs_settings[$log_name] = cw_query_first("select * from $tables[logs_settings] where log_name='$log_name'");
        }
        $registered_logs[$log_name]['settings'] = $logs_settings[$log_name];
        $registered_logs[$log_name]['size'] = cw_logs_size_format($registered_logs[$log_name]['size']);
        $registered_logs[$log_name]['files_count'] = count($log_info['dates']);
    }

    //print_r(compact('registered_logs'));

    foreach($logs_settings as $log_name => $log_info) {
        if (isset($registered_logs[$log_name])) continue;

        $registered_logs[$log_name] = array();
  
        $registered_logs[$log_name]['settings'] = $log_info;
        $registered_logs[$log_name]['size'] = 0;
        $registered_logs[$log_name]['files_count'] = 0;
    }

    //print_r(compact('registered_logs'));

    foreach ($registered_logs as $log_name => $log_info) {
        $lang_var_name = 'lbl_log_'.$log_name;
        if (!cw_query_first_cell("select count(*) from $tables[languages] where name='$lang_var_name' and code='$current_language'")) 
            cw_array2insert("languages", array('code'=>$current_language, 'topic'=>'Labels', 'name'=>$lang_var_name, 'value'=>ucfirst(str_replace('_',' ', $log_name))));      
    }

    return array($logs_settings, $registered_logs, $summary_size);
}
