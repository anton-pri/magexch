<?php
cw_load('sections', 'product', 'user', 'taxes', 'image');

if ($action and !in_array($js_tab, array('accessories', 'arrivals', 'bottom_line', 'clearance', 'hot_deals', 'super_deals'))) die('Access denied');

if ($action == 'update' && is_array($data)) {
    unset($data[0]); // data[0] relates to 'add new' form
    foreach($data as $id=>$val) {
        if ($js_tab == 'hot_deals') {
            if ($hot_deal == $id) $val['hot_deal'] = 1;
            else $val['hot_deal'] = 0;
        }

        if (!empty($id)) $val['id'] = $id;
        if ($val['del'] == 'Y')
            cw_delete_from_section($js_tab, $val['id']);
        else
            cw_array2insert($js_tab, $val, true);
    }

    cw_header_location("index.php?target=special_sections&js_tab=$js_tab");
}

if ($action == 'add') {
    $products = explode(" ", $newproduct_id);
    if (is_array($products))
    foreach($products as $product_id) {
        if (empty($product_id)) continue;
        $data[0]['product_id'] = $product_id;

        db_query("delete from ".$tables[$js_tab]." where product_id='$product_id'".($js_tab == 'super_deals'?" and membership_id='".intval($data[0]['membership_id'])."'":""));
        cw_array2insert($js_tab, $data[0], true);

        if (is_array($memberships) && $js_tab == 'super_deals')
        foreach($memberships as $memid) {
            db_query("delete from ".$tables[$js_tab]." where product_id='$product_id' and membership_id='".intval($memid)."'");
            $data[0]['membership_id'] = $memid;
            cw_array2insert($js_tab, $data[0], true);
        }
    }
    cw_header_location("index.php?target=special_sections&js_tab=$js_tab");
}

$smarty->assign('arrivals', cw_call('cw_sections_get', array('section' => 'arrivals')));
$smarty->assign('hot_deals', cw_call('cw_sections_get', array('section' => 'hot_deals')));
$smarty->assign('clearance', cw_call('cw_sections_get', array('section' => 'clearance')));
$smarty->assign('accessories', cw_call('cw_sections_get', array('section' => 'accessories')));
$smarty->assign('bottom_line', cw_call('cw_sections_get', array('section' => 'bottom_line')));

$super_deals = cw_call('cw_sections_get', array('section' => 'super_deals'));
$sections_by_memberships = array();
$all_memberships = cw_user_get_memberships(array('C', 'R'));
$membership_names = array();
$membership_names[0] = cw_get_langvar_by_name('lbl_retail_level');
if ($all_memberships)
foreach($all_memberships as $val)
    $membership_names[$val['membership_id']] = $val['membership'];

$smarty->assign('membership_names', $membership_names);

if (is_array($super_deals)) {
    $all_memberships = cw_user_get_memberships(array('C', 'R'));
    foreach($super_deals as $val) {
/*
            $val['is_variants'] = (bool) cw_query_first_cell("select count(*) from $tables[product_variants] where product_id='$val[product_id]'");
*/

        $sections_by_memberships[$val['membership_id']][] = $val;
    }
}
$smarty->assign('super_deals', $sections_by_memberships);

$location[] = array(cw_get_langvar_by_name('lbl_special_sections'), '');

$smarty->assign('js_tab', $js_tab);
$smarty->assign('main', 'special_sections');
