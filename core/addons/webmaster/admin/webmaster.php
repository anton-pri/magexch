<?php
namespace cw\webmaster;

// use $target, $mode and $action params to define subject and action to call
// e.g. $target_$mode_$action or $target_$mode or $target_$action
$action_function = join('_',array_filter(array($target,$mode,$action)));

// Default action
if (empty($action_function) || !function_exists('cw\\'.addon_name.'\\'.$action_function)) {
	$action_function = 'webmaster_view';
}

// Call action
$action_result = cw_call('cw\\'.addon_name.'\\'.$action_function);

// Action can return instance of Error via error() function
// see docs/core.error.txt
if (is_error($action_result)) {
    cw_add_top_message($action_result->getMessage(), 'E');
}

return $action_result;

/* ================================================================================== */

/* Actions */

// Show popup with edit form
function webmaster_view() {
    global $identifiers, $request_prepared, $smarty;
    
    $webmaster_status = cw_session_register('webmaster_status');
    
    if (empty($identifiers['A']) || empty($webmaster_status)) {
        return error('You must be logged in as admin and enable webmaster mode');
    }

    if (!$request_prepared['type']) {
        return error('Empty type of webmaster entry'); // return Error instance
    }
    if (!$request_prepared['key']) {
        return error('Empty webmaster entry'); // return Error instance
    }
    
    $type = $request_prepared['type'];
    $key = $request_prepared['key'];
    
    // $data[value] -  single param for simple edit form with textarea
    //                 extend in hook with additional params if necessary
    $data = cw_event('on_webmaster_view_'.$type, array($key)); 

    if (is_error($data)) {
        return $data;
    }
    
    $smarty->assign(array(
        'type' => $type,
        'key'  => $key,
        'xss'  => md5($type.$key.$webmaster_status),
        'data' => $data,
    ));

    cw_add_ajax_block(array(
        'id'        => 'webmaster_modify',
        'action'    => 'remove',
    ));
    cw_add_ajax_block(array(
        'id'        => 'script',
        'content'    => '$("body").append("<div id=\'webmaster_modify\' style=\'display:hidden;\'>");',
    ));
    cw_add_ajax_block(array(
        'id'        => 'webmaster_modify',
        'action'    => 'update',
        'template'  => 'addons/webmaster/webmaster_modify_popup.tpl',
    ));

    $title = cw_get_langvar_by_name('lbl_webmaster_title_'.$type, array('key'=>$key, 'popup_title'=>$data['popup_title']),false,true,true,true);
    $edit = cw_get_langvar_by_name('lbl_edit',null,false,true,true,true);
    
    cw_add_ajax_block(array(
        'id'        => 'script',
        'content'  => "sm('webmaster_modify',null,null,true,'$edit $title')",
    ));    
    return $data;
}

// Update POST handler
function webmaster_modify() {
    global $identifiers, $request_prepared, $config;
   
    $webmaster_status = cw_session_register('webmaster_status');
    
    if (empty($identifiers['A']) || empty($webmaster_status)) {
        return error('You must be logged in as admin and enable webmaster mode');
    }
    
    $type = $request_prepared['type'];
    $key = $request_prepared['key'];
    
    if (md5($type.$key.$webmaster_status) != $request_prepared['xss']) {
        return error('Invalid security token');
    }
    
    $result = cw_event('on_webmaster_modify_'.$type, array($key,$request_prepared['data']));
    
    if (is_error($result)) {
        return $result;
    }    
    
    cw_add_top_message("Successful update.");
    
    cw_add_ajax_block(array(
        'id'        => 'script',
        'content'  => 'hm("webmaster_modify");',
    ));
    
    if ($config['webmaster']['do_not_reload'] != 'Y') {
        cw_add_top_message("Reloading...");
        cw_add_ajax_block(array(
            'id'        => 'script',
            'content'  => 'window.location.reload(true);',
        ));
    } else {
        cw_add_top_message("Reload page manually to see the changes.");
    }

}

// Addon settings: Show current status of  webmaster mode for current session
function webmaster_status() {
    
    $webmaster_status = cw_session_register('webmaster_status');

    if(empty($webmaster_status)) {
        $content = '<a href="index.php?target=webmaster&mode=status&action=on" class="ajax">Click to Enable</a>';
    }
    else {
        $content = '<a href="index.php?target=webmaster&mode=status&action=off" class="ajax">Click to Disable</a>';
    }
    
    cw_add_ajax_block(array(
        'id' => 'webmaster_status',
        'content' => $content
    ));
}

// Addon settings: Enable webmaster mode for current session
function webmaster_status_on() {
    $webmaster_status =& cw_session_register('webmaster_status');
    $webmaster_status = md5(uniqid(rand(0,1000), true)); // This value used as hash seed for XSS protection and should not be known on browser side;  sess ID does not fit for this purpose.
    webmaster_status();
}

// Addon settings: Enable webmaster mode for current session
function webmaster_status_off() {
    $webmaster_status =& cw_session_register('webmaster_status');
    $webmaster_status = null;
    cw_session_unregister('webmaster_status');

    // cleanup
    cw_load('files');
    $result = cw_call_delayed('cw_cleanup_cache');

    webmaster_status();
}
