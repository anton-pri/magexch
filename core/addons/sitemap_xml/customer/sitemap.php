<?php
cw_include('addons/sitemap_xml/include/func.php');

if (is_readable($sitemap_filename = cw_sitemap_filename($filename, true))) {
            header('Content-Type: text/xml');
            if ($config['sitemap_xml']['sm_pack_result'] == 'Y') {
				header('Content-Encoding: gzip');
			}
            echo file_get_contents($sitemap_filename);	
}
exit();
