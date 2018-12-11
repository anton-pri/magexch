<?php
namespace CW\wordpress;

function get_header() {
	global $smarty;
	return cw_display('addons/'.addon_name.'/header.tpl', $smarty, false);
}

function get_footer() {
	global $smarty;
	return cw_display('addons/'.addon_name.'/footer.tpl', $smarty, false);
}
