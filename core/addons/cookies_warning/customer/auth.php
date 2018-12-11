<?php
if ($_COOKIE['CW_Accept_Cookies'] == 'Y') {
    $cookies_warning_enabled = false;
} else {
    $cookies_warning_enabled = true;
}

if ($REQUEST_METHOD == "POST") {
    if (($_POST['cookies_accept'])) {
        $cookies_warning_enabled = false; 
        setcookie ('CW_Accept_Cookies', 'Y', time()+31536000);

        $redirect_url = str_replace('force_cookie_warning=Y','',(!empty($_SERVER['REDIRECT_URL'])?$_SERVER['REDIRECT_URL']:$_SERVER['REQUEST_URI']));

        cw_header_location($redirect_url);
    }
}

$smarty->assign('cookies_warning_enabled', $cookies_warning_enabled);
