<?php

$HTTPS = (stristr($_SERVER['HTTPS'], "on") || ($_SERVER['HTTPS'] == 1) || ($_SERVER['SERVER_PORT'] == 443));

if (in_array(APP_AREA, array('seller','customer', 'payment'))) {
	global $app_config_file, $var_dirs, $var_dirs_web, $host_data;

    $host_data = cw_query_first(
    	"select * from $tables[domains] 
    	where ".($HTTPS ? "https_host" : "http_host")." ='".$_SERVER['HTTP_HOST']."'"
    );

    if (!$host_data) {
    	$host_data = cw_call('cw_md_get_domain_data_by_alias', array(&$host_data));
    }

    if ($host_data) {    	

        // Override host data, but keep main skin dir
        $app_config_file['web'] = array_merge($app_config_file['web'], $host_data, array('skin' => $app_config_file['web']['skin']));
        
//        $var_dirs['templates'] .= $host_data['skin'];
//        $var_dirs['cache'] .= $host_data['skin'];
        
//        $var_dirs_web['cache'] .= $host_data['skin'];
        
        $host_data['languages'] = unserialize($host_data['languages']);
        if (!is_array($host_data['languages'])) $host_data['languages'] = array();
        
        $current_domain = &cw_session_register('current_domain', -1);
        $current_domain = $app_config_file['web']['domain_id'] = $host_data['domain_id'];
        cw_session_start($APP_SESS_ID);
    }
    else {
    	$host_data = array('languages' => array());
    }
}
