<?php
function cw_addons_get($parent_addon = '') {
    global $current_language, $tables;

    $addons = cw_query("select m.*, IFNULL(lng1.value, m.addon) as addon_lng, IFNULL(lng2.value, m.descr) as addon_descr_lng from $tables[addons] as m left join $tables[languages] as lng1 ON lng1.code = '$current_language' and lng1.name = CONCAT('addon_name_', m.addon) left join $tables[languages] as lng2 ON lng2.code = '$current_language' and lng2.name = CONCAT('addon_descr_', m.addon) where m.parent='$parent_addon' order by addon_lng");

    $mod_options = cw_query_column("select $tables[addons].addon from $tables[addons], $tables[config_categories] where $tables[addons].addon=$tables[config_categories].category group by $tables[addons].addon", "addon");

    foreach ($addons as $k => $v) {

        // cw_get_langvar_by_name provides name and descr including tooltip
        if ($v['addon'] != $v['addon_lng']) $addons[$k]['addon_lng'] = cw_get_langvar_by_name('addon_name_'.$v['addon']);
        if ($v['descr'] != $v['addon_descr_lng']) $addons[$k]['addon_descr_lng'] = cw_get_langvar_by_name('addon_descr_'.$v['addon']);


        if ($parent_id == 0)
            $addons[$k]['subaddons'] = cw_addons_get($v['addon']);

        if (in_array($v['addon'], $mod_options))
            $addons[$k]['options_url'] = true;
    }

    return $addons;
}

