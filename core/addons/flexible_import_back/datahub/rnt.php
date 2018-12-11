<?php 

cw_include('addons/flexible_import/datahub/rnt_head.php');

global $default_sortby, $display_cols;

function dh_rnt_size_translate ($size_str) {
    $_str = strtolower($size_str);
    $ret_val = 0.0;
    if (strpos($_str, 'ml') !== FALSE) {
        $_str = str_replace('ml','',$_str);
        $ret_val = floatval(trim($_str))/1000;
    } elseif (strpos($_str, 'ltr') !== FALSE) {
        $_str = str_replace('ltr','',$_str);
        $ret_val = floatval(trim($_str));
    }
    return $ret_val;
}

function dh_rnt_items_cmp($a, $b) {
    global $sortby_col, $sort_dir;

    if ($sortby_col == 'Size') { 
        $a_ch = dh_rnt_size_translate($a[$sortby_col]);
        $b_ch = dh_rnt_size_translate($b[$sortby_col]);
    } else {
        $a_ch = $a[$sortby_col];
        $b_ch = $b[$sortby_col];
    }

    if ($a_ch == $b_ch) {
        return 0;
    }

    $rev_sort = ($sort_dir == 1)?-1:1;

    return ($a_ch < $b_ch) ? -1*$rev_sort : 1*$rev_sort;
}


$remember_posted_vars = array(
'search_alu'=>'search_alu_list', 
'live_only', 
'hidden_only', 
'rating_empty_nonempty',
'review_empty_nonempty',
array('empty_rating_search', 'multi'),
array('empty_review_search', 'multi'),
'ratings_and_or',
'reviews_and_or',
'with_images_present'
);

$dh_rnt_search_params = &cw_session_register('dh_rnt_search_params',array());

if ($REQUEST_METHOD == "GET" && !empty($sortby)) {

    if (!in_array($sortby, array_keys($display_cols))) {
        $sortby = $default_sortby;
    }

    if ($dh_rnt_search_params['sortby'] != $sortby) {
        $dh_rnt_search_params['sortby'] = $sortby;
        $dh_rnt_search_params['sort_dir'] = 0;
    } else {
        $dh_rnt_search_params['sort_dir'] = $sort_dir;
    }

    cw_header_location("index.php?target=datahub_rnt&mode=search");   
    exit;
}

if ($REQUEST_METHOD == "GET" && !empty($page)) {

    $dh_rnt_search_params['page'] = $page;

    cw_header_location("index.php?target=datahub_rnt&mode=search");
    exit;
}


