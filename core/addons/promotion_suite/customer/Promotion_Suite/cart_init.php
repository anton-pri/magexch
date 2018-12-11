<?php
// CartWorks.com - Promotion Suite

if (!defined('XCART_START')) { header("Location: ../../"); die("Access denied"); }
if (empty($addons["Promotion_Suite"])) return;

    x_load(
        'cart',
        'shipping',
        'product',
        'user'
    );
if (!empty($logged_userid))
	$userinfo = cw_userinfo($logged_userid, $current_area, false, false, "H");

if (!$cw_is_cart_empty && !in_array($mode, array("wishlist","wl2cart"))) {
# array of bonuses and supplies which are applicable to the cart
# $special_offers_apply = array(
#	"supply" = array (
#		[bonusid.subindex] = array (
#			[bonus_type] = <scalar or array data>
#			[bonus_type] = <scalar or array data>
#			...
#		...
#		)
#	"free_shipping" = array (
#		"type" = N|Y|C|S
#		"products" = array (
#			[product_id] = <quantity>
#			...
#       "method" = array (<shippingid>)
#		)
#	)
#	"free_products" = array (
#		[product_id] = <quantity>
#		...
#	)
#	"discount" = array (
#		"type" = N|Y|S
#		"discount" = <discount>
#		"discount_type" = "percent"|"absolute"
#		"max_discount" = <discount_amount>
#		"products" = array (
#			[product_id] = array (
#				"discount" = <discount>
#				"discount_type" = "percent"|"absolute"
#				"max_discount" = <discount_amount>
#				)
#			...
#		)
#	)
# )
x_session_register("special_offers_apply");
$special_offers_apply = array();
$special_offers_apply['free_shipping'] = false;

$join_statement = $where_statement = '';
if ($domain_info){
	$join_statement = " LEFT JOIN $tables[domain_bonuses] ON $tables[domain_bonuses].bonusid = b.bonusid AND $tables[domain_bonuses].domainid = $domain_info[domainid]";
	$where_statement = " AND $tables[domain_bonuses].bonusid IS NOT NULL";
}
$bonuses = cw_query("SELECT b.bonusid, b.exclusive, b.repeat, b.pid FROM $tables[bonuses] b $join_statement WHERE b.bonus_active='Y' AND b.start_date<UNIX_TIMESTAMP() and (b.end_date+86400)>UNIX_TIMESTAMP() $where_statement ORDER BY priority");

	# Delete all free added products
	foreach ($cart['products'] as $kk=>$vv) {
		if ($vv["special_offer"]["free_product"] == 'Y') cw_delete_from_cart($cart,$vv['cartid']);
		else unset($cart['products'][$kk]['special_offer']);
	}
	# / Delete all free added products

$affected_product_ids = array("all"=> false, "ids"=>array());

if (!empty($bonuses) and is_array($bonuses)) {

    $bonus_subindex = 0;

    for($k=0; $k<count($bonuses); $k++) {

        $b = $bonuses[$k];

        $bid = $b['bonusid'].'.'.($bonus_subindex++); // Add subindex, e.g. 10->10.2

		# Pass the bonus if it's exclusive but some other bonuses already applied
		if ($b['exclusive']=='Y' && !empty($special_offers_apply['supply'])) continue;

		# save the current affected products in case the whole condition is not met
		$_affected_product_ids = $affected_product_ids;

		if (cw_special_offer_check($b['bonusid'])) {

			$current_bonus_supply  = cw_query_hash("SELECT type, data FROM $tables[bonus_supply] WHERE bonusid='$b[bonusid]'","type",false,true);
			$current_bonus_supply  = array_map("unserialize", $current_bonus_supply);

			# Add applicable bonus info
			$special_offers_apply['supply'][$bid] = $current_bonus_supply;

			#
			# Prepare common array of supplies
			#
            $special_offers_apply['free_shipping']['method'] = cw_array_merge($special_offers_apply['free_shipping']['method'],$current_bonus_supply['S']['method']);
			if ($current_bonus_supply['S']['type']=='Y') $special_offers_apply['free_shipping']['type']='Y';
			if (in_array($current_bonus_supply['S']['type'],array('C','S')) && $special_offers_apply['free_shipping']['type']!='Y') $special_offers_apply['free_shipping']['type']=$current_bonus_supply['S']['type'];
			if ($current_bonus_supply['S']['type']=='C') {
				foreach($_affected_product_ids['ids'] as $pid=>$qty)
					$special_offers_apply['free_shipping']['products'][$pid] += ($qty-$affected_product_ids['ids'][$pid]);
			}
			if ($current_bonus_supply['S']['type']=='S') {
				if (is_array($current_bonus_supply['S']['product']))
				foreach($current_bonus_supply['S']['product'] as $i=>$product)
					$special_offers_apply['free_shipping']['products'][$product['pid']] += $product['quantity'];

				if (is_array($current_bonus_supply['S']['category']))
				foreach ($current_bonus_supply['S']['category'] as $k=>$v) {
					$used_quantity = 0;
					foreach ($cart['products'] as $kk=>$vv) {
						$cat_lpos = cw_query_column("SELECT cat.lpos FROM $tables[products_categories] pc, $tables[categories] cat
						WHERE pc.productid='$vv[productid]' AND pc.categoryid=cat.categoryid");
						foreach($cat_lpos as $lpos) {
							$is_parent = cw_query_first_cell("SELECT cat.categoryid FROM $tables[categories] cat WHERE cat.categoryid='$v[cid]' AND $lpos BETWEEN cat.lpos AND cat.rpos");
							if (!empty($is_parent)) break;
						}

						if (!empty($is_parent)) {
							$special_offers_apply['free_shipping']['products'][$vv['productid']] += min($vv['amount'],$v['quantity']);
							$v['quantity'] -= min($vv['amount'],$v['quantity']);
						}
					}
				}
			}

			if (!empty($special_offers_apply['supply'][$bid]['P']))
				foreach ($special_offers_apply['supply'][$bid]['P'] as $v)
					$special_offers_apply['free_products'][$v['pid']] += $v['quantity'];

			# Update set of affected products.
			# Discount Bundles do not capture products, so products could be used later by regular offers
			if (empty($b['pid'])) $affected_product_ids = $_affected_product_ids;

			if ($b['exclusive']=='Y') break;

            $bonuses[$k]['repeat']--;
            $repeatable_conditions = cw_query_first_cell("SELECT count(conditionid) FROM $tables[bonus_conditions] WHERE bonusid='$b[bonusid]' AND type IN ('C','P','M') AND quantity>0");
            if ($repeatable_conditions && $bonuses[$k]['repeat'] > 0) {
                // Repeat this offer again
                array_splice($bonuses,$k,0,array($bonuses[$k]));
            };

		}
	}

	$cart['special_offeres'] = $special_offers_apply;

}
}

# Customers Like you also purchased
if (!empty($product_added) && is_numeric($product_added)) {

if ($config['Promotion_Suite']['customers_like_you_enable']=='Y') {
	$also_pids = cw_query_column("SELECT productid, count(productid) as cnt
FROM $tables[order_details]
WHERE
productid != '$product_added' AND
orderid IN (
	SELECT od.orderid FROM $tables[order_details] od, $tables[orders] o WHERE productid='$product_added' AND o.orderid=od.orderid AND
o.date>(UNIX_TIMESTAMP()-(".$config['Promotion_Suite']['customers_like_you_period']."*7*24*3600)) AND o.status IN ('P','C','Q')
)
GROUP BY productid
ORDER BY cnt DESC
LIMIT ".$config['Promotion_Suite']['customers_like_you_items']);
	$query=array();
	$query['where'][] = $tables['products'].".productid IN ('".implode($also_pids,"','")."')";
	$also_products = cw_search_products($query,$userinfo['membershipid']);
	unset($query);
}

if ($discountbundles=='1') {
	$pids_query = array();
	$discountbundles_pids = cw_query_column("SELECT objid FROM $tables[bonuses] b, $tables[bonus_conditions] bc WHERE b.pid='$product_added' AND b.bonusid=bc.bonusid AND bc.type='P'");
	$pids_query['where'][] = $tables['products'].".productid IN ('".implode($discountbundles_pids,"','")."')";
	$added_products = cw_search_products($pids_query,$userinfo['membershipid']);
} else {
	$added_products[] = cw_select_product($product_added,$userinfo['membershipid']);
}
#cw_print_r($also_products);
$smarty->assign('product_added',$product_added);
$smarty->assign('added_products',$added_products);
$smarty->assign('also_products',$also_products);
unset($addons["Fast_Lane_Checkout"]);

include $xcart_dir."/addons/Promotion_Suite/recently_viewed.php";

}

if ($mode=='add' && !empty($productid) && $discountbundles=='1') {
        $minimal_amount = cw_query_first_cell("SELECT min_amount FROM $tables[products] WHERE productid='$productid'");
        $amount=max($amount,$minimal_amount);
	$discountbundles_pids = cw_query_column("SELECT objid FROM $tables[bonuses] b, $tables[bonus_conditions] bc WHERE b.pid='$productid' AND b.bonusid=bc.bonusid AND bc.type='P' AND bc.objid!='$productid'");
	foreach ($discountbundles_pids as $pid) {
	#
	# Add product to the cart
	#
	$add_product = array();
	$add_product["productid"] = $pid;
        $minimal_amount = cw_query_first_cell("SELECT min_amount FROM $tables[products] WHERE productid='$pid'");
        $add_product["amount"] = max(1,$minimal_amount);
	$add_product["product_options"] = "";
	$add_product["special_offer"]["bundle_pid"] = $productid;

	$result = cw_add_to_cart($cart, $add_product);

	}

}

// CartWorks.com - Promotion Suite
?>
