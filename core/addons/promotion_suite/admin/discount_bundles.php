<?php
    if (!isset($addons['promotion_suite'])) {
        return;
    }

	if (AREA_TYPE != 'A') {
    	return;
    }

    $addon_actions = array(
        'enable'    => 'cw_ps_bundle_process',
        'disable'   => 'cw_ps_bundle_process',
        'drop' 		=> 'cw_ps_bundle_process',
        'build' 	=> 'cw_ps_bundle_build',
        'index'     => 'cw_ps_bundle_index'
    );

	$smarty->assign('main', 'discount_bundles');
    
    if (empty($action) || !isset($addon_actions[$action]) || !function_exists($addon_actions[$action])) {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            return cw_call('cw_ps_bundle_index');
        }
        return;
    }


    $smarty->assign('action', $action);
	
	cw_call($addon_actions[$action], array($action, $category, $category_src, $filter, $products_number, $discount, $disctype));

	if ($_SERVER['REQUEST_METHOD'] == 'POST')
		cw_header_location("$app_catalogs[admin]/index.php?target=$target");

    
    
function cw_ps_bundle_index() {

}


function cw_ps_bundle_process($action, $category, $category_src, $filter, $products_number, $discount, $disctype) {
	global $tables;

	$fields[] = 'offer_id';
	$from_tbls['o'] = 'ps_offers';
	$where[] = 'pid>0';
	
	if ($filter['auto']) $where['auto'] = "auto=1";
	if ($filter['manual']) $where['auto'] = "auto=0";
	if ($filter['auto'] && $filter['manual']) unset($where['auto']);

	if ($category !=0) {
		cw_load('category');
		$subcats = cw_call('cw_category_get_subcategory_ids', array($category));
		$from_tbls['pc'] = 'products_categories';
		$where[] = 'product_id=pid';
		$where[] = 'category_id IN ("'.join('","',$subcats).'")';
	}

	$sql = cw_db_generate_query($fields, $from_tbls, $query_joins='', $where);


	$offers = cw_query_column($sql);

	foreach ($offers as $offer_id) {
		switch ($action) {
		case "drop":
			cw_call('cw_ps_offer_delete', array($offer_id));
			break;
		case "enable":
		case "disable":
			db_query("UPDATE $tables[ps_offers] SET active=".($action=='enable'?'1':'0')." WHERE offer_id='$offer_id'");
		}
	}
	
	$msg = count($offers).' offer(s) were processed';
	cw_add_top_message($msg);
	
}

function cw_ps_bundle_build($action, $category, $category_src, $filter, $products_number, $discount, $disctype) {
	global $tables;

	$from_tbls['pc'] = 'products_categories';
	$fields[] = 'pc.product_id';
	
	if ($products_number<2) $products_number = 2;
	
	if ($category != 0) {
		cw_load('category');
		$subcats = cw_call('cw_category_get_subcategory_ids', array($category));
		$where[] = 'pc.category_id IN ("'.join('","',$subcats).'")';
	}
	
	$query_joins['o'] = array(
		'tblname' => 'ps_offers',
		'on' => 'o.pid = pc.product_id'
	);

	if ($filter) {
		if ($filter['without']) {
			$where_auto[] = 'auto is NULL';
		}
		if ($filter['auto']) {
			$where_auto[] = 'auto = 1';
		}
		if ($filter['manual']) {
			$where_auto[] = 'auto = 0';
		}
		$where[] = '('.join(' OR ', $where_auto).')';
	}

	$sql = cw_db_generate_query($fields, $from_tbls, $query_joins, $where);

	$pids = cw_query_column($sql);
	$products_number--;
	
	$subcats = cw_call('cw_category_get_subcategory_ids', array($category_src));

	$where_src = '1';
	if ($category_src != 0) {
		$where_src = "pc.category_id IN ('".join("','",$subcats)."')";
	}

	foreach ($pids as $pid) {
		$new_pids = cw_query("SELECT pc.product_id as id
		FROM $tables[products_categories] pc 
		WHERE $where_src AND pc.product_id!='$pid' 
		ORDER BY RAND() LIMIT $products_number");

		$data = array(
			'discount' => $discount,
			'disctype' => $disctype,
			'bundle' => $new_pids,
			'auto' => 1
		);
		$offer_id = cw_call('cw_ps_offer_bundle_update',array($pid, $data));
	}

}
