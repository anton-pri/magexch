<?php
if (!defined('APP_START')) die('Access denied');

cw_load('user', 'salesman');

$affiliates = cw_get_affiliates($customer_id);
$childs_sales = 0;
if (!empty($affiliates)) {
	for ($y = 0; $y < count($affiliates); $y++)
		$childs_sales += $affiliates[$y]['sales']+$affiliates[$y]['childs_sales'];

	$smarty->assign ("affiliates", $affiliates);
}

$parent_affiliate = cw_user_get_info($customer_id);
$parent_affiliate['level'] = cw_get_affiliate_level(addslashes($customer_id));
$parent_affiliate['sales'] = cw_query_first_cell("SELECT SUM(commissions) FROM $tables[salesman_payment] WHERE salesman_customer_id='$customer_id'");
$parent_affiliate['childs_sales'] = $childs_sales;
$smarty->assign('parent_affiliate', $parent_affiliate);
?>
