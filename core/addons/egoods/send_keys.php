<?php
if (!defined('APP_START')) die('Access denied');

if (empty($products))
	return false;

cw_load('mail');

$send_keys = false;

foreach($products as $key=>$value){
	if ($value['distribution']) {
		$download_key = keygen($value['product_id'], $config['egoods']['download_key_ttl'], $value['item_id']);
		$products[$key]['download_key'] = $download_key;
		$products[$key]['distribution_filename'] = basename($products[$key]['distribution']);
		$send_keys = true;
	}
}

$smarty->assign('products', $products);

if ($send_keys) {
	$to_customer = cw_user_get_language($userinfo['customer_id']);
	if (empty($to_customer))
		$to_customer = $config['default_customer_language'];

	cw_call('cw_send_mail', array($config['Company']['orders_department'], $userinfo['email'], 'mail/egoods_download_keys_subj.tpl', 'mail/egoods_download_keys.tpl'));
}

?>
