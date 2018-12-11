<?php
namespace cw\custom_magazineexchange_sellers;

$smarty->assign('current_main_dir',     'addons/' . addon_name);
$smarty->assign('current_section_dir',  'seller');
$smarty->assign('main',                 $target);
$smarty->assign('AltImagesDir', $app_web_dir . '/skins_magazineexchange/images');
