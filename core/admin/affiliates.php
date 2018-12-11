<?php
if(!$addons['Salesman'])
    cw_header_location("index.php?target=error_message&error=access_denied&id=26");

$location[] = array(cw_get_langvar_by_name("lbl_affiliates_tree"), "");

if($affiliate) {
	$_customer_id=$customer_id;
	$customer_id=stripslashes($affiliate);
	include $app_main_dir."/include/affiliates.php";
	$customer_id=$_customer_id;
	$smarty->assign('affiliate', $affiliate);
}

$salesmans = cw_query("SELECT * FROM $tables[customers] WHERE usertype = 'B' AND status = 'Y'");
if(!empty($salesmans))
	$smarty->assign('salesmans', $salesmans);

$smarty->assign('main', 'affiliates');
