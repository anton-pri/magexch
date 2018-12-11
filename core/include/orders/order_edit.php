<?php
if (AREA_TYPE =='C' && $doc_data['type'][0] != 'O') return;

define('AOM', 1);

cw_load('cart', 'cart_process', 'mail', 'doc', 'product', 'taxes', 'warehouse', 'aom');

$global_store = array();

$current_carrier = &cw_session_register('current_carrier');

# This flag enables the taxes recalculation if customer profile is modified
$real_taxes = "Y";

$aom_orders = &cw_session_register('aom_orders');
if (!$aom_orders[$doc_id]) {
    $aom_orders[$doc_id] = $doc_data;

    $aom_orders[$doc_id]['info']['coupon'] = $doc_data['info']['coupon'];
    $aom_orders[$doc_id]['info']['use_discount_alt'] = 'Y';
    $aom_orders[$doc_id]['info']['discount_alt'] = $doc_data['info']['discount'];

    $aom_orders[$doc_id]['info']['use_shipping_cost_alt'] = 'Y';
    $aom_orders[$doc_id]['info']['shipping_cost_alt'] = $doc_data['info']['shipping_cost'];
    $aom_orders[$doc_id]['info']['use_shipping_insurance_alt'] = 'Y';
    $aom_orders[$doc_id]['info']['shipping_insurance_alt'] = $doc_data['info']['shipping_insurance'];

/*
    if (cw_query_first_cell("select c.code from $tables[shipping] as s, $tables[shipping_carriers] as c where c.carrier_id=s.carrier_id and s.shipping_id='".$aom_orders[$doc_id]['info']['shipping_id']."'") != 'UPS')
        $current_carrier = "";
*/
}

$aom_orders[$doc_id]['new'] = (strpos($doc_data['type'],'_') !== false);

if ($aom_orders[$doc_id]['type'] == 'G')
    $config['Taxes']['display_taxed_order_totals'] = 'Y';

/*
$aom_orders[$doc_id]['products'] = array();
cw_aom_add_new_products($aom_orders[$doc_id], array('419598'));
$doc_data = cw_doc_get($doc_id, 0);
$aom_orders[$doc_id] = cw_aom_normalize_after_update($aom_orders[$doc_id], $doc_data);
print_r($aom_orders[$doc_id]['products']);
die;
*/
/*
$doc_data = cw_doc_get($doc_id, 0);
$aom_orders[$doc_id] = cw_aom_normalize_after_update($aom_orders[$doc_id], $doc_data);
die;
print_r($aom_orders[$doc_id]);
die;
*/

if ($action == 'preview') {
    $fields_area = cw_profile_fields_get_area($aom_orders[$doc_id]['userinfo']['customer_id'], $aom_orders[$doc_id]['userinfo']['membership_id']);
    list($profile_sections, $profile_fields, $additional_fields) = cw_profile_fields_get_sections('U', true, $fields_area);
    $aom_orders[$doc_id]['userinfo']['profile_sections'] = $profile_sections;
    $aom_orders[$doc_id]['userinfo']['profile_fields'] = $profile_fields;

    $smarty->assign('orders_data', array($aom_orders[$doc_id]));

    $smarty->assign('current_section', '');
    $smarty->assign('main', 'order_print');
    $smarty->assign('home_style', 'iframe');
    $smarty->assign('is_printing', true);

    cw_display('admin/index.tpl', $smarty);
    exit;
}
if ($action == 'save') {
    if ($confirmed == "Y") {
        $aom_orders[$doc_id]['type'] = $aom_orders[$doc_id]['type'][0];
        if ($aom_orders[$doc_id]['new']) {
            $aom_orders[$doc_id]['display_doc_id'] = cw_doc_get_display_id($aom_orders[$doc_id]['type']);
        }

        $display_doc_id = $aom_orders[$doc_id]['display_doc_id'];
        $prefix = $aom_orders[$doc_id]['prefix'];
        $year   = $aom_orders[$doc_id]['year'];

        $aom_orders[$doc_id]['display_id'] = ($prefix?$prefix.' ':'').($config['order']['display_id_format']=='Y'?$year.'/':'').$display_doc_id;
	cw_aom_update_order($aom_orders[$doc_id], $doc_data['products']);
	$aom_orders[$doc_id] = null;

	if ($notify_customer) {
            $doc_data = $doc_data_customer = cw_call('cw_doc_get', array($doc_id));
            if (!empty($doc_data)) {
                $to_customer = ($userinfo['language']?$userinfo['language']:$config['default_customer_language']);
                $doc_data_customer['products'] = cw_doc_translate_products($doc_data['products'], $to_customer);
                $smarty->assign('doc_data', $doc_data_customer);

                if ($doc_data['info']['layout_id'])
                    $layout = cw_web_get_layout_by_id($doc_data['info']['layout_id']);
                else
                    $layout = cw_call('cw_web_get_layout', array('docs_'.$doc_data['type']), true);

                $smarty->assign('layout_data', $layout);
                $smarty->assign('info', $doc_data['info']);
                $smarty->assign('products', $doc_data_customer['products']);
                $smarty->assign('order', $doc_data);
                $smarty->assign('doc', $doc_data);
                $smarty->assign('is_email_invoice', 'Y');

                cw_call('cw_send_mail', array($config['Company']['orders_department'], $doc_data['userinfo']['email'], 'mail/docs/updated_doc_subj.tpl', 'mail/docs/updated_doc.tpl', null, false, true));

                $smarty->assign('is_email_invoice', 'N');
           }
        }
        cw_header_location("index.php?target=$target&mode=details&doc_id=$doc_id");
    } else {
        $js_tab = 'preview';
		$smarty->assign('confirmation', 'Y');
    }
} elseif ($action == "cancel") {
	$smarty->assign('message', 'cancel');
	unset($aom_orders[$doc_id]);
    cw_header_location("index.php?target=$target&doc_id=$doc_id&mode=edit");
}

