<?php
$docs_type = 'O';
cw_load('doc');

$search_data = &cw_session_register('search_data', array());
cw_load('map', 'warehouse', 'product', 'accounting', 'taxes', 'doc');

if ($action == 'reset') {
    $search_data['cost_history'] = array();
    cw_header_location("index.php?target=$target&mode=search");
}

if ($REQUEST_METHOD == "POST" && is_array($posted_data)) {

    $date_fields = array (
        'basic' => array('creation_date_start' => 0, 'creation_date_end' => 1)
    );

    cw_core_process_date_fields($posted_data, $date_fields);
    $posted_data['js_tab'] = $js_tab;
    $search_data['cost_history'][$docs_type] = $posted_data;
    $search_data['cost_history'][$docs_type]['search_sections'] = $search_sections;

	cw_header_location("index.php?target=$target&mode=search");
}

if (empty($search_data['cost_history'][$docs_type])) {
    $search_data['cost_history'] = array();
    $date = getdate(cw_core_get_time());
    $search_data['cost_history'][$docs_type] = array(
        'basic' => array(
            'creation_date_start' => mktime(0, 0, 0, $date['mon'], 1, $date['year']),
            'creation_date_end' => mktime(23, 59, 59, $date['mon'], $date['mday'], $date['year']),
        ),
        'search_sections' => array('tab_search_orders' => 1),
    );
}

if (empty($search_data['cost_history'][$docs_type]['sort_field'])) {
    $search_data['cost_history'][$docs_type]['sort_field'] = 'display_doc_id';
    $search_data['cost_history'][$docs_type]['sort_direction'] = 1;
}

if (
	$sort
	&& in_array($sort, array('doc_id', 'status', 'customer', 'warehouse', 'date', 'total',
							'display_doc_id', 'tax', 'shipping_cost', 'items', 'cost', 'profit'))
)
    $search_data['cost_history'][$docs_type]['sort_field'] = $sort;

if (isset($sort_direction))
    $search_data['cost_history'][$docs_type]['sort_direction'] = $sort_direction;

if (!empty($page))
    $search_data['cost_history'][$docs_type]['page'] = intval($page);

