<?php
cw_load('addons');

// Edit Enable/Disable status for all addons 
if ($action == 'update' && is_array($upd_addons)) {

    db_query("update $tables[addons] set active='0' where status>'".constant('ADDON_TYPE_CORE')."'");

    foreach($upd_addons as $addon => $val)
        db_query("update $tables[addons] set active='1' where addon='$addon'");

    $disabled = cw_query_column("select addon from $tables[addons] where active = 0");
    db_query("update $tables[addons] set active=0 where parent in ('".implode("', '", $disabled)."')");

    cw_header_location("index.php?target=$target&mode=addons");
}

// Enable/disable an addon
if ($action == 'ajax_update') {
	db_query("update $tables[addons] set active=abs(active-1) where addon='$addon' and status>'".constant('ADDON_TYPE_CORE')."'");
	$active = cw_query_first_cell("select active from $tables[addons] where addon='$addon'");
	cw_add_ajax_block(array(
		'id' => 'script',
		'content' => '$("#'.$addon.'").removeClass("on").removeClass("off").addClass("'.($active==1?'on':'off').'");',
	));
	cw_add_ajax_block(array(
		'id' => 'script',
		'content' => '$("[parent='.$addon.']").parent().removeClass("addon_locked")'.($active==1?'':'.addClass("addon_locked")').';',
	));	
}


// Show addons
$addons = cw_addons_get();
if (!isset($status) || empty($status)) {
    $status = constant('ADDON_TYPE_GENERAL');
}
foreach ($addons as $k=>$v) {
    if ($v['status'] < $status) unset($addons[$k]);
}
$smarty->assign('addons', $addons);

$location[] = array(cw_get_langvar_by_name('lbl_addons'), 'index.php?target='.$target);

$smarty->assign('current_section_dir', 'settings');
$smarty->assign('main', 'addons');