if ($REQUEST_METHOD == "POST") {
    if ($mode == 'opentags') {
        $dh_rnt_search_params['search_alu'] = implode(" ",array_keys($tagsprint));
//dev-live different
        $dh_rnt_search_params['live_only'] = 1;
//-------------
        $dh_rnt_search_params['empty_rating_search'] = array();
        if (empty($tags_sortby) || in_array($tags_sortby,array('name','product'))) {
            $dh_rnt_search_params['sortby'] = "$tables[datahub_main_data].name";
            $dh_rnt_search_params['sort_dir'] = isset($tags_sortdir)?$tags_sortdir:0;
        } elseif (in_array($tags_sortby, array('product_id','productid'))) {
            $dh_rnt_search_params['sortby'] = "$tables[datahub_main_data].ID"; 
            $dh_rnt_search_params['sort_dir'] = $tags_sortdir; 
        }

        cw_header_location("index.php?target=datahub_rnt&mode=search");
        exit;
    }
    if ($action == "search") {
        //fill search session array
        foreach ($remember_posted_vars as $var_name_key => $var_name_data) {
            if (is_array($var_name_data))
                $var_name_posted = $var_name_data[0]; 
            else
                $var_name_posted = $var_name_data; 

            if (!is_string($var_name_key)) {
                if (is_array($var_name_data))  
                    $var_name_saved = $var_name_data[0];
                else
                    $var_name_saved = $var_name_data;
            } else
                $var_name_saved = $var_name_key;

            settype($var_name_saved, "string"); 
            settype($var_name_posted, "string");

            $dh_rnt_search_params[$var_name_saved] = $_POST[$var_name_posted];
        }

        if ($dh_rnt_search_params['objects_per_page'] != $objects_per_page) {
            $dh_rnt_search_params['objects_per_page'] = $objects_per_page;  
            $dh_rnt_search_params['page'] = 1;
        }
        cw_header_location("index.php?target=datahub_rnt&mode=search");
        exit;
    } elseif ($action == "hide_show") {
        $process_hide_items = $_POST['process_hide_items'];
        $hide_items = $_POST['hide_items'];
        if (is_array($process_hide_items)) {
            foreach ($process_hide_items as $itemID => $one_val) {
                if ($hide_items[$itemID]) {
                    $hide_qry = "replace into $tables[datahub_rnt_hidden_item] set hiddenID='$itemID'";
                } else {
                    $hide_qry = "delete from $tables[datahub_rnt_hidden_item] where hiddenID='$itemID'";
                }  
                db_query($hide_qry); 
            }    
        }
        cw_header_location("index.php?target=datahub_rnt&mode=search");
        exit;
    } elseif ($action == "print_tag1") {
        $items2print_1 = &cw_session_register('items2print_1', array());
        $items2print_2 = &cw_session_register('items2print_2', array());
        $rnt_items_coords = &cw_session_register('rnt_items_coords', array());
        if (is_array($print_items)) {
            $items2print_1 = array_keys($print_items);

            $items2print_2 = array();
            unset($items2print_2);

            $rnt_items_coords['x_k1'] = floatval($x_k1);    
            $rnt_items_coords['y_k1'] = floatval($y_k1);

            $rnt_items_coords['x_k2'] = floatval($x_k2);
            $rnt_items_coords['y_k2'] = floatval($y_k2);
            if (count(array_keys($print_items))) {
                cw_header_location("index.php?target=datahub_rnt&mode=print");
                exit;
            }  
 
        } else {
            cw_header_location("index.php?target=datahub_rnt&mode=no_tags_sel");
        } 
        exit;
    } elseif ($action == "print_tag2") {
        $items2print_1 = &cw_session_register('items2print_1', array());
        $items2print_2 = &cw_session_register('items2print_2', array());
        $rnt_items_coords = &cw_session_register('rnt_items_coords', array());

        if (is_array($print_items)) {
            $items2print_2 = array_keys($print_items);

            $items2print_1 = array();
            unset($items2print_1);

            $rnt_items_coords['x_k1'] = floatval($x_k1);  
            $rnt_items_coords['y_k1'] = floatval($y_k1);

            $rnt_items_coords['x_k2'] = floatval($x_k2);
            $rnt_items_coords['y_k2'] = floatval($y_k2);

            if (count(array_keys($print_items)))
                cw_header_location("index.php?target=datahub_rnt&mode=print");
        } else
            cw_header_location("index.php?target=datahub_rnt&mode=no_tags_sel");

        exit;
    } elseif ($action == "save_ratings") {

        if ($ratings) { 
            $ratings = $_POST['ratings'];   
            foreach ($ratings as $item_id => $ratings_data) {
                $update_fields = array(); 
                foreach ($ratings_data as $rat_fld => $posted_data) {
                    $update_fields[] = "`$rat_fld` = '$posted_data'";
                } 

                db_query("update $tables[datahub_main_data] set ".implode(", ", $update_fields)." where ID = '$item_id'");
            }
        }

        cw_header_location("index.php?target=datahub_rnt&mode=search");
        exit;
    }
}

$smarty->assign("search_title", "Search for items ids: $search_for");


$search_for = $dh_rnt_search_params['search_alu'];
$smarty->assign('search_alu', $search_for); 

foreach ($remember_posted_vars as $var_name_key => $var_name_data) {

    if (is_string($var_name_key))
        $var_name_saved = $var_name_key;
    else {
        if (is_array($var_name_data)) 
            $var_name_saved = $var_name_data[0];
        else 
            $var_name_saved = $var_name_data;
    }

    if (is_array($var_name_data)) {
        if ($var_name_data[1] == 'multi') { 
            settype($var_name_saved, "string");
            $val2assign = is_array($dh_rnt_search_params[$var_name_saved])?
                array_fill_keys($dh_rnt_search_params[$var_name_saved], 1):array();
        } 
    } else {
        $val2assign = $dh_rnt_search_params[$var_name_saved];          
    }
    $smarty->assign($var_name_saved, $val2assign);
}

