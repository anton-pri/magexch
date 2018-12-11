<?php
cw_load('crypt', 'mail', 'user', 'map', 'profile_fields');

$top_message = &cw_session_register('top_message', array());

if (!empty($customer_id))
    $userinfo = cw_user_get_info($customer_id, 1);

if ($section == 'login_customer' && !empty($customer_id)) {
    cw_header_location('index.php?target=acc_manager');
}

if (!$section) $section = 'general';

if ($action == "contactus" || $section == 'contactus') {

    $is_areas = array(
        "I" => (
            !empty($profile_fields['title']['avail']) ||
            !empty($profile_fields['firstname']['avail']) ||
            !empty($profile_fields['lastname']['avail']) ||
            !empty($profile_fields['company']['avail'])
        ),
        "A" => (
            !empty($profile_fields['b_address']['avail']) ||
            !empty($profile_fields['b_address_2']['avail']) ||
            !empty($profile_fields['b_city']['avail']) ||
            !empty($profile_fields['b_county']['avail']) ||
            !empty($profile_fields['b_state']['avail']) ||
            !empty($profile_fields['b_country']['avail']) ||
            !empty($profile_fields['b_zipcode']['avail']) ||
            !empty($profile_fields['phone']['avail']) ||
            !empty($profile_fields['fax']['avail']) ||
            !empty($profile_fields['avail']['avail']) ||
            !empty($profile_fields['url']['avail'])
        ),
    );

    $smarty->assign('states', cw_map_get_states());
    $smarty->assign('countries', cw_map_get_countries());
    if ($config['General']['use_counties'] == "Y") {
        cw_include("include/counties.php");
    }

    $contact_sections = cw_profile_fields_get_sections('P', $current_area);
    $smarty->assign('contact_sections', $contact_sections);
}

if ($REQUEST_METHOD=="POST" && $action=="contactus") {
    #
    # Send mail to support
    #
    $body = $_POST['body'] = stripslashes($_POST['body']);

    foreach ($_POST as $key=>$val) {
        if ($key != 'additional_values')
            $contact[$key]=$val;
    }

    global $show_antibot_arr;

    $antibot_err = &cw_session_register("antibot_err");
    $page = "on_contact_us";
    if ($addons['image_verification'] && $show_antibot_arr[$page] == 'Y') {
        if (isset($antibot_input_str) && !empty($antibot_input_str)) {
            $antibot_err = cw_validate_image($antibot_validation_val[$page], $antibot_input_str);
        } else {
            $antibot_err = true;
        }
    }

    $fillerror = false;
    $profile_fields = array(
        'email' => array('avail' => 'Y', 'required' => 'Y'),
        'subject' => array('avail' => 'Y', 'required' => 'Y'),
        'body' => array('avail' => 'Y', 'required' => 'Y'),
        'firstname' => array('avail' => 'Y'),
        'lastname' => array('avail' => 'Y'),
        'daytime_phone' => array('avail' => 'Y')
    );
    foreach ($profile_fields as $k => $v) {
        if ($k == "b_county" && $v['avail'] == 'Y' && ($v['required'] == 'Y' || !empty($contact['b_county']))) {
            if ($config['General']['use_counties'] != "Y")
                continue;
            if (!cw_check_county($contact[$k], stripslashes($contact['b_state']), $contact['b_country']))
                $fillerror = true;
        } elseif ($k == "b_state" && $v['avail'] == 'Y' && ($v['required'] == 'Y' || !empty($contact['b_state']))) {
            $has_states = (cw_query_first_cell("SELECT display_states FROM $tables[map_countries] WHERE code = '".$contact['b_country']."'") == 'Y');
            if (is_array($states) && $has_states && !cw_check_state($states, stripslashes($contact['b_state']), $contact['b_country']))
                $fillerror = true;
        } elseif ($k == "email" && $v['avail'] == 'Y' && ($v['required'] == 'Y' || !empty($contact['email']))) {
            if (!cw_check_email($contact['email']))
                $fillerror = true;
        } elseif (empty($contact[$k]) && $v['required'] == 'Y' &&  $v['avail'] == 'Y') {
            $fillerror = true;
        }
    }

    if (!$fillerror && is_array($additional_fields)) {
        foreach($additional_fields as $k => $v) {
            $additional_fields[$k]['value'] = stripslashes($_POST['additional_values'][$v['field_id']]);
            if (empty($_POST['additional_values'][$v['field_id']]) && $v['required'] == 'Y' &&  $v['avail'] == 'Y')
                $fillerror = true;
        }
    }

    if (!$fillerror) {
        $fillerror = (empty($subject) || empty($body));
    }

    if (!$fillerror && !$antibot_err) {
        $contact['b_statename'] = cw_get_state($contact['b_state'], $contact['b_country']);
        $contact['b_countryname'] = cw_get_country($contact['b_country']);
        if ($config['General']['use_counties'] == "Y")
            $contact['b_countyname'] = cw_get_county($contact['b_county']);

        $contact = cw_stripslashes($contact);
        $smarty->assign('contact', $contact);
        $smarty->assign('profile_fields', $profile_fields);
        $smarty->assign('is_areas', $is_areas);
        $smarty->assign('additional_fields', $additional_fields);

        cw_call('cw_send_mail', array($contact['email'], $config['Company']['support_department'], 'mail/contactus/subj.tpl', 'mail/contactus/body.tpl', $config['default_admin_language']));

        $top_message = array(
            'content' => cw_get_langvar_by_name('txt_contact_us_sent'),
            'type' => 'I',
        );

        cw_header_location(cw_call('cw_core_get_html_page_url', array(array("var"=>"help", "section"=>"contactus", 'delimiter' => '&'))));
    } else {
        cw_unset($_POST, 'additional_values');
        $userinfo = $_POST;
        $userinfo['customer_id'] = $userinfo['uname'];
    }
}

