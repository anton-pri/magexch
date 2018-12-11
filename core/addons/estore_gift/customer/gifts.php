<?php

$is_gc_page = $mode == 'giftcert' 
	|| $mode == 'preview'
	|| $mode == 'gc2cart'
	|| $mode == 'modify_gc'
	|| $action == 'delgc'
	|| $action == "addgc2wl";

if (
    !$customer_id
    && !empty($mode)
    && $mode != 'wishlist'
    && !$is_gc_page
) {
    cw_header_location('index.php?target=help&section=login_customer');
}

if ($mode == 'friends') {
    cw_include('addons/estore_gift/wishlist.php');
    $smarty->assign('allow_edit', false);
}
elseif ($is_gc_page) {
    cw_include('addons/estore_gift/cert.php');
}
elseif ($mode == 'gifts') {
    $access_status = &cw_session_register('access_status', array());

    if (!empty($cc))
        cw_include("addons/estore_gift/giftreg_confirm.php");

    if (!empty($eventid)) {

		if (!empty($wlid)) {
			if (cw_query_first_cell("SELECT event_id FROM $tables[wishlist] WHERE wishlist_id='$wild'") == $eventid) {
				$wlid_eventid = &cw_session_register("wlid_eventid");
				$wlid_eventid = $eventid;
				cw_session_save("wlid_eventid");
			}
		}
        cw_include("addons/estore_gift/event_guestbook.php");
        cw_include("addons/estore_gift/giftreg_display.php");
    }
    else
        cw_include("addons/estore_gift/giftreg_search.php");
}
elseif ($mode == 'events') {
    if (isset($_GET['event_id']))
        cw_include('addons/estore_gift/event.php');
    else
        cw_include('addons/estore_gift/events.php');
}
else {
    cw_include('addons/estore_gift/wishlist.php');
}