if (cw_session_is_registered("message")) {
	$message = &cw_session_register("message");
	$smarty->assign('message', $message);
	cw_session_unregister("message");
}

$customer_membership_id = $aom_orders[$doc_id]['userinfo']['membership_id'];
if ($action == 'update_customer' && !$aom_orders[$doc_id]['saved'] && in_array(AREA_TYPE, array('P', 'A', 'G', 'B'))) {
    if ($customer_info['customer_id'] != $aom_orders[$doc_id]['userinfo']['customer_id']) {
        cw_aom_update_customer($aom_orders[$doc_id], $customer_info['customer_id']);
    }
	else {
        if ($customer_info['main_address']['address_id']) $customer_info['main_address'] = cw_user_get_address($customer_info['customer_id'], $customer_info['main_address']['address_id']);
       if ($customer_info['current_address']['address_id']) $customer_info['current_address'] = cw_user_get_address($customer_info['customer_id'], $customer_info['current_address']['address_id']);
        $aom_orders[$doc_id]['userinfo'] = cw_doc_prepare_user_information($customer_info, $aom_orders[$doc_id]['userinfo']);
    }
	cw_header_location("index.php?target=$target&doc_id=$doc_id&mode=edit&js_tab=customer");
}
if ($action == 'update_customer' && $aom_orders[$doc_id]['type'] == 'O' && in_array(AREA_TYPE, array('C'))) {
    if ($customer_info['main_address']['address_id']) $customer_info['main_address'] = cw_user_get_address($customer_info['customer_id'], $customer_info['main_address']['address_id']);
    if ($customer_info['current_address']['address_id']) $customer_info['current_address'] = cw_user_get_address($customer_info['customer_id'], $customer_info['current_address']['address_id']);
    $customer_info['membership_id'] = $user_account['membership_id'];
    $customer_info['usertype'] = $user_account['usertype'];
    $aom_orders[$doc_id]['userinfo'] = cw_doc_prepare_user_information($customer_info, $aom_orders[$doc_id]['userinfo']);
    cw_header_location("index.php?target=$target&doc_id=$doc_id&mode=edit&js_tab=customer");
}

