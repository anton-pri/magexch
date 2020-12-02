<?php
$last_added_product_type = &cw_session_register('last_added_product_type');
cw_load('category', 'image', 'product', 'warehouse', 'user', 'serials', 'group_edit', 'ean', 'xls', 'attributes', 'tags');

if (!$product_id && $current_area != 'A' && $current_area != 'V')
    cw_header_location('index.php');

$file_upload_data = &cw_session_register('file_upload_data');
cw_image_clear(array('products_images_det', 'products_images_thumb'));

$__ge_res = false;

$top_message = &cw_session_register('top_message', array());
$product_modified_data = &cw_session_register('product_modified_data');

function cw_refresh($product_id, $js_tab= '', $added = '') {
    global $ge_id, $target;

    if (!empty($js_tab))
        $js_tab= "&js_tab=".$js_tab;
    if (!empty($ge_id))
        $redirect_ge_id = "&ge_id=".$ge_id;

    cw_event('on_product_modify_end');

    if ($product_id)
        cw_header_location("index.php?target=$target&mode=details&product_id=".$product_id.$redirect_ge_id.$js_tab.$added);
    else
        cw_header_location("index.php?target=$target&mode=add");
}

if ($ge_id && cw_group_edit_count($ge_id) == 0)
    $ge_id = false;

global $product_info;

if ($product_id) {
    $product_info = cw_func_call('cw_product_get', array('id' => $product_id, 'user_account' => $user_account, 'info_type' => 65535, 'lang' => $edited_language, 'for_product_modify' => true));
    if (!$product_info) {
        $top_message = array('content' => cw_get_langvar_by_name('lbl_products_deleted'), 'type' => 'E');
        cw_header_location('index.php?target='.$target);
    }
}

$warehouses = cw_get_warehouses();
$smarty->assign('warehouses', $warehouses);

if (AREA_TYPE == 'A' && $action == "delete_thumbnail" && !empty($product_id)) {
    cw_image_delete($product_id, 'products_images_thumb');
    if ($ge_id && $fields['thumbnail'])
    while ($pid = cw_group_edit_each($ge_id, 100, $product_id))
        cw_image_delete($pid, 'products_images_thumb');
    cw_refresh($product_id);
}
if (AREA_TYPE == 'A' && $action == 'delete_product_image' && !empty($product_id)) {
    cw_image_delete($product_id, 'products_images_det');
    if ($ge_id && $fields['product_image'])
    while ($pid = cw_group_edit_each($ge_id, 100, $product_id))
        cw_image_delete($pid, 'products_images_det');
    cw_refresh($product_id);
}

if ($product_id)
    $smarty->assign('main', 'product_modify');
else
    $smarty->assign('main', 'product_add');

