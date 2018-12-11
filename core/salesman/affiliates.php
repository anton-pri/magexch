<?php
$location[] = array(cw_get_langvar_by_name("lbl_affiliates_tree"), "");

include $app_main_dir."/include/affiliates.php";

if($config['Salesman']['salesman_enable_level'] != 'Y' && $usertype == 'B')
	cw_header_location("index.php?target=error_message&error=access_denied&id=43");

$smarty->assign('main', 'affiliates');
