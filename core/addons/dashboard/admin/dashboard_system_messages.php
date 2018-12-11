<?php

$addon_actions = array(
    'show' => 'dashboard_system_messages_show',
    'hide' => 'dashboard_system_messages_hide',
    'delete' => 'dashboard_system_messages_delete',
);

if (empty($action) || !isset($addon_actions[$action]) || !function_exists($addon_actions[$action])) {
   return;
}

$smarty->assign('action', $action);
cw_call($addon_actions[$action]);

return;



function dashboard_system_messages_hide() {
	$sections = array(
	0 => 'system_messages',
	1 => 'awaiting_actions',
	2 => 'system_info'
	);
	$section_name = $sections[$_GET['type']];
    
	cw_system_messages_hide($_GET['code']);
	cw_ajax_add_block(array(
		'id' => 'system_message_'.$_GET['code'],
		'action' => 'hide'
	));
	cw_ajax_add_block(array(
		'id' => $section_name.'_bottom',
		'action' => 'show'
	));    
}
function dashboard_system_messages_delete() {
	cw_system_messages_delete($_GET['code']);
	cw_ajax_add_block(array(
		'id' => 'system_message_'.$_GET['code'],
		'action' => 'hide'
	));
}

function dashboard_system_messages_show() {
	global $smarty;
	cw_system_messages_show($_GET['type']);

	$sections = array(
	0 => 'system_messages',
	1 => 'awaiting_actions',
	2 => 'system_info'
	);
	$section_name = $sections[$_GET['type']];

	$system_messages = cw_system_messages($_GET['type'],true);
	$smarty->assign($section_name, $system_messages);
	
	cw_ajax_add_block(array(
		'id' => 'dashboard_name_'.$section_name,
		'template' => 'addons/dashboard/admin/sections/'.$section_name.'.tpl'
	));
	cw_ajax_add_block(array(
		'id' => $section_name.'_bottom',
		'action' => 'hide'
	));
}