if ($mode == 'search') {
    $fields = array();
    $from_tbls = array();
    $query_joins = array();
    $where = array();
    $groupbys = array();
    $having = array();
    $orderbys = array();

    $data = $search_data['cost_history'][$docs_type];

    $fields[] = "$tables[docs].doc_id";
    $fields[] = "$tables[docs].display_id";
    $fields[] = "$tables[docs].status";
    $fields[] = "$tables[docs].date";
    $fields[] = "$tables[docs_info].warehouse_customer_id";
    $fields[] = "$tables[docs_info].customer_notes";
    $fields[] = "$tables[docs_info].notes";
    $fields[] = "$tables[docs_info].tax";
    $fields[] = "$tables[docs_info].shipping_cost";
    $fields[] = "$tables[docs_info].total";
	$fields[] = "$tables[docs_user_info].customer_id";
	$fields[] = "$tables[docs_user_info].usertype";
    $fields[] = "$tables[docs_info].extra";
    $fields[] = "wd.title as warehouse_title";
    $fields[] = "sum($tables[docs_items].amount) as items";
	$fields[] = "sum($tables[docs_items].history_cost*$tables[docs_items].amount) as cost";
	$fields[] = "($tables[docs_info].total-$tables[docs_info].tax-$tables[docs_info].shipping_cost-$tables[docs_info].coupon_discount-$tables[docs_info].discount-sum($tables[docs_items].history_cost*$tables[docs_items].amount)) as profit";

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

	$query_joins['docs_items'] = array(
		'on' => "$tables[docs_items].doc_id = $tables[docs].doc_id",
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

    $query_joins['products'] = array(
        'on' => "$tables[products].product_id = $tables[docs_items].product_id",
    );

    if ($data['search_sections']['tab_search_orders']) {
		if ($data['basic']['created'] == 'selected') {
			if (!empty($data['basic']['creation_date_start']))
				$where[] = "$tables[docs].date >= '".$data['basic']['creation_date_start']."'";

			if (!empty($data['basic']['creation_date_end']))
				$where[] = "$tables[docs].date <= '".$data['basic']['creation_date_end']."'";
		} else {
			switch ($data['basic']['created']) {
				case 'this_month':
					$start = mktime(0, 0, 0, date("n"), 1, date("Y"));
					$end = cw_core_get_time();
					$where[] = "$tables[docs].date >= '" . $start . "'";
					$where[] = "$tables[docs].date <= '" . $end . "'";
				break;
				case 'this_week':
					$start = mktime(0, 0, 0, date("n"), date("j") - date("w"), date("Y"));
					$end = cw_core_get_time();
					$where[] = "$tables[docs].date >= '" . $start . "'";
					$where[] = "$tables[docs].date <= '" . $end . "'";
				break;
				case 'today':
					$start = mktime(0, 0, 0, date("n"), date("j"), date("Y"));
					$end = cw_core_get_time();
					$where[] = "$tables[docs].date >= '" . $start . "'";
					$where[] = "$tables[docs].date <= '" . $end . "'";
				break;
			}
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
		    $search_data['cost_history'][$docs_type]['advanced']['expire_date_start']=$expire_date_start_unix;
		}

        if (!empty($data['advanced']['expire_date_end'])) {
		    // Fix: See previous section
		    $expire_date_end_unix = date_format(date_create_from_format($date_format,$data['advanced']['expire_date_end']),"U");
	        $where[] = "$tables[docs_quotes].exp_date <= $expire_date_end_unix";
		    $search_data['cost_history'][$docs_type]['advanced']['expire_date_end']=$expire_date_end_unix;
		}

        if (!empty($data['advanced']['expire_date_start']) || !empty($data['advanced']['expire_date_end'])) {
            $query_joins['docs_quotes'] = array(
                'on' => "$tables[docs_quotes].doc_id=$tables[docs].doc_id",
            );
        }

        if (!empty($data['products']['product']))
            $where[] = "$tables[docs_items].product like '%".$data['products']['product']."%'";

        if (!empty($data['products']['product_excl'])) {
            $where[] = "$tables[docs].doc_id not in (select distinct(doc_id) from $tables[docs_items] where $tables[docs_items].product like '%".$data['products']['product_excl']."%')"; 
//          $where[] = "$tables[docs_items].product not like '%".$data['products']['product_excl']."%'";
        }

        if (!empty($data['products']['product_code']))
            $where[] = "$tables[docs_items].productcode like '%".$data['products']['product_code']."%'";

        if (!empty($data['products']['product_id']))
            $where[] = "$tables[docs_items].product_id = '".$data['products']['product_id']."'";

        if (!empty($data['products']['price_start']))
            $where[] = "$tables[docs_items].price >= '".$data['products']['price_start']."'";

        if (!empty($data['products']['price_end']))
            $where[] = "$tables[docs_items].price <= '".$data['products']['price_end']."'";

        $customer_condition = array();

        if (!empty($data['customer']['by_customer_id']))
            $customer_condition[] = "$tables[customers].customer_id = '".$data['customer']['substring']."'";

        if (!empty($data['customer']['by_firstname']))
            $customer_condition[] = "ca.firstname LIKE '%".$data['customer']['substring']."%'";

        if (!empty($data['customer']['by_lastname']))
            $customer_condition[] = "ca.lastname LIKE '%".$data['customer']['substring']."%'";

        if (
			preg_match("/^(.+)(\s+)(.+)$/", $data['customer']['substring'], $found)
			&& !empty($data['customer']['by_firstname'])
			&& !empty($data['customer']['by_lastname'])
		)
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
        $where[] = "$tables[docs_user_info].customer_id = '".$data['basic']['customer_id']."'";

    if ($data['warehouse_area']) {
        $where[] = "($tables[docs_info].warehouse_customer_id = '".$data['warehouse_area']."' or $tables[docs_user_info].customer_id = '".$data['warehouse_area']."')";
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
        case 'tax':
            $orderbys[] = "$tables[docs_info].tax $direction";
            break;
        case 'shipping_cost':
            $orderbys[] = "$tables[docs_info].shipping_cost $direction";
            break;
        case 'cost':
            $orderbys[] = "cost $direction";
            break;
        case 'items':
            $orderbys[] = "items $direction";
            break;
        case 'profit':
            $orderbys[] = "profit $direction";
            break;
        default:
			$orderbys[] = "$tables[docs].$data[sort_field] $direction";
    }

    $groupbys[] = "$tables[docs].doc_id";

	cw_event('on_prepare_search_orders', array($data, $docs_type, &$fields, &$query_joins, &$where, &$groupbys, &$having, &$orderbys));

    $count_query = cw_db_generate_query(array('count(*)'), $from_tbls, $query_joins, $where, $groupbys, $having, null);
    $_res = db_query($count_query);
    $total_items = db_num_rows($_res);
    db_free_result($_res);

    $app_config_file['interface']['items_per_page']= array(20,50,100,500,1000,5000,10000);
    $smarty->assign('app_config_file', $app_config_file);

    $navigation = cw_core_get_navigation($target, $total_items, $page);
    $navigation['script'] = "index.php?target=$target&mode=search";
    $smarty->assign('navigation', $navigation);

	$orders = array();
	if ($total_items > 0) {
        $page = $data['page'];

        $orders = cw_query(
			$report_query = cw_db_generate_query($fields, $from_tbls, $query_joins, $where, $groupbys, $having, $orderbys)
			. " LIMIT $navigation[first_page], $navigation[objects_per_page]"
		);
    }

    foreach ($orders as $k=>$v) {
        $orders[$k]['extra'] = $v['extra'] = unserialize($v['extra']);
	if (!isset($v['extra']['shipping_no_offer'])) {
		$orders[$k]['extra']['shipping_no_offer'] = cw_query_first_cell("SELECT value FROM $tables[docs_extras] WHERE doc_id=$v[doc_id] AND khash='shipping_no_offer'");
	}
	if (!$orders[$k]['extra']['shipping_no_offer']) {
		$orders[$k]['extra']['shipping_no_offer'] = $v['shipping_cost'];
	}

	$orders[$k]['profit'] = $v['profit'] + $v['shipping_cost'] - $orders[$k]['extra']['shipping_no_offer'];
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

$smarty->assign('search_prefilled', $search_data['cost_history'][$docs_type]);
$smarty->assign('js_tab', $search_data['cost_history'][$docs_type]['js_tab']);
$smarty->assign('attribute_data', array(
	'field' => 'posted_data[advanced][domain_id][]',
	'values' => (array)$search_data['cost_history'][$docs_type]['advanced']['domain_id']
));

$smarty->assign('main', 'report_cost_history');
