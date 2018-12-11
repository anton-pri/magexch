<?php
function cw_auth_rec($tmp, $accl = array(), $flag = true) {
    $ret = array();
    if (is_array($tmp))
    foreach($tmp as $k=>$v) {
        if ((is_array($v['php']) && $accl[$k]) || $flag)
        foreach($v['php'] as $vl) {
            $vl['key'] = $k;
            $ret[md5(serialize($vl))] = $vl;
        }
        $ret = array_merge($ret, cw_auth_rec($v['sub'], $accl, $flag));
    }
    return $ret;
}

function cw_auth_perm($self, $arr_auth, $ma) {
    global $REQUEST_METHOD;
    $fl = false;
    $fl1 = true;

    extract($_REQUEST);
    $sc = cw_auth_rec($arr_auth);
    foreach($sc as $key_ => $v)
        if ($v['target'] == $self && $fl1) {
            if ($v['par'] && !$fl) {
                $str = "if ((".$v['par'].") && \$ma[\$v[\"key\"]] != \"Y\") \$fl=true; else {\$fl=false; \$fl1=false;}";
                eval($str);
            }
        }
    if ($fl) cw_header_location("index.php?target=error_message&error=access_denied");
}

function cw_auth_updates() {
    global $target;
    $check_updates = &cw_session_register('check_updates',0);
    if ($target=='docs_O' && $check_updates == 0) {
        $params = array();
        $params['api_key'] = cw_license_id();
        $params['version'] = 1;
        $params['method']  = 'check_updates';
        $params['request'] = array('domain'=>$_SERVER['HTTP_HOST'],'ip'=>gethostbyname($_SERVER['HTTP_HOST']));
        $check_updates = cw_api_server_call($params,SERVICE_SERVER.SERVICE_SERVER_SCRIPT);
        if ($check_updates) {
            cw_call('cw_updates_exist', array($check_updates['response']));
        }
    }
}

function cw_auth_check_security_targets() {
    global $target, $mode;

    if (AREA_TYPE == 'C') {
        if (in_array($target, array('docs_I', 'docs_O'))) return true;
        return false;
    }
    elseif (!in_array($target, array('index', 'login', 'ecc'))) return true;

    return false;
}

function cw_auth_security() {
    global $customer_id, $top_message, $target;

    if (!$customer_id && cw_func_call('cw_auth_check_security_targets')) {
        $top_message = array( 'type' => 'E', 'content' => cw_get_langvar_by_name('err_session_is_expired'));
        if (AREA_TYPE == 'A' && $target == 'ajax') {
            echo "{login_redirect:1}";
            exit(0);
        }
        $remember_url =& cw_session_register('remember_url', array());

        if (!is_array($remember_url))  
            $remember_url = array();

        $remember_url[AREA_TYPE] = $_SERVER['REQUEST_URI'];

        if (AREA_TYPE == 'C')
            cw_header_location('index.php?target=help&section=login_customer', true, true);
        else
            cw_header_location('index.php', true, true);
    }
    $remember_url = cw_session_register('remember_url');
    if ($customer_id && !empty($remember_url[AREA_TYPE])) {
        cw_session_unregister('remember_url');
        cw_header_location($remember_url[AREA_TYPE], true, true);
    }
    

}

function cw_on_login_crontab($customer_id, $area, $on_register=0) {
	global $config, $smarty, $app_main_dir;

    if ($area != 'A') return null;
    
	$result = array(
		'last_run_date'	=> '',
		'notify'		=> ''
	);

	// Check last cron run
	$last_run = unserialize($config['last_cron_run']);
	$diff = 1000;
	if (!empty($last_run) && is_array($last_run)) {
		$now = time();
		$diff = $now - $last_run['regular'];

		$date_format = (!empty($config['Appearance']['date_format']) ? $config['Appearance']['date_format'] : '%Y-%m-%d');
		$time_format = (!empty($config['Appearance']['time_format']) ? $config['Appearance']['time_format'] : '%H:%M:%S');

		$result['last_run_date'] = strftime(
			$date_format . ' ' . $time_format, 
			$last_run['regular'] + $config['Appearance']['timezone_offset'] * SECONDS_PER_HOUR
		);
	}

	if ($diff > 600) {
		$smarty->assign('crontab', $result);
		$smarty->assign('app_main_dir', $app_main_dir);
        $smarty->assign_by_ref('config',$config);
		$msg = $smarty->fetch('addons/dashboard/admin/sections/crontab.tpl');
		cw_system_messages_add('crontab_warning', $msg, SYSTEM_MESSAGE_COMMON, SYSTEM_MESSAGE_WARNING);
		$smarty->clear_assign(array('crontab','app_main_dir'));
	} else {
		cw_system_messages_delete('crontab_warning');
	}

}