if (
    $REQUEST_METHOD == "POST"
    && ($action == "product_modify" || $action == 'add')
    && (AREA_TYPE == 'A' || AREA_TYPE == 'V')
) {
    $is_variant = false;
# kornev, TOFIX
    if ($product_id && $addons['product_options'])
        $is_variant = cw_product_has_variants($product_id);

    $last_added_product_type = $product_data['product_type'];

    if (!$product_data['eancode'] && $addons['barcode'] && $config['barcode']['gen_product_code'])
        $product_data['eancode'] = cw_product_generate_sku($config['barcode']['gen_product_code'], 'eancode');
    cw_ean_clear($product_data['eancode']);

    if ($mode == 'add') {
        if (!$product_data['productcode'])
            $product_data['productcode'] = cw_product_generate_sku();
        if(!$product_data['membership_ids'])
            $product_data['membership_ids'] = array_keys(unserialize($config['product']['default_product_memberships']));
    }

    $rules = array(
        'category_id' => '',
        'product' => '',
        'productcode' => array('func' => 'cw_error_sku_exists'),
        'manufacturer_code' => array('func' => 'cw_error_manufacturer_code_exists'),
    );

    if ($config['product']['product_descr_is_required'] == 'Y') 
        $rules['fulldescr'] = '';

    $product_data['product_id'] = $product_id;
    $product_data['attributes'] = $attributes;
	if ($config['Appearance']['categories_in_products'] == '0') {
		unset($rules['category_id']);
	} elseif ($config['Appearance']['categories_in_products'] == '1' && empty($product_data['category_id'])) {
		$product_data['category_id'] = 1;
	}
    $fillerror = cw_call('cw_error_check', array(&$product_data, $rules, 'P'));

    if (!$fillerror) {
        $is_new_product = false;
        if (!$product_id) {
            $is_new_product = true;

            $product_data['product_id'] = $product_id = cw_array2insert('products', array('productcode' => $product_data['productcode'], 'product_type' => $product_data['product_type']));

            if (cw_image_check_posted($file_upload_data['products_images_thumb'])) {
                if (!$file_upload_data['products_images_det'])
                    cw_image_copy($file_upload_data, 'products_images_thumb', 'products_images_det');
                cw_image_save($file_upload_data['products_images_thumb'], array('id' => $product_id));
            }

            if (cw_image_check_posted($file_upload_data['products_images_det']))
                cw_image_save($file_upload_data['products_images_det'], array('id' => $product_id));
        }
        else {

            if ($pdf_file_name != 'none' && $pdf_file_name != "") {
                $destination = $var_dirs['pdf'].'/'.$pdf_file_name;
                $path = cw_move_uploaded_file('pdf_file', $destination);
                if ($path)
                    $product_data['pdf_link'] = $var_dirs_web['pdf'].'/'.$pdf_file_name;
            }

            if (cw_image_check_posted($file_upload_data['products_images_thumb'])) {
                cw_image_save($file_upload_data['products_images_thumb'], array('id' => $product_id));
            }

            if (cw_image_check_posted($file_upload_data['products_images_det']))
                cw_image_save($file_upload_data['products_images_det'], array('id' => $product_id));

            if($fields['thumbnail'])
                cw_group_edit_copy($ge_id, 'products_images_thumb', 'id', $product_id);

            if($fields['product_image'])
                cw_group_edit_copy($ge_id, 'products_images_det', 'id', $product_id);
        }

		if ($config['Appearance']['categories_in_products'] == '1') {
			if ($product_info)
				$old_product_categories = cw_query_column("SELECT category_id FROM $tables[products_categories] WHERE product_id='$product_id'");

			db_query("update $tables[products_categories] set main=0 where product_id = '$product_id'");
			$query_data_cat = array(
				'category_id' => $product_data['category_id'],
				'product_id' => $product_id,
				'main' => 1,
				'orderby' => cw_query_first_cell("select orderby from $tables[products_categories] where category_id = '$product_data[category_id]' and product_id = '$product_id' and main = 1"),
			);
			cw_array2insert('products_categories', $query_data_cat, true);

			if($fields['category_id'])
				cw_group_edit_copy_category($product_id);

			if (!is_array($product_data['category_ids'])) $product_data['category_ids'] = array();
			if ($product_data['category_ids'])
			foreach ($product_data['category_ids'] as $k=>$v) {
				if (!$v) continue;
				$query_data_cat = array(
					'category_id' => $v,
					'product_id' => $product_id,
					'main' => 0,
					'orderby' => cw_query_first_cell("select orderby from $tables[products_categories] where category_id = '$product_data[category_id]' and product_id = '$product_id'"),
				);
				if (!cw_query_first_cell("select count(*) from $tables[products_categories] where category_id = '$v' AND product_id = '$product_id'"))
					cw_array2insert('products_categories', $query_data_cat);
			}
			db_query("delete from $tables[products_categories] where product_id = '$product_id' and main = 0 and category_id not in ('".implode("','", $product_data['category_ids'])."')");

			if($fields['category_ids'])
				cw_group_edit_copy_category($product_id, false);
		}

        if (empty($product_data['min_amount']))
            $product_data['min_amount'] = 1;

        cw_core_process_date_fields($product_data, null, array('' => array('shippings')));
        $product_data['shippings'] = addslashes(serialize($product_data['shippings']));
        $product_data['discount_avail'] = isset($product_data['discount_avail'])?1:0;

        $query_fields = array('product', 'product_type', 'descr', 'fulldescr', 'productcode', 'eancode', 'manufacturer_code', 'distribution', 'distribution', 'free_shipping', 'shipping_freight', 'discount_avail', 'min_amount', 'return_time', 'low_avail_limit', 'free_tax', 'features_text', 'specifications', 'pdf_link', 'shippings', 'auto_serials', 'dim_x', 'dim_y', 'dim_z', 'cost');

        $lng_data = $product_data;
        $lng_data['code'] = $edited_language;
        $lng_data['product_id'] = $product_id;
        $lng_fields = array('product_id', 'code', 'product', 'descr', 'fulldescr', 'features_text', 'specifications');
        cw_array2insert('products_lng', $lng_data, true, $lng_fields);
        if ($ge_id && $fields) {
            $group_edit_fields = array_intersect($lng_fields, array_keys($fields));
            if ($group_edit_fields)
                cw_group_edit_copy($ge_id, 'products_lng', 'product_id', $product_id, $group_edit_fields, "code='$edited_language'");
        }

        $product_data['warehouse_customer_id'] = 0;
	if (!$is_variant)
        cw_array2insert('products_warehouses_amount', $product_data, 1, array('product_id', 'avail', 'avail_ordered', 'avail_sold', 'avail_reserved', 'variant_id', 'warehouse_customer_id'));
	else cw_call('cw_warehouse_recalculate', array($product_id));

        cw_call('cw_product_update_status', array($product_id, $product_data['status']));
        if ($fields['status'])
            cw_group_edit_copy_product_status($product_data['status']);

        if ($edited_language != $config['default_admin_language'])
            cw_unset($query_fields, 'descr', 'fulldescr', 'product', 'features_text', 'specifications');
        if (!$addons['warehouse'])
            $query_fields[] = 'avail';

        if (!$is_variant) {
            $query_fields[] = 'weight';
        }

        cw_array2update('products', $product_data, "product_id = '$product_id'", $query_fields);

        if (AREA_TYPE == 'A')
            cw_insert_product_to_sections($product_id, $ins_sections);

        cw_membership_update('products', $product_id, $product_data['membership_ids'], 'product_id');
        if ($fields['membership_ids'])
            cw_group_edit_copy_memberships($product_data['membership_ids']);

        if (!empty($fields)) {
            $do_not_update = array('price', 'thumbnail', 'product_image', 'category_id', 'category_ids', 'membership_ids');
            $to_update = array_intersect($query_fields, array_keys($fields));
            $to_update = array_intersect($to_update, array_diff($to_update, $do_not_update));
            if (count($to_update))
                cw_group_edit_copy($ge_id, 'products', 'product_id', $product_id, $to_update);
        }
		if ($config['Appearance']['categories_in_products'] == '1') {
			cw_recalc_subcat_count($category_id);
			if (is_array($old_product_categories))
				$category_ids = cw_array_merge($old_product_categories, $category_ids);
			$category_ids = cw_array_merge($category_ids, $product_data['category_ids'], array($category_id));
			cw_recalc_subcat_count(cw_category_get_path($category_ids));
		}

        if ($is_new_product)
			cw_add_top_message(cw_get_langvar_by_name('msg_adm_product_add'));
        else
            cw_add_top_message(cw_get_langvar_by_name("msg_adm_product_upd"));

        if (!$is_variant) {
            //cw_product_replace_price($product_id, $product_data['price'], 0, $is_new_product, $product_data['is_manual_price']);
            if (isset($product_data['price']) && isset($product_data['list_price'])) { 
                cw_product_update_price($product_id, 0, 0, 0, 1, 1, $product_data['price'], $product_data['list_price']);
            }
        }

        if ($fields['price'] && !$is_variant) {

            if ($ge_id) {
			    while ($pid = cw_group_edit_each($ge_id, 1, $product_id)) {
			    	if ($pid != $product_id) {
			    		cw_product_replace_price($pid, $product_data['price'], 0);
			    	}
			    }
		    }
        }

        cw_func_call('cw_items_attribute_classes_save', 
            array('item_id' => $product_id, 'attribute_class_ids' => $product_data['attribute_class_ids'], 'item_type' => 'P'));
        cw_log_add('product_modify', $product_data);
# kornev, it have to be product_data here - because we change the attributes in the error_check function
        cw_call('cw_attributes_save', array('item_id' => $product_id, 'item_type' => 'P', 'attributes' => $product_data['attributes'], 'language' => $edited_language, array('update_posted_only'=>true, 'is_default' => false)));
        cw_attributes_group_update($ge_id, $product_id, 'P', $fields);

        cw_func_call('cw_product_build_flat', array('product_id' => $product_id));
        cw_group_edit_end($product_id);

        cw_product_update_system_info($product_id, array('supplier_customer_id'=>$product_data['supplier']));
        cw_group_edit_copy_system_info($product_id, array('supplier_customer_id'=>$product_data['supplier']));
        cw_warehouse_recalculate($product_id);
        cw_product_filter_recalculate_price_ranges();
        // tags
        if (!empty($product_data['tags'])) {
            $tags = explode(',', $product_data['tags']);
            cw_tags_set_product_tags($tags, $product_id);
        } else {
            cw_tags_clear_product_tags($product_id);
        }
    }
    else {
		cw_add_top_message($fillerror, 'E');

        $product_modified_data = $product_data;
        $product_modified_data['product_id'] = $product_id;
        cw_core_process_date_fields($product_modified_data, null, array('' => array('membership_ids', 'status')));

        if ($file_upload_data['products_images_thumb']) {
            $file_upload_data['products_images_thumb']['is_redirect'] = false;
            $product_modified_data['image_thumb'] = $file_upload_data['products_images_thumb'];
        }

        if ($file_upload_data['products_images_det']) {
            $file_upload_data['products_images_det']['is_redirect'] = false;
            $product_modified_data['image_det'] = $file_upload_data['products_images_det'];
        }
    }

    cw_refresh($product_id);
}

