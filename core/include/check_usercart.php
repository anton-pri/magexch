<?php
	global $smarty, $user_account, $customer_id, $user_address;
	
    cw_load('taxes');
    $cart = &cw_session_register('cart', array());

    if ($user_account['membership_id']) $config['Appearance']['show_cart_summary'] = $user_account['show_summary'];

    $settings = cw_query_first("select show_prices from $tables[memberships] where membership_id='".$user_account['membership_id']."'");
    $user_account['show_prices'] = $settings['show_prices'];
