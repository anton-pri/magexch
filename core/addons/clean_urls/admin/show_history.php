<?php

$url_list = cw_clean_url_get_url_list_from_history($item_id, $item_type);
$smarty->assign('clean_urls_list', $url_list);

$smarty->assign('main', 'clean_url_history_list');
$smarty->assign('home_style', 'popup');
