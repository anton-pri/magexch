<?php

function cw_tabs_js_abstract($data, $return = null) {
    return $return?$return:$data;
}

function cw_tabs_build_tree($items, $parent_id) {
    global $accl;

    $return = array();

    foreach($items as $k=>$item) {
        if ($item['parent_menu_id'] == $parent_id) {
            if ($item['access_level'] && !$accl[$item['access_level']]) continue;
            $item['subitems'] = cw_call('cw_tabs_build_tree', array($items, $item['menu_id']));
            $return[] = $item;
        }
    }

    return $return;
}
