<?php

/**
 * Define main cron task script:
 *
 * cron_call -  All scripts in queue are executed in same run via cw_call()
 *              Error in one cron handler blocks all other cron tasks.
 *              Use when exec() is prohibited.
 *		 
 * cron_exec -  All scripts in queue are executed independently via exec()
 *              Error in one cron handler doesn't affect all other cron tasks.
 *              Recommended.
 */
define('CW_CRON_SCRIPT', 'cron_exec');

define('IS_CRON', true);

if (defined('APP_START') && constant('APP_START')) {
    cw_include('cron/'.constant('CW_CRON_SCRIPT').'.php');
    exit(0);
}

if (!defined('APP_START')) {

    define('IS_CRON_AS_CLI', true);
    
    $app_dir = realpath(dirname(__FILE__).'/../../');

    $params = $_SERVER['argv'];
    if (empty($params) && !empty($_GET)) $params = array_keys($_GET);
    if (empty($params[1])) die("Unauthorized access. Code is required\n");
    $_REQUEST['area'] = 'cron';
    $_REQUEST['target'] = constant('CW_CRON_SCRIPT');
    $_REQUEST['code'] = $params[1];
    $_REQUEST['manual_run'] = isset($params[2])?$params[2]:'';
    $_REQUEST['counter'] = isset($params[3])?$params[3]:0;

    include $app_dir.'/index.php';
    exit(0);
}

die('Unauthorized execution as '.php_sapi_name());
