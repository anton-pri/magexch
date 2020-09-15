<?php

if ($REQUEST_METHOD == "POST") {

    cw_load('logs');

    list($logs_settings, $registered_logs, $logs_files_summary_size) = cw_get_logs_files_and_settings();

    $act_message = array();
    foreach ($posted_settings as $log_name=>$ls) {
        if ($action == 'delete' || $action == 'delete_totally') {

            if ($ls['delete_files']) { 
                $deleted_files = cw_delete_logs_files($log_name, $registered_logs);

                if (!empty($deleted_files))
                    $act_message[] = "Deleted files of the log '$log_name'";  
            
                if ($action == 'delete_totally') {
                    db_query("delete from $tables[logs_settings] where log_name='$log_name'");
                    $act_message[] = "Deleted settings of the log '$log_name'";
                }  
            }
        } else { 
            unset($ls['delete_files']);
            cw_array2update('logs_settings', 
                        array('email_notify' => intval($ls['email_notify']),
                              'email_notify_once' => intval($ls['email_notify_once']),    
                              'max_days' => intval($ls['max_days']), 
                              'action_after_max_days' => $ls['action_after_max_days'],
                              'active' => intval($ls['active'])
                             ), 
                        "log_name='$log_name'");
            $act_message[] = "Updated settings of the log '$log_name'";  
         }

    }

    if (!empty($act_message)) 
        cw_add_top_message(implode('<br>', $act_message), 'I');   
}

cw_header_location("index.php?target=logs");
