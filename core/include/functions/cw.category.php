<?php
function cw_category_delete($cat, $is_show_process = false) {
	global $tables;

	// can't delete root category
	if ($cat == 1) {
		return 1;
	}

    $path = cw_category_get_path($cat);
    $parent_category_id = cw_query_first_cell("select parent_id from $tables[categories] where category_id='$cat'");

	# Delete products from subcategories
	$prods = db_query("select $tables[products_categories].product_id from $tables[products_categories], $tables[categories_parents] where ($tables[categories_parents].parent_id ='$cat' or $tables[categories_parents].category_id = '$cat') and $tables[products_categories].category_id=$tables[categories_parents].category_id and $tables[products_categories].main=1");

    cw_load( 'image', 'attributes', 'product');

    $index = 0;
	if ($prods)
	while ($prod = db_fetch_array($prods)) {
	    cw_call('cw_delete_product', array('product_id' => $prod['product_id'], 'update_categories' => false));
		if ($index++ % 10 == 0) cw_flush('.');
		if ($index % 1000 == 0) cw_flush('<br/>');
    }
	db_free_result($prods);

	# Delete subcategories
	$subcats = cw_category_get_subcategory_ids($cat);
    $subcats[] = $cat;

	if (is_array($subcats)) {

		db_exec("DELETE FROM $tables[categories] WHERE category_id IN (?)", array($subcats));
		db_exec("DELETE FROM $tables[categories_stats] WHERE category_id IN (?)", array($subcats));
		db_exec("DELETE FROM $tables[products_categories] WHERE category_id IN (?)", array($subcats));
		db_exec("DELETE FROM $tables[categories_subcount] WHERE category_id IN (?)", array($subcats));
		db_exec("DELETE FROM $tables[featured_products] WHERE category_id IN (?)", array($subcats));
		db_exec("DELETE FROM $tables[categories_lng] WHERE category_id IN (?)", array($subcats));
		db_exec("DELETE FROM $tables[categories_memberships] WHERE category_id IN (?)", array($subcats));
		db_exec("DELETE FROM $tables[categories_parents] WHERE category_id IN (?)",array($subcats));
		db_exec("DELETE FROM $tables[categories_parents] WHERE parent_id IN (?)",array($subcats));

		cw_image_delete($subcats, 'categories_images_thumb');

        cw_call('cw_attributes_cleanup', array($subcats,'C'));

		if ($index++ % 10 == 0) cw_flush('.');
		if ($index % 1000 == 0) cw_flush('<br/>');
	}
	
    cw_recalc_subcat_count($path, $is_show_process);
	return $parent_category_id;
}

