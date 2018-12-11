<?php

$page_data = cw_call('cw_ab_staticpages_get', array('page_id' => $page_id, 'active' => 1));
if (!$page_data) cw_header_location('index.php');

$smarty->assign('page_data', $page_data);

$location[] = array($page_data['name'], '');
$smarty->assign('main', 'pages');

if ($page_data['type'] == 'staticpopup') {
    $smarty->assign('home_style', 'popup');
}
