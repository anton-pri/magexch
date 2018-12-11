<?php

if (APP_AREA != 'admin' && APP_AREA != 'seller') return;

if (!in_array($target,array('','index','dashboard','quick_data','dashboard_system_messages'),true)) return;

define('SEARCH_LIMIT_FOR_AUTOCOMPLETE', 10);
define('NEWS_RSS_URL', 'http://www.cartworks.com/adminfeed.xml');

$tables['dashboard'] = 'cw_dashboard';

$cw_allowed_tunnels[] = 'dashboard_display_prepare';
$cw_allowed_tunnels[] = 'dashboard_section_statistic';

// Define abstract dashboard builder
cw_include('addons/dashboard/include/func.dashboard.php');

cw_addons_set_controllers(
    array('post', 'admin/index.php', 'addons/dashboard/admin/index.php'),
    array('replace', 'admin/dashboard.php', 'addons/dashboard/admin/configuration.php'),
    array('replace', APP_AREA . '/quick_data.php', 'addons/dashboard/admin/quick_data.php'),
    array('replace', 'admin/dashboard_system_messages.php', 'addons/dashboard/admin/dashboard_system_messages.php')
);

// Define all specific dashboard section builders
//cw_include('addons/dashboard/sections/dashboard_section_example.php');
cw_include('addons/dashboard/sections/dashboard_section_default.php');

// Register hooks
cw_addons_set_hooks(
	//array('post', 'dashboard_build_sections', 'dashboard_section_example'),
    //array('post', 'dashboard_build_sections', 'dashboard_section_statistic'),
    array('post', 'dashboard_build_sections', 'dashboard_section_search'),
    array('post', 'dashboard_build_sections', 'dashboard_section_graph'),
    array('post', 'dashboard_build_sections', 'dashboard_last_orders'),
    array('post', 'dashboard_build_sections', 'dashboard_section_system_messages'),
    array('post', 'dashboard_build_sections', 'dashboard_section_pending_reviews'),
    array('post', 'dashboard_build_sections', 'dashboard_section_awaiting'),
    array('post', 'dashboard_build_sections', 'dashboard_section_system_info'),
    array('post', 'dashboard_build_sections', 'dashboard_section_news')
);

cw_addons_add_css('addons/dashboard/admin/main.css');

cw_addons_set_template(
    array('post', 'admin/main/main.tpl', 'addons/dashboard/admin/index.tpl')
);

/*
 * jqPlot http://www.jqplot.com/
 */

// Excanvas is required only for IE versions below 9
preg_match("/(MSIE|Version)(?:\/| )([0-9.]+)/", $_SERVER['HTTP_USER_AGENT'], $matches);

if (
	count($matches)
	&& $matches[1] == 'MSIE' 
	&& $matches[2] < 9.0
) {
	cw_addons_add_js('addons/dashboard/js/excanvas.min.js');
}

cw_addons_add_js('addons/dashboard/js/jquery.jqplot.min.js');
cw_addons_add_js('addons/dashboard/js/plugins/jqplot.dateaxisrenderer.min.js');
cw_addons_add_js('addons/dashboard/js/plugins/jqplot.highlighter.min.js');
cw_addons_add_css('addons/dashboard/js/jquery.jqplot.min.css');