if ($action == 'update_totals' && in_array(AREA_TYPE, array('P', 'A', 'G'))) {

    $display_doc_id = $aom_orders[$doc_id]['display_doc_id']    = $total_details['display_doc_id'];
    $prefix         = $aom_orders[$doc_id]['prefix']            = $total_details['prefix'];
    $year           = $aom_orders[$doc_id]['year'];

    $aom_orders[$doc_id]['display_id'] = ($prefix?$prefix.' ':'').($config['order']['display_id_format']=='Y'?$year.'/':'').$display_doc_id;


    if (cw_core_strtotime($total_details['date']))
        $aom_orders[$doc_id]['date'] = cw_core_strtotime($total_details['date']);

        $current_carrier = $selected_carrier;

        if ($total_details['use_shipping_cost_alt'] == "Y") {
            $total_details['shipping_cost_alt'] = cw_aom_validate_price($total_details['shipping_cost_alt']);
            $aom_orders[$doc_id]['info']['shipping_cost'] = $total_details['shipping_cost_alt'];
            $aom_orders[$doc_id]['info']['shipping_cost_alt'] = $total_details['shipping_cost_alt'];
            $aom_orders[$doc_id]['info']['use_shipping_cost_alt'] = "Y";
        }
        else {
            $aom_orders[$doc_id]['info']['use_shipping_cost_alt'] = "N";
        }

        if (!empty($total_details['use_shipping_insurance_alt'])) {
            $total_details['shipping_insurance_alt'] = cw_aom_validate_price($total_details['shipping_insurance_alt']);
            $aom_orders[$doc_id]['info']['shipping_insurance'] = $total_details['shipping_insurance_alt'];
            $aom_orders[$doc_id]['info']['shipping_insurance_alt'] = $total_details['shipping_insurance_alt'];
            $aom_orders[$doc_id]['info']['use_shipping_insurance_alt'] = "Y";
        }
        else {
            unset($aom_orders[$doc_id]['info']['use_shipping_insurance_alt']);
        }

        if (!empty($total_details['use_discount_alt']) && !empty($total_details['discount_alt'])) {
            $aom_orders[$doc_id]['info']['discount_alt'] = $aom_orders[$doc_id]['info']['discount'] = $total_details['discount_alt'] = cw_aom_validate_price($total_details['discount_alt']);
            $aom_orders[$doc_id]['info']['use_discount_alt'] = "Y";
        } else {
            unset($aom_orders[$doc_id]['info']['use_discount_alt']);
        }

        if (!empty($total_details['use_coupon_discount_alt']) && !empty($total_details['coupon_discount_alt'])) {
            $aom_orders[$doc_id]['info']['coupon_discount_alt'] = $aom_orders[$doc_id]['coupon_discount'] = $total_details['coupon_discount_alt'] = cw_aom_validate_price($total_details['coupon_discount_alt']);
            $aom_orders[$doc_id]['info']['use_coupon_discount_alt'] = "Y";
            if (empty($total_details['coupon_alt'])) {
                $aom_orders[$doc_id]['info']['coupon'] = $aom_orders[$doc_id]['info']['coupon'] = "#".$aom_orders[$doc_id]['doc_id'];
                $aom_orders[$doc_id]['info']['use_coupon_alt'] = "Y";
            }
        } else {
            unset($aom_orders[$doc_id]['info']['use_coupon_discount_alt']);
        }
        if (!empty($total_details['coupon_alt'])) {
            if ($total_details['coupon_alt'] == '__old_coupon__') {
                $aom_orders[$doc_id]['info']['coupon'] = $aom_orders[$doc_id]['info']['coupon'] = $doc_data['order']['coupon'];
                cw_unset($aom_orders[$doc_id]['info'], "use_coupon_alt");
            } else {
                $aom_orders[$doc_id]['info']['coupon'] = $aom_orders[$doc_id]['info']['coupon'] = $total_details['coupon_alt'];
                $aom_orders[$doc_id]['info']['use_coupon_alt'] = "Y";
            }
        }

        $aom_orders[$doc_id]['info']['expiration_date'] = cw_core_strtotime($total_details['expiration_date']);
        $aom_orders[$doc_id]['info']['payment_id'] = $total_details['payment_method'];
        $aom_orders[$doc_id]['info']['payment_label'] = cw_func_call('cw_payment_get_label', array('payment_id' => $total_details['payment_method']));

        if (isset($total_details['shipping_id'])) 
            $aom_orders[$doc_id]['info']['shipping_id'] = $total_details['shipping_id'];

        $aom_orders[$doc_id]['info']['shipping_label'] = cw_query_first_cell("SELECT shipping FROM $tables[shipping] WHERE shipping_id='".$total_details['shipping_id']."'");
        $aom_orders[$doc_id]['info']['salesman_customer_id'] = "";//$total_details['salesman_customer_id'];

        $aom_orders[$doc_id]['info']['cod_type_id'] = $total_details['cod_type_id'];
        $cod_info = cw_query_first("select title, leaving_type from $tables[shipping_cod_types] where cod_type_id='".$total_details['cod_type_id']."'");
        $aom_orders[$doc_id]['info']['cod_leaving_type'] = $cod_info['leaving_type'];
        $aom_orders[$doc_id]['info']['cod_type_label'] = $cod_info['title'];

        $aom_orders[$doc_id]['info']['shipment_paid'] = $total_details['shipment_paid'];

        cw_header_location("index.php?target=$target&doc_id=$doc_id&mode=edit&js_tab=totals");
}

