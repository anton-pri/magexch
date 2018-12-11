<?php
/*
 * Vendor:	CW
 * addon:	wordpress
 * description:	
 *  addon prepares dynamic layout elements for 3-rd party software, such as heade, minicart, footer
 */
 
namespace CW\wordpress;

const addon_namespace = 'CW\wordpress';
const addon_name = 'wordpress';

if (APP_AREA == 'customer') {
	cw_include('addons/'.addon_name.'/func.php');
	
	cw_set_controller(
        'customer/'.addon_name.'.php',
        'addons/'.addon_name.'/elements.php',
        EVENT_REPLACE
    );	
}


