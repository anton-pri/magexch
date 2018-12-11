<?php

define('SITEMAP_MAX_SCAN_DEPTH', 0);

if (APP_AREA == 'admin') {
    
    cw_addons_set_controllers(
        array('replace', 'admin/sitemap_xml.php', 'addons/sitemap_xml/admin/sitemap_xml.php'),
        array('replace', 'admin/cron_sitemap_xml.php', 'addons/sitemap_xml/cron/cron_sitemap_xml.php')
    );

    cw_addons_set_template(
        array('replace', 'admin/sitemap_xml/sitemap_xml.tpl', 'addons/sitemap_xml/sitemap_xml.tpl')
);
}

if (APP_AREA == 'customer' && $target == 'sitemap') {
	cw_set_controller('customer/sitemap.php','addons/sitemap_xml/customer/sitemap.php', EVENT_REPLACE);
}

if (APP_AREA == 'cron') {
	cw_include('addons/sitemap_xml/include/func.php');
    cw_set_controller('init/abstract.php','addons/sitemap_xml/include/abstract.php',EVENT_POST);
}
