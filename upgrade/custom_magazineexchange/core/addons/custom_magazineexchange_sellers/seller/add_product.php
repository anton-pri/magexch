<?php
namespace cw\custom_magazineexchange_sellers;
cw_load('category', 'image', 'product', 'warehouse', 'user', 'serials', 'group_edit', 'ean', 'xls', 'attributes', 'tags', 'mail');


function cw_seller_product_refresh($product_id, $added = '') {
    global $target;

    if ($product_id)
        cw_header_location("index.php?target=$target&product_id=".$product_id.$added);
    else
        cw_header_location("index.php?target=$target");
}

global $product_info, $current_area;

$seller_product_run_preview = &cw_session_register('seller_product_run_preview', '');

if ($product_id) {
    $_current_area = $current_area;
    $current_area = 'A';
    $product_info = cw_func_call('cw_product_get', array('id' => $product_id, 'user_account' => $user_account, 'info_type' => 65535, 'lang' => $edited_language, 'for_product_modify' => true));
    $current_area = $_current_area;
    //print_r([$current_area, $user_account, $product_info]);
    if (!$product_info) {
        $top_message = array('content' => cw_get_langvar_by_name('lbl_products_deleted'), 'type' => 'E');
        cw_header_location('index.php?target='.$target);
    }
}

if ($REQUEST_METHOD == "POST") {
    //print('<pre>');
    //print_r([$_POST/*, $request_prepared*/]);
    //print('</pre>');

    $file_upload_data = &cw_session_register('file_upload_data');
    cw_image_clear(array('products_images_det', 'products_images_thumb', 'products_detailed_images'));

    if ($mode == "add" || $mode == "update") {
        
        $product_data = array_merge($request_prepared['product_data'], 
            [
                'product_type' => 1,
                'avail' => 9999,
                'price' => 9999.99,
                'list_price' => 9999.99,
                'status' => ($action == 'publish') ? 1 : ($product_id ? $product_info['status'] : 0),
                'membership_ids' => [0],
                'min_amount' => 1,
                'discount_avail' => 0,
                'supplier' => $customer_id,
                'category_id' => end(explode('|', $product_data['category_id'])),
            ]
        );

        //print('<pre>');
        //print_r(['PD'=>$product_data]);
        //print('</pre>');
        
        if (!$product_data['productcode'])
            $product_data['productcode'] = cw_product_generate_sku();

        if(!$product_data['membership_ids'])
            $product_data['membership_ids'] = array_keys(unserialize($config['product']['default_product_memberships']));

        if (!$product_id) {
            $is_new_product = true;
    
            $product_data['product_id'] = 
                $product_id = 
                cw_array2insert('products', 
                    array('productcode' => $product_data['productcode'], 'product_type' => $product_data['product_type'])
                );
        }        

        if (cw_image_check_posted($file_upload_data['products_images_thumb'])) {
            //if (!$file_upload_data['products_images_det'])
            //    cw_image_copy($file_upload_data, 'products_images_thumb', 'products_images_det');
            cw_image_save($file_upload_data['products_images_thumb'], array('id' => $product_id));
        }
    
        if (cw_image_check_posted($file_upload_data['products_images_det']))
            cw_image_save($file_upload_data['products_images_det'], array('id' => $product_id));    
        

        if (is_array($file_upload_data['products_detailed_images'])) {
            foreach($file_upload_data['products_detailed_images'] as $image) {
                $image_posted = cw_image_check_posted($image);
                if ($image_posted) {
                    $image_id = cw_image_save($image, array('alt' => $alt, 'id' => $product_id));
                }
            }
        } 


        if ($config['Appearance']['categories_in_products'] == '1') {
			if ($product_info)
				$old_product_categories = cw_query_column("SELECT category_id FROM $tables[products_categories] WHERE product_id='$product_id'");

            db_query("update $tables[products_categories] set main=0 where product_id = '$product_id'");
            
			$query_data_cat = array(
				'category_id' => $product_data['category_id'],
				'product_id' => $product_id,
				'main' => 1,
                'orderby' => 
                    cw_query_first_cell(
                        "select orderby from $tables[products_categories] where category_id = '$product_data[category_id]' and product_id = '$product_id' and main = 1"
                    ),
			);
			cw_array2insert('products_categories', $query_data_cat, true);

            if (!is_array($product_data['category_ids'])) $product_data['category_ids'] = array();
            
			if ($product_data['category_ids']) {
                foreach ($product_data['category_ids'] as $k=>$v) {
                    if (!$v) continue;
                    $query_data_cat = array(
                        'category_id' => $v,
                        'product_id' => $product_id,
                        'main' => 0,
                        'orderby' => 
                            cw_query_first_cell(
                                "select orderby from $tables[products_categories] where category_id = '$product_data[category_id]' and product_id = '$product_id'"
                            ),
                    );
                    if (!cw_query_first_cell("select count(*) from $tables[products_categories] where category_id = '$v' AND product_id = '$product_id'"))
                        cw_array2insert('products_categories', $query_data_cat);
                }
            }
			db_query("delete from $tables[products_categories] where product_id = '$product_id' and main = 0 and category_id not in ('".implode("','", $product_data['category_ids'])."')");
        }

        $query_fields = [
            'product', 
            /*
            'product_type', 
            */
            'descr', 
            'fulldescr', 
            'productcode', 
            /*
            'eancode', 
            'manufacturer_code', 
            'distribution', 
            'free_shipping', 
            'shipping_freight',
            */
            'discount_avail', 
            'min_amount', 
            /*
            'return_time', 
            'low_avail_limit', 
            'free_tax', 
            'features_text', 
            'specifications', 
            'pdf_link', 
            'shippings', 
            'auto_serials', 
            'dim_x', 
            'dim_y', 
            'dim_z', 
            'cost'
            */
        ];

        $lng_data = $product_data;
        $lng_data['code'] = $edited_language;
        $lng_data['product_id'] = $product_id;
        $lng_fields = array('product_id', 'code', 'product', 'descr', 'fulldescr', 'features_text', 'specifications');
        cw_array2insert('products_lng', $lng_data, true, $lng_fields);

        $product_data['warehouse_customer_id'] = 0;
        cw_array2insert(
            'products_warehouses_amount', 
            $product_data, 
            1, 
            array('product_id', 'avail', 'avail_ordered', 'avail_sold', 'avail_reserved', 'variant_id', 'warehouse_customer_id')
        );
        cw_call('cw_product_update_status', array($product_id, $product_data['status']));

        //$query_fields[] = 'avail';
        $query_fields[] = 'weight';

        cw_array2update('products', $product_data, "product_id = '$product_id'", $query_fields);

        cw_membership_update('products', $product_id, $product_data['membership_ids'], 'product_id');

        cw_call('cw_attributes_save', 
            array(
                'item_id' => $product_id, 
                'item_type' => 'P', 
                'attributes' => $product_data['attributes'], 
                'language' => $edited_language, 
                array('update_posted_only'=>true, 'is_default' => false)
                )
            );

        cw_func_call('cw_product_build_flat', array('product_id' => $product_id));
        cw_product_update_system_info($product_id, array('supplier_customer_id'=>$product_data['supplier']));

        if (isset($product_data['price']) && isset($product_data['list_price'])) { 
            cw_product_update_price($product_id, 0, 0, 0, 1, 1, $product_data['price'], $product_data['list_price']);
        }

        if ($is_new_product) {
            if ($product_data['quick_listing']['quantity'] || $product_data['quick_listing']['price']) {
                $data = array(
                    'product_id'    => intval($product_id),
                    'seller_id'     => $product_data['supplier'],
                    'comments'      => cw_strip_tags($product_data['quick_listing']['comments']),
                    'price'         => floatval($product_data['quick_listing']['price']),
                    'quantity'      => intval($product_data['quick_listing']['quantity']),
                    'condition'     => intval($product_data['quick_listing']['condition']),
                    'is_digital'    => 0
                );
                cw_array2insert('magazine_sellers_product_data', $data, true);
            }
        }

        if ($is_new_product)
            cw_add_top_message(cw_get_langvar_by_name('msg_seller_product_add'));
        else
            cw_add_top_message(cw_get_langvar_by_name("msg_seller_product_upd".($action == 'publish'?'_published':'')));

        $file_upload_data = [];
        if ($action == "preview") {
            $seller_product_run_preview = 'Y';
        }
        
        cw_session_save();

        $smarty->assign('product_id', $product_id);
        $smarty->assign('product_data', $product_data);
        $smarty->assign('is_new_product', $is_new_product);
        $smarty->assign('seller_info', $user_account);
        cw_call(
            'cw_send_mail', 
            array(
                $config['Company']['site_administrator'], 
                $config['Company']['site_administrator'], 
                "addons/custom_magazineexchange_sellers/mail/seller/modified_product_subj.tpl", 
                "addons/custom_magazineexchange_sellers/mail/seller/modified_product.tpl",
                $config['default_admin_language']
            )
        );

        cw_seller_product_refresh($product_id);
    }

    //print_r(compact('product_data', 'product_id'));
    //die;
}

