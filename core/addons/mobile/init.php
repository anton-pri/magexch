<?php
/*
 * Vendor: CW
 * addon: mobile skin
 */

const mobile_addon_name 		= 'mobile';
const mobile_addon_skin_prefix	= '.mobi';

cw_include('addons/' . mobile_addon_name . '/include/func.mobile.php');
cw_include('addons/' . mobile_addon_name . '/include/Mobile_Detect.php');

if (APP_AREA == 'admin') {
	cw_addons_set_template(
		array('post', 'main/docs/extras.tpl', 'addons/' . mobile_addon_name . '/extras.tpl'),
        array('post', 'main/docs/extras_title.tpl', 'addons/' . mobile_addon_name . '/extras.tpl')
	);
}

if (APP_AREA == 'customer') {
	cw_addons_set_controllers(
	    array('post', 'init/abstract.php', 'addons/' . mobile_addon_name . '/init/mobile.php'),
        array('pre', 'customer/referer.php', 'addons/' . mobile_addon_name . '/customer/referer.php')
	);

	cw_addons_set_hooks(
	    array('post', 'cw_code_get_template_dir', 'cw_mobile_code_get_template_dir'),
	    array('post', 'cw_doc_place_order', 'cw_mobile_doc_place_order')
	);

	cw_set_hook('cw_md_get_domain_data_by_alias', 'cw_mobile_get_domain_data_by_alias');
	
	cw_addons_set_template(
		array('post', 'elements/copyright.tpl', 'addons/' . mobile_addon_name . '/bottom_links.tpl')
	);
}
