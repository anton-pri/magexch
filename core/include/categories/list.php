<?php
cw_load('image', 'category', 'group_edit');

$categories_to_delete = &cw_session_register('categories_to_delete', array());
$search_data = &cw_session_register('search_data', array());

if ($action == 'search') {
    $search_data['categories'] = $posted_data;
    cw_header_location('index.php?target='.$target.'&mode=search');
}

if ($action == 'list') {
    if (empty($delete_arr)) {
        $top_message = array('content' => cw_get_langvar_by_name('lbl_please_select_categories_for_editing'), 'type' => 'I');
        cw_header_location('index.php?target='.$target);
    }
    $cat_ids = array_keys($delete_arr);
    $ge_id = cw_group_edit_add($cat_ids);
    $cat_id = $cat_ids[0];
    cw_header_location('index.php?target='.$target.'&mode=edit&cat='.$cat_id.'&ge_id='.$ge_id);
}

if ($action == 'apply' && is_array($posted_data)) {
    foreach ($posted_data as $k => $v) {
        $query_data = array(
            'order_by' => intval($v['order_by']),
        );
        cw_array2update('categories', $query_data, "category_id='".$k."'");
        cw_category_update_status($k, $v['status']);
    }

    if ($cat) {
        $path = cw_category_get_subcategory_ids($cat);
        if (!empty($path))
            cw_recalc_subcat_count($path);
    }
    else
        cw_recalc_subcat_count();

    $top_message = array('content' => cw_get_langvar_by_name('msg_adm_categories_upd'), 'type' => 'I');

    cw_header_location("index.php?target=$target".($mode?'&mode='.$mode:'')."&cat=$cat&js_tab=$js_tab");
}

if ($action == 'delete') {
    if ($confirmed == "Y") {
# kronev, for the big amount of products - the
        cw_display_service_header('lbl_delete_categories').'<br/>';

        if (is_array($categories_to_delete))
        foreach($categories_to_delete as $cat)
            $parent_category_id = cw_call('cw_category_delete', array($cat, true));

        $top_message = array('content' => cw_get_langvar_by_name('msg_adm_category_del'), 'type' => 'I');
        cw_header_location('index.php?target=categories&cat='.$parent_category_id);
    }
    else {
        $categories_to_delete = (is_array($delete_arr) ? array_keys($delete_arr) : null);
        cw_header_location("index.php?target=categories&cat=$cat&mode=delete");
    }
}
if (empty($featured_type)) $featured_type = 'featured_products';

if ($action == 'update_product_section' && is_array($posted_data)) {
        foreach ($posted_data as $product_id=>$v) {
		    $query_data = array(
			    "avail" => ($v['avail']?1:0),
				"product_order" => intval($v['product_order'])
            );
			cw_array2update($featured_type, $query_data, "product_id='$product_id' AND category_id='$cat'");
        }
		$top_message['content'] = cw_get_langvar_by_name("msg_adm_featproducts_upd");
    cw_header_location("index.php?target=$target&cat=$cat&js_tab=$js_tab");
}

if ($action == 'delete_product_section' && is_array($posted_data)) {
		foreach ($posted_data as $product_id=>$v) {
		    if (empty($v['to_delete'])) continue;
    		db_query ("DELETE FROM ".$tables[$featured_type]." WHERE product_id='$product_id' AND category_id='$cat'");
		}
	    $top_message['content'] = cw_get_langvar_by_name("msg_adm_featproducts_del");

    cw_header_location("index.php?target=$target&cat=$cat&js_tab=$js_tab");
}

if ($action == 'add_product_section' && !empty($newproduct_id)) {
        $products = explode(" ", $newproduct_id);
        if (is_array($products))
        foreach($products as $product_id) {
		$newavail = $newavail?1:0;
		if ($neworder == "") {
			$maxorder = cw_query_first_cell("SELECT MAX(product_order) FROM ".$tables[$featured_type]." WHERE category_id='$cat'");
			$neworder = $maxorder + 10;
		}
		    if (cw_query_first("SELECT product_id FROM $tables[products] WHERE product_id='$product_id'"))
			    db_query("REPLACE INTO ".$tables[$featured_type]." (product_id, product_order, avail, category_id) VALUES ('$product_id','$neworder','$newavail', '$cat')");
		}
        $top_message['content'] = cw_get_langvar_by_name("msg_adm_featproducts_upd");

    cw_header_location("index.php?target=$target&cat=$cat&js_tab=$js_tab");
}


if ($action == 'delete_one_cat') {
    $categories_to_delete = array($cat);
    cw_header_location("index.php?target=categories&cat=$cat&mode=delete");
}

$smarty->assign('js_tab', $js_tab);

$featured_types = array('featured_products', 'new_arrivals');
foreach($featured_types as $tbl) {
    $products = cw_query ("SELECT ".$tables[$tbl].".*, $tables[products].product from ".$tables[$tbl].", $tables[products] where ".$tables[$tbl].".product_id=$tables[products].product_id AND ".$tables[$tbl].".category_id='$cat' order by ".$tables[$tbl].".product_order");
    $smarty->assign ($tbl, $products);
}
$smarty->assign('main', 'categories');

if ($mode == 'delete' && $confirmed != "Y" && is_array($categories_to_delete)) {
    $subcats = $ids = array();
    foreach($categories_to_delete as $val) {
        $ids[] = $val;
        $curr_subcats = cw_query_column("SELECT c.category_id FROM $tables[categories] as c, $tables[categories_parents] as cp WHERE c.category_id=cp.parent_id and cp.parent_id='$val'");
        if ($curr_subcats) $ids = array_merge($ids, $curr_subcats);
    }
    $subcats = cw_query("SELECT category_id, category FROM $tables[categories] WHERE category_id in ('".implode("', '", $ids)."')");

    if (is_array($subcats))
    foreach ($subcats as $k=>$v) {
# kronev, for the big amount of products - there are no any sense to display it
        $subcats[$k]['products_count'] = cw_query_first_cell("SELECT count(*) FROM $tables[products_categories], $tables[products] WHERE $tables[products_categories].category_id='$v[category_id]' AND $tables[products_categories].product_id=$tables[products].product_id AND $tables[products_categories].main=1");
//        $subcats[$k]['products_count'] = (is_array($subcats[$k]['products']) ? count($subcats[$k]['products']) : 0);
    }
    $smarty->assign('subcats', $subcats);
    $smarty->assign('main', 'category/delete_confirmation');
}
elseif ($mode == 'search') {
    $search_data['categories']['page'] = $page;

    list($categories, $navigation) = cw_func_call('cw_category_search', array('data' => $search_data['categories']));
    $navigation['script'] = 'index.php?target='.$target.'&mode=search';

    $smarty->assign('navigation', $navigation);
    $smarty->assign('categories', $categories);
    $smarty->assign('search_prefilled', $search_data['categories']);

    $smarty->assign('js_tab', 'search');
    $smarty->assign('main', 'categories');
}

$location[] = array(cw_get_langvar_by_name('lbl_categories'), '');

$smarty->assign('subcategories', cw_category_get_subcategories($cat?$cat:0));
$smarty->assign('current_category', cw_func_call('cw_category_get', array('cat' => $cat)));
$smarty->assign('category_location', cw_category_get_location($cat, 'categories', 1));
$smarty->assign('cat', $cat);
