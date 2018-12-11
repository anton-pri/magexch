<?php
$is_cart = $is_ajax ? 'N' : 'Y';
$smarty->assign('is_cart', $is_cart);

cw_load('cart', 'cart_process', 'user', 'taxes', 'profile_fields', 'category', 'warehouse', 'attributes', 'checkout');

# kornev, we are define this globaly for the speed
$userinfo = cw_call('cw_checkout_userinfo', array($user_account));

$cart = &cw_session_register('cart', array());

/*
print('<pre>'); 
print_r($cart);
print('</pre>');
*/

$top_message = &cw_session_register('top_message', array());

cw_call('cw_payment_interruption');

if ($action == 'address_update')  $action = 'update'; // TOFIX: This action is still used on checkout

if ($action == 'clear_cart') {
    cw_event('on_clear_cart', array($cart));

    if (!empty($cart['products']) && is_array($cart['products'])) {
        foreach ($cart['products'] as $k => $v) {
            cw_call_delayed('cw_product_run_counter', array('product_id' => $v['product_id'], 'count' => 1, 'type' => 2));
        }
    }

	$cart = array();
	cw_header_location('index.php?target='.$target);
}

if ($action == "add_order") {
    if (empty($customer_id)) {
        $top_message['type'] = "W";
        $top_message['content'] = cw_get_langvar_by_name("lng_please_logon_to_see_the_order");
        cw_header_location('index.php?target='.$target);
    }
    cw_load('salesman_orders', 'warehouse');
    $order = cw_call('cw_get_salesman_order',array($doc_id));
    if (count($order['products']) && $customer_id == $order['customer'] && $order['status'] == 1) {
        foreach($order['products'] as $product) {
            cw_call('cw_warehouse_add_to_cart_simple',array($product['product_id'], $product['amount'], $product['product_options'], $product['price'], $order['id']));
        }
        cw_event('on_add_order_cart',array($doc_id));
    }
    else {
        $top_message['type'] = "E";
        $top_message['content'] = cw_get_langvar_by_name("lng_you_havnt_access_to_order");
    }
    cw_header_location('index.php?target='.$target);
}

if ($action == 'add' && !empty($product_id)) {
	// if in cart products from invoice, then new product can't be added
	if (!empty($cart['info']['quote_doc_id'])) {
		$top_message['type'] = "E";
		$top_message['content'] = cw_get_langvar_by_name("err_add_product_to_cart_with_quote");
		cw_header_location('index.php?target='.$target);
	}

    if (is_array($amount)) {
        foreach($amount as $key=>$val) {
            if (empty($val)) continue;
        	$add_product = array();
        	$add_product['product_id'] = abs(intval($product_id));
        	$add_product['amount'] = abs(intval($val));
        	$add_product['product_options'] = $product_options;
        	$add_product['price'] = abs(doubleval($price));
            $add_product['warehouse_customer_id'] = $key;
	        $result = cw_call('cw_add_to_cart', array(&$cart, $add_product));
        }
    }
    else {
        cw_load('warehouse');
        $result = cw_call('cw_warehouse_add_to_cart_simple', array($product_id, $amount, $product_options, $price));
    }

    if ($addons['accessories']) {
        cw_include('addons/accessories/add_to_cart.php');
    }

	if (!empty($result['redirect_to']))
		cw_header_location($result['redirect_to']);

    $products = cw_call('cw_products_in_cart',array($cart, $userinfo));
    $cart = cw_func_call('cw_cart_actions', array('action' => $action, 'products' => $products, 'userinfo' => $userinfo), $cart);
	$cart = cw_func_call('cw_cart_calc', array('cart' => $cart, 'products' => $products, 'userinfo' => $userinfo));

    cw_event('on_add_cart',array(&$cart, $result));

	#
	# Redirect
	# Use DO_NOT_REDIRECT_CART if you call cart.php from other controllers and want get control back to your controller
    if (!defined('DO_NOT_REDIRECT_CART') || constant('DO_NOT_REDIRECT_CART')!==true) {
        if ($config['General']['redirect_to_cart'] == "Y" || $redirect_to_cart) {
            cw_header_location('index.php?target='.$target);
        }
        else {
            cw_save_customer_cart($customer_id, $cart);
            if (!empty($HTTP_REFERER)) {
                $tmp = parse_url($HTTP_REFERER);
                if ($config['General']['return_to_dynamic_part'] == "Y" && $is_hc == "Y" && (strpos($tmp['path'], ".html") !== false || substr($tmp['path'], -1) == "/")) {
                    if(substr($tmp['path'], -1) == "/")
                        cw_header_location("index.php?target=index");
                    elseif (strpos($HTTP_REFERER, "-c-") !== false)
                        cw_header_location("index.php?cat=$cat&page=$page");
                    else
                        cw_header_location("index.php?target=product&product_id=".$add_product['product_id']);
                }
                else
                    cw_header_location($HTTP_REFERER);
            }
            else
                cw_header_location("index.php?target=product&cat=$cat&page=$page");
        }
    }
}

