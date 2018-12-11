<?php
/*
 * Vendor: cw
 * addon: linkedin_login
 */

/*
 * init.php
 * this file only defines constants, variables, functinos, hooks and event hanlers
 * no real routine must be here on init stage
 */

// Use namespace for your own addon as vendor\addon_name
//namespace cw\linkedin_login;

// Constants definition
// these constants are defined in scope of addon's namespace
//const addon_name    = 'linkedin_login';       

// Include functions
cw_include('addons/linkedin_login/include/func.php');

if (APP_AREA == 'customer') {
    cw_set_controller('include/check_useraccount.php', 'addons/linkedin_login/post_init.php', EVENT_POST);

    cw_set_controller(APP_AREA.'/login_with_linkedin.php', 'addons/linkedin_login/customer/login_with_linkedin.php', EVENT_REPLACE);

    cw_event_listen('on_logout', 'cw_linkedin_on_logout');

    cw_addons_set_template(
        array('post', 'buttons/social_media_panel.tpl', 'addons/linkedin_login/customer/auth-button.tpl')
    );
}
