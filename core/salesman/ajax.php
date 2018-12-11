<?php
if ($mode == 'memberships')
    include $app_main_dir.'/include/ajax/memberships.php';
if ($mode  == 'product_by_ean')
    include $app_main_dir.'/include/ajax/product_by_ean.php';
if ($mode == 'categories')
    include $app_main_dir.'/include/ajax/categories.php';
if (in_array($mode, array('counties', 'states', 'regions', 'cities')))
    include $app_main_dir.'/include/map/ajax_countries.php';
if ($mode == 'aom')
    include $app_main_dir.'/include/orders/order_edit_ajax.php';
exit(0);
