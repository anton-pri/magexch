<?php
cw_load('doc', 'cart_process');

global $customer_id;
if (!cw_doc_allowed_to_customer($request_prepared['doc_id'], $customer_id))
    cw_header_location("index.php?target=error_message&error=access_denied&id=40");

$cart = &cw_session_register('cart', array());

$cart_attempt_backup = &cw_session_register('cart_attempt_backup', array());

if ($request_prepared['save_backup'] == 'Y') {
    $cart_attempt_backup = $cart;
    cw_session_save();
}

$info_type = 1 + 512 + 8192;

$doc_data = cw_doc_get($request_prepared['doc_id'], $info_type);

$cart_now = $cart;

$cart = array();

$cart['userinfo'] = $doc_data['userinfo'];
unset($cart['userinfo']['profile_sections']);
unset($cart['userinfo']['profile_fields']);


$userinfo = cw_user_get_info($customer_id, 1);

foreach (['status', 'language', 'flag'] as $profile_field)
    $cart['userinfo'][$profile_field] = $userinfo[$profile_field];

$active_address = 
    ($doc_data['userinfo']['main_address']['main'] == 1) ? 
    $doc_data['userinfo']['main_address'] : 
    $doc_data['userinfo']['current_address'];

foreach ([
    'firstname', 
    'lastname', 
    //'company_id', 
    'address_id', 
    'company', 
    'title', 
    'address', 
    'address_2', 
    'city', 
    'county', 
    'region', 
    'state', 
    'country',
    'zipcode',
    'phone',
    'fax',
    'titleid',
    'statename',
    'countryname'
    ] as $address_field) {
        if (isset($active_address[$address_field])) {
            $cart['userinfo'][$address_field] = $active_address[$address_field]; 
            switch ($address_field) {
                case 'lastname':
                    $names = [];
                    if (!empty($cart['userinfo']['firstname']))
                        $names[] = $cart['userinfo']['firstname'];

                    if (!empty($cart['userinfo']['lastname']))
                        $names[] = $cart['userinfo']['lastname'];

                    $cart['userinfo']['fullname'] = implode(' ', $names);
                    $cart['userinfo']['custom_fields_by_name'] = ['suspend_account' => ''];
                    $cart['userinfo']['company_id'] = 0;
                break;
                case 'address_id':
                    $cart['userinfo']['main'] = $doc_data['userinfo']['main_address']['main'];
                    $cart['userinfo']['current'] = $doc_data['userinfo']['current_address']['main'];                    
                break;    
            } 
        } else {
            print("cant find $address_field in active_address <br>");
        }
    }

$cart['pos'] = '';
$cart['orders'] = [
    0 => [
        'info' => [], 
        'products' => [],
        'warehouse_customer_id' => $doc_data['info']['warehouse_customer_id'],
    ]
];

//$cart['orders'][0]['info']

foreach (
    ['payment_surcharge',
    'discount',
    'discount_value',
    'coupon',
    'coupon_discount',
    'subtotal',
    'display_subtotal',
    'discounted_subtotal',
    'display_discounted_subtotal',
    'weight',
    'shipping_cost',
    'shipping_insurance',
    'display_shipping_cost',
    'total'] as $info_field) {
        if (isset($doc_data['info'][$info_field]))
            $cart['orders'][0]['info'][$info_field] = $doc_data['info'][$info_field];   
        else 
            print("Cant find $info_field in doc info <br>");    
    }

$cart['orders'][0]['info']['shipping_surcharge'] = 0;
$cart['orders'][0]['info']['shipping_no_offer'] = 0; 
$cart['orders'][0]['info']['taxed_subtotal'] = $doc_data['info']['extra']['tax_info']['taxed_subtotal'];
$cart['orders'][0]['info']['taxes'] = $doc_data['info']['applied_taxes'];
$cart['orders'][0]['info']['tax_cost'] = $doc_data['info']['tax'];
    
$cartid = 0;
foreach ($doc_data['products'] as $product) {
    $product['cartid'] = ++$cartid;
    $product['list_price'] = $product['original_price'];
    unset($product['original_price']);
    
    unset($product['is_auto_calc']);
    unset($product['end_price']);
    unset($product['seller_data_id']);
    unset($product['price_deducted_tax']);

    $product['seller'] = Array(
        'id' => $product['warehouse_customer_id'],
        'seller_item_id' => $product['extra_data']['seller_item']['seller_item_id'],
        'is_digital' => 
            cw_query_first_cell(
                "SELECT is_digital 
                FROM $tables[magazine_sellers_product_data] 
                WHERE seller_item_id={$product['extra_data']['seller_item']['seller_item_id']}"
            ),
    );
    unset($product['extra_data']);
    $product['seller_id'] = $product['seller']['id'];
    $product['seller_item_id'] = $product['seller']['seller_item_id'];

    $cart['orders'][0]['products'][] = $product;
}


$cart['payment_surcharge'] = [0, 0];

$cart['info'] = [];

foreach(
    ['giftcert_discount',
    'total',
    'display_discounted_subtotal',
    'display_subtotal',
    'shipping_id',
    'shipping_cost',
    'shipping_insurance',
    'discount',
    'weight',
    'coupon_discount',
    'subtotal',
    'discounted_subtotal',
    'display_shipping_cost',
    'payment_id'] as $info_field) {
        if (isset($doc_data['info'][$info_field]))
            $cart['info'][$info_field] = $doc_data['info'][$info_field];
        else    
            print("cant find $info_field in doc info<br>");
    }

$cart['info']['applied_giftcerts'] = $doc_data['info']['giftcert_ids'];
$cart['info']['taxed_subtotal'] = $doc_data['info']['extra']['tax_info']['taxed_subtotal'];
$cart['info']['tax_cost'] = $doc_data['info']['tax'];
$cart['info']['shipping_no_offer'] = 0;
$cart['info']['taxes'] = $doc_data['info']['applied_taxes'];
    

$cart['delivery'] = $doc_data['info']['shipping_label'];
$cart['max_cartid'] = $cartid;
$cart['products'] = $cart['orders'][0]['products'];

cw_cart_normalize($cart);
$cart = cw_func_call('cw_cart_calc', array('cart' => $cart, 'products' => $cart['products'], 'userinfo' => $cart['userinfo']));

/*
print('<pre>');
print_r(compact('doc_data', 'cart_now' ,'cart', 'cart_attempt_backup'));
print('</pre>');
*/

cw_header_location("index.php?target=cart&mode=checkout&step=1");