if (!empty($search_for) || $dh_rnt_search_params['live_only'] || !empty($dh_rnt_search_params['empty_rating_search']) || !empty($dh_rnt_search_params['empty_review_search'])) {

    $search_conditions = array(1);
    $select_fields = array("$tables[datahub_main_data].*", "$tables[datahub_rnt_hidden_item].hiddenID");

    if (!empty($search_for)) { 
        $search_for = str_replace("\n", " ", $search_for);
        $search_items_arr = explode(" ", $search_for);
        $search_items_arr = array_map("trim", $search_items_arr);
        $search_conditions[] = "$tables[datahub_main_data].ID in ('".implode("','", $search_items_arr)."')";
    }

    if ($dh_rnt_search_params['live_only']) {
        $live_only_inn_join = " inner join $tables[products] on $tables[products].product_id = $tables[datahub_main_data].ID AND  $tables[products].status=1";
        $live_only_inn_join .= " left join $tables[datahub_main_data_images] on $tables[datahub_main_data_images].id=$tables[datahub_main_data].cimageurl ";
        $select_fields[] = "$tables[datahub_main_data_images].web_path as products_cimageurl";

        if ($dh_rnt_search_params['with_images_present'] == 1) {
            $search_conditions[] = "$tables[datahub_main_data_images].filename != '' AND $tables[datahub_main_data_images].filename NOT LIKE '%no_image%'";
        } elseif ($dh_rnt_search_params['with_images_present'] == 2) {
            $search_conditions[] = "$tables[datahub_main_data_images].filename = '' OR $tables[datahub_main_data_images].filename like '%no_image%'";
        }
    } else {
        if ($dh_rnt_search_params['sortby'] == 'products_cimageurl') 
            $dh_rnt_search_params['sortby'] = $default_sortby;
    }

    if ($dh_rnt_search_params['hidden_only']) {
        $hidden_join = " inner join $tables[datahub_rnt_hidden_item] on $tables[datahub_rnt_hidden_item].hiddenID = $tables[datahub_main_data].ID ";
    } else {
        $hidden_join = " left outer join $tables[datahub_rnt_hidden_item] on $tables[datahub_rnt_hidden_item].hiddenID = $tables[datahub_main_data].ID ";
        $search_conditions[] = " $tables[datahub_rnt_hidden_item].hiddenID is NULL"; 
    } 

    if (!empty($dh_rnt_search_params['empty_rating_search'])) {
        $ratings_search_conditions = array();

        if ($dh_rnt_search_params['rating_empty_nonempty'])
            $eql_sign = '!=';
        else   
            $eql_sign = '=';

        foreach ($dh_rnt_search_params['empty_rating_search'] as $rat_f_name) {
            $ratings_search_conditions[] = "$tables[datahub_main_data].`$rat_f_name` $eql_sign ''";
        } 

        if ($dh_rnt_search_params['ratings_and_or'] == 2)
            $clue_operator = 'AND'; 
        else 
            $clue_operator = 'OR';

        $search_conditions[] = "(".implode(" $clue_operator ", $ratings_search_conditions).")";
    }

    if (!empty($dh_rnt_search_params['empty_review_search'])) {
        $reviews_search_conditions = array();

        if ($dh_rnt_search_params['review_empty_nonempty'])
            $eql_sign = '!=';
        else
            $eql_sign = '=';

        foreach ($dh_rnt_search_params['empty_review_search'] as $rev_f_name) {
            $reviews_search_conditions[] = "$tables[datahub_main_data].`$rev_f_name` $eql_sign ''";
        } 

        if ($_SESSION['reviews_and_or'] == 2)
            $clue_operator = 'AND';
        else 
            $clue_operator = 'OR';

        $search_conditions[] = "(".implode(" $clue_operator ", $reviews_search_conditions).")";
    }

    if (!empty($dh_rnt_search_params['sortby'])) {
        $sortby_col = $dh_rnt_search_params['sortby']; 
    } else {
        $sortby_col = $default_sortby;
    }
    $smarty->assign('sortby_col', $sortby_col);

    if (isset($dh_rnt_search_params['sort_dir']))
        $sort_dir = $dh_rnt_search_params['sort_dir'];
    else
        $sort_dir = 0;

    $smarty->assign('sort_dir', $sort_dir);

    $sort_by_qry = " ORDER BY `$sortby_col` ".($sort_dir?'DESC':'ASC');
    
    if (isset($dh_rnt_search_params['objects_per_page'])) {
        $objects_per_page = $dh_rnt_search_params['objects_per_page'];
    } else {
        $objects_per_page = 100;
    }
    $smarty->assign('objects_per_page', $objects_per_page);

    if (isset($dh_rnt_search_params['page'])) {
        $page = $dh_rnt_search_params['page'];
    } else {
        $page = 1;
    }
    $smarty->assign('page', $page);

    $items_count_res = cw_query("select count(*) as count from $tables[datahub_main_data] $live_only_inn_join $hidden_join where $tables[datahub_main_data].ID != '' and ".implode(" and ", $search_conditions));  

    $total_items = $items_count_res[0]['count']; 

//    print($total_items);

    $smarty->assign('navigation_script', "index.php?target=datahub_rnt");

//navigation.php

    $objects_per_page = intval($objects_per_page);
    if ($objects_per_page <= 0)
        $objects_per_page = 10;

    # Claculate total navigation pages number
    $total_nav_pages = ($total_nav_pages ? $total_nav_pages : ceil($total_items/$objects_per_page)+1);
    $max_nav_pages = 45;

    $page = intval($page);

    if ($page <= 0)
        $page = 1;

    if ($page >= $total_nav_pages)
        $page = $total_nav_pages-1;

    $first_page = $objects_per_page*($page-1);

#
# $total_super_pages - how much groups of pages exists
#
    $total_super_pages = (($total_nav_pages-1) / ($max_nav_pages ? $max_nav_pages : 1));

#
# $current_super_page - current group of pages
#
    $current_super_page = ceil($page / $max_nav_pages);

#
# $start_page - start page number in the list of navigation pages
#
    $start_page = $max_nav_pages * ($current_super_page - 1);

#
# $total_pages - the maximum number of pages to display in the navigation bar
# plus $start_page
    $total_pages = ($total_nav_pages>$max_nav_pages ? $max_nav_pages+1 : $total_nav_pages) + $start_page;

#
# Cut off redundant pages from the tail of navigation bar
#
    if ($total_pages > $total_nav_pages)
        $total_pages = $total_nav_pages;

    if ($page > 1 and $page >= $total_pages) {
        $page = $total_pages - 1;
        $first_page = $objects_per_page*($page-1);
    }

    if ($first_page < 0)
        $first_page = 0;


//print_r(array($page, $total_pages, $total_super_pages, $current_super_page, $start_page + 1));
   $smarty->assign("navigation_page", $page);
   $smarty->assign("total_pages", $total_pages);
   $smarty->assign("total_super_pages", $total_super_pages);
   $smarty->assign("current_super_page", $current_super_page);
   $smarty->assign("start_page", $start_page + 1);

    $smarty->assign('max_nav_pages', $max_nav_pages);
//--navigation.php
    $items_limit = " LIMIT ".((min($page,1)-1)*$objects_per_page).", $objects_per_page";


    $qry = "SELECT ".implode(", ", $select_fields)." FROM $tables[datahub_main_data] $live_only_inn_join $hidden_join WHERE $tables[datahub_main_data].ID != '' AND ".implode(" AND ", $search_conditions).$sort_by_qry.$items_limit;

    if ($_GET['show_query'] == 'Y') 
        print($qry);

    $found_items = cw_query($qry);
    $smarty->assign('search_is_run', 1);

 //   usort($found_items,'dh_rnt_items_cmp');

    $found_items_alus = array();
    $not_found_items_alus = array();
    if (is_array($found_items)) {
        foreach ($found_items as $item) {
            $found_items_alus[] = $item['ID'];   
        }
        if (is_array($search_items_arr)) {
            foreach ($search_items_arr as $srch_i) {
                if (!in_array($srch_i, $found_items_alus) && intval($srch_i)) 
                    $not_found_items_alus[] = $srch_i;
            } 
        } 
    }
    if (count($not_found_items_alus) > 0) {
        $smarty->assign('not_found_items_alus', $not_found_items_alus);
    }
    if (count($found_items) > 0) 
        $smarty->assign('found_items',$found_items);
}

