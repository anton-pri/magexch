<?php
if ($_GET['code']) $code = $_GET['code'];
if ($error == 'disabled_cookies' && isset($ti)) {
	$save_data = cw_db_tmpread(stripslashes($ti));
	$smarty->assign('save_data', $save_data);
	$smarty->assign('ti', $ti);
}

if (in_array($error, array('need_login', 'login_incorrect'))) {
    $location[] = array(cw_get_langvar_by_name('lbl_error'), '');
	$location[] = array(cw_get_langvar_by_name('lbl_authentication'), '');
}
elseif($error == 'http') {

    if ($code == "404") {
        global $clean_url_request_uri; 
        if (!empty($clean_url_request_uri)) {
            $cr_uri = parse_url(urldecode($clean_url_request_uri));

            $suggested_searches = array(); 
            if (!empty($cr_uri['path'])) {
                $exploded_path = explode("/", $cr_uri['path']);
                if (is_array($exploded_path)) {
                    foreach ($exploded_path as $subpaths) {
                        $exploded_subpaths = explode("-", $subpaths);
                        foreach ($exploded_subpaths as $exploded_subpath)
                            $suggested_searches[] = $exploded_subpath;
                    } 
                } else { 
                    $suggested_searches[] = $cr_uri['path'];
                }
            }
            if (!empty($cr_uri['query'])) {
                parse_str($cr_uri['query'], $cr_uri_output);
                if (is_array($cr_uri_output)) {
                    $suggested_searches[] = implode(' ',array_keys($cr_uri_output));  
                    $sugg_search_line = array();
                    foreach ($cr_uri_output as $cufname => $cufval) {
                        if (is_array($cufval)) 
                            $sugg_search_line[] = implode(' ', $cufval);
                        else
                            $sugg_search_line[] = $cufval;    
                    }
                    $suggested_searches[] = implode(' ', $sugg_search_line);   
                }
            }    
            $suggested_searches = array_map('stripslashes', $suggested_searches); 
            $smarty->assign('suggested_searches', $suggested_searches);
        }
    }
    $location[] = array(cw_get_langvar_by_name('lbl_server_http_error'), '');
    $location[] = array(cw_get_langvar_by_name('lbl_server_http_error_'.$code), '');
    $smarty->assign('error_code', $code);
}
else {
    $error = isset($error) ? $error : 'general';
    $location[] = array(cw_get_langvar_by_name('lbl_error'), '');
    $smarty->assign('content', $content);
}

$login_antibot_on = cw_session_register("login_antibot_on");
$antibot_err = cw_session_register("antibot_err");
$username = &cw_session_register("username");
$smarty->assign('username', $username);
$smarty->assign('login_antibot_on', $login_antibot_on);
if ($antibot_err) {
    $smarty->assign('antibot_err', $antibot_err);
    $antibot_err = false;
}
//var_dump($error, $code,$_GET);
$smarty->assign('id', $id);
$smarty->assign('error', $error);
$smarty->assign('main', 'error_message');
