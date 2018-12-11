<?php

global $var_dirs;
if (file_exists($var_dirs['flexible_import'].'/datahub.lock')) {
    $msg = "Manually released lock file datahub.lock";
    cw_datahub_add_log_entry(array('message'=>$msg, 'source'=>"Manually run script $target"));

    @unlink($var_dirs['flexible_import'].'/datahub.lock');

    cw_add_top_message($msg, 'I');
} else {
    cw_add_top_message("Lock file ".$var_dirs['flexible_import'].'/datahub.lock not found','E');
}

global $_SERVER;

if (!empty($_SERVER['HTTP_REFERER'])) 
    cw_header_location($_SERVER['HTTP_REFERER']);

die;
