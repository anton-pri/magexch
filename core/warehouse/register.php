<?php
$newbie = "Y";

$smarty->assign('register_script_name', (($config['Security']['use_https_login']=="Y")?$app_catalogs_secure['warehouse']."/":"")."index.php?target=register");
require $app_main_dir."/include/register.php";
$smarty->assign('newbie', $newbie);