if (
    $REQUEST_METHOD == "POST"
    && ($action == "attributes_modify")
    && (AREA_TYPE == 'A' || AREA_TYPE == 'V')
) {

   $product_data['attributes'] = $attributes;
   cw_call('cw_attributes_save', array('item_id' => $product_id, 'item_type' => 'P', 'attributes' => $product_data['attributes'], 'language' => $edited_language, array('is_default' => false)));

   $top_message = array('content' => cw_get_langvar_by_name('msg_adm_product_wholesale_upd'), 'type' => 'I');

   cw_refresh($product_id, 'attributes');
}

if ($addons['magnifier'])
    cw_include('addons/magnifier/product_magnifier_modify.php');

if ($product_info['product_id'] && $addons['wholesale_trading'])
    cw_include('addons/wholesale_trading/product_wholesale.php');

$product_info['list_tags'] = cw_tags_get_string_tags($product_info['tags']);

if ($section == 'serial_numbers') {
    if (AREA_TYPE == 'A')
         $serial_numbers = cw_get_serial_numbers('', $product_id, true);
    else
        $serial_numbers = cw_get_serial_numbers($customer_id, $product_id);
     $smarty->assign('serial_numbers', array_chunk($serial_numbers, $config['sn']['serial_per_row_product']));
}

