<?php 

cw_include('addons/flexible_import/datahub/rnt_head.php');

global $display_cols, $mag_names, $rating_cols;

$items2print_1 = &cw_session_register('items2print_1', array());
$items2print_2 = &cw_session_register('items2print_2', array());
$items_prices = &cw_session_register('items_prices', array());
//print_r(array($items2print_1, $items2print_2)); die;

unset($tag_type_id);
if (is_array($items2print_1) || is_array($items2print_2)) {

    if (count($items2print_1) > 0) {
        $tag_type_id = 1;
    } 
    elseif (count($items2print_2) > 0) {
        $tag_type_id = 2;
    }

    if ($tag_type_id) {
        $items2print_varname = 'items2print_'.$tag_type_id;
        $items2print = $$items2print_varname; 
    }
}
if (isset($items2print)) {
    $qry = "SELECT $tables[datahub_main_data].*, p.`Regular Price`, p.MSRP, p.`Item Description` FROM $tables[datahub_main_data] LEFT JOIN pos p ON p.`Alternate Lookup`=$tables[datahub_main_data].catalog_id WHERE $tables[datahub_main_data].catalog_id in ('".implode("','", $items2print)."')";

    $print_items = cw_query($qry);

    foreach ($print_items as $pi_k => $pi_v) {
        $max_rating_gid = array(-1,'');
        $just_not_empty_review = '';
        $just_not_empty_review_magazine = ''; 
        if (!intval($pi_v['Winery_Rating']) && !empty($pi_v['Winery_Review'])) {
            $pi_v['Winery_Rating'] = 1;   
        } 
        foreach ($rating_cols as $colgrp_id=>$colgrp) {
            if (intval($pi_v[$colgrp[0]]) > $max_rating_gid[0]) {
                $max_rating_gid[0] = intval($pi_v[$colgrp[0]]);
                $max_rating_gid[1] = $pi_v[$colgrp[1]];
                $max_rating_gid[2] = $mag_names[$colgrp_id];
            }
            if (strlen($pi_v[$colgrp[1]]) > 0) {
                $just_not_empty_review = $pi_v[$colgrp[1]];  
                $just_not_empty_review_magazine = $mag_names[$colgrp_id];
            }  
        } 
        if ($max_rating_gid[0] > -1) 
            $print_items[$pi_k]['max_rating'] = $max_rating_gid;
        elseif ($just_not_empty_review != '') {
            $print_items[$pi_k]['just_not_empty_review'] = $just_not_empty_review;
            $print_items[$pi_k]['just_not_empty_review_magazine'] = $just_not_empty_review_magazine; 
        }
        $region_data = array();
        foreach (array('Region', 'Appellation') as $rf_name)   
            if (!empty($pi_v[$rf_name]))    
                $region_data[] = $pi_v[$rf_name];
        $print_items[$pi_k]['region_data'] = implode(' - ',$region_data);
        if (isset($items_prices[$pi_v['catalog_id']])) {
/*
            $reg_price = $print_items[$pi_k]['Regular Price'];            
            $new_price = $items_prices[$pi_v['catalog_id']];

            $print_items[$pi_k]['list_price'] = max($reg_price, $new_price);//$print_items[$pi_k]['Regular Price'];
            $print_items[$pi_k]['Regular Price'] = min($reg_price, $new_price);//$items_prices[$pi_v['catalog_id']]; 
*/
            if ($items_prices[$pi_v['catalog_id']] > 0) 
                $print_items[$pi_k]['list_price'] = $items_prices[$pi_v['catalog_id']];

        }
        //$print_items[$pi_k]['list_price'] = cw_query_first_cell("SELECT list_price FROM $tables[products_prices] WHERE product_id='{$pi_v['catalog_id']}' AND quantity=1 AND variant_id=0 AND membership_id=0");
    }
} else {
    cw_header_location("index.php?target=datahub_rnt&mode=no_tags_sel");
    exit;
}
  
$smarty->assign('print_items', $print_items);

$rnt_items_coords = &cw_session_register('rnt_items_coords', array());

if (isset($rnt_items_coords['x_k1']) && $tag_type_id == 1) 
$smarty->assign('x_k', $rnt_items_coords['x_k1']);

if (isset($rnt_items_coords['y_k1']) && $tag_type_id == 1)
$smarty->assign('y_k', $rnt_items_coords['y_k1']);

if (isset($rnt_items_coords['x_k2']) && $tag_type_id == 2)
$smarty->assign('x_k', $rnt_items_coords['x_k2']);

if (isset($rnt_items_coords['y_k2']) && $tag_type_id == 2)
$smarty->assign('y_k', $rnt_items_coords['y_k2']);

$smarty->assign('sales_tag', $request_prepared['sales_tag']);

cw_display('addons/flexible_import/datahub/rnt_print'.$tag_type_id.'.tpl', $smarty);
//cw_display('addons/flexible_import/datahub/rnt_print'.$request_prepared['sales_tag'].$tag_type_id.'.tpl', $smarty);

die;