# Recalculate child categories count in Categories counts table
function cw_recalc_subcat_count($category_id = 0, $tick = 0) {
	global $tables, $config;

    if ($tick > 0)
        cw_display_service_header('lbl_recalc_subcat_count').'<br/>';
    elseif ($tick > 0 && $start % $tick == 0)
        cw_flush('.');

    if (!$category_id)
        $where = '';
    elseif(is_array($category_id)) {
        if (is_array(current($category_id)))
        foreach ($categoryid as $k => $v)
            $categoryid[$k] = $v['category_id'];
        $where ="where category_id in ('".implode("', '", $category_id)."')";
    }
    else
        $where = "where parent_id='$category_id' or category_id='$category_id'";

    $subcategories = cw_query_column("select category_id from $tables[categories] $where");
    db_query("delete from $tables[categories_subcount] where category_id in ('".implode("', '", $subcategories)."')");
    $subcategories_list = implode("','", $subcategories);

// Calculates memberships subcategories/products' counts as '0-membership' count plus only unique subcategories/products
// for that membership (vs 0-mbrs) excluding all common with '0-membership' scats/prods 
// Status 1 -- only enabled scats/prods
// Status 0 -- All scats/prods

	$counts="select cm.category_id, cm.membership_id,
		count(DISTINCT if(ifnull(cms.membership_id,-1)=-1,NULL,sc.category_id)) as subcategory_count,
		count(DISTINCT if(ifnull(cms.membership_id,-1)=-1,NULL,if(ifnull(sc.status,0)=0,NULL,sc.category_id))) as subcategory_count_avail,
		count(DISTINCT if(ifnull(pm.membership_id,-1)=-1,NULL,p.product_id)) as product_count,
		count(DISTINCT if(ifnull(pm.membership_id,-1)=-1,NULL,if(ifnull(pr.status,0)=0,NULL,p.product_id))) as product_count_avail
		from $tables[categories_memberships] cm
		left join $tables[categories] sc on cm.category_id = sc.parent_id
		left join $tables[categories_memberships] cms on sc.category_id=cms.category_id and cms.membership_id in (0,cm.membership_id)
		left Join  $tables[products_categories] p on p.category_id=cm.category_id
		left Join  $tables[products] pr on p.product_id=pr.product_id
		left Join  $tables[products_memberships] pm on pm.product_id=p.product_id and pm.membership_id in (0,cm.membership_id)
		where cm.category_id in ('$subcategories_list')
		and cm.membership_id is not null and cm.category_id is not null
		group by cm.category_id, cm.membership_id";
	$counts=cw_query_hash($counts,array('category_id','membership_id'),false);

	$i=0; if (!empty($counts))
	foreach ($counts as $category_id => $data) foreach ($data as $membership_id => $cnts) {
		extract ($cnts);
		$status=0; $arr=compact('category_id','membership_id','status','subcategory_count','product_count');
		cw_array2insert('categories_subcount', $arr, true);
		$status=1; $subcategory_count=$subcategory_count_avail; $product_count=$product_count_avail;
		$arr=compact('category_id','membership_id','status','subcategory_count','product_count');
		cw_array2insert('categories_subcount', $arr, true);

        if ($tick > 0 && $i % $tick == 0)
            cw_flush(". "); 
		  $i++;

	}

# kornev, used only in the shop - so only the shop amount is calculated
# general products count
    cw_load('product');
    $count_products=cw_query_first ("select group_concat(distinct membership_id) as ind from $tables[products_memberships]");
    $count_products=array_fill_keys(explode(',',$count_products['ind']),0);
    db_query("delete from $tables[categories_subcount] where category_id = 0");
    if (is_array($count_products))
    foreach($count_products as $k=>$v) {
        $product_count = intval (cw_func_call('cw_product_search', array('data' => array('count' => 1), 'user_info' => array('membership_id' => $k), 'current_area' => 'C')));
        cw_array2insert('categories_subcount', array('category_id' => 0, 'membership_id' => $k, 'status' => 2, 'product_count' => $product_count), true);
    }
}

function cw_category_disable_subcategories($category_id) {
    global $tables;

    $subcategories = cw_category_get_subcategory_ids($category_id);
    db_query("update $tables[categories] set status=0 where category_id in ('".implode("', '", $subcategories)."')");
}

function cw_category_check_disabled_parents($category_id) {
    global $tables;

    $path = cw_category_get_path($category_id);
    cw_recalc_subcat_count($path);
    if (is_array($path))
        foreach($path as $v)
        if ($v && $v != $category_id) {
            $is_parent_avail = cw_query_first_cell("select status from $tables[categories] where category_id='$v'");
            if (!$is_parent_avail) cw_category_disable_subcategories($v);
        }
}

function cw_category_get_location($cat_id, $target = '', $with_root = 0, $alt_path = 0) {
    global $smarty;

    $path = cw_category_get_path_categories($cat_id);
    $location = array();

    if (is_array($path)) {
        if ($with_root) $location[] = array(cw_get_langvar_by_name('lbl_root_level'), 'index.php?target='.$target);
        foreach($path as $val) {
            if ($alt_path)
                $location[] = array($val['category'], cw_call('cw_core_get_html_page_url', array(array('var' => 'index', 'cat' => $val['category_id']))));
            else {
                $params = array();
                if ($target) $params[] = 'target='.$target;
                $params[] = 'cat='.$val['category_id'];
                $location[] = array($val['category'], 'index.php?'.implode('&', $params));
            }
        }
    }
    return $location;
}

