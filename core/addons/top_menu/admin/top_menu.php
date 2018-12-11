<?php

cw_load('ajax');

if ($_GET['get_menu_subitems']) {
  $prefixes = array('p', 'u');
  $parentid = intval(str_replace($prefixes, '', $_GET['get_menu_subitems']));
  $parent = (string)$_GET['get_menu_subitems'];
} else {
  $parentid = 0;
}



if (defined('IS_AJAX') && constant('IS_AJAX')) {
    global $config;
    if ($_GET['get_menu_subitems']) {
        cw_add_ajax_block(array(
            'id' => $_GET['get_menu_subitems'],
            'action' => 'after',
            'template' => 'addons/top_menu/ajax_categories.tpl',
        ));

        cw_add_ajax_block(array(
            'id' => 'script',
            'content' => 'checkbox_statuses("'.$parent.'")',
        ));

    }

}

set_time_limit(86400);

if ($request_prepared['mode'] == 'update') {
	cw_top_menu_update($request_prepared['update_data']);
	    
    cw_add_top_message('Menu was succesfully updated');
    
    cw_header_location('index.php?target=top_menu');
}

$item_list = top_menu_process(cw_top_menu_make_sql_query());
$sub_list = sub_menu_process(cw_top_menu_make_sql_query($parentid),0,$_GET['level']+1);

//$test_location = cw_top_menu_location('u118', 'ucat');
$smarty->assign('top_menu', $item_list);
$smarty->assign('sub_menu', $sub_list);
$smarty->assign('test_location', $test_location);

$smarty->assign('main', 'top_menu');
