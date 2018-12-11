<?php
/*
 * Default dashborad sections
 */

function dashboard_section_search($params, $return=null) {

    // Set the dashboard code name here
    $name = 'search';

    if (!isset($params['sections'][$name])) return $return;

    // If the section is disabled then skip it on dashboard
    if (
    	$params['mode'] == 'dashboard' 
    	&& $params['sections'][$name]['active'] === '0'
    ) {
    	return $return;
    }

    // Define basic data for configuration
    $return[$name] =  array(
        'title'         => 'Quick search',
        'description'   => 'Widget allows to quickly search order / users / products by id',
        'active'        => 1,       // Default status: 0 or 1; optional
        'pos'           => 10,      // Default position; optional
        'size'          => 'small',// Section size: 'small' (25%), 'medium' (50%) or 'big' (100%); optional
        'frame'         => 1,       // Show frame: 0 or 1; 1 by default; optional
        'header'        => 1,       // Show header: 0 or 1; 1 by default; optional
    );

    if ($params['mode'] == 'setting') return $return;

    $return[$name]['template'] = 'addons/dashboard/admin/sections/search.tpl';

    return $return;
}

function dashboard_section_graph($params, $return=null) {

    // Set the dashboard code name here
    $name = 'graph';

    if (!isset($params['sections'][$name])) return $return;

    // If the section is disabled then skip it on dashboard
    if (
    	$params['mode'] == 'dashboard' 
    	&& $params['sections'][$name]['active'] === '0'
    ) {
    	return $return;
    }

    // Define basic data for configuration
    $return[$name] =  array(
        'title'         => 'Dashboard',
        'description'   => 'Graph showing recent sales statistics',
        'active'        => 1,		// Default status: 0 or 1; optional
        'pos'           => 20,		// Default position; optional
        'size'          => 'medium',// Section size: 'small' (25%), 'medium' (50%) or 'big' (100%); optional
        'frame'         => 1,		// Show frame: 0 or 1; 1 by default; optional
        'header'        => 1,		// Show header: 0 or 1; 1 by default; optional
    );

    if ($params['mode'] == 'setting') return $return;

    // Add content for dashboard in 'dashboard' mode
    // Define either content or template name or both
    $return[$name]['template'] = 'addons/dashboard/admin/sections/graph.tpl';

    return $return;
}

function dashboard_last_orders($params, $return=null) {
    global $tables, $smarty, $current_area, $customer_id;

    if (!isset($params['sections']['last_orders'])) return $return;

    if (
    	$params['mode'] == 'dashboard' 
    	&& $params['sections']['last_orders']['active'] === '0'
    ) {
    	return $return;
    }

    // Define basic data for configuration
    $return['last_orders'] =  array(
        'title' 		=> 'Recent orders',
        'description' 	=> 'Shows most recent orders',
        'active' 		=> 1,
        'pos' 			=> 30,
        'size' 			=> 'small',
    );

	if ($params['mode'] == 'setting') return $return;

	$q = "SELECT d.doc_id, d.status, d.display_id, ca.firstname, du.email, ca.lastname, COUNT(DISTINCT dit.product_id) as qty, di.total
		FROM $tables[docs] d
		LEFT JOIN $tables[docs_info] di ON d.doc_info_id = di.doc_info_id
		LEFT JOIN $tables[docs_items] dit ON dit.doc_id = d.doc_id
		LEFT JOIN $tables[docs_user_info] du ON d.doc_info_id = du.doc_info_id
		LEFT JOIN $tables[customers_addresses] ca ON ca.address_id = du.main_address_id
		WHERE d.type = 'O' ".(($current_area == 'V')?"AND di.warehouse_customer_id='$customer_id'":'')."
		GROUP BY d.doc_id
		ORDER BY d.date DESC
		LIMIT 5"; 
	$result = cw_query_hash($q);
	$orders = array();

	if (is_array($result) && count($result)) {
		
		foreach ($result as $doc_id => $value) {
                        $name = trim($value[0]['firstname'] . ' ' . $value[0]['lastname']);  
                        if (empty($name))
                            $name = $value[0]['email'];  
			$orders[] = array(
				'id'	=> $doc_id,
                'display_id' => $value[0]['display_id'],
				'status'	=> $value[0]['status'],
				'name'	=> $name,
				'qty'	=> $value[0]['qty'],
				'total'	=> $value[0]['total']
			);
		}
	}

    $smarty->assign('orders', $orders);
    $return['last_orders']['template'] = 'addons/dashboard/admin/sections/last_orders.tpl';

    return $return;
}

