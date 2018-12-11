<?php
cw_load('customer.base');

/*
A - admin
B - salesmanager
G - point of sale
C - customer
D - shipping company
I - inner employe
P - warehouse
S - supplier
*/

// artem, TODO: rework function to use as event call with fixed parameters
function cw_user_delete($params) {
	global $var_dirs, $tables, $config;
	global $addons;
    global $smarty;

    extract($params); // damn extract
//    $customer_id, $send_mail, $is_redirect = true
    $customer_id = isset($customer_id) ? $customer_id : 0;
    $send_mail = isset($send_mail) ? $send_mail : false;
    $is_redirect = isset($is_redirect) ? $is_redirect : true;

	cw_load('files', 'product', 'mail');

    $olduser_info = cw_user_get_info($customer_id, 65535);
    $to_customer = $olduser_info['language'];
    $usertype = $olduser_info['usertype'];

	if ($usertype == "A") {
		$users_count = cw_query_first_cell("SELECT COUNT(*) FROM $tables[customers] WHERE usertype='A'");
		if ($users_count == 1) {
			if ($is_redirect)
				cw_header_location("index.php?target=error_message&error=last_admin");
			return false;
		}
	}

/*
# kornev, TOFIX
	if ($usertype == "B" && cw_query_first_cell("SELECT COUNT(*) FROM $tables[addons] WHERE addon='Salesman'") > 0) {
		include $app_main_dir."/addons/Salesman/init.php";

		db_query("delete FROM $tables[salesman_clicks] WHERE salesman_customer_id='$customer_id'");
		db_query("delete FROM $tables[salesman_commissions] WHERE salesman_customer_id='$customer_id'");
		db_query("delete FROM $tables[salesman_payment] WHERE salesman_customer_id='$customer_id'");
		db_query("delete from $tables[salesman_views] WHERE salesman_customer_id='$customer_id'");
		db_query("UPDATE $tables[customers_salesman_info] SET parent_customer_id=0 WHERE customer_id = '$customer_id' AND usertype = 'B'");
	}
*/

    \Customer\delete($customer_id);

    cw_event('on_user_delete', array($customer_id, $olduser_info));

    $main_tables = array(
        'register_fields_values',
        'customers_settings',
        'customers_salesman_info',
        'customers_customer_info',
        'customers_warehouse_info',
        'customers_system_info',
//        'shipping_carriers'
    );

    foreach($main_tables as $v)
    	db_query("delete from ".$tables[$v]." where customer_id='$customer_id'");

    $smarty->assign('userinfo', $olduser_info);

    if ($config['Email']['eml_profile_deleted'] == 'Y' && $send_mail)
            cw_call('cw_send_mail', array($config['Company']['users_department'], $olduser_info['email'], 'mail/profile_deleted_subj.tpl', 'mail/profile_deleted.tpl'));

    if ($config['Email']['eml_profile_deleted_admin'] == 'Y' && $send_mail)
        cw_call('cw_send_mail', array($olduser_info['email'], $config['Company']['users_department'], 'mail/profile_deleted_subj.tpl', 'mail/profile_admin_deleted.tpl', $config['default_admin_language']));
}

function cw_user_add($usertype, $init_fields = array()) {     // TODO: Replace and remove this func
die('Function cw_user_add is obsolete, use cw_user_create_profile');
    global $tables, $customer_id, $current_language;
    $init_fields += array(
        'usertype' => $usertype,
        'status' => 'Y',
        'language' => $current_language,
    );
    if ($usertype == 'I') {
        $init_fields['password'] = cw_user_generate_password();
    }
    elseif ($usertype == 'C' && empty($init_fields['login'])) {
        $init_fields['password'] = cw_user_generate_password();
    }
    $user = cw_array2insert('customers', $init_fields);

    $customer_system_info['customer_id'] = $user;
    $customer_system_info['creation_customer_id'] = $customer_id;
    $customer_system_info['creation_date'] = cw_core_get_time();
    cw_array2insert('customers_system_info', $customer_system_info, true);

    cw_array2insert('customers_settings', array('customer_id' => $user), true);
    return $user;
}

function cw_user_update_address($customer_id, $address_id, $address) {
    global $tables;

    cw_load('profile_fields');

    $address['customer_id'] = $customer_id;
    if ($address_id) {
        $count = cw_query_first_cell("select count(*) from $tables[customers_addresses] where customer_id='$customer_id' and address_id='$address_id'");
        if ($count) $address['address_id'] = $address_id;
    }

    if (is_numeric($address['address_id']) && $address['address_id'] > 0) {
        cw_array2update('customers_addresses', $address, "address_id='$address[address_id]'");
    }
    else {
    	if (in_array($address['address_id'], array('main', 'current'), TRUE)) {
    		$address[$address['address_id']] = 1;
    	}    	
        $address_id = cw_array2insert('customers_addresses', $address, TRUE);
    }
    
    if (is_array($address['custom_fields']) && !empty($address['custom_fields'])) {
        cw_profile_fields_update_type($customer_id, $address_id, 'A', $address['custom_fields']);
    }

    return $address_id;
}

