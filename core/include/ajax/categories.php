<?php
cw_load('category');

$smarty->assign('subcategories', cw_category_get_subcategories($cat, $current_category));
$smarty->assign('current_category', $current_category);
$smarty->assign('name', $name);
$smarty->assign('id', $id);
$smarty->assign('index', $index);
$smarty->assign('el_name', $el_name);
$smarty->assign('multiple', $multiple);
$smarty->assign('parent_category_id', $cat);
$smarty->assign('return_type', $return_type);
cw_display('main/ajax/categories.tpl', $smarty);
exit(0);
?>
