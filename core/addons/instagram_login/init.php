<?php
/*
 * Vendor: cw
 * addon: instagram_login
 */

/*
 * init.php
 * this file only defines constants, variables, functinos, hooks and event hanlers
 * no real routine must be here on init stage
 */

// Use namespace for your own addon as vendor\addon_name
//namespace cw\instagram_login;

// Constants definition
// these constants are defined in scope of addon's namespace
//const addon_name    = 'instagram_login';       

// Include functions
cw_include('addons/instagram_login/include/func.php');

if (APP_AREA == 'customer') {
    cw_set_controller('include/check_useraccount.php', 'addons/instagram_login/post_init.php', EVENT_POST);

    cw_event_listen('on_logout', 'cw_instagram_on_logout');

    cw_addons_set_template(
        array('post', 'buttons/social_media_panel.tpl', 'addons/instagram_login/customer/auth-button.tpl')
    );
}