/*
* if we enable a subcategory - we do the same for parents
* if we disable subcategory - we disable the subcategories of this subcategory
*/
function cw_category_update_status($category_id, $status) {
    global $tables;

    if (is_array($status)) if (in_array(1,$status)) $status=1; else $status=0;

    if ($status==1) {
		$parents = cw_category_get_path($category_id);
		foreach($parents as $cat_id) cw_array2update ('categories', array('status' => 1), "category_id=$cat_id");
    } else {
		$subs = cw_category_get_subcategory_ids($category_id);
		foreach($subs as $cat_id) cw_array2update ('categories', array('status' => 0), "category_id=$cat_id");
    }
}

function cw_category_get_subcategories($cat = 0, $current_category = null, $flag='') {
    global $current_area, $user_account, $tables;

    $search = array('parent_id' => $cat, 'all' => 1);
    if (in_array(AREA_TYPE, array('C', 'B')) && $flag != 'all') {
        $search['active'] = 1;
        $search['membership_id'] = $user_account['membership_id'];
    }

    list($categories, $navigation) = cw_func_call('cw_category_search', array('data' => $search));

    if ($current_category)
        $path = cw_category_get_path($current_category);

    if (is_array($categories))
    foreach($categories as $k=>$v) {
        if ($current_category && in_array($v['category_id'], $path)) $categories[$k]['selected'] = true;
    }
    return $categories;
}

function cw_category_get($params, $return = null) {
    extract($params);

	global $current_area, $tables, $current_language, $user_account;
	global $app_main_dir, $current_location, $config;

	$cat = intval($cat);

    $fields = $from_tbls = $query_joins = $where = array();
# kornev, merge standart and additional variables
    if ($return)
    foreach ($return as $saname => $sadata)
        if (isset($$saname) && is_array($$saname) && empty($$saname)) $$saname = $sadata;

    $from_tbls[] = 'categories';
    $fields[] = "$tables[categories].*";

    $query_joins['categories_subcount'] = array(
        'on' => "$tables[categories_subcount].category_id = $tables[categories].category_id".(($current_area == "C" || $current_area == "B")?" AND $tables[categories_subcount].membership_id = '".@$user_account['membership_id']."'":""),
    );
    $query_joins['c_memb'] = array(
		'tblname'=> 'categories_memberships',
        'on' => "c_memb.category_id = $tables[categories].category_id",
    );

	if ($current_area == "C" || $current_area == "B")
		$fields[] = "$tables[categories_subcount].subcategory_count";
	else
		$fields[] = "MAX($tables[categories_subcount].subcategory_count) as subcategory_count";

    $lang = $lang?$lang:$current_language;
    $query_joins['c_lng'] = array(
		'tblname' => 'categories_lng',
        'on' => "c_lng.category_id = $tables[categories].category_id and c_lng.code='$lang'",
    );
    $fields[] = "IFNULL(c_lng.category, $tables[categories].category) as category";
    $fields[] = "IFNULL(c_lng.description, $tables[categories].description) as description";
    $fields[] = "$tables[categories].category as category_name_orig";

    if (in_array($current_area, array('C', 'B'))) {
		$where[] = "$tables[categories].status=1";
        $where[] = "(c_memb.membership_id IN (0, '".$user_account['membership_id']."') OR c_memb.membership_id IS NULL)";
    }

    $where[] = "$tables[categories].category_id='$cat'";
    $groupbys[] = "$tables[categories].category_id";

    $search_query = cw_db_generate_query($fields, $from_tbls, $query_joins, $where, $groupbys, $having, $orderbys);
	$category = cw_query_first($search_query);

	if (!$category) return false;

    $category['attribute_class_ids'] = cw_func_call('cw_items_attribute_classes_get', array('item_id'=>$category['category_id'], 'item_type' => 'C'));

    $category['membership_ids'] = cw_query_key("select membership_id from $tables[categories_memberships] where category_id = '$cat'");
    $category['image'] = cw_image_get('categories_images_thumb', $cat);

	return $category;
}

