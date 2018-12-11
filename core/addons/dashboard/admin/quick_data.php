<?php
/*
 * get quick data
 * 
 * return json data
 * 
 * */
if (isset($_POST['type'])) {
	global $config, $user_account, $tables, $current_area, $customer_id, $allowed_seller_display_order_statuses;

	$search_type = $_POST['type'];

	// ger orders data for graph
	if ($search_type == 'graph') {
		$period = $_POST['period'];

		switch ($period) {
			case 0:	// today
				$from 	= mktime(0, 0, 0, date('n'), date('j'), date('Y'));
				$to 	= time() + $config['Appearance']['timezone_offset'] * SECONDS_PER_HOUR;
				$step	= floor(($to - $from) / SECONDS_PER_HOUR);	// hours in current period
				$step	= ($step > 12) ? 12 : $step;
				$step	= floor(($to - $from) / $step);	// one period in seconds
			break;

			case 1:	// yesterday
				$yesterday = time() - SECONDS_PER_DAY;
				$from 	= mktime(0, 0, 0, date('n', $yesterday), date('j', $yesterday), date('Y', $yesterday));
				$to 	= mktime(23, 59, 59, date('n', $yesterday), date('j', $yesterday), date('Y', $yesterday));
				$step	= SECONDS_PER_HOUR * 1; // seconds in 1 hour
			break;

			case 2:	// last 7 days
				$time_7_days_ago = time() - SECONDS_PER_WEEK;
				$from 	= mktime(0, 0, 0, date('n', $time_7_days_ago), date('j', $time_7_days_ago), date('Y', $time_7_days_ago));
				$to 	= time() + $config['Appearance']['timezone_offset'] * SECONDS_PER_HOUR;
				$step	= SECONDS_PER_DAY;	// seconds in day
			break;

			case 3:	// last 30 days
				$time_30_days_ago = time() - SECONDS_PER_DAY * 30;
				$from 	= mktime(0, 0, 0, date('n', $time_30_days_ago), date('j', $time_30_days_ago), date('Y', $time_30_days_ago));
				$to 	= time() + $config['Appearance']['timezone_offset'] * SECONDS_PER_HOUR;
				$step	= SECONDS_PER_DAY;	// seconds in one day
			break;

			case 4:	// current month
				$from 	= mktime(0, 0, 0, date('n'), 1, date('Y'));
				$to 	= time() + $config['Appearance']['timezone_offset'] * SECONDS_PER_HOUR;

                if ($to - $from < SECONDS_PER_DAY) {    // less one day
				    $step = SECONDS_PER_HOUR * 2; // seconds in 2 hours
                }
                elseif ($to - $from < SECONDS_PER_DAY * 7) {    // less 7 day
                    $step = SECONDS_PER_HOUR * 12; // seconds in 12 hours
                }
                else {
                    $step = SECONDS_PER_DAY; // seconds in one day
                }
			break;

			default:// today
				$from 	= mktime(0, 0, 0, date('n'), date('j'), date('Y'));
				$to 	= time() + $config['Appearance']['timezone_offset'] * SECONDS_PER_HOUR;
				$step	= floor(($to - $from) / SECONDS_PER_HOUR);	// hours in current period
				$step	= ($step > 12) ? 12 : $step;
				$step	= floor(($to - $from) / $step);	// one period in seconds
			break;
		}

		$result = array();
		$start_time = $from;
		$end_time 	= $to;
		// fill result array
		while ($end_time >= $start_time) {
			$result[] = array(
				'count' 	=> 0, 
				'amount' 	=> 0, 
				'margin' 	=> 0, 
				'date' 		=> date('Y-m-d H:i:s', $start_time),
				'timestamp' => $start_time
			);
			$start_time += $step;
		}

		// if last time not present in array, then need add
		if ($result[count($result)-1]['timestamp'] < $to) {
			$result[] = array(
				'count' 	=> 0, 
				'amount' 	=> 0, 
				'margin' 	=> 0, 
				'date' 		=> date('Y-m-d H:i:s', $to),
				'timestamp' => $to
			);
		}

		$q = "SELECT d.doc_id, d.date, di.total, de.value as margin
			FROM $tables[docs] d
                        INNER JOIN $tables[order_statuses] os ON d.status = os.code " . ($current_area=='V'?'':"AND os.inventory_decreasing = 1 ") . 
		       "LEFT JOIN $tables[docs_info] di ON d.doc_info_id = di.doc_info_id
			LEFT JOIN $tables[docs_extras] de ON d.doc_id = de.doc_id AND de.khash = 'margin_value'
			WHERE (d.date BETWEEN $from AND $to) AND d.type = 'O' 
                        ".($current_area=='V'?" AND di.warehouse_customer_id='$customer_id' AND d.status in ('".implode("','", $allowed_seller_display_order_statuses)."') ":'')."
			ORDER BY d.date ASC";
		$orders = cw_query_hash($q);

		if (is_array($orders) && count($orders)) {
			$key = 0;

			foreach ($orders as $order) {
				// find next period
				while (
					$order[0]['date'] > $result[$key]['timestamp']
					&& isset($result[$key])					
				) {
					$key++;
				}
				$result[$key]['count'] 	+= 1;
				$result[$key]['amount'] += floatval($order[0]['total']);
				$result[$key]['margin'] += floatval($order[0]['margin']);
			}
		}

		exit(json_encode($result));
	}

	// search users
	if ($search_type == 'user_C') {
		$posted_data = $_POST['posted_data'];
		$substring = trim($posted_data['substring']);

		$result = array();

		$users = cw_query("SELECT c.customer_id, ca.firstname, ca.lastname, c.email
			FROM $tables[customers] c
			LEFT JOIN $tables[customers_addresses] ca ON c.customer_id = ca.customer_id
			WHERE c.usertype in ('C', 'R') 
				AND (ca.firstname LIKE '%$substring%' OR ca.lastname LIKE '%$substring%' OR c.email LIKE '%$substring%')
            GROUP BY c.customer_id
			ORDER BY ca.firstname, ca.lastname, c.email
			LIMIT " . SEARCH_LIMIT_FOR_AUTOCOMPLETE);

		if (is_array($users) && count($users)) {

			foreach ($users as $user) {
				$name = trim($user['firstname'] . ' ' . $user['lastname']);
				$name = empty($name) ? $user['email'] : $name . ' (' . $user['email'] . ')';
				$result[] = array('id' => $user['customer_id'], 'name' => $name);
			}
		}
		    
		exit(json_encode($result));
	}

	// search orders
	if ($search_type == 'docs_O') {
		$posted_data = $_POST['posted_data'];
		$display_id = $posted_data['doc_id'];
		
		$result = array();
		
		$orders = cw_query($orders_query = "SELECT d.doc_id, d.display_id FROM $tables[docs] d LEFT JOIN $tables[docs_info] di ON d.doc_info_id = di.doc_info_id WHERE d.display_id like '$display_id%' AND d.type = 'O'".($current_area=='V'?" AND di.warehouse_customer_id='$customer_id' ":'')."LIMIT ".SEARCH_LIMIT_FOR_AUTOCOMPLETE);

	    foreach ($orders as $order) {
	    	$result[] = array('id' => $order['doc_id'], 'name' => $order['display_id']);
	    }
	    
	    exit(json_encode($result));
	}

	// search products
	if ($search_type == 'products') {
		cw_load('product');

		$posted_data = $_POST['posted_data'];
		$sort_direction = isset($_POST['sort_direction']) ? $_POST['sort_direction'] : 1;

		$sort_fields = cw_product_get_sort_fields();
		$sort_fields['quantity'] = cw_get_langvar_by_name("lbl_in_stock");

		if (is_array($posted_data) && !empty($posted_data)) {
		    $date_fields = array (
		        '' => array(
		        	'sold_date_start'		=> 0, 
		        	'sold_date_end' 		=> 1, 
		        	'creation_date_start' 	=> 0, 
		        	'creation_date_end' 	=> 1, 
		        	'modify_date_start' 	=> 0, 
		        	'modify_date_end'		=> 1
		    	),
		    );

		    $multiple_fields = array(
		        '' => array('categories', 'avail_types', 'product_types', 'warehouse_customer_id'),
		    );
		    cw_core_process_date_fields($posted_data, $date_fields, $multiple_fields);

		    $search_data = $posted_data;
		    $search_data['sort_direction'] = $sort_direction;
		    $search_data['limit'] = SEARCH_LIMIT_FOR_AUTOCOMPLETE;
		    $search_data['flat_search'] = 1;

		    $info_type = 0;

		    list ($products, $navigation, $product_filter) = 
		    	cw_func_call(
		    		'cw_product_search', 
		    		array(
		    			'data' 			=> $search_data, 
		    			'user_account' 	=> $user_account, 
		    			'current_area' 	=> 'A', 
		    			'info_type' 	=> $info_type
		    		)
		    	);

		    $result = array();

		    if (is_array($products) && count($products)) {
		    	
		    	foreach ($products as $product) {
		    		$result[] = array('id' => $product['product_id'], 'name' => $product['product']);
		    	}
		    }
		    
		    exit(json_encode($result));
		}
	}
}

exit(json_encode(array()));
