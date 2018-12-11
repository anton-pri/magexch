<?php
/*
 * Vendor: cw
 * addon: mslive_login
 */

/*
 * init.php
 * this file only defines constants, variables, functinos, hooks and event hanlers
 * no real routine must be here on init stage
 */

// Use namespace for your own addon as vendor\addon_name
//namespace cw\mslive_login;

// Constants definition
// these constants are defined in scope of addon's namespace
//const addon_name    = 'mslive_login';       

// Include functions
cw_include('addons/mslive_login/include/func.php');

if (APP_AREA == 'customer') {
    cw_set_controller('include/check_useraccount.php', 'addons/mslive_login/post_init.php', EVENT_POST);

    cw_set_controller(APP_AREA.'/mslive_login.php', 'addons/mslive_login/customer/mslive_login.php', EVENT_REPLACE);

    cw_event_listen('on_logout', 'cw_mslive_on_logout');

    cw_addons_set_template(
        array('post', 'buttons/social_media_panel.tpl', 'addons/mslive_login/customer/auth-button.tpl')
    );
}
