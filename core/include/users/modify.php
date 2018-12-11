<?php
/* Modify controller should handle following mode
 * - update
 * - add
 */
cw_load('cart', 'category', 'crypt', 'mail', 'user', 'profile_fields', 'map', 'check_user_field', 'checkout', 'warehouse');

global $customer_id, $user;

if (AREA_TYPE=='C' && $mode=='add' && empty($update_fields['basic']['password'])) {
	$update_fields['basic']['password2'] = $update_fields['basic']['password'] = cw_user_generate_password();
}

$display_antibot = false;

$search = &cw_session_register('search', array());
$smarty->assign('purchased_products', isset($search['purchased_products'])?$search['purchased_products']:array());


/*
 * require $app_main_dir.'/include/users/register.php'; {
 */

$fill_error = &cw_session_register('fill_error', array());
$filled_profile = &cw_session_register('filled_profile', array());

$fields_area = cw_profile_fields_get_area($user, $salesman_membership, $self_modification, (AREA_TYPE == 'A'?$usertype:null));
list($profile_sections, $profile_fields, $additional_fields) = cw_profile_fields_get_sections('U', true, $fields_area);

$userphoto = array();

if ($user) {
    $userinfo = cw_call('cw_user_get_info', array($user, 65535));
    $userphoto = cw_call('cw_user_get_avatar', array($user));
}

if ($action == 'update') {
    $fill_error = array();

    $update_fields['address'] = cw_user_address_array($update_fields['address']); // make sure we processes array of addresses

    // password is not required for existing user
    if ($mode != 'add') $profile_fields['basic']['password']['is_required'] = 0;

    // handle flags "as_new" and "is_same_address"
    foreach ($update_fields['address'] as $type=>$address) {

        if ($address['as_new'])
            $update_fields['address'][$type]['address_id'] = 0;

        if (($type == 'current' || $address['address_id'] == 'current') && $update_fields['is_same_address'])
            unset($update_fields['address'][$type]); // Do not check shipping address if it is the same as billing
    }

    // validate profile fields
    
    if ($is_checkout) $profile_fields = array('basic' => $profile_fields['basic']);
    $fill_error = cw_check_user_field_validate($user, $update_fields, $profile_fields);

    // validate duplicated email, but not for checkout
    if ($mode == 'add' && 
        !empty($update_fields['basic']['email']) && 
        $customer_to_merge = \Customer\getByEmail($update_fields['basic']['email'])
    ) {
        $fill_error['basic']['email_exists'] = true;
    }
    if ($request_prepared['is_checkout'] && $fill_error['basic']['email_exists']) {
        unset($fill_error['basic']['email_exists']);
        if (empty($fill_error['basic']))  unset($fill_error['basic']);
    }

    // validation is OK
    if (!count($fill_error)) {
        $new_profile = cw_check_user_field_build_profile($userinfo, $update_fields, $profile_fields);

        $is_new_profile = false;
        if ($mode == 'add') {
            $new_profile['usertype'] = $usertype;
            $user = cw_user_create_profile($new_profile);

            // Login registered user
            $identifiers = &cw_session_register('identifiers', array());
            $identifiers['C'] = array( 'customer_id' => $user);
            if (!empty($customer_to_merge['customer_id'])) {
                $identifiers['C']['customer_to_merge'] = $customer_to_merge['customer_id'];
            }

            $is_new_profile = true;

            cw_event('on_login',array($user,'C',1));
        }

        cw_user_update($new_profile, $user, $customer_id);

        if (!isset($identifiers['C']['customer_to_merge']))
            cw_user_send_modification_mail($user, $is_new_profile);

        cw_event('on_profile_modify', array($user, $new_profile));

        if ($mode != 'add') {
            cw_add_top_message(cw_get_langvar_by_name('msg_profile_upd'), 'I');
        }
    }
    // validation is failed
    else {
        $filled_profile = $update_fields;
        if ($fill_error['basic']['email_exists']) {
            $top_message_txt = cw_get_langvar_by_name('err_field_email_exists', array('email'=>$update_fields['basic']['email']), false, true);
        } else {
            $top_message_txt = cw_check_user_get_error($fill_error);
        }
        cw_add_top_message($top_message_txt,'E');
    }

    if (AREA_TYPE == 'A') {
        if (empty($fill_error)) $mode = 'modify';
        cw_header_location("index.php?target=$target&mode=$mode&user=$user&js_tab=$js_tab");
    }
    elseif (defined('IS_AJAX')) {
    	$customer_id = $user;
        cw_call('cw_checkout_login');
    }
    else
        cw_header_location("index.php?target=$target".($mode?"&mode=$mode":'')."&js_tab=$js_tab");
}

