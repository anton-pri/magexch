<?php
# kornev, TOFIX
if(!$addons['Salesman'])
    cw_header_location("index.php");

$elements = cw_query ("SELECT * FROM $tables[salesman_banners_elements] ORDER BY elementid");
if(!empty($elements))
	$smarty->assign ("elements", $elements);

$smarty->assign('home_style', 'iframe');
$smarty->assign('current_section_dir', 'sales_manager');
$smarty->assign('main', 'element_list');