$items2print_1 = &cw_session_register('items2print_1', array());
$items2print_2 = &cw_session_register('items2print_2', array());
$rnt_items_coords = &cw_session_register('rnt_items_coords', array());

if ($mode == 'print') {
    if (!empty($items2print_1)) 
        $smarty->assign('items2print_1', 'Y');
    if (!empty($items2print_2))
        $smarty->assign('items2print_2', 'Y');
}


if (!isset($rnt_items_coords['x_k1']))
    $rnt_items_coords['x_k1'] = 1.00;

if (!isset($rnt_items_coords['y_k1']))
    $rnt_items_coords['y_k1'] = 1.00;

if (!isset($rnt_items_coords['x_k2']))
    $rnt_items_coords['x_k2'] = 1.00;

if (!isset($rnt_items_coords['y_k2']))
    $rnt_items_coords['y_k2'] = 1.00;

$smarty->assign('x_k1', $rnt_items_coords['x_k1']);
$smarty->assign('y_k1', $rnt_items_coords['y_k1']);
$smarty->assign('x_k2', $rnt_items_coords['x_k2']);
$smarty->assign('y_k2', $rnt_items_coords['y_k2']);

// display the output
//$smarty->display($app_files_dir.'/search_page.tpl');
$smarty->assign('main', 'datahub_rnt');
