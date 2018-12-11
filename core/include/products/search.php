<?php
if (!$use_search_conditions) $use_search_conditions = 'general';

$save_search_id           = &cw_session_register('save_search_id', 0);
$current_loaded_search_id = &cw_session_register('current_loaded_search_id', 0);

# The list of the fields allowed for searching can be extended by addons
cw_event('on_allowable_search_fields', array(&$allowable_search_fields, $current_area));

$allowable_search_fields = cw_array_merge($allowable_search_fields, array(
	"substring",
        "tag",
	"by_title",
	"by_shortdescr",
	"by_fulldescr",
	"by_ean",
	"by_productcode",
	"category_id",
	"category_main",
	"category_extra",
	"search_in_subcategories",
	"price_min",
	"price_max",
	"avail_min",
	"avail_max",
	"weight_min",
        "weight_max",  
	"created_by",
	'avail_types')
);

if ($current_area != 'C') 	$allowable_search_fields[] = 'status';

if ($REQUEST_METHOD == 'GET' && $mode == 'search') {
	# Check the variables passed from GET-request
	$get_vars = array();
	foreach ($_GET as $k=>$v) {
		if (in_array($k, $allowable_search_fields))
			$get_vars[$k] = $v;
	}

    if ($new_search) $search_data['products'][$use_search_conditions] = array();

	# Prepare the search data
	if (!empty($get_vars)) {
		$search_data['products'][$use_search_conditions] = cw_array_merge($search_data['products'][$use_search_conditions], $get_vars);
		$search_data['products'][$use_search_conditions]['flat_search'] = 1;
	}
	
	unset($get_vars);
}

if (!empty($use_search_conditions) && is_array($search_data['products'])) 
    $search_data['products'][$use_search_conditions]["substring"] = html_entity_decode(urldecode($search_data['products'][$use_search_conditions]["substring"]));

$sort_fields = cw_call('cw_product_get_sort_fields');
$smarty->assign('sort_fields', $sort_fields);

if ($config['Appearance']['display_productcode_in_list'] != "Y" && ($current_area == 'C' || $current_area == 'B'))
    unset($sort_fields['productcode']);

if($current_area == 'A' || $current_area == 'P')
    $sort_fields['quantity'] = cw_get_langvar_by_name("lbl_in_stock");

    
if ($action == 'save_search' && !empty($save_search_name) && is_array($posted_data)) {
    if ($save_search_restore) {
        cw_array2update('saved_search', array('name'=>addslashes($save_search_name), 'type'=>'P', 'sql_query'=>'', 'params'=>serialize($search_data['products'][$use_search_conditions])), "ss_id = '$save_search_restore'");
        $save_search_id = $save_search_restore;
        $current_loaded_search_id = $save_search_restore; 
    } else { 
        $save_search_id = cw_array2insert('saved_search', array('name'=>addslashes($save_search_name), 'type'=>'P', 'sql_query'=>'', 'params'=>serialize($search_data['products'][$use_search_conditions])));
    }
    cw_add_top_message("Saved search '$save_search_name'", 'I');      
    $action = 'search';
}


if ($action == 'reset') {
    $search_data['products'][$use_search_conditions] = array();
    cw_header_location("index.php?target=$target&mode=search".($link?"&$link":''));
}
elseif ($action == 'search' && is_array($posted_data)) {
    $date_fields = array (
        '' =>array('sold_date_start' => 0, 'sold_date_end' => 1, 'creation_date_start' => 0, 'creation_date_end' => 1, 'modify_date_start' => 0, 'modify_date_end' => 1),
    );

    $multiple_fields = array(
        '' => array('categories', 'avail_types', 'product_types', 'warehouse_customer_id'),
    );
    cw_core_process_date_fields($posted_data, $date_fields, $multiple_fields);

    $current_loaded_search_id = 0;

    if (!empty($posted_data)) {
        $search_data['products'][$use_search_conditions] = $posted_data;
        $search_data['products'][$use_search_conditions]['js_tab'] = $js_tab;
    }

    $search_data['products'][$use_search_conditions]['search_sections'] = $search_sections;
    cw_header_location("index.php?target=$target&mode=search&page=1".($link?"&$link":''));
}
elseif ($action == 'save_search_load') {
    if (!empty($save_search_restore)) {
        $saved_search_data = cw_query_first("select * from $tables[saved_search] where ss_id='$save_search_restore' and type='P'");
        if (!empty($saved_search_data)) {
            if (!empty($saved_search_data['params'])) {
                $search_data['products'][$use_search_conditions] = unserialize($saved_search_data['params']);  
                $current_loaded_search_id = $save_search_restore;
                cw_add_top_message("Loaded '$saved_search_data[name]'", 'I');
            }   
        }
    } else {
        $current_loaded_search_id = 0;
        $save_search_id = 0;
        $search_data['products'][$use_search_conditions] = 0;
    }
    cw_header_location("index.php?target=$target&mode=search");
} elseif ($action == 'delete_search_load') {
    if ($current_loaded_search_id)  {
        db_query("delete from $tables[saved_search] where ss_id = '$current_loaded_search_id'"); 
        $current_loaded_search_id = 0;
        $save_search_id = 0;
        $search_data['products'][$use_search_conditions] = 0;
    }
    cw_header_location("index.php?target=$target&mode=search");
}

if (empty($search_data['products'][$use_search_conditions])) {
    $search_data['products'][$use_search_conditions] = array(
        'search_sections' => array('tab_basic_search' => 1),
    );
}
if ($search_data['products'][$use_search_conditions]['flat']) {
    $search_data['products'][$use_search_conditions]['search_sections'] = array(
        'tab_basic_search' => 1, 'tab_add_search' => 1,
    );
}

