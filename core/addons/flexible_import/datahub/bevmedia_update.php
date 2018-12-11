<?php

set_time_limit(86400);
//error_reporting(E_ERROR);

print('<h2>Updating bevaccess_supplement table</h2><br><hr>');

require('core/addons/flexible_import/datahub/hub_v1/import/constants.php');
//error_reporting(E_ERROR);

bevaccess_supplement::import_and_update();

die('Done.');