function cw_user_get_addresses($customer_id) {
    $result = array();
    $addresses = Customer\Address\get(null, $customer_id);
    if ($addresses)
    foreach($addresses as $k=>$address)
        $result[$address['address_id']] = cw_user_process_address($address);
    return $result;
}

function cw_user_get_addresses_smarty($params) {
    if (!$params['customer_id']) return;
    $user_addresses = cw_user_get_addresses($params['customer_id']);
    $result = array();
    if ($params['main'] || $params['current']) {
        foreach ($user_addresses as $address_id => $address_info) {
            if ($params['main'] && $address_info['main']) {
                $result[$address_id] = $address_info;
                continue;
            }  
            if ($params['current'] && $address_info['current'])
                $result[$address_id] = $address_info;  
        }  
    } else {
        $result = $user_addresses;
    }
    return $result;
}

function cw_user_check_addresses($customer_id) {
    global $tables;

    $main = Customer\Address\getMain($customer_id);
    if (empty($main)) {
        Customer\Address\setAddressType($customer_id,'main');
    }

    $current = \Customer\Address\getCurrent($customer_id);
    if (empty($current)) {
        Customer\Address\setAddressType($customer_id,'current');
    }

}

function cw_user_delete_address($customer_id, $address_id) {
    global $tables;
    \Customer\Address\delete($address_id, $customer_id);
    if ($address_id>0) {
        db_query("DELETE FROM $tables[register_fields_values] WHERE key_id='$address_id' AND key_type='A'");
    }
    cw_user_check_addresses($customer_id);
}

function cw_user_process_address($address) {
    if ($address) {
        $address['titleid'] = cw_user_detect_title(addslashes($address['title']));
        $address['title'] = cw_user_get_title($address['titleid']);

        $address['statename'] = cw_get_state($address['state'], $address['country']);
        $address['countryname'] = cw_get_country($address['country'],null,true);
        if ($config['General']['use_counties'] == 'Y')
            $address['countyname'] = cw_get_county($address['county']);
        if (empty($address['custom_fields']))
            $address['custom_fields'] =  cw_user_get_custom_fields($address['customer_id'], $address['address_id'], 'A');
    }
    return $address;
}

/*
 * Get address by ID $address_id from address book
 * also 'main' or 'current' accepted as address_id if customer_id is specified
 */
function cw_user_get_address($customer_id, $address_id) {
    global $tables;

    $type = null;

    if ($address_id == 'main' || $address_id == 'current') {
        $type = $address_id;
        $address_id = null;
        $customer_id = intval($customer_id); // $customer_id becomes required, null is not acceptable
    }

    $address = Customer\Address\get($address_id, $customer_id, $type);

    return cw_user_process_address($address);
}

/*
 * Returns address by type for current user
 */
function cw_user_get_address_by_type($type) {
    global $customer_id, $config;

    $user_address = &cw_session_register('user_address', array());
    $address = $user_address[$type.'_address'];

    if (!empty($customer_id) && (empty($address) || $address['customer_id']!=$customer_id)) {
        $address = cw_user_get_address($customer_id, $type);
    }

    if (empty($address) && $config['General']['apply_default_country'] == "Y") {
        return cw_user_get_default_address();
    }

    return cw_user_process_address($address);

}

function cw_user_get_default_address() {
    global $config;

    $address = array(
        'country' => $config['General']['default_country'],
        'state' => $config['General']['default_state'],
        'zipcode' => $config['General']['default_zipcode'],
        'city' => $config['General']['default_city'],
    );
    return cw_user_process_address($address);
}

function cw_user_get_custom_fields($customer_id, $key_id = 0, $key_type = '', $hash_field='field_id') {
    global $tables;

    $return = cw_query_hash($q="select rf.$hash_field, rfl.value from $tables[register_fields_values] as rfl, $tables[register_fields] as rf where rf.field_id = rfl.field_id and rfl.customer_id='$customer_id' and rf.type != 'D' and rfl.key_id='$key_id' and rfl.key_type='$key_type'", $hash_field, false, true);

    return $return;
}

function cw_user_get_system_info($customer_id) {
    global $tables;

    $info = cw_query_first("select * from $tables[customers_system_info] where customer_id='$customer_id'");
    $info['customer_modified_by'] = cw_user_get_label($info['modification_customer_id']);
    $info['customer_created_by'] = cw_user_get_label($info['creation_customer_id']);
    return $info;
}

function cw_user_get_addition_info($customer_id, $usertype) {
    global $tables;

    $info = false;
//    if (in_array($usertype, array('C', 'R', 'S')))
    $info = cw_query_first("select * from $tables[customers_customer_info] where customer_id='$customer_id'");
    return $info;
}

# info_type - used bit mask
# 00000000 00000000 (0) - basic (customers.*)
# 00000000 00000001 (1) - address information
# 00000000 00000010 (2) - password information

# 00000000 00001000 (8) - addition fields
# 00000000 00010000 (16) - companies/warehouses
# 00000000 00100000 (32) - system info
# 00000000 01000000 (64) - additional info (from different tables for different types of profiles)

