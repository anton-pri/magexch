<?php

if ($manufacturer_id) {
	
	$f_categories = cw_clean_url_manufacturers_categories($manufacturer_id);
	cw_load('image');
	foreach ($f_categories as $k=>$v) {
		$f_categories[$k]['image'] = cw_image_get('facet_categories_images', $v['url_id']);
	}
	$smarty->assign('f_categories',$f_categories);

	if (APP_AREA == 'admin') {
		
		cw_include('addons/clean_urls/admin/custom_facet_urls.php');
	
		if ($REQUEST_METHOD == 'POST') {
			if ($action == 'delete_fcat') {
				foreach ($fcat as $url_id=>$v) {
					if (isset($v['delete']) && $v['delete']=='1') {
						$url_id = intval($url_id);
						db_query("DELETE FROM $tables[manufacturers_categories] WHERE manufacturer_id='$manufacturer_id' AND url_id='$url_id'");
					}
				}
			}
			
			if ($action == 'update_fcat') {
				foreach ($fcat as $url_id=>$v) {
					$url_id = intval($url_id);
					cw_array2update('manufacturers_categories', array('pos'=>$v['pos']), "manufacturer_id='$manufacturer_id' AND url_id='$url_id'");
				}			
			}

			if ($action == 'add_fcat') {
				/* Do not worry about posted var name "to_delete", it is from re-used template. In this context used to adding of new entries */
				foreach ($to_delete as $url_id=>$v) {
					$url_id = intval($url_id);
					$data = array(
						'manufacturer_id'=>$manufacturer_id,
						'url_id' => $url_id,
						'pos' => 0,
						);
					cw_array2insert('manufacturers_categories', $data, true);
				}			
			}
			if (in_array($action,array('add_fcat','update_fcat','delete_fcat','search_facet')))
				cw_header_location("index.php?target=$target&manufacturer_id=$manufacturer_id&js_tab=fcat");
		}
		
		$smarty->assign('js_tab', $js_tab);
	}
}
