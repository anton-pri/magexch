<?php
cw_include('addons/sitemap_xml/include/func.php');

if (empty($addons['sitemap_xml']))
	return false;

if ($REQUEST_METHOD == 'POST' && $mode == 'sitemap_xml_create') {
	cw_display_service_header();
	cw_include('addons/sitemap_xml/include/create_sitemap_xml.php');
    exit();
	cw_header_location('index.php?target=sitemap_xml');
}

if ($addons['multi_domains']) {
	$all_domains = cw_md_get_domains();
    foreach ($all_domains as $k=>$d) {
		$all_domains[$k]['filetime'] = is_readable(cw_sitemap_filename($d['name']))?date("Y-m-d H:i:s", filectime(cw_sitemap_filename($d['name']))):0;
	}
	$smarty->assign('all_domains',$all_domains);

}

$smarty->assign('cd_path', $app_main_dir.'/cron');
$smarty->assign('main','sitemap_xml');
$smarty->assign('hide_domain','Y');
