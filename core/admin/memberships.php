<?php

$recalc_subcat_count = false;

if (!cw_query_first_cell("select count(*) from $tables[memberships] where area='C' and membership_id=0")) {
# kornev. problem with auto-increment, which is not set to 0
    $mem_id = cw_array2insert('memberships', array('membership_id' => 0, 'area' => 'C'), 1);
    db_query("update $tables[memberships] set membership_id=0 where membership_id='$mem_id'");
}

if ($action == 'update' && !empty($posted_data)) {
    $is_default = array();
	foreach ($posted_data as $id => $v) {
		$membership = $v['membership'];
		if ($edited_language != $config['default_admin_language'])
			unset($v['membership']);

        if ($v['default_membership'] && !$is_default[$v['area']]) $is_default[$v['area']] = 1;
        else $v['default_membership'] = 'N';

		cw_array2update("memberships", $v, "membership_id = '$id'");
		db_query("REPLACE INTO $tables[memberships_lng] VALUES ('$id','$edited_language','$membership')");
	}

    cw_header_location("index.php?target=$target");
}

if ($action == 'add' && !empty($add['membership'])) {
	if (empty($add['orderby']))
		$add['orderby'] = cw_query_first_cell("SELECT MAX(orderby) FROM $tables[memberships] WHERE area = '$add[area]'")+1;
	$add['active'] = $add['active'];
	$id = cw_array2insert("memberships", $add);
	db_query("INSERT INTO $tables[memberships_lng] VALUES ('$id','$edited_language','$add[membership]')");
	if ($add['area'] == 'C' || $add['area'] == 'R') {
        cw_load("category");
        cw_recalc_subcat_count(0, 100);
    }
    // Copy default fields setting for new membership
    db_query("INSERT INTO $tables[register_fields_avails] ( `field_id` , `area` , `is_avail` , `is_required` ) (
    SELECT field_id, CONCAT(area, '_$id' ) , is_avail, is_required
    FROM $tables[register_fields_avails]
    WHERE area IN ('$add[area]', '#$add[area]')
    )");

    cw_header_location("index.php?target=$target");
}

if ($action == 'delete' && !empty($to_delete)) {
    cw_call('cw_user_delete_memberships',array($to_delete));
    cw_header_location("index.php?target=$target");
}

$memberships = array();
$memberships['A'] = array();
$memberships['C'] = array();
$memberships['V'] = array();

$tmp = cw_query("SELECT $tables[memberships].*, COUNT($tables[customers].customer_id) as users, IFNULL($tables[memberships_lng].membership, $tables[memberships].membership) as membership FROM $tables[memberships] LEFT JOIN $tables[customers] ON $tables[customers].membership_id = $tables[memberships].membership_id LEFT JOIN $tables[memberships_lng] ON $tables[memberships].membership_id = $tables[memberships_lng].membership_id AND $tables[memberships_lng].code = '$edited_language' GROUP BY $tables[memberships].membership_id ORDER BY IF(FIELD($tables[memberships].area, 'A','P','C','R','B','V') > 0, FIELD($tables[memberships].area, 'A','P','C','R','B','V'), 100), $tables[memberships].orderby");

if (!empty($tmp))
foreach ($tmp as $v)
    if (isset($memberships[$v['area']]))
        $memberships[$v['area']][] = $v;

$memberships_lbls = array();
foreach ($memberships as $k => $v)
	$memberships_lbls[$k] = cw_get_langvar_by_name('lbl_'.$k.'_membership_levels');

$no_membership = cw_query_hash("SELECT usertype, count(customer_id) as users FROM $tables[customers] WHERE membership_id = 0 GROUP BY usertype", 'usertype', false, true);
$smarty->assign('no_membership', $no_membership);
$smarty->assign('memberships', $memberships);
$smarty->assign('memberships_lbls', $memberships_lbls);

$location[] = array(cw_get_langvar_by_name('lbl_edit_membership_levels'), '');
$smarty->assign('main', 'memberships');
