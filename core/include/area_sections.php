<?php
// Main left menu
cw_load('tabs');

global $target, $config;
global $app_skins_dirs;

$current_section_dir = 'main';

$items = cw_query_hash("
    SELECT m.menu_id as hash_key, m.* 
    FROM $tables[navigation_menu] m
    LEFT JOIN $tables[addons] a on m.addon=a.addon 
    WHERE m.area = '$current_area' 
        and (a.active or (m.addon='' AND a.addon is null))
        and is_loggedin & ".($customer_id?1:2)."
    ORDER BY orderby", 'hash_key',false, false);

foreach ($items as $menu_id=>$v) {
    if (!empty($v['access_level']) && !$accl[$v['access_level']]) {
        unset($items[$menu_id]);
        continue;
    }
    
    if (!empty($v['func_visible']) && function_exists($v['func_visible']) && !cw_call($v['func_visible'],array($v))) {
        unset($items[$menu_id]);
        continue;        
    }

    if ($v['target'] == $target) {

        // Active menu
        $mid = $selected = $menu_id;
        do {
            $items[$mid]['selected'] = 1;
            $mid = $items[$mid]['parent_menu_id'];
        } while ($mid>0 && isset($items[$mid]) && $i++<10);

        if ($v['skins_subdir']!='') $current_section_dir = $v['skins_subdir'];
    }
    
}
$menu = cw_call('cw_tabs_build_tree',array($items, 0));

$smarty->assign('menu', $menu);
$smarty->assign('current_main_dir', $app_skins_dirs[AREA_TYPE]);
$smarty->assign('current_section_dir', $current_section_dir);
