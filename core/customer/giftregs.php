<?php
// TODO: DELETE THIS CONTROLLER

if (!$addons['Gift_Registry'])
    cw_header_location('index.php');

$access_status = &cw_session_register("access_status", array());


if ($mode == "preview") {
	$html_content = cw_query_first_cell("SELECT html_content FROM $tables[giftreg_events] WHERE event_id='$eventid'");
	if (!empty($html_content))
		echo $html_content;
	else
		echo "<br /><br /><br /><br /><h3 align=\"center\">".cw_get_langvar_by_name("lbl_no_html_content",false,false,true)."</h3>";
	exit;
}

if (!empty($cc)) {
# Confirm/Decline the participation by recipient
# $cc - is a confirmation code passed via GET request
	cw_include('addons/Gift_Registry/giftreg_confirm.php');
}

if (!empty($eventid)) {

	if (!empty($wlid)) {
		if (cw_query_first_cell("SELECT event_id FROM $tables[wishlist] WHERE wishlist_id='$wild'") == $eventid) {
			$wlid_eventid = &cw_session_register("wlid_eventid");
			$wlid_eventid = $eventid;
			cw_session_save("wlid_eventid");
		}
	}

	cw_include('addons/Gift_Registry/event_guestbook.php');
	cw_include('addons/Gift_Registry/giftreg_display.php');
}
else
	cw_include('addons/Gift_Registry/giftreg_search.php');

?>
