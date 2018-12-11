<?php
if (!defined('APP_START')) die('Access denied');

if (!$addons['sn']) cw_header_location('index.php');

cw_load('serials');

if ($current_area == 'A')
    $serial_numbers = cw_get_serial_numbers('', $product_id);
else
    $serial_numbers = cw_get_serial_numbers($customer_id, $product_id);

if (is_array($print) && is_array($serial_numbers))
    foreach($serial_numbers as $k=>$sn)
        if (!$print[$sn['sn']]) unset($serial_numbers[$k]);

$smarty->assign('serial_numbers', $serial_numbers);
?>
