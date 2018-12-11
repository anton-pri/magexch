<?php
namespace CW\bookmarks;

global $config, $smarty, $mobile_select_type;

// Remove bookmark for mobile skin
if ($mobile_select_type == 1) {
    unset($config[addon_name]);
    cw_addons_unset_template(
        array('post', 'elements/bottom.tpl', 'addons/'.addon_name.'/bookmarks.tpl'),
        array('post', 'elements/bottom_admin.tpl', 'addons/'.addon_name.'/bookmarks.tpl')
    );
    cw_addons_delete_resource('css','addons/'.addon_name.'/bookmarks.css');
    cw_addons_delete_resource('js','addons/'.addon_name.'/bookmarks.js');
    cw_event_delete_listener('on_login','CW\bookmarks\on_login');
    cw_event_delete_listener('on_customer_delete','CW\bookmarks\on_customer_delete');
    cw_event_delete_listener('on_sessions_delete','CW\bookmarks\on_sessions_delete');
    $smarty->assign('config', $config);
}