# 00000100 00000000 (1024) - custom fields info by field id as array keys
# 00001000 00000000 (2048) - custom fields info by field names as array keys
# 11111111 11111111 (65535) - full information
function cw_user_get_info($customer_id, $info_type = 0) {
	global $tables, $current_language, $default_user_profile_fields, $config;
	global $addons;

 	$userinfo = \Customer\get($customer_id);

    if (empty($userinfo)) return null;

    $userinfo = array_merge($userinfo, (array)cw_query_first("SELECT membership, flag FROM $tables[memberships] WHERE membership_id = '$userinfo[membership_id]'"));

# kornev, TOFIX
    if ($userinfo['usertype'] == 'B')
        $userinfo['plan_id'] = cw_query_first_cell("SELECT plan_id FROM $tables[salesman_commissions] WHERE salesman_customer_id='$userinfo[customer_id]'");

    if ($info_type & 1) {
        $userinfo['addresses'] = cw_user_get_addresses($customer_id);
        $userinfo['main_address'] = cw_user_get_address($customer_id,'main');
        $userinfo['current_address'] = cw_user_get_address($customer_id, 'current');
	$address = empty($userinfo['main_address']) ? $userinfo['current_address'] : $userinfo['main_address'];
	$userinfo['firstname'] = $address['firstname'];
	$userinfo['lastname'] = $address['lastname'];
	$userinfo['fullname'] = trim($address['firstname'] . ' ' . $address['lastname']);
	unset($address);
    }

    if ($info_type & 2) {
        cw_load('crypt');
// For security reason password must be left encrypted in userinfo data
/*
	$userinfo['password'] = text_decrypt($userinfo['password']);
	if (is_null($userinfo['password']))
	    cw_log_flag("log_decrypt_errors", "DECRYPT", "Could not decrypt password for the user ".$userinfo['customer_id'], true);
	elseif ($userinfo['password'] !== false)
	    $userinfo['password'] = stripslashes($userinfo['password']);
*/
    }

    if ($info_type & 8) {
        cw_load('profile_fields');
	    $userinfo['additional_fields'] = cw_profile_fields_get_additional($customer_id);
    }

    if ($info_type & 16) {
		if ($userinfo['usertype'] == 'B')
			$userinfo['salesman_info'] = cw_get_salesman_info($customer_id);
    }

    if ($info_type & 32)
        $userinfo['system_info'] = cw_user_get_system_info($customer_id);

    if ($info_type & 64) {
        $userinfo['additional_info'] = cw_user_get_addition_info($customer_id, $userinfo['usertype']);
    }

    if ($info_type & 256)
        $userinfo['addresses'] = cw_user_get_addresses($customer_id);

    if ($info_type & 1024)  
        $userinfo['custom_fields'] = cw_user_get_custom_fields($customer_id);
    
    if ($info_type & 2048)
        $userinfo['custom_fields_by_name'] = cw_user_get_custom_fields($customer_id, 0,'','field');

	return $userinfo;
}

function cw_user_check_county($county_id, $state_code, $country_code) {
	global $tables;

	if (is_numeric($county_id)) {
		if (cw_user_check_state($state_code, $country_code) && cw_query_first_cell("select count(*) from $tables[map_counties], $tables[map_states] where $tables[map_counties].state_id=$tables[map_states].state_id AND $tables[map_counties].county_id='$county_id' AND $tables[map_states].code='$state_code'"))
    			return (bool) cw_query_first_cell("select count(*) from $tables[map_counties], $tables[map_states] where $tables[map_counties].state_id=$tables[map_states].state_id AND $tables[map_counties].county_id='$county_id' AND $tables[map_states].code='$state_code' AND $tables[map_states].country_code='$country_code'");
	}

	return true;
}

function cw_user_check_region($region_id, $county_id, $state_code, $country_code) {
    global $tables;

/*
    if (is_numeric($secto_id)) {
        if (cw_user_check_state($state_code, $country_code) && cw_user_check_county($county_id, $state_code, $country_code) && cw_query_first_cell("select count(*) from $tables[map_sectors] where $tables[map_sectors].county_id='$county_id'"))
                return (bool) cw_query_first_cell("select count(*) from $tables[map_counties] where $tables[map_sectors].county_id='$county_id' and sector_id='$sector_id'");
    }
*/

    return true;
}

function cw_user_check_state($state_code, $country_code) {
    global $tables;

    $is_states = cw_query_first_cell("select count(*) from $tables[map_states] where country_code='$country_code'");
    if ($is_states && cw_query_first_cell("select count(*) from $tables[map_states] where code='$state_code' and country_code='$country_code'"))
        return true;
    return false;
}

#
# Get additional register fields settings
#
function cw_get_add_contact_fields($area = '') {
	global $tables;

	if (!empty($area)) {
		$fields = cw_query("SELECT *, IF(avail LIKE '%$area%', 'Y', '') as avail, IF(required LIKE '%$area%', 'Y', '') as required FROM $tables[contact_fields] ORDER BY orderby");
	}
	else {
		$fields = cw_query("SELECT * FROM $tables[contact_fields] ORDER BY orderby");
	}

	if ($fields) {
		foreach ($fields as $k => $v) {
			$fields[$k]['title'] = cw_get_languages_alt("lbl_contact_field_".$v['field_id']);
			if (empty($area)) {
				$fields[$k]['avail'] = cw_keys2hash($v['avail']);
				$fields[$k]['required'] = cw_keys2hash($v['required']);
			}
			elseif ($v['type'] == 'S' && !empty($v['variants'])) {
				$fields[$k]['variants'] = explode(";", $v['variants']);
			}
		}
	}

	return $fields;
}

