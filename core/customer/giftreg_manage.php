<?php
// TODO: DELETE THIS CONTROLLER

# kornev, TOFIX, also check security
if (!$addons['Gift_Registry'])
	cw_header_location('index.php');

$location[] = array(cw_get_langvar_by_name("lbl_gift_registry"), 'index.php?target=giftreg_manage');

if ($REQUEST_METHOD == "POST" && $action == "move_product")
	cw_include('addons/Gift_Registry/giftreg_wishlist.php');
else {
	if (!empty($eventid) && ($action == "gb" || $mode == "guestbook")) {
		$modify_mode = true;
		cw_include('addons/Gift_Registry/event_guestbook.php');
	}
	cw_include('addons/Gift_Registry/event_modify.php');
}
