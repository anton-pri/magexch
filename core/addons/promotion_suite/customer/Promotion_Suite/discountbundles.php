<?php
// CartWorks.com - Promotion Suite 

if (!defined('XCART_START')) { header("Location: ../../"); die("Access denied"); }
if (empty($addons["Promotion_Suite"])) return;

$discountbundles_pids = cw_query_column("SELECT objid FROM $tables[bonuses] b, $tables[bonus_conditions] bc WHERE b.pid='$productid' AND b.bonusid=bc.bonusid AND bc.type='P'");
$discountbundles = array('products'=>array(), 'taxed_price'=>0, 'discount_amount'=>0, 'discounted_price'=>0, 'discount'=>array());
if (!empty($discountbundles_pids)) {
	$discountbundles['discount'] = unserialize(cw_query_first_cell("SELECT data FROM  $tables[bonuses] b, $tables[bonus_supply] bs WHERE b.pid='$productid' AND b.bonusid=bs.bonusid AND bs.type='D'"));
	$pids_query['where'][] = $tables['products'].".productid IN ('".implode($discountbundles_pids,"','")."')";
	$discountbundles['products'] = cw_search_products($pids_query,$userinfo['membershipid']);
	if (!empty($discountbundles['products'])) {
		foreach ($discountbundles['products'] as $bt_product) {
			if ($discountbundles['discount']['discount_type'] == 'absolute' && $bt_product['taxed_price']<$discountbundles['discount']['discount']) {
				# Absolute discount is greater than one of the product price. Whole bundle is invalid.
				unset($discountbundles_pids, $pids_query, $discountbundles);
				return false;
			}
			$discountbundles['taxed_price'] += $bt_product['taxed_price'];
		}
		if ($discountbundles['discount']['discount_type'] == 'percent') $discountbundles['discount_amount'] = $discountbundles['taxed_price']*$discountbundles['discount']['discount']/100;
		else $discountbundles['discount_amount'] = $discountbundles['discount']['discount']*count($discountbundles_pids);
		$discountbundles['discounted_price'] = $discountbundles['taxed_price'] - $discountbundles['discount_amount'];
	}
	$smarty->assign_by_ref('discountbundles',$discountbundles);
	unset($discountbundles_pids, $pids_query);
}

// CartWorks.com - Promotion Suite 
?>
