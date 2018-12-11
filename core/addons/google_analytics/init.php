<?php
/*
 * Vendor: cw
 * addon: google_analytics
 */

/*
 * init.php
 * this file only defines constants, variables, functinos, hooks and event hanlers
 * no real routine must be here on init stage
 */

// Constants definition
// these constants are defined in scope of addon's namespace
const addon_name    = 'google_analytics';       

if (APP_AREA == 'customer') {
    cw_addons_set_template(
        array('post', 'elements/bottom.tpl', 'addons/'.addon_name.'/customer/ga_traditional.tpl'), 
//
//    {if $home_style ne 'iframe' && $home_style ne 'popup'}
//        {include file='elements/bottom.tpl'}
//    {/if}
//
        array('post', 'customer/service_js.tpl', 'addons/'.addon_name.'/customer/ga_asynchronous.tpl')
    );
}
