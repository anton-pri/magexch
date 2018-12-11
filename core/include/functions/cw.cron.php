<?php
cw_event_listen('on_cron_regular',  'cw_spool_send_mails');
cw_event_listen('on_cron_hourly',   'cw_cron_sessions_delete');
cw_event_listen('on_cron_hourly',   'cw_cron_expand_images');
cw_event_listen('on_cron_daily',    'cw_cron_invoice_check_params');
cw_event_listen('on_cron_biweekly', 'cw_cron_optimize_table');
cw_event_listen('on_cron_monthly',  'cw_cron_doc_delete_temp');
cw_event_listen('on_cron_monthly',  'cw_cron_logging_archive');

/**
 * Optimize table, try to analyze for InnoDB
 * 
 * @return array of strings with mysql replies
 */
function cw_cron_optimize_table() {
    $return = array();
    
    $tbls = cw_query_column("SHOW TABLES");
    foreach ($tbls as $t) {
        $m = cw_query_first("OPTIMIZE TABLE $t");
        $return[] = join(' | ',$m);
        if ($m['Msg_type'] != 'status') {
            $m = cw_query_first("ANALYZE TABLE $t");
            $return[] = join(' | ',$m);
        }
    }
    
    return $return;
}

function cw_cron_invoice_check_params($time) {
	global $tables, $config, $smarty;


		$expired_invoices = array();
		$notify_invoices  = array();
		$days_notify = intval($config['General']['days_notify_expiration_invoice']);

		$result = cw_query("SELECT d.doc_id, d.display_id, ui.customer_id, di.expiration_date
							FROM $tables[docs] d
							LEFT JOIN $tables[docs_info] di ON di.doc_info_id = d.doc_info_id
							LEFT JOIN $tables[docs_user_info] ui ON ui.doc_info_id = d.doc_info_id
							WHERE di.expiration_date <> 0 AND d.type = 'I' AND status <> 'C' AND status <> 'F'");
		if (!empty($result)) {

			foreach ($result as $value) {

				if (!empty($value['customer_id'])) {
					$expiration_date = $value['expiration_date'];
					$days_left = floor(($expiration_date - $time) / SECONDS_PER_DAY);					

					// invoice is expired
					if ($days_left <= 0) {
						cw_load('mail', 'doc');
						$doc_id = $value['doc_id'];
						$status = 'F';
						$expired_invoices[] = "ID:" . $doc_id . " NUMBER:" . $value['display_id'];

						cw_call('cw_doc_change_status', array($doc_id, $status));
					} 
					// X days left to invoice expiration date
					elseif ($days_notify > 0 && $days_left == $days_notify) {
						cw_load('mail', 'doc', 'web');
						$doc_id = $value['doc_id'];
						$doc_data = cw_call('cw_doc_get', array($doc_id, 8192));
						$notify_invoices[] = "ID:" . $doc_id . " NUMBER:" . $value['display_id'];
						
						if (empty($doc_data)) {
							continue;
						}
						
						if ($doc_data['info']['layout_id']) {
							$layout = cw_web_get_layout_by_id($doc_data['info']['layout_id']);
						}
						else {
							$layout = cw_web_get_layout('docs_'.$doc_data['type']);
						}
						
						$smarty->assign('layout_data', $layout);    
						$smarty->assign('info', $doc_data['info']);
						$smarty->assign('products', $doc_data['products']);
						$smarty->assign('order', $doc_data);
						$smarty->assign('doc', $doc_data);
						cw_call('cw_send_mail', array(
							$config['Company']['orders_department'], 
							$doc_data['userinfo']['email'], 
							'mail/docs/customer_subj.tpl', 
							'mail/docs/customer.tpl', 
							null, 
							false, 
							true
						));
					}
				}
			}
		}
		$message = "";
		if (count($expired_invoices)) $message = "\n Expired invoices: '" . implode(", ", $expired_invoices). "'";
		if (count($notify_invoices))  $message .= "\n Notify invoices: '" . implode(", ", $notify_invoices). "'";

    return $message;

}

function cw_cron_doc_delete_temp($time) {
	global $tables;
	$time_boundary = $time - SECONDS_PER_DAY; // delete temporary docs older than 1 day
	$docs = cw_query_column("select doc_id from $tables[docs] where type like '_\_' and date<'$time_boundary'"); // like O_ or I_
	if (is_array($docs)) {
		cw_load('doc');
		foreach ($docs as $doc_id)
			cw_call('cw_doc_delete', array($doc_id));
		return count($docs).' temporary docs were deleted';
	}
}

/**
 * Cron task.
 * Delete expired session. Allows pre- and post-processing of expired sessions
 */
function cw_cron_sessions_delete($time) {
	global $tables;

    $expired_session_ids = cw_query_column("SELECT sess_id FROM $tables[sessions_data] where expiry<$time");
    
    cw_load('user');
    cw_event_listen('on_before_session_delete','cw_user_on_before_session_delete');
    
    $log = array();
    foreach ($expired_session_ids as $sid) {
        $data = unserialize(cw_query_first_cell("SELECT data FROM $tables[sessions_data] where sess_id='$sid'"));
        $l = cw_event('on_before_session_delete', array($sid, $data), array());
        if (!empty($l)) $log[$sid] = $l;
    }

	db_query("delete from $tables[sessions_data] where expiry<$time");
	db_query("delete from $tables[temporary_data] where expire<$time");

	// get group edit data of expired sessions
	$ge_ids = cw_query_column("select $tables[group_editing].ge_id from $tables[group_editing] left join $tables[sessions_data] on $tables[group_editing].sess_id = $tables[sessions_data].sess_id where $tables[sessions_data].sess_id IS NULL");
	if ($ge_ids)
		foreach($ge_ids as $ge_id)
			db_query("delete from $tables[group_editing] where ge_id='$ge_id'");

	$log['on_sessions_delete'] = cw_event('on_sessions_delete', array($expired_session_ids),array());
    $log[] = count($expired_session_ids).' expired sessions were deleted';
    return $log;
}

/*
 * Archive and delete all logging records older than month
 */
function cw_cron_logging_archive($time) {

    global $var_dirs, $tables, $config, $app_config_file, $app_dir;

    $log = array();

    $past = $time - constant('SECONDS_PER_DAY')*30;

    if (!file_exists($var_dirs['logs_archive'])) 
     mkdir($var_dirs['logs_archive']);

    $curr_year = date('Y', $past);
    if (!file_exists($var_dirs['logs_archive'].'/'.$curr_year)) 
     mkdir($var_dirs['logs_archive'].'/'.$curr_year); 

    $curr_month = date('m', $past);
    if (!file_exists($var_dirs['logs_archive'].'/'.$curr_year.'/'.$curr_month))
     mkdir($var_dirs['logs_archive'].'/'.$curr_year.'/'.$curr_month);

    $arch_log_file_name = $curr_year.'/'.$curr_month.'/'.date('d_H_i_s', $past).'.csv';
    $arch_log_name = $var_dirs['logs_archive'].'/'.$arch_log_file_name.'.gz';

    $archive_where_string_qry = " ld.date <= $past";

    $arch_select_qry = "\"select ld.* from $tables[logged_data] as ld where $archive_where_string_qry order by logid\"";

    $mysql_db = $app_config_file['sql']['db'];
    $mysql_user = $app_config_file['sql']['user']; 
    $mysql_password = $app_config_file['sql']['password'];
    $mysql_host = $app_config_file['sql']['host'];

    $shell_comm = "echo $arch_select_qry | mysql --host=$mysql_host --user=$mysql_user --password=$mysql_password $mysql_db | gzip > $arch_log_name";

    shell_exec($shell_comm);

    if (file_exists($arch_log_name)) {  
     $log[] = "Log before ".date('Y-m-d H:i:s', $past)." saved to archive: $arch_log_name";
     if (filesize($arch_log_name) > 4096) {
        db_query("delete from ld using $tables[logged_data] as ld where $archive_where_string_qry");
        $log[] = "Archived log is cleared";
     } else {
         $log[] = "Error: Archived log seems too small and was not cleared to avoid data loss. Please check archive.";
     }

    } else {
     $log[] = 'Error: Cannot save log to file: '.$arch_log_name.'. Monthly log is not cleared.';
    }

    /**
     *  Archive logs in /var/logs
     */
    // before deletion double check that we are working in var/log
    if (!empty($var_dirs['log']) && strpos($var_dirs['log'],$app_dir)!==false && strpos($var_dirs['log'],'/var/log')!==false) {
        $arch_log_file_name = 'logs_archive_'.date('Y_m_d_His', $past).'.tgz';
        $log[] = "Archive and delete log files older than 30 days. File {$var_dirs['log']}/{$arch_log_file_name}";
        $shell = "cd {$var_dirs['log']} && find . -type f -regextype posix-extended -regex '.*-[0-9]{6}\.php' -mtime +30 -print0 | tar --remove-files -czvf {$var_dirs['log']}/{$arch_log_file_name} --null -T -";
        $log[] = $shell;
        $shell_log = shell_exec($shell);
        shell_exec("cd $app_dir"); // Return back to app root for safity
        $log[] = "\n".$shell_log;
        if (strpos($shell_log,'./php-')==false) {
            $log[] = 'Error: Unusual shell command response. Please check above in this log';
        }
    }

    return $log;
    
}

/**
 * Resize images to expand canvas to cw_available_images.min_width
 */
function cw_cron_expand_images($time, $counter) {
    global $tables, $available_images;

    define('PROCESS_IMAGES_AT_ONCE', 200);
    
    cw_load('image', 'files');
    $types = cw_query_column("SELECT name FROM $tables[available_images] WHERE min_width>0");
    
    $fail = false;
    $counter = 0;
    $log = array();
    
    foreach ($types as $type) {
        $min_width = $available_images[$type]['min_width'];
        $images = cw_query("SELECT image_id FROM {$tables[$type]} 
            WHERE (image_x < $min_width OR image_y < $min_width) AND image_y*image_x > 0 AND filename!=''
            LIMIT ".constant('PROCESS_IMAGES_AT_ONCE'));
        
        foreach ($images as $image_data) {
            $image_data =  cw_query_first("SELECT * FROM {$tables[$type]} WHERE 
                image_id = $image_data[image_id] AND (image_x < $min_width OR image_y < $min_width) LIMIT 1");
            if (empty($image_data)) continue;
            $image_path = $image_data['image_path'];
            $md5 = $image_data['md5'];
            $image_data['image_path'] = cw_realpath($image_data['image_path']);
            $result = cw_image_resize($image_data, $available_images[$type]['max_width'],$min_width);
            if ($result) {
                $similar_images = cw_query("SELECT * FROM {$tables[$type]} WHERE md5='$md5' AND image_path='$image_path'");
                foreach ($similar_images as $image) {
                    $log_str = "{$image['image_x']}x{$image['image_y']} {$image['image_size']} => {$image_data['image_x']}x{$image_data['image_y']} {$image_data['image_size']}";
                    $image['image_x'] = $image_data['image_x'];
                    $image['image_y'] = $image_data['image_y'];
                    $image['image_size'] = $image_data['image_size'];
                    $image['md5'] = $image_data['md5'];
                    cw_array2update($type, $image,"image_id = {$image['image_id']}");
                    $log[$type][$image['image_id']] = $image_path.': '.$log_str;
                }
            } else {
                $fail = true;
                $log[$type][$image_data['image_id']] = 'FAIL: check write permission for '.$image_data['image_path'];
            }
        }
            
        $counter += count($images);
        
        if ($counter >= constant('PROCESS_IMAGES_AT_ONCE')) break;
    }
    
    if ($fail == true) {
        cw_log_add('php','ERROR: Cron task cw_cron_expand_images() faced error. Please check corresponding log.');
    }
    
    return $log;
}
