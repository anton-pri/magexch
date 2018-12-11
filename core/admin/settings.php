<?php
cw_load('config');

$cat = addslashes($request_prepared['cat']);
if (empty($cat)) $cat ='General';

// Update settings
if ($action == 'update') {
   
    if (!empty($cat)) {
        cw_config_update($cat, $configuration);
    }

    if (!empty($adv_search_configuration)) {
        db_query("update $tables[config] set value='".serialize($adv_search_configuration)."' where name='adv_search_attributes_config'");
    }

    cw_header_location("index.php?target=$target&cat=$cat");
}

$config_category = cw_query_first("SELECT c.category, IFNULL(lng.value, c.category) AS title, m.addon, c.is_local
        FROM $tables[config_categories] AS c
        LEFT JOIN $tables[addons] AS m ON m.addon = c.category
        LEFT JOIN $tables[languages] AS lng ON lng.code = '$current_language' AND lng.name = CONCAT('option_title_', c.category)
        WHERE c.category='$cat'
        LIMIT 1
    "); 
    
$addon_name = cw_get_langvar_by_name('addon_name_'.$config_category['addon']);

// Show settings
# Cache .htaccess location
$smarty->assign('cache_htaccess_location', $app_dir.'\var\cache\.htaccess');

$smarty->assign('categories', cw_config_get_categories());

$memberships = cw_user_get_memberships(array('C', 'R'));
if (!empty($memberships))
    $smarty->assign('memberships', $memberships);

$shippings = cw_query("select $tables[shipping].* from $tables[shipping], $tables[shipping_carriers] where $tables[shipping].carrier_id=$tables[shipping_carriers].carrier_id and active=1 ORDER BY orderby");
$smarty->assign('shippings', $shippings);

$location[] = array(cw_get_langvar_by_name('lbl_settings'), 'index.php?target='.$target);

$smarty->assign('cat', $cat);
$smarty->assign('config_category',$config_category);
if ($config_category['addon'] || $config_category['is_local'])
    $smarty->assign('category_location', array(array($config_category['title'])));
$smarty->assign('addon', $config_category['addon']);
$smarty->assign('addon_name', $addon_name);

$smarty->assign('main', 'settings'); // admin/settings/settings.tpl
