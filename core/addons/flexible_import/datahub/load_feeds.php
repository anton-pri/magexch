<?php

set_time_limit(86400);

if (function_exists('cw_flexible_import_recurring_imports'))
    cw_flexible_import_recurring_imports(0);
else
    print('addon func file is not loaded');

die('Done.');
