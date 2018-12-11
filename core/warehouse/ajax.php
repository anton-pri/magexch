<?php
if ($mode == 'barcode') {
    cw_load('barcode');
    cw_barcode_get($barcode, $type, $width, $height);
}
if (in_array($mode, array('counties', 'states', 'regions'))) {
    include $app_main_dir.'/include/map/ajax_countries.php';
}

if ($mode == 'layout')
    include $app_main_dir.'/include/ajax/layout.php';
exit(0);
