<?php
global $HTTPS;

$referer = trim(substr(@$HTTP_REFERER, 0, 255), '/');
$domain = cw_mobile_get_domain_data();
$mobile_host = ($HTTPS ? 'https://' : 'http://') . $domain['mobile_host'];

// don't save referer for mobile host
if (strstr($referer, $mobile_host && !empty($domain['mobile_host']))) {
    $HTTP_COOKIE_VARS['RefererCookie'] = array();
}
