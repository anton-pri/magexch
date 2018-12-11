<?php
namespace cw\addon_skeleton;

// use $target, $mode and $action params to define subject and action to call
// e.g. $target_$mode_$action or $target_$mode or $target_$action
$action_function = join('_',array_filter(array($target,$mode,$action)));

// According to PSR all functions/constant/classes definition must be separated from logic
// Include once same_file_name.actions.php
cw_include_once(dirname(strstr(__FILE__,'addons/'.addon_name)).'/'.basename(__FILE__,'.php').'.actions.php');

// Default action
/*
if (empty($action_function) || !function_exists('cw\\'.addon_name.'\\'.$action_function)) {
	$action_function = 'view';
}
*/

// Call action
$action_result = cw_call('cw\\'.addon_name.'\\'.$action_function);

// Action can return instance of Error via error() function
// see docs/core.error.txt
if (is_error($action_result)) {
    cw_add_top_message($action_result->getMessage(), 'E');
}

$smarty->assign('current_main_dir', 'addons/'.addon_name);
$smarty->assign('current_section_dir', 'admin');

return $action_result;
