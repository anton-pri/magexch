<?php
// TODO: non optimal link thru doc_info_id - it is primary key in two tables docs_user_info and docs_info. The same link can be arranged via doc_id or by joining all three tables

global $orders;

cw_load('map', 'warehouse', 'product', 'accounting', 'taxes', 'doc');

$search_data = &cw_session_register('search_data', array());

$save_search_id           = &cw_session_register('save_search_id', 0);
$current_loaded_search_id = &cw_session_register('current_loaded_search_id', 0);

if (in_array($action,array('feedback_display', 'feedback_save')) && ($customer_id == $order_customer_id || $current_area == 'A')) {
    if ($action == 'feedback_display') {

        global $smarty;

        cw_load('ajax','product');

        $seller_feedback = cw_query_first("SELECT * FROM $tables[magexch_sellers_feedback] WHERE seller_id = '$order_seller_id' AND customer_id = '$order_customer_id' AND doc_id='$feedback_order_id'");

        $smarty->assign('seller_feedback', $seller_feedback);
        $smarty->assign('order_seller_id', $order_seller_id); 
        $smarty->assign('feedback_order_id', $feedback_order_id);
        $smarty->assign('order', cw_call('cw_doc_get', array($feedback_order_id)));  
        cw_add_ajax_block(array(
            'id' => 'seller_feedback_popup',
            'action' => 'update', 
            'template' => 'addons/custom_magazineexchange/seller_feedback.tpl',
        ),'seller_feedback_popup');

        cw_add_ajax_block(array(   
            'id' => 'script',
            'content' => 'sm("seller_feedback_popup",550, 0, true, "My Feedback left for Sellers")',
        ),'seller_feedback_popup_script');
    }
}

if (in_array($action, array('update', 'delete', 'mass_update')) || in_array($mode, array('delete', 'delete_all')) )
    cw_include('include/orders/process.php');

if ($action == 'print' && is_array($doc_ids)) {
    global $doc_id;
    $doc_id = implode(',', array_keys($doc_ids));
    cw_include('include/orders/order.php');
}

if ($action == 'reset') {
    $search_data['orders'] = array();
    cw_header_location("index.php?target=$target&mode=search");
} elseif ($action == 'save_search_load') {
    if (!empty($save_search_restore)) {
        $saved_search_data = cw_query_first("select * from $tables[saved_search] where ss_id='$save_search_restore' and type='O'");
        if (!empty($saved_search_data)) {
            if (!empty($saved_search_data['params'])) {
                $search_data['orders'][$docs_type] = unserialize($saved_search_data['params']);  
                $current_loaded_search_id = $save_search_restore;
                cw_add_top_message("Loaded '$saved_search_data[name]'", 'I');
            }   
        }
    } else {
        $current_loaded_search_id = 0;
        $save_search_id = 0;
        $search_data['orders'][$docs_type] = 0;
    }
    cw_header_location("index.php?target=$target&mode=search");
} elseif ($action == 'delete_search_load') {
    if ($current_loaded_search_id)  {
        db_query("delete from $tables[saved_search] where ss_id = '$current_loaded_search_id'"); 
        $current_loaded_search_id = 0;
        $save_search_id = 0;
        $search_data['orders'][$docs_type] = 0;
    }
    cw_header_location("index.php?target=$target&mode=search");
}

/**
 * Extract search criteria from GET request
 */

// The list of the fields allowed for searching can be extended by addons
$allowable_search_fields = array();
cw_event('on_allowable_order_search_fields', array(&$allowable_search_fields, $current_area));

$allowable_search_fields = cw_array_merge($allowable_search_fields, array(
    'status','created')
);
if ($REQUEST_METHOD == 'GET' && $mode == 'search') {

    # Check the variables passed from GET-request
    $get_vars = array();
    if (isset($_GET['data']) && is_array($_GET['data']))
    foreach ($_GET['data'] as $k=>$v) {
        if (in_array($k, $allowable_search_fields))
            $get_vars['basic'][$k] = $v;
    }

    # Prepare the search data
    if (!empty($get_vars)) {
        $search_data['orders'][$docs_type] = cw_array_merge($search_data['orders'][$docs_type], $get_vars);
        $search_data['orders'][$docs_type]['search_sections']['tab_search_orders'] = 1;
    }

    unset($get_vars);
}