function dashboard_section_system_messages($params, $return=null) {
	global $config, $smarty, $app_main_dir;

	// Set the dashboard code name here
	$name = 'system_messages';

        if (!isset($params['sections'][$name])) return $return;

	// If the section is disabled then skip it on dashboard
	if (
		$params['mode'] == 'dashboard'
		&& $params['sections'][$name]['active'] === '0'
	) {
		return $return;
	}

	// Define basic data for configuration
	$return[$name] =  array(
		'title'         => 'System messages',
		'description'   => 'system messages',
		'active'        => 1,		// Default status: 0 or 1; optional
		'pos'           => 0,		// Default position; optional
		'size'          => 'medium',// Section size: 'small' (25%), 'medium' (50%) or 'big' (100%); optional
		'frame'         => 1,		// Show frame: 0 or 1; 1 by default; optional
		'header'        => 1,		// Show header: 0 or 1; 1 by default; optional
);


	if ($params['mode'] == 'setting') {
		return $return;
	}

 
	/*
	 * GET SYSTEM MESSAGES
	 */
	$system_messages = cw_system_messages(constant('SYSTEM_MESSAGE_COMMON'),true);
	$smarty->assign('system_messages', $system_messages);
	$return[$name]['template'] = 'addons/dashboard/admin/sections/system_messages.tpl';

	if (empty($system_messages)) unset($return[$name]);
 
	return $return;
}

