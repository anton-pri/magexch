<?php
global $host_data, $HTTPS;

$referer = trim(substr(@$HTTP_REFERER, 0, 255), '/');
$current_host = ($HTTPS ? 'https://' : 'http://') . $host_data['http_host'];

// don't save referer for alias host
if (strstr($referer, $current_host && !empty($host_data['http_host']))) {
    $HTTP_COOKIE_VARS['RefererCookie'] = array();
}
