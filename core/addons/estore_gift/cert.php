<?php
global $app_dir, $app_skin_dir, $app_main_dir, $tables, $config, $smarty;
global $cart, $customer_id, $userinfo;

if (!empty($gc_id) && !empty($customer_id)) {
    $gc_array = cw_query_first("select * from $tables[giftcerts] where gc_id='$gc_id'");
    $smarty->assign('gc_id', $gc_id);
    $smarty->assign('gc_array', $gc_array);
}
elseif (
	$action == "gc2cart" 
	|| $action == "addgc2wl" 
	|| $mode == "preview"
) {
	// if in cart products from invoice, then new giftcert can't be added
	if (!empty($cart['info']['quote_doc_id'])) {
		$top_message = &cw_session_register('top_message', array());
		$top_message['type'] = "E";
		$top_message['content'] = cw_get_langvar_by_name("err_add_product_to_cart_with_quote");
		cw_header_location('index.php?target=cart');
	}

    $fill_error = (empty($purchaser) || empty($recipient_to));
    $amount_error = (
    	($amount < $config['estore_gift']['min_gc_amount']) 
    	|| ($config['estore_gift']['max_gc_amount'] > 0 && $amount > $config['estore_gift']['max_gc_amount'])
    );

    if ($send_via == "E") {
        // Send via Email
        $fill_error = ($fill_error || empty($recipient_email));

        $giftcert = array (
            "purchaser" 		=> stripslashes($purchaser),
            "recipient" 		=> stripslashes($recipient_to),
            "message" 			=> stripslashes($message),
            "amount" 			=> $amount,
            "send_via" 			=> $send_via,
            "recipient_email" 	=> $recipient_email
        );
    }
    else {
        // Send via Postal Mail
        $has_states = (cw_query_first_cell("SELECT display_states FROM $tables[map_countries] WHERE code = '" . $recipient['country'] . "'") == 'Y');
        $fill_error = (
        	$fill_error 
        	|| empty($recipient_firstname) 
        	|| empty($recipient_lastname) 
        	|| empty($recipient_address) 
        	|| empty($recipient_city) 
        	|| empty($recipient_zipcode) 
        	|| (empty($recipient['state']) && $has_states) 
        	|| empty($recipient['country']) 
        );

		if (
            $config['estore_gift']['allow_customer_select_tpl'] != 'Y'
            || cw_gift_wrong_template($gc_template)
        ) {
            $gc_template = $config['estore_gift']['default_giftcert_template'];
        }

        $gc_template = stripslashes($gc_template);

        $giftcert = array (
            "purchaser" 			=> stripslashes($purchaser),
            "recipient" 			=> stripslashes($recipient_to),
            "message" 				=> stripslashes($message),
            "amount" 				=> $amount,
            "send_via" 				=> $send_via,
            "recipient_firstname" 	=> stripslashes($recipient_firstname),
            "recipient_lastname" 	=> stripslashes($recipient_lastname),
            "recipient_address" 	=> stripslashes($recipient_address),
            "recipient_city" 		=> stripslashes($recipient_city),
            "recipient_zipcode" 	=> $recipient_zipcode,
            "recipient_state" 		=> $recipient['state'],
            "recipient_statename" 	=> cw_get_state($recipient['state'], $recipient['country']),
            "recipient_country" 	=> $recipient['country'],
            "recipient_countryname" => cw_get_country($recipient['country']),
            "recipient_phone" 		=> $recipient_phone,
            "tpl_file" 				=> $gc_template
        );
    }

    if (!$fill_error && !$amount_error) {

        if ($action == "addgc2wl")
            include $app_main_dir . '/addons/estore_gift/wishlist.php';

        if ($mode == "preview") {
            $smarty->assign('giftcerts', array($giftcert));

            header("Content-Type: text/html");
            header("Content-Disposition: inline; filename=giftcertificates.html");

            $_tmp_smarty_debug = $smarty->debugging;
            $smarty->debugging = false;

            if (!empty($gc_template)) {
                $css_file = preg_replace('/\.tpl$/', '.css', $gc_template);
                $css_fullpath = $app_dir . $app_skin_dir . '/addons/estore_gift/' . $css_file;

                if (
                    file_exists($css_fullpath)
                    && $css_file != $gc_template
                ) {
                    $smarty->assign('css_file', $css_file);
                }
            }

            cw_display("addons/estore_gift/gc_customer_print.tpl", $smarty);
            $smarty->debugging = $_tmp_smarty_debug;
            exit;
        }

        if (isset($gcindex) && isset($cart['giftcerts'][$gcindex])) {
            $cart['giftcerts'][$gcindex] = $giftcert;
        }
        else {
            $cart['giftcerts'][] = $giftcert;
        }
        
        $products = cw_call('cw_products_in_cart',array($cart, $userinfo));
    	$cart = cw_func_call('cw_cart_calc', array('cart' => $cart, 'products' => $products, 'userinfo' => $userinfo));

        cw_header_location("index.php?target=cart");
    }
}
elseif ($action == "delgc") {
	if (!empty($cart['info']['quote_doc_id'])) {
		$top_message = &cw_session_register('top_message', array());
		$top_message['type'] = "E";
		$top_message['content'] = cw_get_langvar_by_name("err_delete_product_from_cart_with_quote");
		cw_header_location('index.php?target=cart');
	}

	array_splice($cart['giftcerts'], $gcindex, 1);
    $products = cw_call('cw_products_in_cart',array($cart, $userinfo));
    $cart = cw_func_call('cw_cart_calc', array('cart' => $cart, 'products' => $products, 'userinfo' => $userinfo));
	cw_header_location("index.php?target=cart");
}

