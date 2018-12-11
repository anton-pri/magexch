<?php

$smarty->assign('main_tbl_fields', $mtf = cw_datahub_prepare_main_columns_popup(cw_datahub_get_main_table_fields()));
//print_r($mtf);

$smarty->assign('main', 'datahub_item_select_popup');
$smarty->assign('home_style', 'iframe');
define('PREVENT_XML_OUT', true);
