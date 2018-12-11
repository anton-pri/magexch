<?php
/*
 * Vendor: CW
 * addon: bookmarks
 */

namespace CW\bookmarks;

const addon_name = 'bookmarks';
const addon_target = 'bookmarks';

$tables['bookmarks'] = 'cw_bookmarks';

if (APP_AREA != 'admin') return true;

cw_include('addons/'.addon_name.'/func.php');

if ($target == addon_target) {

    cw_set_controller(APP_AREA.'/'.addon_target.'.php','addons/'.addon_name.'/'.addon_target.'.php', EVENT_REPLACE);

}

cw_addons_set_template(
    array('post', 'elements/bottom.tpl', 'addons/'.addon_name.'/bookmarks.tpl'),
    array('post', 'elements/bottom_admin.tpl', 'addons/'.addon_name.'/bookmarks.tpl')
);


cw_event_listen('on_login','CW\bookmarks\on_login');
cw_event_listen('on_customer_delete','CW\bookmarks\on_customer_delete');
cw_event_listen('on_sessions_delete','CW\bookmarks\on_sessions_delete');

cw_addons_add_css('addons/'.addon_name.'/bookmarks.css');
cw_addons_add_js('addons/'.addon_name.'/bookmarks.js');

cw_set_controller('addons/mobile/init/mobile.php','addons/'.addon_name.'/addons/mobile.php', EVENT_POST);
