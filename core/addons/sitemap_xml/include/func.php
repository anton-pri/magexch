<?php

function cw_sitemap_name($name, $pack=true) {
	global $config;
	return (empty($name)?'':$name.'.').$config['sitemap_xml']['sm_filename'].(($config['sitemap_xml']['sm_pack_result']=='Y' && $pack)?'.gz':'');
}

function cw_sitemap_filename($name, $pack=true) {
	global $app_dir;
	return $app_dir.'/'.cw_sitemap_name($name, $pack);
}

function cw_sitemap_cron() {
	cw_include('addons/sitemap_xml/cron/cron_process.php');
}

function cw_sitemap_echo($msg,$level) {
    if (!defined('IS_CRON')) {
        echo "[$level]".str_repeat('. ',$level).$msg."<br/>\n";
        cw_flush();
    }
}

/**
 * This recursive function takes product filter cache (generate it if necessary), 
 * follows all links and collects URLs in global $sitemap_pf_links
 */
function cw_sitemap_parse_product_filter($product_filter, $attributes_for_scan, $attributes=array()) {
    global $sitemap_pf_links;
    
    static $info_type = 1448;// 8+32+128+256+1024
    
    static $current_area = 'C';
    
    static $level = 0;

    $level++;

    $attributes_get = array();
    foreach ($attributes as $att_id => $id) {
        $attributes_get[] = "att[{$att_id}][{$id}]={$id}";
    }
    $attributes_query = join('&',$attributes_get);

    foreach ($product_filter as $k=>$a) {
        
        if (!in_array($a['pf_display_type'],array('T','G','W','E'))) continue;

        $dry_run = false; // Convert links to seo but do not include to sitemap and do not scan deeper
        
        if (!in_array($a['attribute_id'],$attributes_for_scan)) {
            $dry_run = true;
        }
            
        $attributes_for_scan = array_diff($attributes_for_scan,array($a['attribute_id']));
        
        foreach ($a['values'] as $kk=>$vv) {
            $_attributes = $attributes;
            $_attributes[$a['attribute_id']] = $vv['id'];

            if (empty($vv['link'])) {
                $vv['link'] = cw_call('cw_clean_url_get_seo_url',array('index.php?target=search&mode=search&'.$attributes_query."&att[{$a['attribute_id']}][{$vv['id']}]={$vv['id']}"));
            }
            $product_filter[$k]['values'][$kk]['link'] = $vv['link'];

            if ($dry_run) continue;
            
            $sitemap_pf_links[] = $vv['link'];
            cw_sitemap_echo($vv['link'],$level);
            //cw_sitemap_echo("{$a['name']}:{$vv['name']} -> {$vv['link']} ({$vv['counter']})", $level);
            
            if (
                $vv['counter'] == 1 ||
                $level > constant('SITEMAP_MAX_SCAN_DEPTH')
            ) { 
                continue; // no need follow deeper when only one product
            } 
                    
            $data = array(
                'query_only' => true,
                'flat_search' => true,
                'status' => array(1),
                'attributes' => $_attributes,
            );
            $search_query = cw_func_call('cw_product_search', array('data' => $data, 'current_area' => $current_area, 'info_type' => 1024));
            $pf_cache_key = substr($search_query,0,strpos($search_query,'ORDER BY'));
            
            $pfr = cw_cache_get(array($pf_cache_key),'PF_search');
            if (empty($pfr)) {
                // cw_sitemap_echo("Generate {$vv['link']}", $level);
                // Re-search
                $data['query_only'] = null;
                list($products, $navigation, $pfr) = cw_func_call('cw_product_search', array('data' => $data, 'current_area' => $current_area, 'info_type' => 1024));
            }
            
            if (empty($pfr)) continue;

            //cw_sitemap_echo("Go inside {$vv['link']}", $level);
            $pfr = cw_call('cw_sitemap_parse_product_filter', array($pfr,$attributes_for_scan,$_attributes));
            //cw_sitemap_echo("Back. Save {$vv['link']} as ".md5(serialize(array($pf_cache_key))), $level);
            cw_cache_save($pfr,array($pf_cache_key),'PF_search');
            
        }
    }
    $level--;
    return $product_filter;
}
