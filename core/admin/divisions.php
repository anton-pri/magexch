<?php
if (!$addons['warehouse'])
    cw_header_location('index.php');

cw_load('warehouse', 'profile_fields');

if ($action == 'update' && is_array($divisions)) {
    foreach($divisions as $k=>$v) {
        $to_insert = array(
            'title' => $v['title'],
            'address_id' => $v['address_id'],
            'backorder' => is_array($v['backorder'])?array_sum($v['backorder']):0,
            'enabled' => is_array($v['enabled'])?array_sum($v['enabled']):0,
        );
        if (!$k && !$v['title']) continue;
        if ($k) $to_insert['division_id'] = $k;
        $k = cw_array2insert('warehouse_divisions', $to_insert, true, array('title', 'division_id', 'address_id', 'backorder', 'enabled'));
        if (!$v['address_id']) {
            $v['address_id'] = cw_array2insert('customers_addresses', array('customer_id' => 0));
            db_query("update $tables[warehouse_divisions] set address_id='$v[address_id]' where division_id='$k'");
        }
        $v['address']['address_id'] = $v['address_id'];
        cw_array2insert('customers_addresses', $v['address'], true);//"address_id='$v[address_id]'");
    }
    cw_header_location("index.php?target=$target");
}

if ($action == 'delete' && is_array($del)) {
    foreach($del as $div_id=>$val)
        cw_call('cw_warehouse_delete_division', array($div_id));
    cw_header_location("index.php?target=$target");
}

if ($action == 'reset_amount' && $division_id) {
    cw_load('accounting', 'product');

    cw_warehouse_reset_amount($division_id, $reset[$division_id]);
    cw_header_location('index.php?target='.$target);
}

if ($action == 'reset_all_amount' && $division_id) {
    cw_load('accounting');
    cw_warehouse_reset_all_amount($division_id);
    cw_header_location('index.php?target='.$target);
}

list($profile_sections, $profile_fields, $additional_fields) = cw_profile_fields_get_sections('U', true, 'P');
$smarty->assign('profile_fields', $profile_fields);

$smarty->assign('divisions', cw_warehouse_get_divisions());

$location[] = array(cw_get_langvar_by_name('lbl_divisions'), 'index.php?target='.$target);
$smarty->assign('main', 'divisions');
