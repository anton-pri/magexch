<?php
cw_load('ajax', 'email', 'product', 'user');

$product_info = cw_func_call('cw_product_get', array('id' => $product_id, 'user_account' => $user_info, 'info_type' => 0));

// get oswner id
$user_owner_id = cw_query_first_cell("
    SELECT creation_customer_id
    FROM $tables[products_system_info]
    WHERE product_id = '$product_id'
");

$user_owner_email = "";
if (!empty($user_owner_id)) {
    $user_owner_info = cw_call('cw_user_get_info', array('customer_id' => $user_owner_id, 'info_type' => 0));
    $user_owner_email = $user_owner_info['email'];
}

// send notification email to product owner and admin
$from = $config['Company']['site_administrator'];
$to = !empty($config['Company']['inventory_department'])?$config['Company']['inventory_department']:$config['Company']['site_administrator'];
$mail_subject = "The notification about reported sold out product";
$mail_body = '<b>You have received this notification from <a href="' . $current_location . '">';
$mail_body .= $config['Company']['company_name'] . '</a></b><br />';
$mail_body .= 'A user reports that the product <a href="' . $current_location . '/index.php?target=product&product_id=';
$mail_body .= $product_id . '">' . $product_info['product'] . '</a> is sold out.<br />';
cw_send_simple_mail($from, $to, $mail_subject, $mail_body);

if (!empty($user_owner_email) && $to != $user_owner_email) {
    cw_send_simple_mail($from, $user_owner_email, $mail_subject, $mail_body);
}

cw_add_ajax_block(array(
    'id' => 'report_about_sold',
    'action' => 'update',
    'content' => cw_get_langvar_by_name('lbl_reported')
));
