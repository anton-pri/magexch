<?php

set_time_limit(86400);

print("<h1>BevAccess Daily Update script</h1><br />");

global $is_interim;

cw_datahub_load_beva_daily(false, $is_interim);

$interim_ext = '';
if ($is_interim)
    $interim_ext = 'interim_';

print("<h3>$res_str...</h3><a href='index.php?target=datahub_".$interim_ext."buffer_match'>Return to main edit page</a>");
die;
