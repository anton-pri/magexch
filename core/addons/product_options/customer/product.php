<?php
$product_options = cw_call('cw_get_product_classes', array($product_id));
$products_options_ex = cw_call('cw_get_product_exceptions', array($product_id));
$variants = cw_call('cw_get_product_variants', array($product_id, $user_account['membership_id']));

$membership_id = $user_account['membership_id'];

if (empty($options)) {
	$options = cw_call('cw_get_default_options', array($product_id, $product_info['min_amount'], $user_account['membership_id']));
}

$product_options = cw_call('cw_product_options_set_selected', array($product_options, $options));

if (!empty($product_options))
	$smarty->assign('product_options', $product_options);

if (!empty($products_options_ex))
	$smarty->assign('products_options_ex', $products_options_ex);

if (!empty($variants)) {
	foreach ($variants as $v) {
		if ($v['taxed_price'] != 0) {
			$smarty->assign('variant_price_no_empty', true);
			break;
		}
	}

	$smarty->assign('variants', $variants);
}

$smarty->assign('err', $err);
$smarty->assign('product_options_count', is_array($product_options) ? count($product_options) : 0);