$aom_orders[$doc_id] = cw_aom_normalize_after_update($aom_orders[$doc_id], $doc_data);

if (!isset($aom_orders[$doc_id]['info']['use_shipping_cost_alt'])) {
    if ($aom_orders[$doc_id]['info']['shipping_cost'] == $aom_orders[$doc_id]['info']['shipping_cost_alt']) 
        $aom_orders[$doc_id]['info']['use_shipping_cost_alt'] = "N";
    else
        $aom_orders[$doc_id]['info']['use_shipping_cost_alt'] = "Y";
}

$smarty->assign('cart_giftcerts', $aom_orders[$doc_id]['giftcerts']);

# user information
cw_load('map', 'profile_fields');
$smarty->assign('countries', cw_map_get_countries());

if (!$aom_orders[$doc_id]['userinfo']['usertype']) $aom_orders[$doc_id]['userinfo']['usertype'] = cw_doc_get_defaulttype($aom_orders[$doc_id]['type']);
$fields_area = cw_profile_fields_get_area($aom_orders[$doc_id]['userinfo']['customer_id'], $aom_orders[$doc_id]['userinfo']['membership_id'], 0, $aom_orders[$doc_id]['userinfo']['usertype'], true);
list($profile_sections, $profile_fields, $additional_fields) = cw_profile_fields_get_sections('U', true, $fields_area);

$smarty->assign('profile_sections', $profile_sections);
$smarty->assign('profile_fields', $profile_fields);
$smarty->assign('additional_fields', $additional_fields);
$smarty->assign('cart_customer', $aom_orders[$doc_id]['userinfo']);
$smarty->assign('customer', $doc_data['userinfo']);

$mem_usertype = $aom_orders[$doc_id]['userinfo']['usertype'];
$smarty->assign('memberships', cw_user_get_memberships($mem_usertype));

if ($real_taxes == "Y") {
	global $current_area, $customer_id, $user_account;
	$_saved_data = compact("current_area", "customer_id", "user_account");
	$current_area = $current_area == 'G'?'G':'C';
	$customer_id = $aom_orders[$doc_id]['userinfo']['customer_id'];
	$user_account = $aom_orders[$doc_id]['userinfo'];
}

$smarty->assign('payment_methods', cw_func_call('cw_payment_search', array('data' => array('type' => 1))));
//$smarty->assign('shipping', cw_func_call('cw_shipping_search', array('data' => array('active' => 1))));

//$smarty->assign('salesmen', cw_user_get_salesmans_for_register());
$smarty->assign('cod_types', cw_shipping_get_shipping_cod_types($aom_orders[$doc_id]['info']['shipping_id']));

$config['Appearance']['allow_update_quantity_in_cart'] = "N";

$smarty->assign('default_use_only_ean', 'Y');

$smarty->assign('order', $aom_orders[$doc_id]);
$smarty->assign('orig_order', $doc_data);

if ($doc_data['info']['layout_id'])
    $smarty->assign('layout_data', cw_web_get_layout_by_id($doc_data['info']['layout_id']));
else
    $smarty->assign('layout_data', cw_web_get_layout('docs_' . $docs_type));

$coupons = cw_query("select * from $tables[discount_coupons] where coupon_type != 'free_ship' ORDER BY coupon");
$coupon_exists = (cw_query_first_cell("select count(*) from $tables[discount_coupons] WHERE coupon = '".addslashes($aom_orders[$doc_id]['coupon'])."'") > 0) ? "Y" : "";
if (!empty($coupons))
	$smarty->assign('coupons', $coupons);
$smarty->assign('coupon_exists', $coupon_exists);

$smarty->assign('js_tab', $js_tab);
$smarty->assign('main', 'order_edit');
