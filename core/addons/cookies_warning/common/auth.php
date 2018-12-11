<?php

//cw_log_add('cookies_warning', [$target, $_COOKIE['CW_Accept_Cookies']]);

if ($_COOKIE['CW_Accept_Cookies'] != 'Y') {
    //cw_log_add('cookies_warning', 'CW_Accept_Cookies SET Y');
    setcookie ('CW_Accept_Cookies', 'Y', time()+31536000); 
}
