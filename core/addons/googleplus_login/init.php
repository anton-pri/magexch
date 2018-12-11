<?php
/*
 * Vendor: cw
 * addon: googleplus_login
 */

/*
 * init.php
 * this file only defines constants, variables, functinos, hooks and event hanlers
 * no real routine must be here on init stage
 */

// Use namespace for your own addon as vendor\addon_name
//namespace cw\googleplus_login;

// Constants definition
// these constants are defined in scope of addon's namespace
//const addon_name    = 'googleplus_login';       

// Include functions
cw_include('addons/googleplus_login/include/func.php');

/*
if (APP_AREA == 'admin') {
    // Define own controller which does not exists yet using EVENT_REPLACE
     cw_set_controller(APP_AREA.'/'.addon_target.'.php','addons/'.addon_name.'/admin/'.addon_target.'.php', EVENT_REPLACE);
}
*/
if (APP_AREA == 'customer') {
    // Sometimes some part of initialization must be after all addons init - in post_init.php
    cw_set_controller('include/check_useraccount.php', 'addons/googleplus_login/post_init.php', EVENT_POST);

    cw_event_listen('on_logout', 'cw_googleplus_on_logout');

    cw_addons_set_template(
        array('post', 'buttons/social_media_panel.tpl', 'addons/googleplus_login/customer/auth-button.tpl')
    );
}