if ($addons['estore_products_review'])
    cw_include('addons/estore_products_review/product_modify.php');

$is_default_attributes = false;
if (empty($product_info)) {
    $smarty->assign('new_product', 1);
# kornev, if the product is new and the product_modified_data is empty - we should set the default values for the attributes
    if (empty($product_modified_data)) $is_default_attributes = true;
}

if (!empty($product_modified_data)) {
    # Restore saved product data
    $product_info = cw_stripslashes($product_modified_data);
    $product_info['image_det']['tmbn_url']   =  $app_web_dir."/index.php?target=image&type=products_images_det&id=".$product_id."&tmp=1&imgid=0&timestamp=".time();
    $product_info['image_thumb']['tmbn_url'] =  $app_web_dir."/index.php?target=image&type=products_images_thumb&id=".$product_id."&tmp=1&imgid=0&timestamp=".time();
    $attributes = $product_info['attributes'];

	if ($config['Appearance']['categories_in_products'] == '1') {
		if (!empty($product_info['category_ids']) && is_array($product_info['category_ids'])) {
			$product_info['add_category_ids'] = array_flip($product_info['category_ids']);
			foreach ($product_info['add_category_ids'] as $k => $v)
				$product_info['add_category_ids'][$k] = true;
		}
    }

    if ($product_modified_data['image_det'] && $file_upload_data['products_images_det'])
        $file_upload_data['products_images_det']['is_redirect'] = false;

    if ($product_modified_data['image_thumb'] && $file_upload_data['products_images_thumb'])
        $file_upload_data['products_images_thumb']['is_redirect'] = false;
}

