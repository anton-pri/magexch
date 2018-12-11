<?php
$referer = substr(@$HTTP_REFERER,0,255);
#Don't count referers that came from the same site
if (!(strstr($referer, 'http://'.$app_config_file['web']['http_host'].'/') || strstr($referer, 'https://'.$app_config_file['web']['http_host'].'/') || $referer == '') && !isset($HTTP_COOKIE_VARS['RefererCookie'])) {
    $curr_time = cw_core_get_time();
    $referer_result = cw_query_first_cell("SELECT COUNT(*) FROM $tables[referers] WHERE referer='".addslashes($referer)."'");
	if ($referer_result)
	    db_query("UPDATE $tables[referers] SET visits = (visits+1), last_visited='$curr_time' WHERE referer='".addslashes($referer)."'");
    else
	    db_query("REPLACE INTO $tables[referers] (referer, visits, last_visited) VALUES('".addslashes($referer)."', '1', '$curr_time')");
}

# If user have no cookie with referer to place from where he came set it
# It will be used later when he decides to register
$referer_session = &cw_session_register("referer_session");
if (!isset($HTTP_COOKIE_VARS['RefererCookie']) || empty($referer_session)) {
	if(empty($referer_session)) {
		$referer_session = (isset($HTTP_COOKIE_VARS['RefererCookie'])?$HTTP_COOKIE_VARS['RefererCookie']:$referer);
	}
	$referer = $referer_session;
	$_tmp = parse_url($current_location);
	@cw_set_cookie("RefererCookie", $referer, time()+3600*24*180, "/", $_tmp['host']);
}
?>
