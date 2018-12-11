<?php 
/*
$app_dir = rtrim(realpath(dirname(__FILE__)), XC_DS);
$app_files_dir_name = 'ratingsntags';
$app_files_dir = $app_dir.'/'.$app_files_dir_name;
$smarty_skin_dir = '';
$var_dirs = array('cache'=>$app_files_dir.'/cache', 'templates_c'=>$app_files_dir.'/templates_c');
$index_script = "rnt.php";
*/

//dev-live different
/*
define('HOST', 'localhost');
define('DBUSER', 'saratoga_dbuser');
define('DBPASS', 'vm5BTzB=4QXn');
define('STORE_UPDATES', 'saratoga_live_hub');
*/
//-------------------
global $display_cols;

$display_cols = array(
'ID'=>"ALU",
'name'=>"Name",
'price'=>"Price",
'Vintage'=>"Vintage",
'Size'=>"Size",
'country'=>"Country",
'varietal'=>"Varietal",
'Appellation'=>"Appelation",
'sub_appellation'=>"Sub-Appellation",
'Region'=>"Region",
"products_cimageurl" => "Image"
);

global $default_sortby;

$default_sortby = 'ID';

global $rating_cols;

$rating_cols = array(
array(
'RP_Rating',
'RP_Review'
),
array(
'WS_Rating',
'WS_Review'
),
array(
'WE_Rating',
'WE_Review'
),
array(
'DC_Rating',
'DC_Review'
),
array(
'ST_Rating',
'ST_Review'
),
array(
'W_S_Rating',
'W_S_Review'
),
array(
'BTI_Rating',
'BTI_Review'
),
array(
'Winery_Rating',
'Winery_Review'
)
);

global $mag_names;

$mag_names = array('Robert Parker', 'Wine Spectator', 'Wine Enthusiast', 'Decanter', 'Stephen Tanzer', 'Wine & Spirits', 'Beverage Tasting Institute', 'Winery');

$smarty->assign('display_cols', $display_cols);
$smarty->assign('rating_cols', $rating_cols);
