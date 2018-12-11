<?php

set_time_limit(86400);
//cw_log_add('interim_load',1);

if (function_exists('cw_flexible_import_recurring_imports_interim'))
    cw_flexible_import_recurring_imports_interim(0);
else
    print('addon func file is not loaded');
//*/
die('Done.');