$smarty->assign('current_main_dir',     'addons/' . addon_name);
$smarty->assign('current_section_dir',  'seller');
$smarty->assign('main',                 $target);
$smarty->assign('seller_product_run_preview', $seller_product_run_preview);
$seller_product_run_preview = 'N';

/*
print('<pre>');
print_r([$product_id, $product_info]); 
print('</pre>');
die;
*/
if ($product_info['category_id']) {
    $safety_cnt = 15;
    $parent_cats = [$product_info['category_id']];
    $category_path = [];
    $category_id = $product_info['category_id'];
    do {
        $category_data = 
            cw_query_first("SELECT parent_id, category FROM $tables[categories] WHERE category_id = $category_id");
        
        $category_id = $category_data['parent_id'];    
            
        if ($category_id && $category_id!=282) {    
            $parent_cats[] = $category_id;
        }    

        if ($category_id)
            $category_path[] = $category_data['category'];

        $safety_cnt--;    
    } while ($category_id > 0 && $safety_cnt>0);

    $product_info['category_id'] = implode('|', array_reverse($parent_cats));
    $product_info['category_path'] = implode("&nbsp;<span>></span>&nbsp;", array_reverse($category_path));
}


$smarty->assign('product', $product_info);
$smarty->assign('product_id', $product_info['product_id']);
$attributes = cw_func_call('cw_attributes_get', array('item_id' => $product_id, 'item_type' => 'P', 'prefilled' => $attributes, 'is_default' => $is_default_attributes, /*'attribute_class_ids' => $product_info['attribute_class_ids'],*/ 'language' => $edited_language));
$smarty->assign('attributes', $attributes);

//print_r($attributes); die;