function cw_category_generate_path($category_id, &$path) {
    global $tables;

    $parent = cw_query_first_cell($sql="select parent_id from $tables[categories] where category_id='$category_id'");
    if ($parent) {
        cw_category_generate_path($parent, $path);
        $path[] = $parent;
    }
}

function cw_category_update_path($cat, $rec = false) {
    global $tables;

    $path = array();
    $level = 0;
    cw_category_generate_path($cat, $path);
    $path[] = $cat;
    db_query("delete from $tables[categories_parents] where category_id='$cat'");
    foreach($path as $val)
        cw_array2insert('categories_parents', array('category_id' => $cat, 'parent_id' => $val, 'level' => $level++));

    if ($rec) {
        $subcats = cw_category_get_subcategory_ids($cat);
        if (count($subcats))
        foreach($subcats as $scat_id)
            cw_category_update_path($scat_id);
    }
}

function cw_category_get_path_categories($cat) {
     global $tables;

    if (!is_array($cat)) $cat = array($cat);

    $path = cw_query($sql="select c.category, c.category_id from $tables[categories] as c, $tables[categories_parents] as cp where c.category_id=cp.parent_id and cp.category_id in ('".implode("', '", $cat)."') group by c.category_id order by level asc");
    return $path;
}

// Get all parents
function cw_category_get_path($cat) {
     global $tables;

    if (!is_array($cat)) $cat = array($cat);

    $path = cw_query_column("select parent_id from $tables[categories_parents] where category_id in ('".implode("', '", $cat)."') group by parent_id order by level");
    return $path;
}

// Get all subcategories
function cw_category_get_subcategory_ids($cat) {
    global $tables;

    if (!is_array($cat)) $cat = array($cat);

    return cw_query_column("select category_id from $tables[categories_parents] where parent_id in ('".implode("', '", $cat)."') group by category_id");

}

function cw_category_category_path($category_id) {
    global $tables;

    $data = cw_query_column("select category FROM $tables[categories] as c, $tables[categories_parents] as cp where cp.category_id='$category_id' and c.category_id=cp.parent_id order by cp.level");
    return $data;
}