#
# Transform key string to hash-array
#
function cw_keys2hash($str) {
	$tmp = array();

# kornev
    $arr = explode(":", $str);
    foreach($arr as $val)
        $tmp[$val] = 'Y';
    return $tmp;
/*
	if (strlen($str) == 0)
		return $tmp;

	for ($x = 0; $x < strlen($str); $x++)
		$tmp[$str[$x]] = 'Y';

	return $tmp;
*/
}

function cw_user_get_titles() {
    global $tables;

    $titles = cw_query("select * from $tables[titles] where active = 'Y' order by orderby, title");
    if (!empty($titles)) {
        foreach ($titles as $k => $v) {
            $name = cw_get_languages_alt("title_".$v['titleid']);
            $titles[$k]['title_orig'] = $v['title'];
            if (!empty($name))
                $titles[$k]['title'] = $name;
        }
    }

    return $titles;
}

function cw_user_detect_title($title) {
    global $tables;

    if (empty($title))
        return false;

    return cw_query_first_cell("select Titleid from $tables[titles] where title = '$title'");
}

function cw_user_get_title($titleid, $code = false) {
    global $tables, $current_language;

    if (empty($titleid))
        return false;

    $title = cw_get_languages_alt("title_".$titleid, $code);
    if (empty($title))
        $title = cw_query_first_cell("select title from $tables[titles] where titleid = '$titleid'");

    return $title;
}

function cw_user_is_right_password($passed_password, $db_password_hash) {

    $parts = explode(':', $db_password_hash);

    if (sizeof($parts) != 2) 
        return false;

    if (md5($parts[1] . $passed_password) == $parts[0]) {
        return true;
    }

    return false;
}

function cw_user_get_hashed_password($raw_password) {

    $_tail_long = md5(uniqid(rand())); # 32 chars

    $from_chr = rand(0, 24); # any 8 chars

    $hash_tail = substr($_tail_long, $from_chr, 8);

    $result = md5($hash_tail . $raw_password) . ':' . $hash_tail;

    return $result; 
}

//TODO: anton: this function is not used
function cw_user_change_password($customer_id) {
    global $tables, $smarty, $config;

    cw_load('mail', 'crypt');

    $full_pwd = md5(uniqid(rand())); # 32 chars
    $from_chr = rand(0, 24); # any 8 chars
    $new_password = substr($full_pwd, $from_chr, 8);
    $crypted = addslashes(cw_crypt_text($new_password));
    db_query("update $tables[customers] set password='$crypted' where customer_id='$customer'");

    $smarty->assign('new_password', $new_password);
    $user_email = cw_query_first_cell("select email from $tables[customers] where customer_id='$customer_id'");
    cw_call('cw_send_mail', array($config['Company']['users_department'], $user_email, 'mail/password_modified_subj.tpl', 'mail/password_modified.tpl'));
}

function cw_user_get_memberships($usertype) {
    global $tables, $current_language, $addons;

    if (!is_array($usertype))
        $usertype_arr[] = $usertype;
    else
        $usertype_arr = $usertype;

    foreach($usertype_arr as $ty) {
        if ($ty == 'R' and !$addons['wholesale_trading']) continue;
        if ($ty == 'G' and !$addons['pos']) continue;
        $condition[] = "$tables[memberships].area = '$ty'";
    }

    if (!$condition) return array();
    return cw_query("SELECT $tables[memberships].area, $tables[memberships].membership_id, IF($tables[memberships].membership_id, IFNULL($tables[memberships_lng].membership, $tables[memberships].membership), '".cw_get_langvar_by_name('lbl_retail_level')."') as membership FROM $tables[memberships] LEFT JOIN $tables[memberships_lng] ON $tables[memberships].membership_id = $tables[memberships_lng].membership_id AND $tables[memberships_lng].code = '$current_language' WHERE ($tables[memberships].active = 'Y' or $tables[memberships].membership_id=0) and (".implode(" or ", $condition).") ORDER BY $tables[memberships].area, $tables[memberships].orderby");
}

// artem, TODO: useless function, we do not use login anymore, check where it is used and delete
function cw_user_generate_login($prefix) {
    global $tables;

    while(true) {
        $name = $prefix.cw_core_generate_string(7, false);
        $count = cw_query_first_cell("select count(*) from $tables[customers] where login='$name'");
        if (!$count) break;
    }
    return $name;
}

function cw_user_generate_password() {
    return cw_core_generate_string(10);
}

function cw_user_get_real_usertype($user = '') {
    global $customer_id, $tables;

    if (empty($user)) $user = $customer_id;

    return cw_query_first_cell("select usertype from $tables[customers] where customer_id='$user'");
}

function cw_user_get_stored_cart($customer_id) {
    global $tables;
    return unserialize(cw_query_first_cell("select cart from $tables[customers_customer_info] where customer_id='$customer_id'"));
}