// Get license id from license file
function cw_license_id() {
    global $app_dir;
    static $license = '';

    if (empty($license)) {
        $license = file($app_dir.'/include/LICENSE');
        $license = strtoupper(trim($license[0]));
    }

    return $license;
}

// Request "license_check" license server API
// Check returns license status
function cw_license_api_get() {
    global $app_config_file;
    
    $params = array();
    $params['api_key'] = cw_license_id();
    $params['version'] = 1;
    $params['method']  = 'license_check';
    $params['request'] = array('domain'=>$app_config_file['web']['http_host']);

    return cw_api_server_call($params,SERVICE_SERVER.SERVICE_SERVER_SCRIPT);
}


// Request license info in admin area after login and store
function cw_license_check() {
    global $config, $tables;

    // Request license check and store
    $result = cw_license_api_get();

    // Store every success check result
    if ($result['status'] == 'success') {
        cw_cache_save($result,'license');
    } 
    
    // Rewrite expired check result
    if (!($license = cw_cache_get('license'))) {
        $license = $result;
        cw_cache_save($result,'license');
    };

    $message = '';
    
    if ($license['status'] == 'success') {
        // API request is success. Let's check license response

        $check = $license['response'];
        
        if ($check['status'] == 1) {
            cw_system_messages_delete('license_check');
        } else {
            $message = $check['status_note'];
        }
        
        
    } else {
        $license['response']['level'] = 2;
        $message = ($license['status'] == 'error')?$license['message']:"License server is unreachable";
    }


    if ($message) {
		global $smarty;
		$smarty->assign('license_message',$message);
		$msg = $smarty->fetch('admin/main/licchk.tpl');
		cw_system_messages_add('license_check', $msg, SYSTEM_MESSAGE_COMMON, SYSTEM_MESSAGE_CRITICAL);
        if ($license['response']['admin_message']) {
            cw_system_messages_add('license_check_message', $license['response']['admin_message'], SYSTEM_MESSAGE_COMMON, SYSTEM_MESSAGE_ERROR);
        }

		$smarty->clear_assign(array('license_message'));
    }

    // Perform actions
    db_query("DELETE FROM $tables[languages] where name IN ('txt_lpop_warning_A','txt_lpop_warning_C')");
    if (in_array($license['response']['status'],array(3,6))) {
        // Show admin and customer messages
        //
        // update langvars txt_lpop_warning_A, txt_lpop_warning_C
        global $smarty;
        $smarty->assign('area', 'admin');
        $smarty->assign('lpop_warning', $license['response']['admin_popup']?$license['response']['admin_popup']:cw_get_langvar_by_name('txt_lpop_warning_default'));
        $value_A = $smarty->fetch('admin/main/lpop.tpl');
        $smarty->assign('area', 'customer');
        $smarty->assign('lpop_warning', $license['response']['customer_popup']?$license['response']['customer_popup']:cw_get_langvar_by_name('txt_lpop_warning_default'));
        $value_С = $smarty->fetch('admin/main/lpop.tpl');
 
        $languages = cw_query_column("SELECT code FROM $tables[languages_settings]");
        foreach ($languages as $l) {
            $data = array(
                'code' => $l,
                'name' => 'txt_lpop_warning_A',
                'topic' => 'Text',
                'value' => db_escape_string($value_A),
            );
            cw_array2insert('languages', $data, true);
            $data = array(
                'code' => $l,
                'name' => 'txt_lpop_warning_C',
                'topic' => 'Text',
                'value' => db_escape_string($value_С),
            );
            cw_array2insert('languages', $data, true);
        }
    }
    cw_cache_clean('lang');
    
    return $result;
}
