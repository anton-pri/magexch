<?php
if (!defined('APP_START')) die('Access denied');

if ($current_area == "A") $subdir = '';
else $subdir = '/'.$customer_id;

$root_dir = $var_dirs['files'].$subdir;
?>
