<?php
require_once('constants.php');

$file_name = !empty($_GET['filename']) ? $_GET['filename']  : 'keep';

$ts =  date('Y-m-d__H-i-s');
$target_dir = DB_BU_DIR . "{$ts}_{$file_name}";
$command = "mkdir $target_dir";
system("$command");

$sql = "mysqldump -u " . DBUSER . " -p" . DBPASS  . " --add-drop-database -q --create-options -c " . STORE_UPDATES . " > $target_dir/" . DB_BU_FILE;
system("$sql");

system("gzip -r $target_dir");





