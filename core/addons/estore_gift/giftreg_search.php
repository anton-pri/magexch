<?php
$search_data = &cw_session_register("search_data", array());

$location[] = array(cw_get_langvar_by_name("lbl_giftreg_search"), "");

if ($REQUEST_METHOD == "POST") {
#
# Update the search_data
#
	if (is_array($post_data)) {
		foreach ($post_data as $k=>$v) {
			$search_data[$k] = addslashes($v);
		}
		$search_data['start_date'] = mktime(0, 0, 0, $StartMonth, $StartDay, $StartYear);
		$search_data['end_date'] = mktime(0, 0, 0, $EndMonth, $EndDay, $EndYear);
	}
	cw_header_location("index.php?target=giftregs&mode=search");
}

# Generate start event date if it's empty
if (empty($search_data['start_date'])) {
	$current_date = time();
	$search_data['start_date'] = mktime(0,0,0,date("m",$current_date),date("d",$current_date),date("Y",$current_date));
}

# Generate end event date if it's empty
if (empty($search_data['end_date']))
	$search_data['end_date'] = mktime(0,0,0,date("m",$search_data['start_date']),date("d",$search_data['start_date']),date("Y",$search_data['start_date'])+1);

if ($mode == "search") {
#
# Search for Gift Registries
#
	$query_condition = "1";
	# Search by creator's name
	if (!empty($search_data['name']))
		$query_condition .= " AND ($tables[customers].firstname LIKE '%$search_data[name]%' OR $tables[customers].lastname LIKE '%$search_data[name]%' OR CONCAT($tables[customers].firstname, ' ', $tables[customers].lastname) LIKE '%$search_data[name]%')";

	# Search by creator's email
	if (!empty($search_data['email']))
		$query_condition .= " AND $tables[customers].email='$search_data[email]'";

	# Search by substring...
	if (!empty($search_data['substring'])) {
		$substring_condition = "$tables[giftreg_events].title LIKE '%$search_data[substring]%'";
		# ...including search in description
		if ($search_data['inc_desciption'] == "Y") {
			$substring_condition = "($substring_condition OR $tables[giftreg_events].description LIKE '%$search_data[substring]%')";
		}
		$query_condition .= " AND $substring_condition";
	}

	# Search by status
	if (!empty($search_data['status']))
		$query_condition .= " AND $tables[giftreg_events].status='$search_data[status]'";
	# else by all statuses
	else
		$query_condition .= " AND ($tables[giftreg_events].status='P' OR $tables[giftreg_events].status='G')";

	# Search events from start date through end date
	$query_condition .= " AND $tables[giftreg_events].event_date>='$search_data[start_date]' AND $tables[giftreg_events].event_date<='$search_data[end_date]'";

	$total_items_in_search = intval(cw_query_first_cell("SELECT COUNT(*) FROM $tables[giftreg_events], $tables[customers] WHERE $query_condition AND $tables[customers].customer_id=$tables[giftreg_events].customer_id"));

    $navigation = cw_core_get_navigation($target, $total_items_in_search, $page);
    $navigation['script'] = "index.php?target=giftregs&mode=search";
    $smarty->assign('navigation', $navigation);

	$result = cw_query("SELECT $tables[giftreg_events].*, $tables[customers].firstname, $tables[customers].lastname FROM $tables[giftreg_events], $tables[customers] WHERE $query_condition AND $tables[customers].customer_id=$tables[giftreg_events].customer_id ORDER BY $tables[giftreg_events].event_date LIMIT $navigation[first_page], $navigation[objects_per_page]");

	if (is_array($result))
    foreach($result as $k=>$v)
        $result[$k]['products'] = cw_query_first_cell("select count(*) from $tables[wishlist] where event_id='$v[event_id]'");

	$smarty->assign('search_result', $result);
}

$smarty->assign('search_data', $search_data);

$smarty->assign('main', 'giftreg');
?>