if ($mode == 'search') {
   
    if ($current_area == 'C' && strpos($search_data['products'][$use_search_conditions]["substring"], 'search/') !== false) {
        $substring_redirect = $search_data['products'][$use_search_conditions]["substring"];
        $search_data['products'][$use_search_conditions]["substring"] = '';
        cw_header_location($current_location.'/'.$substring_redirect);
    }
 
    global $product_filter;

    if (empty($sort) && $current_area == 'A') {
        $sort = 'orderby';
        $sort_direction = 0;
    }

    if (!empty($sort) && isset($sort_fields[$sort]))
        $search_data['products'][$use_search_conditions]['sort_field'] = $sort;

    if (isset($sort_direction))
        $search_data['products'][$use_search_conditions]['sort_direction'] = $sort_direction;

    if ($current_area == 'C' && !empty($config['Appearance']['products_order']) && empty($search_data['products'][$use_search_conditions]['sort_field'])) {
        $search_data['products'][$use_search_conditions]['sort_field'] = $config['Appearance']['products_order'];
        $search_data['products'][$use_search_conditions]['sort_direction'] = 0;
    }

    if (!empty($page) && $search_data['products'][$use_search_conditions]['page'] != intval($page))
        $search_data['products'][$use_search_conditions]['page'] = $page;

    $info_type = isset($search_data['products'][$use_search_conditions]['info_type'])?$search_data['products'][$use_search_conditions]['info_type']:8+32+128;
    list($products, $navigation, $product_filter) = cw_func_call('cw_product_search', array('data' => $search_data['products'][$use_search_conditions], 'user_account' => $user_account, 'current_area' => $current_area, 'info_type' => $info_type));

    if ($save_search_id > 0) {
        global $products_search_query;
        cw_array2update('saved_search', array("sql_query"=>addslashes($products_search_query)), "ss_id = '$save_search_id'");
        $save_search_id = 0;
    }      

    if ((!defined('IS_AJAX') || !constant('IS_AJAX')) && 
        $current_area == 'C' && 
        $config['search']['single_search_res'] == 'Y' && 
        count($products)==1 && 
        $page<2 &&
        !defined('IS_ROBOT')
        ) {
        $product_url_param['var'] = 'product';
	$first_product = reset($products);
        $product_url_param['product_id'] = $first_product['product_id'];
        $product_url = html_entity_decode(cw_call('cw_core_get_html_page_url', array($product_url_param)));
        cw_header_location($product_url);
    }

    // alexv - #30133 2nd+ product list page in pop up bug
    $search_target = isset($target) ? $target : 'search';

    $page_get_params =          array(
                                    'var'               => $search_target,
                                    'mode'              => 'search',
                                    'delimiter'         => '&',
                                );
    if (isset($field_product)) {
        $page_get_params['field_product']       = $field_product;
        $page_get_params['field_product_id']    = $field_product_id;
        $page_get_params['field_amount']        = $field_amount;

    }


    $navigation['script_raw'] = cw_call(
	    						'cw_core_get_html_page_url', 
	    						array(array_merge($page_get_params, array(
	    							'att' 				=> $search_data['products'][$use_search_conditions]['attributes'],
	    						)))
                                );
//    $navigation['script'] = cw_call('cw_product_get_filter_replaced_url', array('product_filter'=>$product_filter, 'ns'=>$navigation['script_raw']));
    $navigation['script'] = cw_call('cw_clean_url_get_seo_url', array($navigation['script_raw']));

    if ($current_area == 'A') {
    	$navigation['script'] = str_replace('index.php', 'admin/index.php', $navigation['script']);
    }
    // alexv - #30133 2nd+ product list page in pop up bug

    $smarty->assign('navigation', $navigation);
    $smarty->assign('products', $products);
    $smarty->assign('product_filter', $product_filter);

	if ($config['product']['pf_is_ajax'] == 'Y' && $ajax_filter) {
		$ns = cw_call(
			'cw_core_get_html_page_url',
            array($page_get_params)
		);
		$url = cw_product_get_filter_replaced_url($product_filter, $ns);
		$smarty->assign('replaced_url', $url);
     $smarty->assign('ajax_filter', $ajax_filter);
	}

    // turn off infinite scroll if products more than max_count_view_products
	if (
		!empty($app_config_file['interface']['max_count_view_products']) 
		&& $navigation['total_items'] >= $app_config_file['interface']['max_count_view_products']
	) {
		$smarty->assign("infinite_scroll_manual_off", "Y");
	}
}

$noindex = !empty($sort) || empty($att);
$smarty->assign('noindex', $noindex);

$smarty->assign('saved_searches', cw_query("select name, ss_id from $tables[saved_search] where type='P' order by name, ss_id"));
$smarty->assign('current_loaded_search_id', $current_loaded_search_id);
$smarty->assign('current_loaded_search_name', cw_query_first_cell("select name from $tables[saved_search] where type='P' and ss_id='$current_loaded_search_id'"));

$search_data['products'][$use_search_conditions]["substring"] = htmlentities(stripslashes($search_data['products'][$use_search_conditions]["substring"]));
$smarty->assign('js_tab', $search_data['products'][$use_search_conditions]['js_tab']);
$smarty->assign('search_prefilled', cw_stripslashes($search_data['products'][$use_search_conditions]));
$smarty->assign('warehouses', cw_get_warehouses());
$smarty->assign('mode', $mode);
