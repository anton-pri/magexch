<?php
$location[] = array(cw_get_langvar_by_name("lbl_news_management"), "index.php?target=news");
include $app_main_dir."/addons/news/news.php";
$smarty->assign('main', "news_management");
$smarty->assign('dialog_tools_data', $dialog_tools_data);
