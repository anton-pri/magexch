<?php

const anti_scrapping_addon_name = 'anti_scrapping_robot';
const anti_scrapping_addon_version = '0.1';

if (constant('APP_AREA') == 'customer') {
	cw_include('addons/' . anti_scrapping_addon_name . '/include/func.anti_scrapping.php');

	cw_addons_set_controllers(
		array('post', 'init/robot.php', 'addons/' . anti_scrapping_addon_name . '/init/robot.php')
	);

	cw_addons_set_hooks(
		array('post', 'cw_product_search', 'cw_anti_scrapping_product_search'),
		array('post', 'cw_product_get', 'cw_anti_scrapping_product_get')
	);
}
