<?php
cw_load('http');

if($type == 'preview') {
	if(!$preview)
		cw_close_window();
	$res = cw_http_post_request($app_http_host, $app_web_dir."/banner.php", "type=preview&preview=".$preview);
	$smarty->assign('banner', $res[1]);
	$smarty->assign('mode', 'display');
}

$smarty->assign('home_style', 'iframe');
$smarty->assign('current_section_dir', 'sales_manager');
$smarty->assign('main', 'preview_banner');
