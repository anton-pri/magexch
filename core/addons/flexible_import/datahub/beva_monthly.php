<?php

set_time_limit(86400);

print("<h1>BevAccess Monthly script</h1><br />");

cw_datahub_load_beva_monthly(false, false);

print("<h3>$res_str...</h3><a href='index.php?target=datahub_buffer_match'>Return to main edit page</a>");
die;

