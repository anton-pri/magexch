<?php
if (isset($_GET['created_by'])) {
    $user_data = cw_call('cw_user_get_info', array($_GET['created_by'], 1));
    $userphoto = cw_call('cw_user_get_avatar', array($_GET['created_by']));
    $current_user = array();

    if ($user_data['usertype'] == seller_area_letter) {
        $address = empty($user_data['main_address']) ? $user_data['current_address'] : $user_data['main_address'];
        $current_user['id'] = $user_data['customer_id'];
        $current_user['name'] = trim($address['firstname'] . ' ' . $address['lastname']);
        $current_user['address'] = trim($address['countryname'] . ', ' . $address['statename'] . ', ' . $address['city']);
        $current_user['avatar'] = $userphoto;

        $smarty->assign('current_user_seller', $current_user);
    }
}
// If some search criteria passed as get params, then consider corrsponding tab on search for as active
if ($REQUEST_METHOD == 'GET') {
    $search_data = &cw_session_register("search_data", array());
    if (!is_array($search_data))
        $search_data = array();

    if (!empty($_GET['status']) || !empty($_GET['avail_types'])) {

        $search_data['products']['general']['search_sections']['tab_add_search'] = 1;
    }
}