$smarty->assign('section', $section);

$smarty->assign('query_string', urlencode($QUERY_STRING));

if ($mode == 'add' && !count($product_info['uns_shippings'])) $product_info['uns_shippings'] = unserialize($config['product']['default_product_shippings']);

$smarty->assign('product', $product_info);
$smarty->assign('product_id', $product_info['product_id']);
if ($product_id)
    $smarty->assign('product_categories', cw_query_column("select category_id from $tables[products_categories] where product_id='$product_id' and main=0"));

if (!empty($ge_id)) {
    $total_items = cw_group_edit_count($ge_id);

    $navigation = cw_core_get_navigation($target, $total_items, $page);
    $navigation['script'] = 'index.php?target='.$target.'&mode=details&product_id='.$product_id.'&ge_id='.$ge_id;
    $smarty->assign('navigation', $navigation);

    $smarty->assign('products', cw_query("SELECT $tables[products].product, $tables[products].productcode, $tables[group_editing].obj_id FROM $tables[products], $tables[group_editing] WHERE $tables[products].product_id = $tables[group_editing].obj_id AND $tables[group_editing].ge_id = '$ge_id' LIMIT $navigation[first_page], $navigation[objects_per_page]"));
    $smarty->assign('ge_id', $ge_id);
}

$product_modified_data = '';

$smarty->assign('fillerror', $fillerror);

if (!empty($category_id))
    $smarty->assign('default_category_id', intval($category_id));

$smarty->assign('product_languages', $product_languages);
$memberships = cw_user_get_memberships(array('C', 'R'));
if (!empty($memberships))
    $smarty->assign('memberships', $memberships);

$shippings = cw_query("select $tables[shipping].* from $tables[shipping], $tables[shipping_carriers] where $tables[shipping].carrier_id=$tables[shipping_carriers].carrier_id and active=1 ORDER BY orderby");
$smarty->assign('shippings', $shippings);

$check_sections = array("arrivals", "hot_deals", "clearance", "super_deals", "featured_products");
$sec = array();
foreach($check_sections as $val)
    $sec[$val] = cw_query_first("select * from ".$tables[$val]." where product_id='$product_id'".($val == 'featured_products'?" and category_id=0":""));
$smarty->assign('sec', $sec);

$location[] = array(cw_get_langvar_by_name('lbl_adm_product_management'), 'index.php?target='.$target);
if ($product_id) {
    $location[] = array(cw_get_langvar_by_name('lbl_product_modify'), 'index.php?target='.$target.'&mode=details&product_id='.$product_id);
    $location[] = array($product_info['product'], '');
}
else
    $location[] = array(cw_get_langvar_by_name('lbl_product_add'), '');

$smarty->assign('js_tab', $js_tab);

$smarty->assign('last_added_product_type', $last_added_product_type);

cw_load("serials");
if (AREA_TYPE == 'A' || AREA_TYPE == 'V')
    $serial_numbers = cw_get_serial_numbers('', $product_id, true);
else
    $serial_numbers = cw_get_serial_numbers($user_account['warehouse_customer_id'], $product_id);
//$smarty->assign("serial_numbers", array_chunk($serial_numbers, $config['sn']['serial_per_row_product']));

$attributes = cw_func_call('cw_attributes_get', array('item_id' => $product_id, 'item_type' => 'P', 'prefilled' => $attributes, 'is_default' => $is_default_attributes, /*'attribute_class_ids' => $product_info['attribute_class_ids'],*/ 'language' => $edited_language));
$smarty->assign('attributes', $attributes);

// Suppliers
$smarty->assign('suppliers', cw_user_get_suppliers());