if ($action == "delete" && !empty($productindex)) {
	// product from invoice can't be deleted
	if (!empty($cart['info']['quote_doc_id'])) {
		$top_message['type'] = "E";
		$top_message['content'] = cw_get_langvar_by_name("err_delete_product_from_cart_with_quote");
		cw_header_location('index.php?target='.$target);
	}
    cw_event('on_delete_cart',array($productindex));

    $product_id = cw_delete_from_cart($cart, $productindex);

    $products = cw_call('cw_products_in_cart',array($cart, $userinfo));
    $cart = cw_func_call('cw_cart_calc', array('cart' => $cart, 'products' => $products, 'userinfo' => $userinfo));
    cw_add_top_message(cw_get_langvar_by_name('msg_product_deleted_from_cart'));
	cw_header_location('index.php?target='.$target);
}

if (in_array($action, array('update', 'ajax_update', 'checkout'))) {
	if (!empty($productindexes)) {
        $warehouse_selection = array();

                cw_event('on_cart_productindexes_update', array(&$cart, $productindexes));

		$min_amount_warns = cw_call('cw_update_quantity_in_cart', array(&$cart, $productindexes, $warehouse_selection));

		if (!empty($min_amount_warns) && !empty($cart['products'])) {
			$min_amount_ids = array();
			foreach ($cart['products'] as $k => $v) {
				if (!isset($min_amount_warns[$v['cartid']])
				||  !isset($productindexes[$k])
				||   isset($min_amount_ids[$v['product_id']])) {
					continue;
				}

				$product_name = cw_query_first_cell("SELECT IF($tables[products_lng].product IS NULL OR $tables[products_lng].product = '', $tables[products].product, $tables[products_lng].product) as product FROM $tables[products] LEFT JOIN $tables[products_lng] ON $tables[products].product_id = $tables[products_lng].product_id AND $tables[products_lng].code = '$current_language' WHERE $tables[products].product_id = '$v[product_id]'");
				cw_add_top_message(cw_get_langvar_by_name('lbl_cannot_buy_less_X', array('quantity' => $min_amount_warns[$v['cartid']], 'product' => $product_name)), 'W');
				$min_amount_ids[$v['product_id']] = true;
			}

		}
	}

	if (!empty($shipping_id))
		$cart['info']['shipping_id'] = $shipping_id;

    if (!empty($payment_id))
        $cart['info']['payment_id'] = $payment_id;

    if (!empty($shipping_arr))
        $cart['info']['shipping_arr'] = $shipping_arr;
    if (!empty($carrier_arr))
        $cart['info']['carrier_arr'] = $carrier_arr;

    if (!isset($cart['info']['quote_doc_id']) || empty($cart['info']['quote_doc_id'])) {
        $products = cw_call('cw_products_in_cart',array($cart, $userinfo));
    	$cart = cw_func_call('cw_cart_calc', array('cart' => $cart, 'products' => $products, 'userinfo' => $userinfo));
    }

    if (!$is_ajax) {
        $url_args[] = "target=$target";
        if ($action == 'checkout') $url_args[] = 'mode=checkout';
        if ($mode) $url_args[] = 'mode='.$mode;
        $url_args[] = "step=".(++$step);
        cw_header_location('index.php?'.implode('&', $url_args));
    }
}

$products = cw_call('cw_products_in_cart', array($cart, $userinfo));

if (!empty($cart["products"]) && is_array($products) && count($products) < count($cart["products"])) {
        #
        # The products array in the cart does not accord to the store
        #
        foreach ($products as $k=>$v)
            $prodids[] = $v["cartid"];

        if (is_array($prodids)) {
            foreach ($cart["products"] as $k=>$v) {
                if (in_array($v["cartid"], $prodids))
                    $cart_prods[$k] = $v;
            }

            $cart["products"] = $cart_prods;
        }
        else {
            $cart = "";
        }

        cw_header_location("cart.php?$QUERY_STRING");
}

