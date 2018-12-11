<?php
/**
 * Global platform variables. These variables cannot be passed as GET or POST parameter
 * 
 * besides some historical global vars such as $tables or $config, all new system global vars named as $cw_something
 * Do not use such a notation for own global vars to avoid system corruption
 * 
 */


global $app_dir, $app_main_dir, $app_config_file, $var_dirs, $area, $target, $request_prepared;
global $tables, $config, $smarty, $customer_id, $location, $top_message, $addons, $user_account, $identifiers; 
global $all_languages, $ajax_blocks;

// benchmark related vars
global $__bench, $__bench_counter, $__bench_depth;
$__bench_counter = 1;

// $cw_allowed_tunnels contains array of functions names allowed to call from templates using {tunnel} tag
global $cw_allowed_tunnels;
$cw_allowed_tunnels = array();

// $cw_attributes - array of all attributes organized in several subarrays. Initiated and used in API defined in cw.attributes.php 
global $cw_attributes;

// $cw__langvars - cached langvars pre lang/area
global $cw__langvars;

// $cw__call_delayed - store all registered calls via cw_call_delayed()
// These functions will be executed as shutdown functions
global $cw__call_delayed;

// $cw_trusted_variables - array of variable names with allowed HTML content. <script> is prohibited anyway
global $cw_trusted_variables;
$cw_trusted_variables = array();

global $HTTPS;

global $smarty;
