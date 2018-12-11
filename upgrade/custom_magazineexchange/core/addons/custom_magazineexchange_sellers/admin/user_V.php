<?php
namespace cw\custom_magazineexchange_sellers;

// use $target, $mode and $action params to define subject and action to call
// e.g. $target_$mode_$action or $target_$mode or $target_$action
$action_function = join('_',array_filter(array($target,$mode,$action)));

// Call action
$action_result = cw_call('cw\\'.addon_name.'\\'.$action_function);

// Action can return instance of Error via error() function
// see docs/core.error.txt
if (is_error($action_result)) {
    cw_add_top_message($action_result->getMessage(), 'E');
}

return $action_result;

/* ================================================================================== */

/* Actions */

function user_V_modify() {
    global $smarty, $tables, $current_language, $user;

    $query = "SELECT cms.contentsection_id, cms.active,
                    IF(ISNULL(l.name), cms.name, l.name) AS name, 
                    IF(ISNULL(sp.contentsection_id), 0,1) AS selected
            FROM $tables[cms] AS cms 
            LEFT JOIN $tables[cms_alt_languages] AS l 
                ON cms.contentsection_id = l.contentsection_id AND l.code = '$current_language'
            LEFT JOIN $tables[magazine_sellers_pages] as sp
                ON cms.contentsection_id = sp.contentsection_id AND sp.customer_id = '$user'
            WHERE `type`='staticpage'
            ORDER BY name";
    
    $promopages = cw_query($query);

    $smarty->assign('promopages', $promopages);
}

function user_V_modify_update() {
    cw_event_listen('on_profile_modify', 'cw\\'.addon_name.'\\on_profile_modify');
}





