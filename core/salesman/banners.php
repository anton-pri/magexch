<?php
$banners = cw_query ("SELECT * FROM $tables[salesman_banners] WHERE avail = 'Y' AND banner_type <> 'P'");
$smarty->assign ("banners", $banners);

$smarty->assign('http_location', $http_location);

$smarty->assign('main', 'banners');