function dashboard_section_pending_reviews($params, $return=null) {
    global $config, $tables, $smarty;

    // Set the dashboard code name here
    $name = 'pending_reviews';

    if (!isset($params['sections'][$name])) return $return;

    // If the section is disabled then skip it on dashboard
    if (
        $params['mode'] == 'dashboard'
        && $params['sections'][$name]['active'] === '0'
        || $config['estore_products_review']['status_created_reviews'] == 1
    ) {
        return $return;
    }

    // Define basic data for configuration
    $return[$name] =  array(
        'title'         => 'Pending reviews',
        'description'   => 'Shows how many pending reviews await for admin decision',
        'active'        => 1,       // Default status: 0 or 1; optional
        'pos'           => 50,      // Default position; optional
        'size'          => 'small', // Section size: 'small' (25%), 'medium' (50%) or 'big' (100%); optional
        'frame'         => 1,       // Show frame: 0 or 1; 1 by default; optional
        'header'        => 1,       // Show header: 0 or 1; 1 by default; optional
    );

    if ($params['mode'] == 'setting') return $return;

    $count_pending_reviews = cw_query_first_cell("
        SELECT count(review_id)
        FROM $tables[products_reviews]
        WHERE status = 0
    ");

    $smarty->assign('count_pending_reviews', $count_pending_reviews);

    $return[$name]['template'] = 'addons/dashboard/admin/sections/pending_reviews.tpl';

    return $return;
}


function dashboard_section_awaiting($params, $return=null) {
	global $config, $smarty, $tables, $customer_id;
    // Set the dashboard code name here
    $name = 'awaiting_actions';

    if (!isset($params['sections'][$name])) return $return;

    // If the section is disabled then skip it on dashboard
    if ($params['mode'] == 'dashboard' && $params['sections'][$name]['active']==='0') return $return;

    // Define basic data for configuration
    $return[$name] =  array(
        'title'         => 'Awaiting action(s)',
        'description'   => 'These actions await for admin attention',
        'active'        => 1,       // Default status: 0 or 1; optional
        'pos'           => 50,       // Default position; optional
        'size'          => 'small',   // Section size: 'small' (25%), 'medium' (50%) or 'big' (100%); optional
        'frame'         => 1,       // Show frame: 0 or 1; 1 by default; optional
        'header'        => 1,       // Show header: 0 or 1; 1 by default; optional; igmored if frame is 0
    );

    if ($params['mode']=='setting') return $return;

	// Products approval counter
	$cnt = cw_query_first_cell("SELECT count(*) FROM $tables[products] WHERE status=2");
	if ($cnt == 0) cw_system_messages_delete('product_approvals');
	else cw_system_messages_add('product_approvals',cw_get_langvar_by_name('lbl_product_approvals').' -
	<a href="index.php?target=products&mode=search&status[]=2">'.$cnt.'</a>', constant('SYSTEM_MESSAGE_AWAITING'));
	
	// Reviews counter
	$cnt = cw_query_first_cell("SELECT count(*) FROM $tables[products_reviews] WHERE status=0");
	if ($cnt == 0) cw_system_messages_delete('product_reviews');
	else cw_system_messages_add('product_reviews',cw_get_langvar_by_name('lbl_product_reviews').' -
	<a href="index.php?target=estore_reviews_management">'.$cnt.'</a>', constant('SYSTEM_MESSAGE_AWAITING'));

	// Incoming messages
    $cnt = cw_messages_get_new_messages_counter($customer_id);
	if ($cnt == 0) cw_system_messages_delete('incoming_messages');
	else {
        cw_system_messages_add('incoming_messages',cw_get_langvar_by_name('lbl_incoming_messages').' -
	<a href="index.php?target=message_box&sort_field=read_status&sort_direction=1">'.$cnt.'</a>', constant('SYSTEM_MESSAGE_AWAITING'));
        cw_system_messages_update_data('incoming_messages',$cnt);
    }

	// Quotes counter
	$cnt = cw_query_first_cell("SELECT count(*) FROM $tables[docs] WHERE type='I' and status='Q'");
	if ($cnt == 0) cw_system_messages_delete('quote_requests');
	else cw_system_messages_add('quote_requests',cw_get_langvar_by_name('lbl_quote_requests').' -
	<a href="index.php?target=docs_I&mode=search&data[status]=Q">'.$cnt.'</a>', constant('SYSTEM_MESSAGE_AWAITING'));

	cw_event('on_dashboard_awaiting_actions'); // Handlers must add lines via cw_system_messages_add (type = SYSTEM_MESSAGE_AWAITING)

	/*
	 * GET SYSTEM MESSAGES
	 */
	$system_messages = cw_system_messages(constant('SYSTEM_MESSAGE_AWAITING'),true);
	$smarty->assign('awaiting_actions', $system_messages);
	$return[$name]['template'] = 'addons/dashboard/admin/sections/awaiting_actions.tpl';

	if (empty($system_messages)) unset($return[$name]);
	
    return $return;
}

function dashboard_section_system_info($params, $return=null) {
	global $tables, $smarty;
	
    // Set the dashboard code name here
    $name = 'system_info';

    if (!isset($params['sections'][$name])) return $return;

    // If the section is disabled then skip it on dashboard
    if ($params['mode'] == 'dashboard' && $params['sections'][$name]['active']==='0') return $return;

    // Define basic data for configuration
    $return[$name] =  array(
        'title'         => 'System Information',
        'description'   => 'This is example of dashboard section explains how to build your own widget',
        'active'        => 1,       // Default status: 0 or 1; optional
        'pos'           => 60,       // Default position; optional
        'size'          => 'small',   // Section size: 'small' (25%), 'medium' (50%) or 'big' (100%); optional
        'frame'         => 1,       // Show frame: 0 or 1; 1 by default; optional
        'header'        => 1,       // Show header: 0 or 1; 1 by default; optional; igmored if frame is 0
    );

    if ($params['mode']=='setting') return $return;

    // Add content for dashboard in 'dashboard' mode
    // Define either content or template name or both

	// Categories counter
	$cat_cnt = cw_query_first_cell("SELECT count(*) FROM $tables[categories]");
	$facet_cnt = cw_query_first_cell("SELECT count(*) FROM $tables[clean_urls_custom_facet_urls]");
	cw_system_messages_add('category_count',
	cw_get_langvar_by_name('lbl_category').' | '.cw_get_langvar_by_name('lbl_facet_count').' - <a href="index.php?target=categories">'.$cat_cnt.'</a> | <a href="index.php?target=custom_facet_urls">'.$facet_cnt.'</a>', constant('SYSTEM_MESSAGE_SYSTEM'));

	// Products counter
	$product_cnt = cw_query_first_cell("SELECT count(*) FROM $tables[products]");
	cw_system_messages_add('product_count',cw_get_langvar_by_name('lbl_product_count').' -
	<a href="index.php?target=products&mode=search&new_search=1">'.$product_cnt.'</a>', constant('SYSTEM_MESSAGE_SYSTEM'));

    // Orders counter
	$order_cnt = cw_query_hash("SELECT status, count(*)  FROM $tables[docs] WHERE type='O' GROUP BY status",'status',false,true);
	$msg = cw_get_langvar_by_name('lbl_order_count').' -';
	foreach ($order_cnt as $status=>$count) {
		$msg .= ' <a href="index.php?target=docs_O&mode=search&data[status]='.$status.'" class="order_'.$status.'" title="'.$status.'">&nbsp;'.$count.'&nbsp;</a>';
	}
	cw_system_messages_add('order_count',$msg, constant('SYSTEM_MESSAGE_SYSTEM'));


	// Customers counter
	$customer_cnt = cw_query_first_cell("SELECT count(*) FROM $tables[customers] WHERE usertype='C'");
	cw_system_messages_add('customer_count',cw_get_langvar_by_name('lbl_customer_count').' - 
	<a href="index.php?target=user_C&mode=search&new_search=1">'.$customer_cnt.'</a>', constant('SYSTEM_MESSAGE_SYSTEM'));

	// Mail counter
	$mail_cnt = cw_query_first_cell("SELECT count(*) FROM $tables[mail_spool]");
	cw_system_messages_add('mail_count',cw_get_langvar_by_name('lbl_mail_queue').' - 
	<a href="index.php?target=mail_queue">'.$mail_cnt.'</a>', constant('SYSTEM_MESSAGE_SYSTEM'));

	// Sess counter
	$sess_cnt = cw_query_first_cell("SELECT count(*) FROM $tables[sessions_data] WHERE expiry>".cw_core_get_time());
	cw_system_messages_add('session_count',cw_get_langvar_by_name('lbl_active_sessions').' - 
	<a href="index.php?target=sessions">'.$sess_cnt.'</a>', constant('SYSTEM_MESSAGE_SYSTEM'));
	
	cw_event('on_dashboard_system_info'); // Handlers must add lines via cw_system_messages_add (type = SYSTEM_MESSAGE_SYSTEM)
	
	/*
	 * GET SYSTEM MESSAGES
	 */
	$system_messages = cw_system_messages(constant('SYSTEM_MESSAGE_SYSTEM'),true);
	$smarty->assign('system_info', $system_messages);
    $return[$name]['template'] = 'addons/dashboard/admin/sections/system_info.tpl';

	if (empty($system_messages)) unset($return[$name]);	
	

    return $return;
}

function dashboard_section_news($params, $return=null) {
	global $smarty;
    // Set the dashboard code name here
    $name = 'news_info';

    if (!isset($params['sections'][$name])) return $return;

    // If the section is disabled then skip it on dashboard
    if ($params['mode'] == 'dashboard' && $params['sections'][$name]['active']==='0') return $return;

    // Define basic data for configuration
    $return[$name] =  array(
        'title'         => 'News and Information',
        'description'   => 'This is example of dashboard section explains how to build your own widget',
        'active'        => 1,       // Default status: 0 or 1; optional
        'pos'           => 70,       // Default position; optional
        'size'          => 'medium',   // Section size: 'small' (25%), 'medium' (50%) or 'big' (100%); optional
        'frame'         => 1,       // Show frame: 0 or 1; 1 by default; optional
        'header'        => 1,       // Show header: 0 or 1; 1 by default; optional; igmored if frame is 0
    );

    if ($params['mode']=='setting') return $return;

    // Add content for dashboard in 'dashboard' mode
    // Define either content or template name or both
    //$return[$name]['content']  = '<h2>This example dashboard section explains how to build your own widget</h2>';
    
    if (!($content = cw_cache_get('dashboard','rss'))) {
		$rss = simplexml_load_file(constant('NEWS_RSS_URL'));
		for ($i=0; $i<10; $i++) {
			$items[] = $rss->channel->item[$i];
		}
		$smarty->assign('rss', $items);
		$content = $smarty->fetch('addons/dashboard/admin/sections/news.tpl');
		$smarty->clear_assign('rss');
		unset($rss, $items);
		cw_cache_save($content,'dashboard','rss');
	}

    $return[$name]['content'] = $content;

    return $return;
}

function dashboard_section_statistic($params, $return=null) {

    global $smarty, $tables, $config, $current_area, $customer_id, $allowed_seller_display_order_statuses;
    
    if ($params['mode']=='setting') return $return;

    if ($current_area == 'V') {
        $sales_order_statuses = $allowed_seller_display_order_statuses;
    } else {
        $sales_order_statuses = cw_query_column("SELECT code FROM $tables[order_statuses] WHERE inventory_decreasing = 1", 'code');
    }

    $today_from = mktime(0, 0, 0, date('n'), date('j'), date('Y'));
    //$today_to = $today_from + SECONDS_PER_DAY;
    
    $month_from = mktime(0, 0, 0, date('n'), 1, date('Y'));
    //$month_to = $today_to;
    
    $product_sales_today = cw_query_first_cell("SELECT sum(di.total) as total
			FROM $tables[docs_info] di
			INNER JOIN $tables[docs] d ON d.doc_info_id = di.doc_info_id
			WHERE (d.date >= $today_from) 
                    AND d.type = 'O' 
                    AND d.status IN ('".join("','",$sales_order_statuses)."')".(($current_area == 'V')?" AND di.warehouse_customer_id='$customer_id'":''));

    $product_sales_month = cw_query_first_cell("SELECT sum(di.total) as total
			FROM $tables[docs_info] di
			INNER JOIN $tables[docs] d ON d.doc_info_id = di.doc_info_id
			WHERE (d.date >= $month_from) 
                    AND d.type = 'O' 
                    AND d.status IN ('".join("','",$sales_order_statuses)."')".(($current_area == 'V')?" AND di.warehouse_customer_id='$customer_id'":''));

    $product_sales_overall = cw_query_first("SELECT count(d.doc_id) as cnt, sum(di.total) as total
			FROM $tables[docs_info] di
			INNER JOIN $tables[docs] d ON d.doc_info_id = di.doc_info_id
			WHERE  d.type = 'O' 
                    AND d.status IN ('".join("','",$sales_order_statuses)."')".(($current_area == 'V')?" AND di.warehouse_customer_id='$customer_id'":''));

    $smarty->assign(array(
        'product_sales_today' => $product_sales_today,
        'product_sales_month' => $product_sales_month,
        'product_sales_overall' => $product_sales_overall['total'],
        'product_sales_average' => $product_sales_overall['cnt']>0?$product_sales_overall['total']/$product_sales_overall['cnt']:0,
        ));

    return $return;
}
