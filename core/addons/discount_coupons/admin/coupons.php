<?php
$saved_coupon_data = &cw_session_register('saved_coupon_data', array());

$location[] = array(cw_get_langvar_by_name('lbl_discount_coupons'), '');

if ($action == 'update' && is_array($posted_data)) {
    foreach ($posted_data as $coupon=>$v)
      db_query("update $tables[discount_coupons] set status='$v[status]' where coupon='$coupon'");

    $top_message["content"] = cw_get_langvar_by_name("msg_discount_coupons_upd");
    cw_header_location("index.php?target=$target");
}

if ($action == 'delete' && is_array($posted_data)) {
    foreach ($posted_data as $coupon=>$v)
        if ($v['to_delete'])
            db_query("delete from $tables[discount_coupons] where coupon='$coupon'");

    $top_message["content"] = cw_get_langvar_by_name("msg_discount_coupons_upd");
    cw_header_location("index.php?target=$target");
}
	
if ($action == 'add') {
    cw_core_process_date_fields($add_coupon, array('' => array('expire' => 0)));

    switch ($add_coupon['apply_to']) {
		case '':
			$add_coupon['product_id'] = 0;
			$add_coupon['category_id'] = 0;
			break;
		case 'product':
			$add_coupon['category_id'] = 0;
			$add_coupon['apply_product_once'] = $add_coupon['how_to_apply_p'];
			break;
		case 'category':
			$add_coupon['product_id'] = 0;
			if ($add_coupon['how_to_apply_c'] == 1)
				$add_coupon['apply_product_once'] = 1;
			elseif ($how_to_apply_c == 2) {
				$add_coupon['apply_product_once'] = 0;
				$add_coupon['apply_category_once'] = 0;
			}
			else {
				$add_coupon['apply_product_once'] = 1;
				$add_coupon['apply_category_once'] = 0;
			}
			break;
    }

    if (empty($add_coupon['coupon']) || (empty($add_coupon['discount']) && $add_coupon['coupon_type'] != 'free_ship') || cw_query_first_cell("select count(*) from $tables[discount_coupons] where coupon='$add_coupon[coupon]'") > 0) {
        $saved_coupon_data = $add_coupon;
		$top_message = array('content' => cw_get_langvar_by_name('msg_err_discount_coupons_add'), 'type' => 'E');
        cw_header_location("index.php?target=$target&mode=add");
    }
	else {
        cw_array2insert('discount_coupons', $add_coupon, 1, array('coupon', 'discount', 'coupon_type', 'minimum', 'times', 'per_user', 'expire', 'status', 'product_id', 'category_id', 'recursive', 'apply_category_once', 'apply_product_once'));
        $top_message['content'] = cw_get_langvar_by_name('msg_discount_coupons_add');
        cw_session_unregister('saved_coupon_data');
    }
	cw_header_location("index.php?target=$target");
}

if ($mode == 'add') {
    $smarty->assign('main', 'add_new_coupon');
} else {
    $coupons = cw_query("select * from $tables[discount_coupons]");
    $smarty->assign('coupons', $coupons);
    $smarty->assign('main', 'coupons');
}

$smarty->assign('coupon_data', $saved_coupon_data);

$smarty->assign('current_main_dir', 'addons');
$smarty->assign('current_section_dir', 'discount_coupons');