#
# Recover password feature regenerates password and sends it to user if mail match
#
if ($action == 'recover_password' && !empty($email)) {
    $accounts = cw_query("SELECT customer_id, password, usertype, email FROM $tables[customers] WHERE email='$email' AND status='Y' and usertype='$current_area'");
    #
    # Regenerate password
    #
/* do not expose to anyone if email is registered or not in the system
    if (empty($accounts)) {
        $top_message = array(
            'content' => cw_get_langvar_by_name('txt_password_recover_message_not_found'),
            'type' => 'E',
        );
        cw_header_location(cw_call('cw_core_get_html_page_url', array(array('var'=>'help', 'section'=>'password', 'email'=>urlencode($email), 'delimiter' => '&'))));
    }
*/
   foreach ($accounts as $key => $account) {
       $reset_keys = explode(":",$account['password']);
       $reset_key = $reset_keys[0];
       $smarty->assign('reset_url', $reset_url = "index.php?target=help&amp;section=reset_password&amp;reset_key=$reset_key&amp;email=".urlencode($email));
       $smarty->assign('user_info', $account); 
       cw_call('cw_send_mail', 
           array($config['Company']['support_department'], 
                 $email, 
                 "mail/password_recover_confirm_subj.tpl", 
                 "mail/password_recover_confirm.tpl",
                 null, false, false, array(), true)
       );
       
   } 
   $top_message = array(
        'content' => cw_get_langvar_by_name('txt_password_recovery_email_confirmation_sent'),
        'type' => 'I',
   );
   cw_header_location(cw_call('cw_core_get_html_page_url', array(array('var'=>'help', 'section'=>'password', 'email'=>urlencode($email), 'delimiter' => '&'))));
}

if ($REQUEST_METHOD=="GET" && $section=="reset_password" && !empty($reset_key) && !empty($email)) {

    $reset_key = preg_replace("/[^A-Za-z0-9\s\s+]/",'',substr($reset_key,0,32));

    $email = urldecode($email);

    $account = cw_query_first("SELECT customer_id, password, usertype, email FROM $tables[customers] WHERE email='$email' and password like '$reset_key:%' AND status='Y'");

 
    if (!empty($account)) {
        $account['password'] = cw_user_generate_password();
        cw_array2update(
            'customers', 
            array('password' => cw_call('cw_user_get_hashed_password', array($account['password']))),
            "customer_id = '$account[customer_id]'"
        );   
            
        $smarty->assign('accounts', array($account));
        cw_call('cw_send_mail', array($config['Company']['support_department'], $email, "mail/password_recover_subj.tpl", "mail/password_recover.tpl",null, false, false, array(), true));
        $top_message = array('content' => cw_get_langvar_by_name('txt_password_recover_message'), 'type' => 'I');
    } else {
        $top_message = array('content' => cw_get_langvar_by_name('txt_password_recover_expired_key'), 'type' => 'E');
    }
    cw_header_location(cw_call('cw_core_get_html_page_url', array(array('var'=>'help', 'section'=>'password_sent', 'email'=>urlencode($email), 'delimiter' => '&'))));
}

if ($popup_title)
    $smarty->assign('popup_title', $popup_title);

if (!empty($section) && $section != 'general') {
    $location[] = array(cw_get_langvar_by_name('lbl_help_zone'), 'index.php?target=help');
    $location[] = array(cw_get_langvar_by_name('lbl_help_section_'.$section), '');
}
else
    $location[] = array(cw_get_langvar_by_name('lbl_help_zone'), '');

$smarty->assign_by_ref('userinfo', $userinfo);
$smarty->assign_by_ref('fillerror', $fillerror);
if ($addons['image_verification'] && $antibot_err) {
    $smarty->assign_by_ref('antibot_err', $antibot_err);
    cw_session_unregister("antibot_err");
}

$smarty->assign('profile_fields', $profile_fields);
$smarty->assign('additional_fields', $additional_fields);
$smarty->assign('fillerror', $fillerror);

$smarty->assign('current_section_dir', 'help');
$smarty->assign('main', 'help');
$smarty->assign('get_email', urldecode($email));
$smarty->assign('section', $section);