function cw_user_set_language($customer_id, $language) {
    global $tables;

    db_query("update $tables[customers] set language='$language' where customer_id='$customer_id'");
}

function cw_user_get_language($customer_id) {
    global $tables;

    return cw_query_first_cell("select language from $tables[customers_system_info] where customer_id='$customer_id'");
}

function cw_user_default_information(&$account) {
    global $tables, $current_area;

    if (!is_numeric($account['membership_id'])) {
        $account['membership_id'] = intval(cw_user_get_default_membeship($account['usertype']?$account['usertype']:$current_area));
        if (in_array($account['usertype'], array('C', 'R')))
            db_query("update $tables[customers] set membership_id='$account[membership_id]' where customer_id='$account[customer_id]'");
    }
}

function cw_user_get_user_account($customer_id, $where = '') {
    global $tables, $current_area;

    $query = "select c.usertype, c.customer_id, c.email, c.membership_id, ci.company_id, c.change_password from $tables[customers] as c left join $tables[customers_customer_info] as ci on ci.customer_id=c.customer_id where c.customer_id='$customer_id'".($where?" and $where":'');

	$account = cw_query_first($query);
	if ($current_area == 'C') {
        cw_user_default_information($account);
    }
    elseif($current_area == 'G') {
    }
    elseif($current_area == 'P') {
    }

    return $account;
}

function cw_user_get_short_list($usertype, $where = '') {
    global $tables;

    $orderby = 'firstname, lastname';
    if($usertype == 'S') $orderby = 'company';
    return cw_query("select c.customer_id, c.customer_id, ci.company, ca.firstname, ca.lastname from $tables[customers] as c left join $tables[customers_addresses] as ca on ca.customer_id=c.customer_id and ca.main=1 left join $tables[customers_customer_info] as ci on ci.customer_id=c.customer_id where c.usertype='$usertype' $where order by $orderby");
}

function cw_user_get_salesmans_for_register() {
    global $tables;

    return cw_query("select c.customer_id, c.customer_id, ca.firstname, ca.lastname from $tables[memberships] as m, $tables[customers] as c left join $tables[customers_addresses] as ca on ca.customer_id=c.customer_id and ca.main=1 where m.membership_id=c.membership_id and m.show_on_register = 'Y' and c.usertype='B'");
}

function cw_user_get_salesmans_groups_for_register() {
    global $tables, $current_language;

    $usertype = 'B';
    return cw_query_hash("select c.customer_id, c.customer_id, ca.firstname, ca.lastname, c.membership_id, IFNULL(ml.membership, m.membership) as membership from $tables[customers] as c left join $tables[customers_addresses] as ca on ca.customer_id=c.customer_id and ca.main=1, $tables[memberships] as m left join $tables[memberships_lng] as ml on ml.membership_id=m.membership_id and ml.code='$current_language' where m.membership_id=c.membership_id and c.usertype='$usertype' order by membership", array('membership'));
}

#
#  user photos
#

function cw_user_get_photos($user) {
    cw_load('image');
    return cw_image_get_list('customers_images', $user);
}

function cw_user_get_avatar($user) {
    cw_load('image');
    $images = cw_image_get_list('customers_images', $user);
    return $images[0];
}