$cart = cw_func_call('cw_cart_actions', array('action' => $action, 'products' => $products, 'userinfo' => $userinfo), $cart);

if ($action == 'ajax_update') {
    $wcart = cw_func_call('cw_cart_get_warehouses_cart', array('cart' => $cart, 'products' => $products, 'userinfo' => $userinfo));
    $smarty->assign('warehouses_cart', $wcart);
    $smarty->assign('expired', !count($products));
    $smarty->assign('products', $products);

    cw_display('customer/cart/ajax_cart_js.tpl', $smarty);
    exit(0);
}

# kornev, check the requirements before the checkout
if ($mode == 'checkout') {
    if (cw_is_cart_empty($cart)) cw_header_location('index.php?target='.$target);

	cw_session_unregister('secure_oid');

    if ($cart['info']['display_subtotal'] < $config['General']['minimal_order_amount'] && $config['General']['minimal_order_amount'] > 0)
    	cw_header_location('index.php?target=error_message&error=min_order');

    if ($config['General']['maximum_order_amount'] > 0 && $cart['info']['display_subtotal'] > $config['General']['maximum_order_amount'])
        cw_header_location("index.php?target=error_message&max_order");

    if ($config['General']['maximum_order_items'] > 0 && cw_cart_count_items($cart) > $config['General']['maximum_order_items'])
        cw_header_location("index.php?target=error_message&error=max_items");

    $fields_area = cw_profile_fields_get_area($customer_id, $salesman_membership, 1);

    list($profile_sections, $profile_fields, $additional_fields) = cw_profile_fields_get_sections('U', true, $fields_area);
# kornev, the web information is not required here.
    if ($customer_id) unset($profile_sections['web']);
    $smarty->assign('userinfo', $userinfo);
    $smarty->assign('profile_fields', $profile_fields);
    $smarty->assign('profile_sections', $profile_sections);

    cw_addons_add_css('customer/checkout/opc.css');

    cw_call('cw_checkout_prepare');
}

$giftcerts = (!empty($cart['giftcerts']) ? $cart['giftcerts'] : array());

$wcart = cw_func_call('cw_cart_get_warehouses_cart', array('cart' => $cart, 'products' => $products, 'userinfo' => $userinfo));
$smarty->assign('warehouses_cart', $wcart);

if (!cw_is_cart_empty($cart)) {
	$smarty->assign('products', $products);
	$smarty->assign('cart_products', cw_warehouse_group_products($products));
	$smarty->assign('giftcerts', $giftcerts);
}
$smarty->assign('from_quote', (!empty($cart['info']['quote_doc_id']) ? 1 : 0));

cw_save_customer_cart($customer_id, $cart);

if ($addons['recommended_products']) {
    $config['product']['number_of_recommends'] = 4;
    cw_include('addons/recommended_products/recommends.php');
}

if ($action == 'print')
    $smarty->assign('home_style', 'popup');

$smarty->assign('current_section_dir', 'cart');
if (!in_array($mode, array('auth', 'checkout', 'order_message'))) {
    $mode = 'cart';
    $location[] = array(cw_get_langvar_by_name('lbl_cart'), '');
}
$smarty->assign('main', $mode);
$smarty->assign('action', $action);

if($mode == 'checkout') {
    if ($action == 'simple_action') {
        cw_call('cw_checkout_show_cart', array('action' => $action));
    } 
    if ($action == 'show_cart') {
        cw_call('cw_checkout_show_cart', array('action' => $action));
    } 
/*    elseif ($action == 'register' && $is_ajax) {
        cw_func_call('cw_checkout_register', array('update_fields' => $update_fields));

    }
*/
    if ($action == 'update' && $is_ajax) {
        cw_call('cw_checkout_login');
    }
    //elseif ($is_ajax) { return; }

    $location[] = array(cw_get_langvar_by_name('lbl_checkout'), '');
    cw_addons_add_js('js/one_step_checkout.js');
    $smarty->assign('current_section_dir', 'checkout');
    $smarty->assign('main', 'index');
}
//cw_var_dump($cart);
