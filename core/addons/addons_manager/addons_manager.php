<?php

$dir_to_unpack = $var_dirs['tmp'].'/addons';
$addonname = &cw_session_register('addonname');
$files_to_import = array();
$files_to_import = cw_files_get_dir($dir_to_unpack,2);
if (is_array($files_to_import))
    $addonname  =  basename($files_to_import[0]);
else 
    $addonname='';

if ($action == 'upload') {

    $file_path = cw_move_uploaded_file('filename');

    if (is_file($file_path)) {
        cw_rm_dir($dir_to_unpack);
        @mkdir($dir_to_unpack);
        system('tar -xzf '.escapeshellarg($file_path).' -C '.$dir_to_unpack);
        cw_header_location('index.php?target=addons_manager&action=install');
    }

} elseif ($action=='install') {
        $smarty->assign('addonname', $addonname);
}

if (!file_exists($dir_to_unpack."/$addonname/INSTALLED"))
    $smarty->assign('uploaded', $addonname);
$smarty->assign('main', 'addons_manager');