function cw_user_update($userinfo, $customer_id, $by_customer_id) {
    global $tables, $addons;

    foreach(array('email', 'status', 'membership_id', 'language','change_password') as $fld) {
        if (isset($userinfo[$fld])) {
            $customer[$fld] = $userinfo[$fld];
        }
    }
	
    if (AREA_TYPE == 'A' && $userinfo['usertype'])
        $customer['usertype'] = $userinfo['usertype'];
    if (AREA_TYPE == 'A' && $userinfo['customer_id'])
        $customer['customer_id'] = $userinfo['customer_id'];

    $current = cw_user_get_info($customer_id,0);

    //allow password update only by the profile owner or by admin
    if (isset($userinfo['password'])) {
        if (!empty($userinfo['password']) && ($customer_id == $by_customer_id || AREA_TYPE == 'A')) {
            $customer['password'] = cw_call('cw_user_get_hashed_password', array($userinfo['password']));
        }
    }

    cw_event('on_user_update', array($customer_id, $by_customer_id, $customer, $userinfo));

    cw_array2update('customers', $customer, "customer_id='$customer_id'");

    $additional_info = $userinfo['additional_info'];
    $customer_info_fields = array(
        'ssn', 'tax_number', 'birthday', 'birthday_place', 'sex', 'married', 'nationality', 'company', 'employees', 'foundation',
        'foundation_place', 'company_id', 'can_change_company_id', 'tax_id', 'payment_id', 'payment_note', 'tax_exempt',
        'separate_invoices', 'shipping_operated', 'shipment_paid', 'shipping_company_to_carrier_id', 'shipping_company_from_carrier_id',
        'cod_delivery_type_id', 'leaving_type', 'division_id', 'doc_prefix', 'order_entering_format', 'status_note'
    );
    if (!cw_query_first_cell("select count(*) from $tables[customers_customer_info] where customer_id='$customer_id'"))
        cw_array2insert('customers_customer_info', array('customer_id' => $customer_id));
    cw_array2update('customers_customer_info', $additional_info, "customer_id='$customer_id'", $customer_info_fields);


/*
      db_query("update $tables[customers_addresses] set main=0, current=0 where customer_id = '$customer_id'");
    foreach(array('main_address', 'current_address') as $addr)  {
        if (!$userinfo[$addr]) continue;

        $userinfo[$addr]['main'] = $addr == 'main_address';
        $userinfo[$addr]['current'] = $addr == 'current_address';

        $address_id = $userinfo[$addr]['address_id'];
        if (!$address_id) $address_id = cw_array2insert('customers_addresses', array('customer_id' => $customer_id, 'main' => $userinfo[$addr]['main'], 'current' => $userinfo[$addr]['current']));

        cw_user_update_address($customer_id, $address_id, $userinfo[$addr]);
    }
*/
    foreach (cw_user_address_array($userinfo['addresses']) as $address_id=>$address) {
        cw_user_update_address($customer_id, $address['address_id'], $address);
    }
    cw_user_check_addresses($customer_id);

    if (is_array($userinfo['custom_fields'])) {
        $current_custom_fields = cw_user_get_info($customer_id, 1024+2048);
        foreach($userinfo['custom_fields'] as $field_id => $value) {
            db_query("delete from $tables[register_fields_values] where customer_id='$customer_id' AND field_id='$field_id'");
            cw_array2insert('register_fields_values', array('field_id' => $field_id, 'value' => $value, 'customer_id' => $customer_id));

            if ($current_custom_fields['custom_fields'][$field_id] != $value) {
                $field_info = cw_profile_fields_get_field($field_id);
                cw_call('cw_profile_field_updated_'.$field_info['field'], 
                    array($customer_id, $current_custom_fields['custom_fields']['field_id'], $value));
            }
        }
    }

    $customer_system_info = cw_query_first("select * from $tables[customers_system_info] where customer_id='$customer_id'");
    $customer_system_info['customer_id'] = $customer_id;
    if (!$customer_system_info['creation_customer_id']) {
        $customer_system_info['creation_customer_id'] = $by_customer_id;
        $customer_system_info['creation_date'] = cw_core_get_time();
    }
    $customer_system_info['modification_customer_id'] = $by_customer_id;
    $customer_system_info['modification_date'] = cw_core_get_time();
    cw_array2insert('customers_system_info', $customer_system_info, true);

	$salesman_info = cw_query_first("select * from $tables[customers_salesman_info] where customer_id = '$customer_id'");
	$salesman_info['parent_customer_id'] = $userinfo['salesman_info']['parent_customer_id'];
	cw_array2insert('customers_salesman_info', $salesman_info, true);

}

function cw_user_create_profile($fields) {
    global $tables, $customer_id, $current_language;

    cw_load('crypt');

    // Defaults
    // password
    if (empty($fields['password'])) $fields['password'] = cw_user_generate_password();
    // membership
    if (empty($fields['membership_id'])) $fields['membership_id'] = cw_query_first_cell("select membership_id from $tables[memberships] where default_membership='Y' and area='$usertype' ");
    // usertype
    if (empty($fields['usertype'])) $fields['usertype'] = 'C';
    // status
    if (empty($fields['status'])) $fields['status'] = 'Y';
    // language
    if (empty($fields['language'])) $fields['language'] = $current_language;

    // Create profile
    $profile_create = array(
        'password' => cw_call('cw_user_get_hashed_password', array($fields['password'])), //($fields['password']!='anonymous-checkout-user')?cw_call('cw_user_get_hashed_password', array($fields['password'])):'',
        'email' => $fields['email'],
        'membership_id' => $fields['membership_id'],
        'status' => $fields['status'],
        'usertype' => $fields['usertype'],
        'language' => $fields['language'],
    );

    cw_log_add(__FUNCTION__, array($fields, $profile_create));

    $user = cw_array2insert('customers', $profile_create);

    $customers_customer_info = array(
        'customer_id' => $user,
        'web_user' => 1,
    );
    cw_array2insert('customers_customer_info', $customers_customer_info);
    
    if (empty($customer_id)) {
    	$customer_id = $user;
    }

    $customer_system_info = array(
        'customer_id' => $customer_id,
        'creation_customer_id' => $user,
        'creation_date' => cw_core_get_time(),
    );
    cw_array2insert('customers_system_info', $customer_system_info, true);

    cw_array2insert('customers_settings', array('customer_id' => $user), true);

    return $user;
}

