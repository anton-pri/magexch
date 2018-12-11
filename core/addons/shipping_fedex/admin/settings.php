<?php
if ($request_prepared['cat'] != 'shipping_fedex') return;

cw_load('config');

$saved_user_data = &cw_session_register('saved_user_data');
$top_message = &cw_session_register('top_message');


if ($action == 'clear_meter_number') {
    cw_config_update('shipping_fedex', array('meter_number' => ''));
	cw_header_location("index.php?target=settings&cat=shipping_fedex");
}

if ($action == 'get_meter_number') {

    $rules = array(
        'person_name' => '',
        'phone_number' => '',
        'address_1' => '',
        'city' => '',
//        'state' => '',
        'zipcode' => '',
        'country' => '',
    );
    $fillerror = cw_error_check($posted_data, $rules);
    $saved_user_data = $posted_data;
    if ($fillerror) {
        $top_message = array( 'content' => $fillerror, 'type' => 'E');
        cw_header_location("index.php?target=settings&cat=shipping_fedex");
    }

    $meter_number = cw_fedex_get_meter_number($posted_data, $error);
    if ($meter_number) {
        $saved_user_data = '';
        cw_config_update('shipping_fedex', array('meter_number' => $meter_number));
    }
    else
        $top_message = array( 'content' => $error['msg'], 'type' => 'E');
	cw_header_location("index.php?target=settings&cat=shipping_fedex");
}

$smarty->assign('prepared_user_data', $saved_user_data);
