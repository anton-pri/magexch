<?php
// CartWorks.com - Promotion Suite

if (!defined('XCART_START')) { header("Location: ../../"); die("Access denied"); }
if (empty($addons["Promotion_Suite"])) return;

global $special_offers_apply;

if (!empty($special_offers_apply['free_products'])) {
	foreach($special_offers_apply['free_products'] as $pid=>$qty) {
	#
	# Add product to the cart
	#
	$avail_amount = cw_query_first_cell("SELECT avail FROM $tables[products] WHERE productid='$pid'");
    $qty = min($qty,$avail_amount);
	$min_amount = cw_query_first_cell("SELECT min_amount FROM $tables[products] WHERE productid='$pid'");
	if ($qty>=$min_amount) {
		$add_product = array();
		$add_product["productid"] = $pid;
		$add_product["amount"] = $qty;
		$add_product["product_options"] = "";
		$add_product["price"] = 0.00;

		$result = cw_add_to_cart($cart, $add_product);

		# Adjust just added product
		foreach ($cart['products'] as $ck=>$cv)
			if ($cv["cartid"]==$result['productindex']) {
				$cart['products'][$ck]["special_offer"]["free_product"] = 'Y';
				$cart['products'][$ck]["free_amount"] = $qty;
			}

	}
	else {
		unset($special_offers_apply['free_products'][$pid]);
	}
	}

	$products = cw_products_in_cart($cart, (!empty($user_account["membershipid"]) ? $user_account["membershipid"] : ""));

	# Make it really free
	foreach ($products as $k=>$v)
		if ($v["special_offer"]["free_product"] == 'Y') {
			$products[$k]['price'] = 0.00;
			$products[$k]['taxed_price'] = 0.00;
		}

	$cart = cw_array_merge($cart, cw_calculate($cart, $products, $logged_userid, $current_area, 0));

    include $xcart_dir.'/minicart.php';
}
// CartWorks.com - Promotion Suite
?>