function cw_user_send_modification_mail($customer_id, $is_new) {
    global $config, $smarty;

    $userinfo = cw_user_get_info($customer_id, 65535);
    $fields_area = cw_profile_fields_get_area($customer_id);
    list($profile_sections, $profile_fields, $additional_fields) = cw_profile_fields_get_sections('U', true, $fields_area);
    $smarty->assign('userinfo', $userinfo);
    $smarty->assign('profile_sections', $profile_sections);
    $smarty->assign('profile_fields', $profile_fields);
    $smarty->assign('additional_fields', $additional_fields);

    if ($is_new) {
        if ($config['Email']['eml_signin_notif_admin'] == 'Y')
            cw_call('cw_send_mail', array($userinfo['email'], $config['Company']['users_department'], 'mail/users/signin_subj.tpl', 'mail/users/signin_admin.tpl', $config['default_admin_language']));
        if ($config['Email']['eml_signin_notif'] == 'Y') {
            global $update_fields;
            $userinfo['password'] = $update_fields['basic']['password'];
            $smarty->assign('userinfo', $userinfo);
            $smarty->assign('is_new', $is_new);
            cw_call('cw_send_mail', array($config['Company']['users_department'], $userinfo['email'], 'mail/users/signin_subj.tpl', 'mail/users/signin.tpl'));
        }
    }
    else {
        if($config['Email']['eml_profile_modified_customer'] == 'Y')
            cw_call('cw_send_mail', array($config['Company']['users_department'], $userinfo['email'], 'mail/users/modified_subj.tpl', 'mail/users/modified.tpl'));
        if($config['Email']['eml_profile_modified_admin'] == 'Y') {
            cw_call('cw_send_mail', array($userinfo['email'], $config['Company']['users_department'], 'mail/users/modified_admin_subj.tpl', 'mail/users/modified_admin.tpl'));
        }
    }
}

function cw_user_delete_memberships($del) {
    global $tables;

    if (!is_array($del)) $del[] = $del;
    if (!count($del)) return;

    $delete_string = "membership_id IN ('".implode("','", $del)."')";
    if (cw_query_first_cell("SELECT COUNT(*) FROM $tables[memberships] WHERE area IN ('C', 'R') AND ".$delete_string))
        $recalc_subcat_count = true;

    db_query("DELETE FROM $tables[memberships] WHERE ".$delete_string);
    db_query("DELETE FROM $tables[super_deals] WHERE ".$delete_string);
    db_query("DELETE FROM $tables[categories_memberships] WHERE ".$delete_string);
    db_query("DELETE FROM $tables[products_memberships] WHERE ".$delete_string);
    db_query("DELETE FROM $tables[tax_rate_memberships] WHERE ".$delete_string);
    db_query("DELETE FROM $tables[memberships_lng] WHERE ".$delete_string);
    db_query("DELETE FROM $tables[payment_methods_memberships] WHERE ".$delete_string);
    db_query("DELETE FROM $tables[discounts_memberships] WHERE ".$delete_string);
    db_query("DELETE FROM $tables[access_levels] WHERE ".$delete_string);
    db_query("DELETE FROM $tables[newslists_memberships] WHERE ".$delete_string);
    db_query("DELETE FROM $tables[products_prices] WHERE ".$delete_string);
    db_query("DELETE FROM $tables[tax_rate_memberships] WHERE ".$delete_string);

    foreach($del as $id) {
        db_query("DELETE FROM $tables[register_fields_avails] WHERE area LIKE '%_$id'");
    }
    cw_array2update("customers", array("membership_id" => 0), $delete_string);

    if ($recalc_subcat_count) {
        cw_load('category');
        cw_recalc_subcat_count(0, 100);
    }
}

function cw_user_get_label($customer_id, $format = '{{firstname}} {{lastname}} ({{customer_id}})', $doc_id = 0) {
    global $tables;

    if ($doc_id)
        $res = cw_query_first($sql="select c.customer_id, c.customer_id, dui.company, ca.firstname, ca.lastname, ca.country, c.email from $tables[docs] as d, $tables[docs_user_info] as dui left join $tables[customers] as c on c.customer_id=dui.customer_id left join $tables[customers_addresses] as ca on ca.address_id=dui.main_address_id where dui.doc_info_id=d.doc_info_id and d.doc_id='$doc_id'");
    else
        $res = cw_query_first("select c.customer_id, c.customer_id, ci.company, ca.firstname, ca.lastname, ca.country, c.email from $tables[customers] as c, $tables[customers_addresses] as ca, $tables[customers_customer_info] as ci where c.customer_id=ca.customer_id and ca.customer_id=ci.customer_id and ci.customer_id='$customer_id' and main=1");
    if ($res) {
        foreach($res as $k=>$v) {
            if (empty($v)) $v = ' -- ';
            $format = preg_replace('/\{\{'.$k.'\}\}/', $v, $format);
        }
        while(strpos($format, '  ') !== false) $format = preg_replace('/  /', ' ', $format);
    }
    else $format = cw_get_langvar_by_name('lbl_not_present');

    return $format;
}

function cw_user_get_discount_formula($customer_id) {
    global $tables;

    return implode('+', cw_query_column("select discount from $tables[customers_discounts] where customer_id='$customer_id' and discount > 0 order by orderby"));
}

function cw_user_apply_discount($customer_id, $price) {
    global $tables;

    $return = $price;
    $discounts = cw_query_column("select discount from $tables[customers_discounts] where customer_id='$customer_id' and discount > 0 order by orderby");
    if ($discounts)
    foreach($discounts as $discount)
        $return = (1-$discount/100)*$return;
    return $return;
}

function cw_user_apply_discount_by_formula(&$formula, $price) {
    $discounts = explode('+', $formula);
    $return = $price;
    if ($discounts)
    foreach($discounts as $discount) {
        if (!is_numeric($discount)) {
            $formula = '';
            return $price;
        }
        $return = (1-$discount/100)*$return;
    }
    if ($return < 0) return 0;
    return price_format($return);
}

