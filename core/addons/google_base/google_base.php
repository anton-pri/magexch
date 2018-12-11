<?php
if (empty($addons['google_base']))
	return false;

set_time_limit(5*60);

define('GB_XML_OUT',0); // Use for debug purposes;

$top_message =& cw_session_register('top_message');

if ($REQUEST_METHOD == 'POST' && $mode == 'gb_xml_create') {
//	cw_display_service_header();
	cw_include('addons/google_base/create_gb_xml.php');

	cw_header_location('index.php?target=google_base');
}

if ($addons['multi_domains']) {
    $smarty->assign('all_domains',cw_md_get_domains());
}

$smarty->assign('main','google_base');
