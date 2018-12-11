<?php
namespace cw\addon_skeleton;


/* Actions */

function addon_main_target_view() {
    global $request_prepared;
    
    if (!$request_prepared['id']) {
        return error('Invalid instance ID'); // return Error instance
    }
    
    /*
     * Do actions with object here
     */
    
    
    return $object;
}

function addon_main_target_modify() {
}

function addon_main_target_add() {
}

function addon_main_target_delete() {
}

/* Service functions */

function get_data() {
}
