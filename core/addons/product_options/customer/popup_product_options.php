<?php
global $product_id;
if ($mode == 'wishlist') {
	$tmp = cw_query_first("SELECT product_id, amount FROM $tables[wishlist] WHERE wishlist_id = '$id' AND event_id = '$eventid'");
	$product_id = $tmp['product_id'];
	$min_avail = $tmp['amount'];
}
else {
    $cart = &cw_session_register('cart', array());

    if (is_array($cart['products']))
	foreach ($cart['products'] as $k => $p) {
		if ($p['cartid'] == $id) {
			$cartindex = $k;
			break;
		}
	}
	if (isset($cartindex)) {
		$product_id = $cart['products'][$cartindex]['product_id'];
# kornev, we will need to alert customer if the stock of some option is less then current amount
		$min_avail = $cart['products'][$cartindex]['amount'];
	}
}

if (empty($product_id))
	cw_close_window();

if (!$eventid)
	$eventid = '0';

if ($mode == 'wishlist')
	$options = unserialize(cw_query_first_cell("SELECT options FROM $tables[wishlist] WHERE wishlist_id = '$id' AND event_id = '$eventid'"));
else
	$options = $cart['products'][$cartindex]['options'];


if (!empty($options))
foreach ($options as $k => $v)
    $options[$k] = stripslashes($v);

cw_load('product', 'warehouse', 'cart');

$product_info = cw_func_call('cw_product_get', array('id' => $product_id, 'user_account' => $user_account));

$smarty->assign('product', $product_info);

//include $app_main_dir.'/addons/product_options/customer/product.php';
cw_include('addons/product_options/customer/product.php');

if ($REQUEST_METHOD == "POST" && $action == "update") {
	$poptions = $_POST['product_options'];

	if (!cw_check_product_options($product_id, $poptions))
		cw_header_location("index.php?target=popup_poptions&target=$target&id=$id&err=exception");

    if ($mode == 'wishlist') {
        db_query("UPDATE $tables[wishlist] SET options = '".addslashes(serialize($poptions))."' WHERE wishlist_id = '$id' AND event_id = '$eventid'");
    }
    else {
        $variant_id = cw_get_variant_id($product_options, $product_id);
        $amount = cw_warehouse_get_warehouse_avail($cart['products'][$cartindex]['warehouse'], $product_id, null, $variant_id);
//		$amount = cw_get_options_amount($poptions, $cart['products'][$cartindex]['product_id']);
		if ($amount >= $cart['products'][$cartindex]['amount']) {
			$cart['products'][$cartindex]['options'] = $poptions;
			cw_unset($cart['products'][$cartindex], 'variant_id');
		} else {
			cw_header_location("index.php?target=popup_poptions&target=$target&id=$id&err=avail");
		}

		# Recalculate cart totals after updating
        $products = cw_call('cw_products_in_cart',array($cart, $user_account));
        $cart = cw_func_call('cw_cart_calc', array('cart' => $cart, 'products' => $products, 'userinfo' => $user_account));

	}
?>
<script type="text/javascript">
<!--
if (window.opener) window.opener.location.reload();
window.close();
-->
</script>
<?php
	exit;
}

if (!$min_avail)
	$min_avail = cw_query_first_cell("SELECT min_amount FROM $tables[products] WHERE product_id = '$product_id'");

if (!$min_avail)
	$min_avail = 1;

$smarty->assign('id', $id);
$smarty->assign('mode', $mode);
$smarty->assign('eventid', $eventid);
$smarty->assign('min_avail', $min_avail);
$smarty->assign('alert_msg', 'Y');
$smarty->assign('err', $err);

$smarty->assign('home_style', 'popup');
$smarty->assign('current_main_dir', 'addons');
$smarty->assign('current_section_dir', 'product_options/main/options');
$smarty->assign('main', 'popup');