/**
 * POSTed search data has full priority over GET
 */
if ($REQUEST_METHOD == "POST" && is_array($posted_data)) {

    $date_fields = array (
        'basic' => array('creation_date_start' => 0, 'creation_date_end' => 1)
    );

    cw_core_process_date_fields($posted_data, $date_fields);
    $posted_data['js_tab'] = $js_tab;
    $search_data['orders'][$docs_type] = $posted_data;
    $search_data['orders'][$docs_type]['search_sections'] = $search_sections;

    $current_loaded_search_id = 0;

    if ($action == 'save_search' && !empty($save_search_name)) {
        if ($save_search_restore) {
            cw_array2update('saved_search', array('name'=>addslashes($save_search_name), 'type'=>'O', 'sql_query'=>$sql_query4search, 'params'=>serialize($search_data['orders'][$docs_type])), "ss_id = '$save_search_restore'");
            $save_search_id = $save_search_restore;
            $current_loaded_search_id = $save_search_restore; 
        } else { 
            $save_search_id = cw_array2insert('saved_search', array('name'=>addslashes($save_search_name), 'type'=>'O', 'sql_query'=>$sql_query4search, 'params'=>serialize($search_data['orders'][$docs_type])));
        }
        cw_add_top_message("Saved search '$save_search_name'", 'I');      
    }

	cw_header_location("index.php?target=$target&mode=search");
}

if (empty($search_data['orders'][$docs_type])) {
    $search_data['orders'] = array();
    $date = getdate(cw_core_get_time());
    $search_data['orders'][$docs_type] = array(
        'basic' => array(
            'creation_date_start' => mktime(0, 0, 0, $date['mon'], 1, $date['year']),
            'creation_date_end' => mktime(23, 59, 59, $date['mon'], $date['mday'], $date['year']),
        ),
        'search_sections' => array('tab_search_orders' => 1),
    );
}

if (empty($search_data['orders'][$docs_type]['sort_field'])) {
    $search_data['orders'][$docs_type]['sort_field'] = 'display_doc_id';
    $search_data['orders'][$docs_type]['sort_direction'] = 1;
}

if ($sort && in_array($sort, array('doc_id', 'status', 'customer', 'warehouse', 'date', 'total', 'display_doc_id')))
    $search_data['orders'][$docs_type]['sort_field'] = $sort;

if (isset($sort_direction))
    $search_data['orders'][$docs_type]['sort_direction'] = $sort_direction;//abs(intval($search_data['orders'][$docs_type]['sort_direction']) - 1);

if (!empty($page))
    $search_data['orders'][$docs_type]['page'] = intval($page);