function cw_get_salesman_info($customer_id) {
    global $tables;

    return cw_query_first("select * from $tables[customers_salesman_info] where customer_id='$customer_id'");
}

function cw_user_get_default_membeship($area) {
    global $tables;

    return cw_query_first_cell("select membership_id from $tables[memberships] where default_membership='Y' and area='".$area."'");
}


// Function checks if address var contains array of multiple addresses
// Detection method: all keys in multiple addresses must be either integer or reserved words main|current
function cw_user_address_is_multiple($address) {
    if (!is_array($address)) return null;
    foreach (array_keys($address) as $key) {
        if (!is_integer($key) && $key!='main' && $key!='current') return false;
    }
    return true;
}

// Function converts address to array of addresses, so all functions can process input data as multiple addresses
function cw_user_address_array($address) {
    if (!cw_user_address_is_multiple($address)) $address = array($address['address_id']=>$address);
    return $address;
}

#
# function returns the files folder for current user
#
function cw_user_get_files_location() {
	global $customer_id, $var_dirs;
	global $user_account;

	if ($user_account['usertype'] == "A")
		return $var_dirs['files'];

	return $var_dirs['files'].DIRECTORY_SEPARATOR.$customer_id;
}

function cw_user_get_suppliers() {
	global $tables;
	
	$customer_ids = cw_query_column("SELECT customer_id FROM $tables[customers] WHERE usertype='S'");
	foreach ($customer_ids as $id) {
		$suppliers[$id] = cw_call('cw_user_get_info', array($id, 1));
	}

	return $suppliers;
	
}

function cw_user_get_usertypes() {
    global $tables;
    
    $ut = cw_query_column("SELECT distinct(usertype) FROM $tables[customers]");
    $result = array();
    foreach ($ut as $k=>$v) {
        $result[$v] = cw_get_langvar_by_name('lbl_user_'.$v, null, false, true);
        if (empty($result[$v])) $result[$v] = $v.' usertype';
    }

    return $result;

}

/**
 * Hook for sessions cron handler
 * Delete anonymous users from checkout
 */
function cw_user_on_before_session_delete($sid, $data) {
    $customer_to_merge = $data['identifiers']['C']['customer_to_merge'];
    $customer_id = $data['identifiers']['C']['customer_id'];
    if (empty($customer_to_merge) || empty($customer_id)) return null;
    
   
    if ($data['identifiers']['C']['customer_to_merge']>0) {
        cw_call('cw_user_merge', array($customer_id, $customer_to_merge));
        cw_func_call('cw_user_delete', array('customer_id' => $customer_id, 'send_mail' => false));
    }
    
    return array($customer_id,  $customer_to_merge);
}

/**
 * Merge one user into another, disable first account
 */
function cw_user_merge($from_customer_id, $into_customer_id) {
    global $tables;
    
    // Replace all references to customer_ids in all tables
    $tbls = array('customers_discounts','discount_coupons','discount_coupons_login',
        'docs_user_info','giftreg_events','products_reviews','products_reviews_login_keys',
        'products_reviews_ratings','products_reviews_reminder','products_votes','wishlist',
    );
    
    db_query("UPDATE $tables[customers_addresses] SET main=0, `current`=0 WHERE customer_id = $from_customer_id");
 
    foreach ($tbls as $t) {
        if (!empty($tables[$t])) {
            db_query("UPDATE {$tables[$t]} SET customer_id=$into_customer_id WHERE customer_id=$from_customer_id");
        }
    }
            
    // process cw_messages and cw_order_messages_messages
    if (!empty($tables['messages'])) {
     db_query("UPDATE $tables[messages] SET sender_id=$into_customer_id WHERE sender_id=$from_customer_id");
     db_query("UPDATE $tables[messages] SET recipient_id=$into_customer_id WHERE recipient_id=$from_customer_id");
    }

    if (!empty($tables['order_messages_messages'])) {
     db_query("UPDATE $tables[order_messages_messages] SET sender_id=$into_customer_id WHERE sender_id=$from_customer_id");
     db_query("UPDATE $tables[order_messages_messages] SET recepient_id=$into_customer_id WHERE recepient_id=$from_customer_id");
     db_query("UPDATE $tables[order_messages_messages] SET author_id=$into_customer_id WHERE author_id=$from_customer_id");
    }
    
    // Disabe account
    db_query("UPDATE $tables[customers] SET status='N' WHERE customer_id=$from_customer_id");

    // recalc customers_docs_stats
    cw_load('doc');
    cw_call('cw_doc_save_history_totals_by_customer',array($from_customer_id,$into_customer_id));    
}



/* get list of the additional register fields of text type for user search in admin area
/* return [['field_id', 'field'],...]
*/
function cw_user_search_get_register_fields($usertype, $field_type) {
    global $tables;

    $register_fields = cw_query_hash($s="select distinct rv.field_id, rv.field from $tables[register_fields] rv inner join $tables[register_fields_avails] rva on rv.field_id=rva.field_id and rva.area like '%$usertype%' where rv.type='$field_type' and (rva.is_avail=1 or rva.is_required=1)", 'field_id', 0, 1);

    return $register_fields;
}