function cw_category_get_short_list() {
    global $tables, $current_language;

    return cw_query("select c.category_id, IFNULL(lng.category, c.category) as category from $tables[categories] as c
	left join $tables[categories_lng] as lng on lng.category_id=c.category_id and lng.code='$current_language'
	where c.status=1 and c.short_list=1 order by category");
}

function cw_category_get_all() {
	global $tables, $current_language, $current_area, $user_account;

    $fields = array();
    $where = array();
    $orderbys = array();
    $groupbys = array();

    $from_tbls = array('categories');

    $fields[] = "$tables[categories].category_id";
    $fields[] = "$tables[categories].parent_id";
    $fields[] = "$tables[categories].category";
    $fields[] = "$tables[categories].order_by";

    if (in_array($current_area, array('C', 'B'))) {
        $where[] = "($tables[categories_memberships].membership_id = '0' OR $tables[categories_memberships].membership_id = '$user_account[membership_id]')";
        $orderbys[] = "$tables[categories].order_by, $tables[categories].category";

        $where[] = "$tables[categories].status = 1";
        $query_joins['categories_subcount'] = array(
            'on' => "$tables[categories_subcount].category_id = $tables[categories].category_id AND $tables[categories_subcount].membership_id = '$user_account[membership_id]'",
        );
        $fields[] = "$tables[categories_subcount].subcategory_count";
        $fields[] = "$tables[categories_subcount].product_count";
    }
    else {
        $query_joins['categories_subcount'] = array(
            'on' => "$tables[categories_subcount].category_id = $tables[categories].category_id",
        );
    }

    $query_joins['categories_lng'] = array(
        'on' => "$tables[categories_lng].code='$current_language' AND $tables[categories_lng].category_id=$tables[categories].category_id",
    );
    $query_joins['categories_memberships'] = array(
        'on' => "$tables[categories_memberships].category_id = $tables[categories].category_id",
    );

    $fields[] = "IFNULL($tables[categories_lng].category, $tables[categories_lng].category) as category";

    $groupbys[] = "$tables[categories].category_id";
    $search_query = cw_db_generate_query($fields, $from_tbls, $query_joins, $where, $groupbys, null, $orderbys);
    $categories = cw_query($search_query);

    if (!$categories) return array();

	#
	# Add category path

//    $sort_by = array();
	foreach($categories as $k => $v){
        $path = cw_category_get_path($v['category_id']);
		if (is_array($path)){
			$path_name = array();
			foreach($path as $kk => $vv)
				$path_name[] = cw_query_first_cell($sql="select IFNULL(lng.category, c.category) as category from $tables[categories] as c left join $tables[categories_lng] as lng on lng.category_id=c.category_id and lng.code='$current_language' where c.category_id = $vv");
		}
		$categories[$k]['category_path'] = implode('/',$path_name);
//		$sort_by[] = $categories[$k]['category_path'];

	}
//	array_multisort($sort_by, $categories, SORT_ASC, SORT_STRING);
    usort($categories, 'cw_category_sort_by_path');

	return $categories;
}

function cw_category_sort_by_path($el_1, $el_2) {
    return strcmp($el_1['category_path'], $el_2['category_path']) > 0;
}

function cw_category_search($params, $return = null) {
    extract($params);

    global $tables, $current_language;

    $fields = $from_tbls = $query_joins = $where = $groupbys = $having = $orderbys = array();

    $from_tbls[] = 'categories';

# kornev, merge standart and additional variables
    if ($return)
    foreach ($return as $saname => $sadata)
        if (isset($$saname) && is_array($$saname) && empty($$saname)) $$saname = $sadata;

    $query_joins['categories_lng'] = array(
        'on' => "$tables[categories_lng].category_id = $tables[categories].category_id and $tables[categories_lng].code='$current_language'
",
    );

    $query_joins['categories_subcount'] = array(
        'on' => "$tables[categories_subcount].category_id = $tables[categories].category_id",
    );
    $fields[] = "$tables[categories_subcount].subcategory_count";
    $fields[] = "$tables[categories_subcount].product_count";

    $query_joins['categories_subcount_1'] = array(
        'tblname' => 'categories_subcount',
        'on' => "categories_subcount_1.category_id = $tables[categories].category_id and categories_subcount_1.status=1",
    );
    $fields[] = "categories_subcount_1.subcategory_count as subcategory_count_web";
    $fields[] = "categories_subcount_1.product_count as product_count_web";

    $fields[] = "$tables[categories].*";
    $fields[] = "IFNULL($tables[categories_lng].category, $tables[categories].category) as category";
    $fields[] = "IFNULL($tables[categories_lng].description, $tables[categories].description) as description";

    $where[] = 1;
    if ($data['substring'])
        $where[] = "(IFNULL($tables[categories_lng].category, $tables[categories].category) like '%$data[substring]%' or IFNULL($tables[categories_lng].description, $tables[categories].description) like '%$data[substring]%')";

    if (isset($data['parent_id']))
        $where[] = "$tables[categories].parent_id='".intval($data['parent_id'])."'";

    if (isset($data['active']) || isset($data['status'])) {
		$data['status'] = intval($data['status'] || $data['active']);
		$where[] = "$tables[categories].status = '$data[status]'";
    }

    if (isset($data['membership_id'])) {
        $query_joins['categories_memberships'] = array(
            'on' => "$tables[categories_memberships].category_id = $tables[categories].category_id",
        );
        $where[] = "$tables[categories_memberships].membership_id='$data[membership_id]'";
    }

    $groupbys[] = "$tables[categories].category_id";
    $orderbys[] = "$tables[categories].order_by";
    $search_query_count = cw_db_generate_query('count(*)',  $from_tbls, $query_joins, $where, $groupbys, $having, array(), 0);
    $search_query = cw_db_generate_query($fields, $from_tbls, $query_joins, $where, $groupbys, $having, $orderbys);
    $_res = db_query($search_query_count);
    $total_items = db_num_rows($_res);
    db_free_result($_res);

    $page = $data['page'];
    if ($data['count'])
        return $total_items;
    elseif($data['limit'])
        $limit_str = " LIMIT $data[limit]";
    elseif ($data['all'])
        $limit_str = '';
    else {
        $navigation = cw_core_get_navigation($target, $total_items, $page);
        $limit_str = " LIMIT $navigation[first_page], $navigation[objects_per_page]";
    }

    $categories = cw_query($search_query.$limit_str);
    if ($data['all'])
        $navigation = cw_core_get_navigation($target, count($categories), $page, count($categories));

    if (is_array($categories))
    foreach($categories as $k=>$v) {
        $categories[$k]['subcounts'] = cw_query_hash("SELECT cs.status, sum(cs.subcategory_count) as subcategory_count, 
			sum(cs.product_count) as product_count
        FROM $tables[categories_subcount] cs, $tables[categories_parents] cp
        WHERE cs.membership_id='$data[membership_id]' AND
			cs.category_id = cp.category_id AND cp.parent_id='$v[category_id]'
			GROUP BY status", 'status',false,false);
	}

    return array($categories, $navigation);
}

// get count of products for category
function cw_category_product_count($category_id) {
	global $tables, $user_account;
	
	if (empty($category_id)) return 0;
	
	$membership_id = isset($user_account['membership_id']) && !empty($user_account['membership_id']) ? $user_account['membership_id'] : 0;
	
	return cw_query_first_cell("SELECT product_count 
								FROM $tables[categories_subcount] 
								WHERE status = 1 AND category_id = " . $category_id . " AND membership_id = '" . $membership_id . "'");
}

// get category clean url
function cw_category_category_url($category_id) {
	global $tables;
	
	if (empty($category_id)) return "";
	
	return cw_call('cw_core_get_html_page_url', array(array('var' => 'index', 'cat' => $category_id)));
}

function cw_featured_categories_get($current_language) {
     
    global $tables;

    $featured_categories = cw_query("select $tables[categories].category_id,
    IF($tables[categories_lng].category_id IS NOT NULL AND $tables[categories_lng].category != '',
        $tables[categories_lng].category, $tables[categories].category) as category
    from $tables[categories]
    LEFT JOIN $tables[categories_lng]
    ON $tables[categories_lng].code='$current_language' AND $tables[categories_lng].category_id=$tables[categories].category_id
    LEFT JOIN $tables[categories_memberships]
    ON $tables[categories_memberships].category_id = $tables[categories].category_id
    where $tables[categories].status=1 and $tables[categories].featured=1
    and $tables[categories_memberships].membership_id IN(0, '$user_account[membership_id]')
    group by $tables[categories].category_id order by $tables[categories].order_by, $tables[categories].category");

    if (!empty($featured_categories) && count($featured_categories)) {
        foreach ($featured_categories as $key => $category) {
            $featured_categories[$key]['image'] = cw_image_get('categories_images_thumb', $category['category_id']);
        }
    }
    return $featured_categories;
}

function cw_get_all_categories_for_select() {

    if (!($all_categories = cw_cache_get(0, __FUNCTION__))) {
        $all_categories = cw_category_get_all();
        cw_cache_save($all_categories, 0, __FUNCTION__);
    }

    return $all_categories;
}

?>