if ($action == "delete" && @$confirmed == "Y" && !empty($user)) { // TODO: move delete function to another controller
    cw_func_call('cw_user_delete', array('customer_id' => $user, 'send_mail' => true));
    $user = 0;
    $smarty->clear_assign('customer_id');

    cw_header_location('index.php');
}

# TOFIX: move to addon hook
if ($addons['news']) {
    $subscribed = cw_call('cw\news\get_newslists_by_customer',array($user));
    $newslists = cw_call('cw\news\get_available_newslists');
    $smarty->assign('newslists', $newslists);
    $smarty->assign('subscribed', $subscribed);
}

# TOFIX: move to addon hook
if ($addons['salesman'] && (($action == 'update' && $login_type == 'B' ) || $current_area == 'B')) {
    $plans = cw_query("SELECT * FROM $tables[salesman_plans] WHERE status = 'A' ORDER BY title");
    $smarty->assign('plans', $plans);
}

if ($_GET['parent'])
	$smarty->assign('parent', $parent);

if (!empty($addons['image_verification'])) {
	if ($antibot_err) {
		$antibot_err = &cw_session_register("antibot_err");

		$smarty->assign('reg_antibot_err', $antibot_err);

		cw_session_unregister("antibot_err");
	}
	$smarty->assign('display_antibot', $display_antibot);
}

$smarty->assign('user', $user);
$smarty->assign('profile_fields', $profile_fields);
$smarty->assign('profile_sections', $profile_sections);
$smarty->assign('additional_fields', $additional_fields);

# assign information for the main address
$smarty->assign('countries', cw_map_get_countries());
$smarty->assign('states', cw_map_get_states());
if ($config['General']['use_counties'] == 'Y')
    $smarty->assign('titles', cw_map_get_counties());

$smarty->assign('memberships', cw_get_memberships($userinfo?$userinfo['usertype']:$usertype));

$smarty->assign('salesmen', cw_user_get_salesmans_for_register());

cw_load('warehouse');
$possible_warehouses = cw_get_warehouses();
$smarty->assign('possible_warehouses', $possible_warehouses);

if ($m_usertype == 'B' && $current_area == 'A') {
    $parent_profiles = cw_query("select customer_id, firstname, lastname from $tables[customers] where $tables[customers].usertype='B' and customer_id!='$user'");
    $smarty->assign('parent_profiles', $parent_profiles);
}

$smarty->assign('fill_error', $fill_error);
if ($fill_error)
    $userinfo = cw_check_user_field_build_profile($userinfo, $filled_profile, $profile_fields);

$fill_error = array();

$smarty->assign('userinfo', $userinfo);
$smarty->assign('userphoto', $userphoto);

if ($mode == 'delete') // TODO: move delete function to another controller
    $smarty->assign('main', 'profile_delete');
else
    $smarty->assign('main', 'register');
/*
 *  } require $app_main_dir.'/include/users/register.php';
 */



$smarty->assign('current_user_type', $usertype);
$smarty->assign('salesmans', cw_user_get_salesmans_groups_for_register());
$smarty->assign('payment_methods', cw_func_call('cw_payment_search', array('data' => array('type' => 1, 'active' => 1))));

$smarty->assign('js_tab', $js_tab); // TODO: Replace tab mechanism. Collect tabs as even handlers.

