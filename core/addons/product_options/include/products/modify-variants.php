<?php
cw_load( 'product');
$search_variants = &cw_session_register('search_variants');
cw_image_clear(array('products_images_var'));

if ($action == 'product_variants_modify' && $vs) {

//    cw_log_add('mass_update_test',array($vids,$vs,$set_mass_apply,$mass_apply));

    if (!empty($set_mass_apply) && !empty($vids)) {
        foreach ($set_mass_apply as $field2update => $digit_flg) {
            foreach ($vids as $sel_vid) {
                if (isset($vs[$sel_vid][$field2update])) { 
                    $vs[$sel_vid][$field2update] = $mass_apply[$field2update];   
                }
            } 
        } 
    }
//    cw_log_add('mass_update_test',array($vs)); 

    if (is_array($vs))
	foreach($vs as $k => $v) {
        if (AREA_TYPE == 'P') {
            $insert = array();
            $insert['avail'] = cw_convert_number($v['avail']);
            $insert['warehouse_customer_id'] = $user_account['warehouse_customer_id'];
            $insert['variant_id'] = $k;
            $insert['product_id'] = $product_id;
            cw_warehouse_insert_avail($insert, true);
            continue;
        }
		if (isset($v['price'])) $v['price'] = cw_convert_number($v['price']);
		$v['weight'] = cw_convert_number($v['weight']);
		$v['cost'] = cw_convert_number($v['cost']);
		$query_data = array(
			'weight' => $v['weight'],
			'cost' => $v['cost'],
		);

		$v['mpn']=trim($v['mpn']); $v['productcode']=trim($v['productcode']); $v['eancode']=trim($v['eancode']);

		if (!cw_query_first_cell("select count(*) from $tables[product_variants] where productcode = '$v[productcode]'"))
			$query_data['productcode'] = $v['productcode'];

        if (!cw_query_first_cell("select count(*) from $tables[product_variants] where eancode = '$v[eancode]'"))
            $query_data['eancode'] = $v['eancode'];

        //if ($v['mpn']=='' || !cw_query_first_cell("select count(*) from $tables[product_variants] where mpn = '$v[mpn]'"))
        $query_data['mpn'] = $v['mpn'];

		cw_array2update('product_variants', $query_data, "variant_id = '$k'");
        if (isset($v['price'])) cw_product_replace_price($product_id, $v['price'], $k, false, $v['is_manual_price']);
        $v['variant_id'] = $k;
        $v['product_id'] = $product_id;
        $v['warehouse_customer_id'] = 0;

        cw_array2insert('products_warehouses_amount', $v, 1, array('product_id', 'avail', 'avail_ordered', 'avail_sold', 'avail_reserved', 'variant_id', 'warehouse_customer_id'));

        cw_call('cw_warehouse_recalculate', array($product_id));

		if ($ge_id && !$fields['variants'][$k]) {
    		cw_unset($query_data, 'productcode');
    		while ($pid = cw_group_edit_each($ge_id, 1, $product_id)) {
	    		$vid = cw_variants_get_same($k, $pid);
		    	if (empty($vid)) continue;

    			cw_array2update('product_variants', $query_data, "variant_id = '$vid'");
                if (isset($v['price'])) cw_product_replace_price($pid, $v['price'], $vid, false, $v['is_manual_price']);
	    		if ($def_variant == $k) {
					cw_array2update('product_variants', array('def' => ''), "product_id = '$pid'");
		    		cw_array2update('product_variants', array('def' => 'Y'), "product_id = '$pid' and variant_id='$vid'");
				}
		    }
        }
	}

	if (!empty($def_variant)) {
		cw_array2update('product_variants', array('def' => ''), "product_id = '$product_id'");
        cw_array2update('product_variants', array("def" => 'Y'), "product_id = '$product_id' and variant_id='$def_variant'");
	}

	if (is_array($vids) && cw_image_check_posted($file_upload_data['products_images_var'])) {
        $vids = array_keys($vids);
        $vid = array_shift($vids);
        $image_id = cw_image_save($file_upload_data['products_images_var'], array('id' => $vid));
        $res = cw_addslashes(cw_query_first("select * from $tables[products_images_var] where image_id = '$image_id' limit 1"));
        unset($res['image_id']);

        if ($res)
		foreach($vids as $vid) {
            $res['id'] = $vid;
            cw_image_delete($vid, 'products_images_var');
            cw_array2insert('products_images_var', $res);
        }

        if ($ge_id && $fields['variants'])
        while($pid = cw_ge_each($ge_id, 1, $product_id)) {
			$res['id'] = cw_variants_get_same($v, $pid);
			if (empty($res['id'])) continue;
            cw_image_delete($res['id'], 'products_images_var');
            cw_array2insert('products_images_var', $res);
        }
	}

	$top_message = array('content' => cw_get_langvar_by_name('msg_adm_product_variants_upd'), 'type' => 'I');
	$refresh = $rebuild_quick = true;
}
elseif ($action == 'product_variants_rebuild' && AREA_TYPE == 'A') {
	cw_rebuild_variants($product_id, true);
	$top_message['content'] = cw_get_langvar_by_name("msg_adm_product_variants_rebuilded");
	$top_message['type'] = "I";
	$refresh = $rebuild_quick = true;

}
elseif ($action == "variants_delete_image" && !empty($vids) and AREA_TYPE == 'A') {
	foreach ($vids as $k => $v) {
		cw_image_delete($k, 'products_images_var');

		# Delete variants image (Group editing of products functionality)
		if ($ge_id && $fields['variants'][$k]) {
			while ($pid = cw_ge_each($ge_id, 1, $product_id)) {
				$vid = cw_variants_get_same($k, $pid);
				if (!empty($vid))
					cw_image_delete($vid, 'products_images_var');
			}
		}
	}
	$refresh = $rebuild_quick = true;
}
elseif ($action == 'product_variants_search') {
	$search_variants[$product_id] = empty($search) ? array() : $search;
	$refresh = true;
}

