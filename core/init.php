<?php
/*
 * Required includes
 */
include_once $app_main_dir.'/init/constants.php';
include_once $app_main_dir.'/init/config.php';
include_once $app_main_dir.'/include/lib/events/init.php';
include_once $app_main_dir.'/include/functions/cw.hook.php';
include_once $app_main_dir.'/include/functions/cw.core.php';
include_once $app_main_dir.'/init/globals.php';

$__bech_main_id = cw_bench_open_tag('MAIN', 'POINT','');

include_once $app_main_dir.'/include/lib/error/init.php';
include_once $app_main_dir.'/include/lib/cache/init.php';


cw_load('db', 'files', 'session', 'prepare', 'user', 'auth', 'system_messages');

/*
 * AJAX detection
 */
if (cw_is_ajax_request()) {
   cw_load('ajax');
   define('IS_AJAX', true);
}

if (function_exists('date_default_timezone_set')) {
    if (!empty($app_config_file['time_zone']['default_zone'])) 
        date_default_timezone_set($app_config_file['time_zone']['default_zone']);
}

cw_include('init/prepare.php');
include_once $app_main_dir.'/config.php';

// Redefine error_reporting option (see config.php)
error_reporting ($app_error_reporting);

// Connect to database
if (!(@db_connect($app_config_file['sql']['host'], $app_config_file['sql']['user'], $app_config_file['sql']['password']) && db_select_db($app_config_file['sql']['db']))) {
    if (is_readable($app_dir.'/install/index.php')) {
        die('Problem with DB settings. Proceed with <a href="'.str_replace('\\', '/', 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF'])).'/install/index.php" >installation</a>');
    } else {
        die('Problem with DB settings. Installation script also is not found. Please contact administrator.');
    }
}


if (is_readable($app_dir.'/install/index.php')) {
    die('Installation script is accessible - store is vulnerable - please rename install folder or protect it by setting appropriate permissions or just delete');
}
db_query('SET NAMES "utf8"');

if (!in_array($area, array('cron'))) {
    include_once $app_main_dir . '/init/sessions.php';
}

include_once($app_main_dir.'/include/blowfish.php');
$blowfish = new ctBlowfish();

// Init addons
global $_current_hook_order;
$active_addons = cw_call('cw_core_get_addons');
$active_addons[] = array('addon'=>'salesman');
$active_addons[] = array('addon'=>'warehouse');



if (is_array($active_addons))
foreach($active_addons as $adn) {
    $addons[$adn['addon']] = true;
    $_current_hook_order = $adn['orderby'];
    $_include_addon_file ='addons/'.$adn['addon'].'/init.php';
    cw_include($_include_addon_file);
}
unset($active_addons, $adn, $_include_addon_file, $_current_hook_order);

cw_call('cw_hook_controllers_autoload');
cw_include('init/post_init.php');

// Strip tags in all html_* var which are not in trusted list
foreach ($request_prepared as $__var => $__res) {
    if (strpos($__var,'html_')===0 
        && (!in_array($__var, $cw_trusted_variables) 
            || cw_have_script_tag($__res)
            )
    ) {
        $__res = cw_strip_tags($__res);
        $$__var = $request_prepared[$__var] = $__res;
    }
}

// Remove trailing slash and add leading slash
$app_config_file['web']['web_dir'] = with_leading_slash_only($app_config_file['web']['web_dir']);
$app_config_file['web']['skin'] =  with_leading_slash_only($app_config_file['web']['skin']);

$app_skin_dir = $app_config_file['web']['skin'];
$app_web_dir = $app_config_file['web']['web_dir'];
$http_location = 'http://'.$app_config_file['web']['http_host'].$app_config_file['web']['web_dir'];
$https_location = 'https://'.$app_config_file['web']['https_host'].$app_config_file['web']['web_dir'];

cw_include('init/https_detect.php');
$current_location = $HTTPS ? $https_location : $http_location;
$current_host_location = $HTTPS ? 'https://' . $app_config_file['web']['https_host'] : 'http://' . $app_config_file['web']['http_host'];

$config = cw_call('cw_core_get_config');

cw_include('include/logging.php');
cw_include('init/smarty.php');

// Get var_dirs items from addons 
$cw_var_dirs = $var_dirs;
cw_call('on_build_var_dirs', array(&$cw_var_dirs));

// Recreate service folders in var dir
foreach ($cw_var_dirs as $k => $v) {

    $var_dirs[$k] = $v['path'];

    if (!file_exists($v['path']) || !is_dir($v['path'])) {
        @unlink($v['path']);
        cw_mkdir($v['path']);
    }

    if ((!is_writable($v['path']) || !is_dir($v['path'])) && $v['criticality']) {
        exit("Cannot write data to the temporary directory \"" . $v['path'] . "\" Please check if it exists, and has writable permissions.");
    }

    if (fileperms($v['path'])&0777 != $v['mode']) {
        @chmod($v['path'], $v['mode']);
    }

    if (is_array($v['files']) && count($v['files'])) {

        foreach ($v['files'] as $f => $c) {

            if (!file_exists($v['path'] . '/' . $f)) {

                if ($fp = @fopen($v['path'] . '/' . $f, 'w')) {
                    @fwrite($fp, $c['content']);
                    @fclose($fp);
                    @chmod($v['path'] . '/' . $f, $c['mode']);
                }
            }
            elseif (fileperms($v['path'] . '/' . $f)&0777 != $c['mode']) {
                @chmod($v['path'] . '/' . $f, $c['mode']);
            }
        }
    }
}

// Init miscellaneous vars
$smarty->assign('app_web_dir', $app_web_dir);
$smarty->assign('app_http_host', $app_config_file['web']['http_host']);
$smarty->assign('current_location', $current_location);
$smarty->assign('current_host_location', $current_host_location);

foreach ($var_dirs_web as $k=>$v)
    $var_dirs_web[$k] = $current_location.$v;
$smarty->assign_by_ref("var_dirs_web", $var_dirs_web);

$app_catalogs = array();
$app_catalogs_secure = array();
foreach ($app_dirs as $k=>$v) {
    $app_catalogs[$k] = $current_location.($v?with_leading_slash($v):'');
    $app_catalogs_secure[$k] = $https_location.($v?with_leading_slash($v):'');
}

$smarty->assign('catalogs', $app_catalogs);
$smarty->assign('catalogs_secure', $app_catalogs_secure);

# Files directories
$smarty->assign('files_location', $var_dirs['files']);

if (!$config['Company']['start_year']) $config['Company']['start_year'] = date('Y', cw_core_get_time());
$config['Company']['end_year'] = date('Y', cw_core_get_time());

if (empty($config['Appearance']['date_format']))
    $config['Appearance']['date_format'] = "%d-%m-%Y";

$config['Appearance']['datetime_format'] = $config['Appearance']['date_format']." ".$config['Appearance']['time_format'];

if ($HTTPS && $config['performance']['use_cdn_for_https']!='Y') {
    $config['performance']['list_available_cdn_servers'] = '';
}

// Top message - simple way to pass message from page to page
// TODO: Wrap top message mechanism to functions to avoid direct variable usage
global $top_message;
$top_message = &cw_session_register('top_message',array());
if (!empty($top_message)) {
    $smarty->assign('top_message', cw_array_map('nl2br',$top_message));
    $top_message = array();
}

cw_include('init/numbers.php');

$taxes_units = array(
    'ST'  => 'lbl_subtotal',
    'DST' => 'lbl_discounted_subtotal',
    'SH'  => 'lbl_shipping_cost',
    'PM'  => 'lbl_payment_method',
);

if ($config['card_types'])
    $config['card_types'] = unserialize ($config['card_types']);

//filter card types by active flag in customer area
if (APP_AREA == 'customer') {
    $_card_types = array();
    foreach ($config['card_types'] as $card_type) {
        if (!isset($card_type['active']) || $card_type['active'] == 1) {
            $_card_types[] = $card_type;   
        }   
    }    
    $config['card_types'] = $_card_types;
}

$smarty->assign ("card_types", $config['card_types']);

if($config['General']['enable_smarty_debug_panel']=="Y" && !defined('IS_AJAX'))
    $smarty->debugging=true;

$smarty->assign('PROXY_IP', $PROXY_IP);
$smarty->assign('CLIENT_IP', $CLIENT_IP);
$smarty->assign('REMOTE_ADDR', $REMOTE_ADDR);

// Detect crawlers and search robots
cw_include('init/robot.php');

$smarty->assign('addons', $addons);

if (!$addons['image_verification'])
    cw_session_unregister("antibot_validation_val");

if ((isset($_GET['delimiter'])  && $_GET['delimiter']=="tab")
||  (isset($_POST['delimiter']) && $_POST['delimiter']=="tab"))
    $delimiter = "\t";

$available_images = cw_query_hash("select * from $tables[available_images]", 'name', false);
if (is_array($available_images))
foreach($available_images as $k=>$v)
    $tables[$k] = 'cw_'.$k;

// TODO. Move the special sections out from init.php - it requires in customer area only
$special_sections = array('arrivals', 'hot_deals', 'clearance', 'super_deals', 'accessories', 'bottom_line');
$res = null;
foreach($special_sections as $val) {
    $tables[$val] = 'cw_'.$val;
    $res[$val] = cw_get_langvar_by_name("lbl_menu_$val");
}
$smarty->assign('special_sections', $res);

$smarty->assign('request_uri', $_SERVER['REQUEST_URI']);

if (empty($area)) {
    $area = 'customer';
}
elseif (!in_array($area, array_keys($app_dirs))) {
    cw_header_location($app_catalogs['customer'].'/index.php');
}

$prohibited_list = array(
    'auth',
    '',
);

if (in_array($target, $prohibited_list)) $target = 'index';

$target = preg_replace('/[^a-z,^A-Z,_,\-]/ims', '', $target);

$smarty->assign(array(
    'area' => $area,
    'app_area' => $area,
    'current_target' => $target, # target is used in templates in another meaning as target attr in <a> tag
    'app_config_file' => $app_config_file
    ));

cw_include('init/abstract.php');

if (defined('IS_EDITOR')) return;
