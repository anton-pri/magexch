<?php
include $app_main_dir.'/include/security.php';

if(!$addons['estore_gift'])
	cw_header_location('index.php');

$store_search_data_w = cw_session_register("store_search_data_w", array());
if (!empty($search_data) && $mode == "search") {
	$store_search_data_w = $search_data;
	cw_header_location("index.php?target=wishlists&mode=search");
} else {
	$search_data = $store_search_data_w;
}

$smarty->assign('main', "wishlists");
$location[] = array(cw_get_langvar_by_name("lbl_search_wishlists"), "");

# Search wishlists
if ($mode == "search" && !empty($search_data)) {
	$where = array();
	if (!empty($search_data['customer_id'])) {
		$where[] = "($tables[wishlist].customer_id ='$customer_id')";
	}
	if (!empty($search_data['sku'])) {
		$where[] = "$tables[products].productcode = '$search_data[sku]'";
	}
	if (!empty($search_data['product_id'])) {
		$where[] = "$tables[products].product_id = '$search_data[product_id]'";
	}
	if (!empty($search_data['product'])) {
		$where[] = "($tables[products].product LIKE '%$search_data[product]%' OR $tables[products].descr LIKE '%$search_data[product]%' OR $tables[products].fulldescr LIKE '%$search_data[product]%')";
	}

	$where_str = "";
	if (!empty($where))
		$where_str = " AND ".implode(" AND ", $where);

	$_res = db_query("SELECT COUNT($tables[wishlist].wishlist_id) FROM $tables[wishlist], $tables[products], $tables[customers] WHERE $tables[wishlist].product_id = $tables[products].product_id AND $tables[wishlist].customer_id=$tables[customers].customer_id".$where_str." GROUP BY $tables[wishlist].customer_id");
	$total_items = db_num_rows($_res);
	db_free_result($_res);

    $navigation = cw_core_get_navigation($target, $total_items, $page);
    $navigation['script'] = "index.php?target=wishlists&mode=search";
    $smarty->assign('navigation', $navigation);

	$wishlists = cw_query("SELECT $tables[wishlist].wishlist_id, $tables[customers].*, COUNT($tables[products].product_id) as products_count FROM $tables[wishlist], $tables[products], $tables[customers] WHERE $tables[wishlist].product_id = $tables[products].product_id AND $tables[wishlist].customer_id=$tables[customers].customer_id".$where_str." GROUP BY $tables[wishlist].customer_id LIMIT $navigation[first_page], $navigation[objects_per_page]");

	if (!empty($wishlists)) {
		$ids = array();
		foreach ($wishlists as $v) {
			$ids[] = addslashes($v['customer_id']);
		}
		$counts = cw_query_hash("SELECT $tables[wishlist].customer_id, COUNT($tables[products].product_id) as products_count FROM $tables[wishlist], $tables[products] WHERE $tables[wishlist].product_id = $tables[products].product_id AND $tables[wishlist].customer_id IN ('".implode("','", $ids)."') GROUP BY $tables[wishlist].customer_id", "customer_id", false, true);
		foreach ($wishlists as $k => $v) {
			$wishlists[$k]['products_count'] = intval($counts[$v['customer_id']]);
		}

		$smarty->assign('wishlists', $wishlists);
	}

# Display wishlist
} elseif ($mode == "wishlist" && $customer) {
	$wishlist = cw_query("SELECT * FROM $tables[wishlist], $tables[products], $tables[customers] WHERE $tables[wishlist].product_id = $tables[products].product_id AND $tables[wishlist].customer_id=$tables[customers].customer_id AND $tables[wishlist].customer_id='$customer' ");
	if (empty($wishlist))
		cw_header_location("index.php?target=wishlists");
	foreach ($wishlist as $k => $v) {
		if (!empty($v['options'])) {
			$v['options'] = unserialize($v['options']);
			list($variant, $v['product_options']) = cw_get_product_options_data($v['product_id'], $v['options'], $v['membership_id']);
			if(!empty($variant))
				$v = cw_array_merge($v, $variant);
			$wishlist[$k] = $v;
		}
	}

	$location[count($location)-1][1] = "wishlists.php";
	$location[] = array(cw_get_langvar_by_name("lbl_wish_list"), "");

	$smarty->assign('wishlist', $wishlist);
	$smarty->assign('main', "wishlist");
}

$smarty->assign('mode', $mode);
$smarty->assign('search_data', $search_data);
?>
