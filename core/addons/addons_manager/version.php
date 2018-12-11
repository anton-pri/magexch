<?php
header("Content-type: text/plain");
cw_load('addons');

$core_version = trim(file_get_contents($app_main_dir.'/VERSION'));
$skin_version = trim(file_get_contents($app_dir.$app_config_file['web']['skin'].'/VERSION'));
$db_version = cw_query_first_cell("SELECT value FROM $tables[config] WHERE name='version'");

$out = array();

$out[] = '[version]';
$out[] = 'version='.$core_version;
$out[] = 'skin_version='.$skin_version;
$out[] = 'db_version='.$db_version;
$out[] = '[addons]';

$_addons = cw_addons_get();
foreach ($_addons as $_addon)
    $out[] = $_addon['addon'].'='.$_addon['active'];
echo implode("\n", $out);
exit();
?>
