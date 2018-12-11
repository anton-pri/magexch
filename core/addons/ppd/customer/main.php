<?php

if (!defined('APP_START')) { die('The software application has not been initialized.'); }

do {

    if (!isset($addons['ppd'])) {
        break;
    }

    if (!isset($product_id) || empty($product_id)) {
        break;
    }

    require_once $app_main_dir . '/addons/ppd/func.php';

    $product_id = (int) $product_id;
    $customer_id = isset($customer_id) ? (int)$customer_id : 0;

    global $is_ppd_files;
    $is_ppd_files = false;

    $ppd_files = array();
    
    $ppd_files['for_all'] = cw_query('SELECT file_id, size, filename, title, number, fileicon, perms_all FROM ' . $tables['ppd_files'] . ' AS files LEFT JOIN ' . $tables['ppd_types'] . ' AS types ON files.type_id = types.type_id WHERE product_id = \'' . $product_id . '\' AND active = 1 AND perms_all >= 4 ORDER BY number');
    
    $ppd_files['after_purchase'] = array();
    
    if ($customer_id > 0) {
    	$current_time = cw_core_get_time();
    	$time_query_param = null;
    	if (!empty($config['ppd']['ppd_link_lifetime'])) {
    		$time_query_param = ' AND expiration_date > \'' . $current_time . '\'';
    	}
    	
    	$counter_query_param = null;
        if (!empty($config['ppd']['ppd_loading_attempts'])) {
            $counter_query_param = ' AND dloads.allowed_number > dloads.counter';
        }
        
        $ppd_files['after_purchase'] = cw_query('SELECT dloads.file_id, size, filename, title, number, fileicon, perms_owner FROM ' . $tables['ppd_downloads'] . ' AS dloads LEFT JOIN ' . $tables['ppd_files'] . ' AS files ON dloads.file_id = files.file_id LEFT JOIN ' . $tables['ppd_types'] . ' AS types ON files.type_id = types.type_id WHERE dloads.product_id = \'' . $product_id . '\' AND files.active = 1 AND files.perms_owner >= 4 AND files.perms_all = 0 AND dloads.customer_id = \'' . $customer_id . '\'' . $counter_query_param . $time_query_param . ' ORDER BY files.number');
    }
    
    if (empty($ppd_files['after_purchase']) || !is_array($ppd_files['after_purchase'])) {
        $ppd_files['after_purchase'] = cw_query('SELECT file_id, size, filename, title, number, fileicon, 1 as hide_link FROM ' . $tables['ppd_files'] . ' AS files LEFT JOIN ' . $tables['ppd_types'] . ' AS types ON files.type_id = types.type_id WHERE product_id = \'' . $product_id . '\' AND active = 1 AND perms_owner >= 4 AND perms_all = 0 ORDER BY number');
    }
    
    
    if (empty($ppd_files['for_all']) && empty($ppd_files['after_purchase'])) {
    	break;
    }
    
    $ppd_files['for_all'] = ppd_process_files($ppd_files['for_all']);
    $ppd_files['after_purchase'] = ppd_process_files($ppd_files['after_purchase']);
    
    if (empty($ppd_files['for_all']) && empty($ppd_files['after_purchase'])) {
        break;
    }
    $is_ppd_files = true;

    $GLOBALS['_init_time'] = time();
    cw_session_register('_init_time');
    
    $smarty->assign('ppd_files', $ppd_files);

} while (0);




function ppd_process_files(&$files) {
    
	if (empty($files) || !is_array($files)) {
		return array();
	}
	
	foreach ($files as $key => $file) {
        $_real_path = ppd_check_path($file['filename']);
        if (empty($_real_path)) {
            unset($files[$key]);
            continue;
        }
        $files[$key]['size'] = ppd_convertfrom_bytes($file['size']);
        $files[$key]['fileicon'] = ppd_get_url_fileicon($file['fileicon']);
    }
    
    return $files;
}


?>
