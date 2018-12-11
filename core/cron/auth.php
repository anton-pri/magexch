<?php
define('AREA_TYPE', 'C');
$current_area = AREA_TYPE;

require_once $app_main_dir.'/init/prepare.php';

cw_load('files', 'speed_bar', 'sections');
cw_include('init/lng.php');