if (empty($fill_error) && empty($amount_error)) {

	if ($action == "wl") {
		$smarty->assign('giftcert', unserialize(cw_query_first_cell("SELECT object FROM $tables[wishlist] WHERE wishlist_id='$gcindex'")));
		$smarty->assign('action', 'wl');
		$smarty->assign('gcindex', $gcindex);
	}
	elseif (isset($gcindex) && isset($cart['giftcerts'][$gcindex])) {
		$smarty->assign('giftcert', @$cart['giftcerts'][$gcindex]);
		$smarty->assign('gcindex', $gcindex);
	}
}
else {
	$smarty->assign('giftcert', $giftcert);
	$smarty->assign('fill_error', $fill_error);
	$smarty->assign('amount_error', $amount_error);
}

$smarty->assign('min_gc_amount', $config['estore_gift']['min_gc_amount']);
$smarty->assign('max_gc_amount', $config['estore_gift']['max_gc_amount']);

$smarty->assign("profile_fields",
    array(
        "recipient[state]" => array("avail" => "Y", "required" => "Y"),
        "recipient[country]" => array("avail" => "Y", "required" => "Y")
    )
);

if (empty($country)) {
	$country = $config['General']['default_country'];
}

if (!$userinfo) {
    $userinfo = cw_user_get_info($customer_id, 1);
}
cw_load('map');
$smarty->assign('countries', cw_map_get_countries());
$smarty->assign('states', cw_map_get_states($country));
$smarty->assign('userinfo', isset($userinfo['main_address']) ? $userinfo['main_address'] : array());
$smarty->assign('from_quote', (!empty($cart['info']['quote_doc_id']) ? 1 : 0));

$smarty->assign('gc_templates', cw_gift_get_templates($app_dir . $app_skin_dir));
$smarty->assign('allow_tpl', true);
$smarty->assign('mode', $mode);

$location[] = array(cw_get_langvar_by_name('lbl_gift_certificate', ''));
$smarty->assign('current_main_dir', 'addons/estore_gift');
$smarty->assign('current_section_dir','');
$smarty->assign('main', 'cert');
