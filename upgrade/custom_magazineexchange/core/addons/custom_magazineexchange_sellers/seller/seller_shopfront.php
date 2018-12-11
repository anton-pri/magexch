<?php
namespace cw\custom_magazineexchange_sellers;

cw_load( 'image');

// use $mode and $action params to define subject and action to call

//$action_function = $action;
$action_function = $mode.(!empty($action)?'_'.$action:'');
//die("m: $mode a: $action af: $action_function");
// Default action
if (empty($action_function) || !function_exists('cw\\'.addon_name.'\\'.$action_function)) {
    $action_function = 'view';
}

// Call action
$action_result = cw_call('cw\\'.addon_name.'\\'.$action_function);

// Action can return instance of Error via error() function
// see docs/core.error.txt
if (is_error($action_result)) {
    cw_add_top_message($action_result->getMessage(), 'E');
}

return $action_result;

cw_image_clear(array('shopfront_images'));

function view() {
    global $smarty, $customer_id, $tables;

//    print($customer_id);

    $shopfront = cw_call('cw\custom_magazineexchange_sellers\mag_get_shopfront', array($customer_id));
//    print_r($shopfront); 
    $smarty->assign('shopfront', $shopfront);
    $smarty->assign('main', 'seller_shopfront');
    return true;
}

function update() {
    global $request_prepared, $customer_id;

    $file_upload_data = &cw_session_register('file_upload_data');

    $posted_data = $request_prepared['posted_data'];

    $posted_data['holiday_settings_return_date'] = strtotime(str_replace('/','-',$posted_data['holiday_settings_return_date']));
   
    if ($posted_data['holiday_settings_return_date'] <= time() && $posted_data['holiday_settings']) {
        cw_add_top_message(cw_get_langvar_by_name('txt_holiday_settings_date_error'),'E');
        $posted_data['holiday_settings'] = false;
        $update_error = true;
    }

    cw_call('cw\custom_magazineexchange_sellers\mag_delete_shopfront', array($customer_id)); 
    
    $shopfront_id = cw_array2insert('magexch_sellers_shopfront', 
        array('seller_id'=>$customer_id, 
              'shop_name' => $posted_data['shop_name'], 
              'short_desc' => $posted_data['short_desc'], 
              'long_desc' => $posted_data['long_desc'],
              'holiday_settings' => $posted_data['holiday_settings'],
              'holiday_settings_return_date' => $posted_data['holiday_settings_return_date'])
    );

    if (cw_image_check_posted($file_upload_data['shopfront_images']))
        cw_image_save($file_upload_data['shopfront_images'], array('id' => $customer_id));

    if (!$update_error)
        cw_add_top_message(cw_get_langvar_by_name('txt_shopfront_updated_succesfully'),'I');

    cw_header_location("index.php?target=seller_shopfront");
    return true;
}

function delete_image() {
    global $customer_id;
    cw_image_delete($customer_id, 'shopfront_images');
    cw_header_location("index.php?target=seller_shopfront");
    return true;
}
