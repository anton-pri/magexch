<?php
$__start_mictotime = microtime(true);

$app_dir = realpath(dirname(__FILE__));
$app_main_dir = $app_dir.'/core';

// Merge config files
$app_config_file = parse_ini_file($app_dir.'/include/config.ini', true);
$_app_config_file = parse_ini_file($app_dir.'/include/config.local.ini', true);
if (!empty($_app_config_file)) {
    foreach ($app_config_file as $app_config_section=>$app_config) {
        if (isset($_app_config_file[$app_config_section]))
            $app_config_file[$app_config_section] = array_merge((array)$app_config,(array)$_app_config_file[$app_config_section]);
    }
}
$only_in_local_config = array_diff_key($_app_config_file,$app_config_file);
$app_config_file = array_merge($app_config_file,$only_in_local_config);
unset($_app_config_file, $app_config, $app_config_section, $only_in_local_config);

$area = isset($_REQUEST['area'])?$_REQUEST['area']:'customer';
$target = isset($_REQUEST['target'])?$_REQUEST['target']:'index';

define('APP_START', 1);

$request_prepared = array();

define('APP_AREA', $area);

include_once $app_main_dir.'/init.php';

cw_include($area.'/auth.php');
cw_event('on_before_'.$target);
cw_event('on_before_'.$target.'_'.$action);
cw_include($area.'/'.$target.'.php');

if (defined('IS_AJAX') && !defined('PREVENT_XML_OUT')) {
    cw_include($area.'/ajax.php');
    exit(0);
}

$__script_microtime = microtime(true) - $__start_mictotime;

$__bech_display_id = cw_bench_open_tag('DISPLAY','POINT','');
cw_display($area.'/index.tpl', $smarty, true);
cw_bench_close_tag($__bech_display_id);

$__smarty_microtime = microtime(true) - $__start_mictotime - $__script_microtime;

// Time end

if (!defined('IS_AJAX')) {
    // Time end
    $__output_microtime = 'Runtime: '.sprintf("%.4f",$__smarty_microtime+$__script_microtime).' (SCRIPT: '.sprintf("%.4f",$__script_microtime).'; SMARTY: '.sprintf("%.4f",$__smarty_microtime).')';

    if (!$app_config_file['debug']['development_mode']) {
        $__output_microtime = '<!-- '.$__output_microtime.' -->';    
    }
    echo $__output_microtime;
}

cw_bench_close_tag($__bech_main_id);

include_once $app_main_dir.'/include/bench.php';

if (function_exists('fastcgi_finish_request')) fastcgi_finish_request(); // Close connection

// At the end all registered shutdown function called including registered via cw_call_delayed()

// Last shutdown function meters time of shutting down process
$__shutdown_microtime = microtime(true);
register_shutdown_function('cw__shutdown_microtime');
function cw__shutdown_microtime() {
    global $__shutdown_microtime,$app_config_file;
    $__shutdown_microtime = microtime(true)-$__shutdown_microtime;
    $__output_microtime = ' Shutdown: '.sprintf("%.4f",$__shutdown_microtime);
    if (!$app_config_file['debug']['development_mode']) {
        $__output_microtime = '<!-- '.$__output_microtime.' -->';    
    }
    echo $__output_microtime;    
}
