<?php
global $ajax_blocks, $area, $subtarget, $mode;

// customer specific requests

/* TODO: rework AJAX to use XML */
// Old style AJAX JSON requests
if (in_array($action, array('load_address', 'update_address', 'delete_address', 'load_addresses'))) {
    global $user, $self_modification;
    $user = $customer_id;
    $self_modification = 1;
}
if ($mode == 'cart_update') {
    $action = 'ajax_update';
    include $app_main_dir.'/customer/cart.php';
    exit(0);
}
// < Old style AJAX JSON requests

/**
 * Handler for AJAX request index.php?mode=check_mail&email=<address>
 * Check if email already registered
 */
if ($mode == 'check_email') {
    $c = \Customer\getByEmail($request_prepared['email']);
    cw_add_ajax_block(array(
        'id' => 'check_email_result',
        'action' => 'remove',
    ));
    cw_add_ajax_block(array(
        'id' => 'check_email_result',
        'action' => 'json',
        'content' => json_encode(!empty($c)), // true - exists; false - not
    ));
    if (!empty($c) && !$guest) {
        cw_add_ajax_block(array(
            'id' => 'profile_form',
            'action' => 'prepend',
            'template' => 'customer/acc_manager/_check_email_result.tpl',
        ));
    }
}

cw_include('include/ajax.php');

// END
// script does not return back to this file