if ($mode == 'search') {
    $fields = array();
    $from_tbls = array();
    $query_joins = array();
    $where = array();
    $groupbys = array();
    $having = array();
    $orderbys = array();

    $data = $search_data['orders'][$docs_type];

    $fields[] = "$tables[docs].doc_id";
    $fields[] = "$tables[docs].display_id";
    $fields[] = "$tables[docs].status";
    $fields[] = "$tables[docs].date";
    $fields[] = "$tables[docs_info].warehouse_customer_id";
    $fields[] = "$tables[docs_info].customer_notes";
    $fields[] = "$tables[docs_info].notes";
    $fields[] = "$tables[docs_user_info].customer_id";
    $fields[] = "$tables[docs_user_info].usertype";
    $fields[] = "$tables[docs_info].total";
    $fields[] = "wd.title as warehouse_title";

    $from_tbls[] = 'docs';

    $where[] = "$tables[docs].type = '$docs_type'";

    $query_joins['docs_info'] = array(
        'on' => "$tables[docs_info].doc_info_id = $tables[docs].doc_info_id",
        'is_inner' => 1,
    );
    $query_joins['docs_user_info'] = array(
        'on' => "$tables[docs_user_info].doc_info_id = $tables[docs].doc_info_id",
        'is_inner' => 1,
    );

    $query_joins['ca'] = array(
        'tblname' => 'customers_addresses',
        'on' => "ca.address_id = $tables[docs_user_info].main_address_id",
    );

    $query_joins['wd'] = array(
        'tblname' => 'warehouse_divisions',
        'on' => "wd.division_id = $tables[docs_info].warehouse_customer_id",
    );

    $query_joins['customers'] = array(
        'on' => "$tables[customers].customer_id = $tables[docs_user_info].customer_id",
    );


    if ($data['search_sections']['tab_search_orders']) {

        if($data['basic']['created']=='today'){
            $where[] = "$tables[docs].date >= '".strtotime(date("d-m-Y"))."'";

        } elseif($data['basic']['created']=='this_week'){
            $where[] = "$tables[docs].date >= '".strtotime('monday this week')."'";

        } elseif($data['basic']['created']=='this_month'){
            $where[] = "$tables[docs].date >= '".strtotime('01-'.date("m-Y"))."'";

        } elseif($data['basic']['created']=='selected'){
            if (!empty($data['basic']['creation_date_start']))
                $where[] = "$tables[docs].date >= '".$data['basic']['creation_date_start']."'";

            if (!empty($data['basic']['creation_date_end']))
                $where[] = "$tables[docs].date <= '".$data['basic']['creation_date_end']."'";

        } elseif($data['basic']['created']=='all_time'){
            $where[] = "$tables[docs].date >= 0";            
        }

        if (!empty($data['basic']['status'])) {
            if (is_array($data['basic']['status'])) 
                $where[] = "$tables[docs].status in ('".implode("','", $data['basic']['status'])."')";
            else
                $where[] = "$tables[docs].status = '".$data['basic']['status']."'";
        }

        if (!empty($data['basic']['doc_id_start']))
            $where[] = "$tables[docs].display_doc_id >= '".$data['basic']['doc_id_start']."'";

        if (!empty($data['basic']['doc_id_end']))
            $where[] = "$tables[docs].display_doc_id <= '".$data['basic']['doc_id_end']."'";
    }

    if ($data['search_sections']['tab_search_orders_advanced']) {

        if (!empty($data['advanced']['total_start']))
            $where[] = "$tables[docs_info].total >= '".$data['advanced']['total_start']."'";

        if (!empty($data['advanced']['total_end']))
            $where[] = "$tables[docs_info].total <= '".$data['advanced']['total_end']."'";

        if (!empty($data['advanced']['payment_id']))
            $where[] = "$tables[docs_info].payment_id IN ('".implode("', '", $data['advanced']['payment_id'])."')";

        if (!empty($data['advanced']['shipping_id']))
            $where[] = "$tables[docs_info].shipping_id IN ('".implode("', '", $data['advanced']['shipping_id'])."')";

		// Date format conversion for date_create_from_format function.-- e.g., d/m/Y instead of %d/%m/%Y
		$date_format=str_replace("%","",$config['Appearance']['date_format']);

        if (!empty($data['advanced']['expire_date_start'])) {
		    // Fix: in table docs_quotes the date is represented in Unix Format
		    $expire_date_start_unix = date_format(date_create_from_format($date_format,$data['advanced']['expire_date_start']),"U");
	        $where[] = "$tables[docs_quotes].exp_date >= $expire_date_start_unix";
		    // Fix: when we feed main/select/date.tpl with date in dd/mm/yyyy format, the trigger effect arises
		    // (dd/mm/yyyy -> mm/dd/yyyy) To avoid this we're gonna feed it with date in Unix format. That works correctly
		    $search_data['orders'][$docs_type]['advanced']['expire_date_start']=$expire_date_start_unix;
		}

        if (!empty($data['advanced']['expire_date_end'])) {
		    // Fix: See previous section
		    $expire_date_end_unix=date_format(date_create_from_format($date_format,$data['advanced']['expire_date_end']),"U");
	        $where[] = "$tables[docs_quotes].exp_date <= $expire_date_end_unix";
		    $search_data['orders'][$docs_type]['advanced']['expire_date_end']=$expire_date_end_unix;
		}

        if (!empty($data['advanced']['expire_date_start']) || !empty($data['advanced']['expire_date_end'])) {
            $query_joins['docs_quotes'] = array(
                'on' => "$tables[docs_quotes].doc_id=$tables[docs].doc_id",
            );
        }

        if (!empty($data['products']['product']))
            $where[] = "$tables[docs_items].product like '%".$data['products']['product']."%'";

        if (!empty($data['products']['product_code']))
            $where[] = "$tables[docs_items].productcode like '%".$data['products']['product_code']."%'";

        if (!empty($data['products']['product_id']))
            $where[] = "$tables[docs_items].product_id = '".$data['products']['product_id']."'";

        if (!empty($data['products']['price_start']))
            $where[] = "$tables[docs_items].price >= '".$data['products']['price_start']."'";

        if (!empty($data['products']['price_end']))
            $where[] = "$tables[docs_items].price <= '".$data['products']['price_end']."'";

        $query_joins['docs_items'] = array(
            'on' => "$tables[docs_items].doc_id=$tables[docs].doc_id",
        );

        $customer_condition = array();

        if (!empty($data['customer']['by_customer_id']))
            //$customer_condition[] = "$tables[customers].customer_id = '".$data['customer']['substring']."'";
            $customer_condition[] = 
                "$tables[customers].customer_id IN ('".implode("','",cw_doc_get_linked_customers_list($data['customer']['substring']))."')";

        if (!empty($data['customer']['by_firstname']))
            $customer_condition[] = "ca.firstname LIKE '%".$data['customer']['substring']."%'";

        if (!empty($data['customer']['by_lastname']))
            $customer_condition[] = "ca.lastname LIKE '%".$data['customer']['substring']."%'";

        if (preg_match("/^(.+)(\s+)(.+)$/", $data['customer']['substring'], $found) && !empty($data['customer']['by_firstname']) && !empty($data['customer']['by_lastname']))
            $customer_condition[] = "ca.firstname LIKE '%".$found[1]."%' AND ca.lastname LIKE '%".$found[3]."%'";

        if (!empty($data['customer']['by_email']))
            $customer_condition[] = "$tables[customers].email LIKE '%".$data['customer']['substring']."%'";

        if ($customer_condition)
            $where[] = "(".implode(" OR ", $customer_condition).")";

        $customer_condition = array();
        if (!empty($data['customer']['city']))
            $customer_condition[] = "caa.city like '%".$data['customer']['city']."%'";

        if (!empty($data['customer']['state']))
            $customer_condition[] = "caa.state='".$data['customer']['state']."'";

        if (!empty($data['customer']['country']))
            $customer_condition[] = "caa.country='".$data['customer']['country']."'";

        if (!empty($data['customer']['zipcode']))
            $customer_condition[] = "caa.zipcode LIKE '%".$data['customer']['zipcode']."%'";

        if ($customer_condition) {
            $query_joins['caa'] = array(
                'tblname' => 'customers_addresses',
            );

            if($data['customer']['type'] == '1')
                $query_joins['caa']['on'] = "caa.address_id = $tables[docs_user_info].main_address_id";
            elseif($data['customer']['type'] == '2')
                $query_joins['caa']['on'] = "caa.address_id = $tables[docs_user_info].current_address_id";
            else
                $query_joins['caa']['on'] = "(caa.address_id = $tables[docs_user_info].main_address_id or caa.address_id = $tables[docs_user_info].current_address_id)";

            $where[] = "(".implode(" AND ", $customer_condition).")";
        }
    }
    elseif($shop_company)
        $where[] = "$tables[docs_info].company_id = '".$shop_company."'";

    if ($data['basic']['salesman_customer_id'])
        $where[] = "$tables[docs_info].salesman_customer_id = '".$data['basic']['salesman_customer_id']."'";

    if (!empty($data['basic']['customer_id']))
        $where[] = "$tables[docs_user_info].customer_id IN ('".implode("','",cw_doc_get_linked_customers_list($data['basic']['customer_id']))."')";

    if ($data['warehouse_area']) {
        $where[] = "($tables[docs_info].warehouse_customer_id = '".$data['warehouse_area']."' or $tables[docs_user_info].customer_id = '".$data['warehouse_area']."')";
    }

    $orders_domains_attribute_id = cw_query_first_cell("select attribute_id from $tables[attributes] where addon='multi_domains' and item_type='O'");
    if (!empty($orders_domains_attribute_id)) {
        $query_joins['domain_attr_val'] = array(
            'tblname' => 'attributes_values',
            'on' => "domain_attr_val.attribute_id='$orders_domains_attribute_id' and domain_attr_val.item_type='O' and domain_attr_val.item_id=$tables[docs].doc_id"
        );
        $fields[] = "domain_attr_val.value as domain_id";
    }

    $direction = ($data["sort_direction"] ? "DESC" : "ASC");
    switch ($data['sort_field']) {
        case 'customer':
            $orderbys[] = "ca.firstname $direction, ca.lastname $direction";
            break;
        case 'warehouse':
            $orderbys[] = "warehouse_title $direction";
            break;
        case 'total':
            $orderbys[] = "$tables[docs_info].total $direction";
            break;
        default:
			$orderbys[] = "$tables[docs].$data[sort_field] $direction";
    }

    $groupbys[] = "$tables[docs].doc_id";

    cw_event('on_prepare_search_orders', array($data, $docs_type, &$fields, &$query_joins, &$where, &$groupbys, &$having, &$orderbys));

# kornev, we don't need to sort the result in counter - because it's faster to do it this way
    $count_query = cw_db_generate_query(array('count(*)'), $from_tbls, $query_joins, $where, $groupbys, $having, null);

    $_res = db_query($count_query);
    $total_items = db_num_rows($_res);
    db_free_result($_res);

    $navigation = cw_core_get_navigation($target, $total_items, $page);
    $navigation['script'] = "index.php?target=$target&mode=search";
    $smarty->assign('navigation', $navigation);

	if ($total_items > 0) {
        $page = $data['page'];

        $orders_search_query = cw_db_generate_query($fields, $from_tbls, $query_joins, $where, $groupbys, $having, $orderbys);
        $orders = cw_query($orders_search_query." LIMIT $navigation[first_page], $navigation[objects_per_page]");
        
        if ($save_search_id > 0) {
            cw_array2update('saved_search', array("sql_query"=>addslashes($orders_search_query)), "ss_id = '$save_search_id'");
            $save_search_id = 0;
        }         
        
    }

    $all_domains_data = cw_query_hash("select * from $tables[domains] order by domain_id","domain_id", false, false);
    $currency_symbol_category_id = cw_query_first_cell("select config_category_id from $tables[config] where name='currency_symbol'");
    $currency_rate_category_id = $currency_symbol_category_id;

    foreach ($all_domains_data as $aldd_k=>$aldd_v) {
        $all_domains_data[$aldd_k]['domain_id'] = $aldd_k;
        $_primary_currency_symbol = cw_query_first_cell("select value from $tables[domains_config] where domain_id='$aldd_k' and name='currency_symbol' and config_category_id='$currency_symbol_category_id'");
        if ($config['General']['currency_symbol'] == $_primary_currency_symbol) continue;
        $all_domains_data[$aldd_k]['currency_options'] = array('primary_currency_symbol'=>$_primary_currency_symbol, 
        'primary_currency_rate' => cw_query_first_cell("select value from $tables[domains_config] where domain_id='$aldd_k' and name='currency_rate' and config_category_id='$currency_rate_category_id'"));
    }

    $smarty->assign('all_domains_data', $all_domains_data);


	if ($orders) {

		foreach ($orders as $k => $v) {
			$orders[$k]['extras'] = cw_call('cw_doc_get_extras_data', array($v['doc_id']));

            if (!empty($orders[$k]['domain_id'])) {
                $orders[$k]['domain_data'] = $all_domains_data[$orders[$k]['domain_id']]; 
            }
		}
    }

    $smarty->assign('orders', $orders);
	$smarty->assign('mode', $mode);
}

$location[] = array(cw_get_langvar_by_name('lbl_docs_info_'.$docs_type), 'index.php?target='.$target);

$smarty->assign('docs_type', $docs_type);

$smarty->assign('countries', cw_map_get_countries());
$smarty->assign('states', cw_map_get_states());
$smarty->assign('payment_methods', cw_func_call('cw_payment_search', array('data' => array('type' => 1))));
$smarty->assign('shippings', cw_func_call('cw_shipping_search', array('data' => array('active' => 1))));

$smarty->assign('saved_searches', cw_query("select name, ss_id from $tables[saved_search] where type='O' order by name, ss_id"));
$smarty->assign('current_loaded_search_id', $current_loaded_search_id);
$smarty->assign('current_loaded_search_name', cw_query_first_cell("select name from $tables[saved_search] where type='O' and ss_id='$current_loaded_search_id'"));

$smarty->assign('search_prefilled', $search_data['orders'][$docs_type]);
$smarty->assign('js_tab', $search_data['orders'][$docs_type]['js_tab']);

$smarty->assign('main', 'orders');
