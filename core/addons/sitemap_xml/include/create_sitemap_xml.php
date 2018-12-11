<?php
global $current_domain;
global $addons, $app_web_dir;

cw_load('product','category','taxes');

$success = false;
    
$is_mdm = !empty($tables['domains']);

if (!$is_mdm) $domains = array(0);

if (!is_array($domains)) {
	cw_flush('No domains specified');
	cw_add_top_message('No domains specified. Please select domains which should be included into the sitemap.','W');
	return false;
}

foreach ($domains as $domain_id) {
if ($addons['multi_domains']) {
    $domain = cw_func_call('cw_md_domain_get',array('domain_id'=>$domain_id));
}
$_filename = cw_sitemap_name('', false);
if ($is_mdm) {
    $http_location = without_slash("http://".$domain['http_host'].$domain['web_dir']);
    $https_location = without_slash("https://".$domain['https_host'].$domain['web_dir']);
    $_filename = cw_sitemap_name($domain['name'], false);
}

// Use https by default
if ($config['sitemap_xml']['use_http'] != 'Y') {
    $http_location = $https_location;
}

$today = date('Y-m-d');

if (($filename = cw_allow_file($_filename, true)) && $file = cw_fopen($_filename, 'w', true)) {
	$success = true;
	$data = array(
		'home' => array(''),
		'cat_manuf' => array(),
		'product' => array(),
		'static' => array(),
	);

    $current_domain = $domain_id;

    $r = cw_func_call('cw_category_search',array('data'=>array('status'=>1,'all'=>true)));
    unset($r[1]); // release navigation info
    $items =& $r[0];

	if (is_array($items)) {
		cw_flush('<h2>'.cw_get_langvar_by_name('lbl_categories', null, false, true).'</h2>');
		foreach($items as $v) {
			cw_flush('.');
            $url = cw_call('cw_core_get_html_page_url', array(array('var'=>'index','cat'=>$v['category_id'])));
            $url = str_replace(with_slash($app_web_dir), '', $url);
			if (!empty($url)) {
				$data['cat_manuf'][] = $url;
			} else {
				$data['cat_manuf'][] = 'index.php?cat=' . $v['category_id'];
			}
		}
		cw_flush(count($items)."<br />\n");
	}

    // Home page facets in different combinations
    if (isset($app_config_file['interface']['product_filter_home_class'])) {
        cw_flush("<h2>Home facet categories</h2> <br />\n");

        global $sitemap_pf_links, $current_language;

        $home_cache_key = array($current_language, 0);
        list($product_filter, $navigation) = cw_cache_get($home_cache_key, 'PF_home');
        $attributes_for_scan = array_column($product_filter,'attribute_id');
        $product_filter = cw_call('cw_sitemap_parse_product_filter', array($product_filter,$attributes_for_scan));
        cw_cache_save(array($product_filter, $navigation),$home_cache_key,'PF_home');

        foreach($sitemap_pf_links as $v) {
            $data['cat_manuf'][] = ltrim($v,'/');

        }
        cw_flush("<br />\nTotal: ".count($sitemap_pf_links)."<br />\n");
    }
    
    // Facets with custom description
    $facets = cw_query("SELECT cfu.url_id, cfuo.attribute_value_ids 
    FROM $tables[clean_urls_custom_facet_urls] cfu, $tables[clean_urls_custom_facet_urls_options] cfuo
    WHERE cfu.description!='' AND cfu.url_id=cfuo.url_id");
    if ($facets) {
        cw_flush("<h2>Facet categories with description</h2> <br />\n");
        $out = '';
        foreach ($facets as $v) {
            $attr_value_ids = array_filter(array_map('trim',explode(',',$v['attribute_value_ids'])));
            $attributes_get = array();
            $_attributes = array();
            foreach ($attr_value_ids as $attr_value_id) {
                $attr_id = cw_query_first_cell("select attribute_id from $tables[attributes_default] 
                    where attribute_value_id='$attr_value_id'");
                $attributes_get[] = "att[{$attr_id}][{$attr_value_id}]={$attr_value_id}";
                $_attributes[$attr_id] = $attr_value_id;
            }
            
            $search_query_data = array(
                'flat_search' => true,
                'status' => array(1),
                'attributes' => $_attributes,
            );
            $search_count = cw_func_call('cw_product_search', array('data' => $search_query_data, 'count_only'=>true,'current_area' => 'C', 'info_type' => 0));
            if ($search_count[1]['total_items'] == 0) continue;            
            
            $attributes_query = join('&',$attributes_get);
            //cw_var_dump($attr_value_ids,$attributes_query);
            $out .= $data['cat_manuf'][] = ltrim(cw_call('cw_clean_url_get_seo_url',array('index.php?target=search&mode=search&'.$attributes_query)),'/');
            $out .= "<br />\n";
        }
        cw_flush($out);
    }
    

	if ($addons['manufacturers']) {

		$r = cw_func_call('cw_manufacturer_search',array('data'=>array('avail'=>1,'all'=>true)));
		unset($r[1]); // release navigation info
		$items =& $r[0];
		
		cw_flush('<h2>'.cw_get_langvar_by_name('lbl_manufacturers', null, false, true).'</h2>');
        $manufacturer_attr_id = cw_call('cw_attributes_filter',array(array('field'=>'manufacturer_id','item_type'=>'P'),true,'attribute_id'));	
        $counter = 0;
		foreach($items as $v) {
            
            $search_query_data = array(
                'flat_search' => true,
                'status' => array(1),
                'attributes' => array($manufacturer_attr_id=>$v['manufacturer_id']),
            );
            $search_count = cw_func_call('cw_product_search', array('data' => $search_query_data, 'count_only'=>true,'current_area' => 'C', 'info_type' => 0));
            if ($search_count[1]['total_items'] == 0) continue;
            $url = cw_call('cw_core_get_html_page_url', array(array('var'=>'manufacturers','manufacturer_id'=>$v['manufacturer_id'])));
            $url = str_replace(with_slash($app_web_dir), '', $url);
            if (!empty($url)) {
                $data['cat_manuf'][] = $url;
            } else {
				$data['cat_manuf'][] = 'index.php?target=manufacturer.php&manufacturer_id=' . $v['manufacturer_id'];
			}
            cw_flush($v['manufacturer']. "<br />\n");
            $counter ++;
		}
		cw_flush($counter."<br />\n");
	}

    $r = cw_func_call('cw_product_search', array('data'=>array('status'=>array(1),'flat_search'=>true,'all'=>true),'current_area'=>'C','info_type'=>0,'user_account'=>null,'product_id_only'=>true));    
    unset($r[1]); // release navigation info
    $items =& $r[0];

    $lastmod = array();

	if (is_array($items)) {
		cw_flush('<h2>'.cw_get_langvar_by_name('lbl_products', null, false, true).'</h2>');
        $i=0;
		foreach($items as $v) {
            $url = cw_call('cw_core_get_html_page_url', array(array('var'=>'product','product_id'=>$v['product_id'])));
            $url = str_replace(with_slash($app_web_dir), '', $url);
            if (!empty($url)) {
                $data['product'][$v['product_id']] = $url;
            } else {
				$data['product'][$v['product_id']] = 'index.php?target=product&product_id=' . $v['product_id'];
			}
            if ($lm = cw_query_first_cell("SELECT modification_date FROM $tables[products_system_info] WHERE product_id='$v[product_id]'")) {
                $lastmod[$v['product_id']] = $lm;
            }
            cw_flush('.');
            $i++; 
            if ($i%100 == 0) {
                cw_flush($i."<br />\n");
            }
		}
		cw_flush("<br />\nTotal: $i <br />\n");
	}

    $items = cw_query("SELECT contentsection_id as page_id FROM $tables[cms] WHERE type='staticpage' AND active='Y'");
	if (is_array($items)) {
		cw_flush('<h2>'.cw_get_langvar_by_name('lbl_cs_type_separate_static_page', null, false, true).'</h2>');
        $cms_sitemap_attr_id = cw_call('cw_attributes_filter',array(array('field'=>'add_to_sitemap','item_type'=>'AB'),true,'attribute_id'));	
        $i=0;
		foreach($items as $v) {
            if (cw_attribute_get_value($cms_sitemap_attr_id,$v['page_id']) != 1) continue;
            $url = cw_call('cw_core_get_html_page_url', array(array('var'=>'pages','page_id'=>$v['page_id'])));
            $url = str_replace(with_slash($app_web_dir), '', $url);
            if (!empty($url)) {
                $data['static'][] = $url;
            } else {
				$data['static'][] = 'index.php?target=pages&page_id=' . $v['page_id'];
			}
            cw_flush($url."<br />\n");
            $i++;
		}
		cw_flush("Total: $i <br />\n");
	}

	unset($items,$r);

    cw_event('on_sitemap_data', array(&$data));

    $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
    $xml .= '<?xml-stylesheet type="text/xsl" href="'.$smarty->get_template_vars('SkinDir').'/addons/sitemap_xml/style.xsl"?>'."\n";
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.google.com/schemas/sitemap/0.84 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">'."\n";
    $xml .= '//<![CDATA[#Generated by sitemap_xml generator by CartWorks software, http://www.cartworks.com //]]>'."\n";

	fwrite($file, $xml);

	foreach ($data as $type => $list) {
		foreach ($list as $k=>$v) {
			$xml = '<url><loc>' . with_slash($http_location) . $v . '</loc><lastmod>'.(($type=='product' && !empty($lastmod[$k]))?date('Y-m-d',$lastmod[$k]):$today).'</lastmod><changefreq>' 
                                        . $config['sitemap_xml']['sm_frequency_' . $type] . '</changefreq><priority>' 
                                        . $config['sitemap_xml']['sm_priority_' . $type] . '</priority></url>'."\n";
			fwrite($file,$xml);
		}
	}

	fwrite($file, '</urlset>');
	fclose($file);
	if ($config['sitemap_xml']['sm_pack_result'] == 'Y') {
		@unlink($filename.'.gz');
        exec("gzip --suffix .gz $filename");
	}

	$index[] = $http_location.'/index.php?target=sitemap&amp;filename='.$domain['name'];
}
} // foreach


// Create index
// http://www.google.com/support/webmasters/bin/answer.py?answer=71453
/*
<?xml version="1.0" encoding="UTF-8"?>
   <sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
   <sitemap>
      <loc>http://www.example.com/sitemap1.xml.gz</loc>
      <lastmod>2004-10-01T18:23:17+00:00</lastmod>
   </sitemap>
   <sitemap>
      <loc>http://www.example.com/sitemap2.xml.gz</loc>
      <lastmod>2005-01-01</lastmod>
   </sitemap>
   </sitemapindex>
*/

if ($success && is_array($index)) {
	cw_flush('<h2>Sitemap Index</h2>');
	$_filename = cw_sitemap_name('index', false);
	if (($filename = cw_allow_file($_filename, true)) && $file = cw_fopen($_filename, 'w', true)) {

    $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
    $xml .= '<?xml-stylesheet type="text/xsl" href="'.$smarty->get_template_vars('SkinDir').'/addons/sitemap_xml/style.xsl"?>'."\n";
    $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.google.com/schemas/sitemap/0.84 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">'."\n";
    $xml .= '//<![CDATA[#Generated by sitemap_xml generator by CartWorks software, http://www.cartworks.com //]]>'."\n";

    	fwrite($file, $xml);

	    foreach ($index as $sitemap) {
            $xml = '<sitemap><loc>'.$sitemap.'</loc><lastmod>'.$today.'</lastmod></sitemap>'."\n";
            fwrite($file,$xml);
    	}

	    fwrite($file, '</sitemapindex>');
	    fclose($file);
	    if ($config['sitemap_xml']['sm_pack_result'] == 'Y') {
			@unlink($filename.'.gz');
			exec("gzip --suffix .gz $filename");
		}
	}
	cw_flush("\n");
    if (!defined('IS_CRON')) echo '<br />';

	cw_add_top_message(cw_get_langvar_by_name('msg_sitemap_xml', null, false, true));

    if ($config['sitemap_xml']['sm_ping_google']=='Y') {
        /* Ping Google to update sitemaps
        http://www.google.com/support/webmasters/bin/answer.py?answer=183669
        */
        cw_flush('Sending update request: '.'www.google.com'.'/webmasters/tools/ping'.'?sitemap='.urlencode($http_location.'/'.$_filename));
        cw_flush("\n");
        if (!defined('IS_CRON')) echo '<br />';
        cw_load('http');
        list($header,$body)=cw_http_get_request('www.google.com','/webmasters/tools/ping','sitemap='.urlencode($http_location.'/'.$_filename));

        $msg = "\nGoogle response:\n".$body;
        if (!defined('IS_CRON')) $msg = '<br /><br />'.$msg;
        echo $msg;
        cw_add_top_message($msg);
        
    }
    
    $success_msg = "Sitemap XML <a href='$http_location/$_filename'>file</a> updated";
    cw_call('cw_system_messages_add',array('sitemap_xml', $success_msg, SYSTEM_MESSAGE_COMMON, SYSTEM_MESSAGE_INFO));
    
} // if $success
else {
    $msg = cw_get_langvar_by_name('msg_err_file_permission_denied', null, false, true);
    echo $msg;
    echo "<br />File: $_filename<br />";
	cw_add_top_message($msg,'E');
    
    $warn_msg = "Sitemap XML. <acronym title='$_filename'>".cw_get_langvar_by_name('msg_err_file_permission_denied').'</acronym>';
    cw_call('cw_system_messages_add',array('sitemap_xml', $warn_msg, SYSTEM_MESSAGE_COMMON, SYSTEM_MESSAGE_WARNING));
    cw_call('cw_system_messages_show', array('sitemap_xml'));
}

if (!defined('IS_CRON'))    cw_flush("\n <a href='index.php?target=sitemap_xml'>Continue</a>");
