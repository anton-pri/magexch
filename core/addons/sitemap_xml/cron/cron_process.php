<?php
cw_include('init/lng.php');
global $domains;
$_domains = cw_func_call('cw_md_get_domains');
$domains = array_column($_domains, 'domain_id');
unset($_domains);
define('IS_CRON',true);
cw_include('addons/sitemap_xml/include/create_sitemap_xml.php');

echo "\n".$top_message['type'].': '.$top_message['content']."\n";