if ($rebuild_quick) {
    cw_func_call('cw_product_build_flat', array('product_id' => $product_id));

	if ($ge_id)
    while($pid = cw_group_edit_each($ge_id, 100))
	    cw_func_call('cw_product_build_flat', array('product_id' => $pid));
}

if ($refresh)
    cw_refresh($product_id, 'product_variants');

# Get the product options list
$product_options = cw_call('cw_get_product_classes', array($product_id));
if(!empty($product_options))
	$smarty->assign('product_options', $product_options);

$variants = cw_call('cw_get_product_variants', array($product_id));

$svariants = $search_variants[$product_id];
if ($svariants && !empty($variants)) {
	$tmp = current($variants);
	$cnt = count($tmp['options']);
	unset($tmp);

	foreach ($variants as $k => $v) {
		$local_cnt = 0;
		foreach ($svariants as $cid => $c) {
			foreach ($c as $oid) {
				if (isset($v['options'][$oid]) && $v['options'][$oid]['product_option_id'] == $cid)
					$local_cnt++;
			}
		}

		if ($local_cnt != $cnt) {
			unset($variants[$k]);
		}
	}

} elseif (!is_array($svariants)) {
	$smarty->assign('is_search_all', 'Y');
}

if (!empty($variants)) {
	$smarty->assign('variants', $variants);

	# Check default variant
	foreach ($variants as $vid => $v) {
		if ($v['def'] == 'Y') {
			$vid_def = cw_get_default_variant_id($product_id);
			if ($vid != $vid_def)
				$smarty->assign('def_variant_failure', true);
			break;
		}
	}

	if (!empty($variant_id)) {
		foreach($variants as $k => $v) {
			if ($k == $variant_id) {
				$variant = $v;
				break;
			}
		}

		if (!empty($variant))
			$smarty->assign('variant', $variant);
	}
}

$smarty->assign('search_variants', $svariants);

$smarty->assign('is_variants', (cw_query_first_cell("select count(*) FROM $tables[product_variants] where product_id = '$product_id'") > 0 ? "Y" : ""));
