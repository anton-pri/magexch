<?php
// TODO: DELETE THIS CONTROLLER

define("NUMBER_VARS", "amount");

cw_load('user');

if (!$addons['Gift_Certificates'])
	cw_header_location('index.php');

$cart = &cw_session_register('cart', array());

if (!$config['Gift_Certificates']['min_gc_amount'])
	$config['Gift_Certificates']['min_gc_amount'] = 0;

if (!$config['Gift_Certificates']['max_gc_amount'])
	$config['Gift_Certificates']['max_gc_amount'] = 0;

#
# Gift certificates addon
#
if (!empty($gc_id) && !empty($customer_id)) {
	$gc_array = cw_query_first("SELECT * FROM $tables[giftcerts] WHERE gc_id='$gc_id'");
	if (count($gc_array) == 0)
		$gc_array = "";

	$smarty->assign('gc_array', $gc_array);
}
elseif ($action == "gc2cart" || $action== "addgc2wl" || $mode == "preview") {
	$fill_error = (empty($purchaser) || empty($recipient));
	$amount_error = (($amount < $config['Gift_Certificates']['min_gc_amount']) || ($config['Gift_Certificates']['max_gc_amount'] > 0 && $amount > $config['Gift_Certificates']['max_gc_amount']));

	#
	# Add GC to cart
	#
	if ($send_via == "E") {
		#
		# Send via Email
		#
		$fill_error = ($fill_error || empty($recipient_email));

		$giftcert = array (
			"purchaser" => stripslashes($purchaser),
			"recipient" => stripslashes($recipient),
			"message" => stripslashes($message),
			"amount" => $amount,
			"send_via" => $send_via,
			"recipient_email" => $recipient_email
		);
	}
	else {
		#
		# Send via Postal Mail
		#
		$has_states = (cw_query_first_cell("SELECT display_states FROM $tables[map_countries] WHERE code = '".$recipient_country."'") == 'Y');
		$fill_error = ($fill_error || empty($recipient_firstname) || empty($recipient_lastname) || empty($recipient_address) || empty($recipient_city) || empty($recipient_zipcode) || (empty($recipient_state) && $has_states) || empty($recipient_country) || (empty($recipient_county) && $has_states && $config['General']['use_counties'] == "Y"));

		if (empty($gc_template) || $config['Gift_Certificates']['allow_customer_select_tpl'] != 'Y') {
			$gc_template = $config['Gift_Certificates']['default_giftcert_template'];
		}
		else {
			$gc_template = stripslashes($gc_template);
		}

		$giftcert = array (
			"purchaser" => stripslashes($purchaser),
			"recipient" => stripslashes($recipient),
			"message" => stripslashes($message),
			"amount" => $amount,
			"send_via" => $send_via,
			"recipient_firstname" => stripslashes($recipient_firstname),
			"recipient_lastname" => stripslashes($recipient_lastname),
			"recipient_address" => stripslashes($recipient_address),
			"recipient_city" => stripslashes($recipient_city),
			"recipient_zipcode" => $recipient_zipcode,
			"recipient_county" => $recipient_county,
			"recipient_countyname" => cw_get_county($recipient_county),
			"recipient_state" => $recipient_state,
			"recipient_statename" => cw_get_state($recipient_state, $recipient_country),
			"recipient_country" => $recipient_country,
			"recipient_countryname" => cw_get_country($recipient_country),
			"recipient_phone" => $recipient_phone,
			"tpl_file" => $gc_template
		);
	}

	#
	# If gcindex is empty - add
	# overwise - update
	#
	if (!$fill_error && !$amount_error) {
		if (!empty($addons['Gift_Certificates']) && $action == "addgc2wl") {
			cw_include('addons/Wishlist/wishlist.php');
		}

		if ($mode == "preview") {
			$smarty->assign('giftcerts', array($giftcert));

			header("Content-Type: text/html");
			header("Content-Disposition: inline; filename=giftcertificates.html");

			$_tmp_smarty_debug = $smarty->debugging;
			$smarty->debugging = false;

			cw_display("addons/Gift_Certificates/gc_customer_print.tpl",$smarty);
			$smarty->debugging = $_tmp_smarty_debug;
			exit;
		}

		if (isset($gcindex) && isset($cart['giftcerts'][$gcindex]))
			$cart['giftcerts'][$gcindex] = $giftcert;
		else
			$cart['giftcerts'][] = $giftcert;

		cw_header_location("index.php?target=cart");
	}
}
elseif ($action == "delgc") {
	#
	# Remove GC from cart
	#
	array_splice($cart['giftcerts'],$gcindex,1);
	cw_header_location("index.php?target=cart");
}



if ($addons['manufacturers'])
	cw_include('addons/manufacturers/customer_manufacturers.php');

require $app_main_dir."/include/countries.php";
require $app_main_dir."/include/states.php";
if ($config['General']['use_counties'] == "Y")
    include $app_main_dir."/include/counties.php";

if (empty($fill_error) && empty($amount_error)) {
	if ($action == "wl") {
		$smarty->assign('giftcert', unserialize(cw_query_first_cell("SELECT object FROM $tables[wishlist] WHERE wishlist_id='$gcindex'")));
		$smarty->assign('action', 'wl');
		$smarty->assign('wlitem', $gcindex);
	}
	elseif (isset($gcindex) && isset($cart['giftcerts'][$gcindex])) {
		$smarty->assign('giftcert', @$cart['giftcerts'][$gcindex]);
	}
}
else {
	$smarty->assign('giftcert', $giftcert);
	$smarty->assign('fill_error', $fill_error);
	$smarty->assign('amount_error', $amount_error);
}

if (!empty($customer_id))
	$smarty->assign('userinfo', cw_user_get_info($customer_id));

$smarty->assign('min_gc_amount', $config['Gift_Certificates']['min_gc_amount']);
$smarty->assign('max_gc_amount', $config['Gift_Certificates']['max_gc_amount']);

cw_session_save();

$smarty->assign("profile_fields",
	array(
		"recipient_state" => array("avail" => "Y", "required" => "Y"),
		"recipient_country" => array("avail" => "Y", "required" => "Y")
	)
);

$smarty->assign('main', "giftcert");

$location[] = array(cw_get_langvar_by_name("lbl_gift_certificate", ""));

$smarty->assign('gc_templates', cw_gc_get_templates($smarty->template_dir));
?>
