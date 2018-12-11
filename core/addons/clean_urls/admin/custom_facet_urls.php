<?php
cw_load('attributes', 'image');

$custom_facet_url_data = &cw_session_register('custom_facet_url_data', array());
$top_message = &cw_session_register('top_message');
$search_data = &cw_session_register('search_data', array());
$file_upload_data = &cw_session_register('file_upload_data');

$facet_urls_sort_options = array('custom_facet_url'=>"$tables[clean_urls_custom_facet_urls].custom_facet_url", 
                                 'clean_urls'=>"$tables[clean_urls_custom_facet_urls_options].clean_urls",
                                 'title'=>"$tables[clean_urls_custom_facet_urls].title");

if ($mode == "add" || $mode == "details") {
	if ($action == "update") {
		$custom_facet_url_data = array();
		$custom_facet_url = str_replace(" ", "_", $custom_facet_url);
		$custom_facet_url = strtolower($custom_facet_url);
           
        $clean_urls_by_options = explode('###', $clean_urls);       

        foreach ($clean_urls_by_options as $cu_k => $cu_v) {
            $clean_urls_by_options[$cu_k] = str_replace("?/","|",trim($cu_v, "?/"));
        }  

        if (count($clean_urls_by_options) == 1) { 
            if (!empty($custom_facet_url)) 
                $result = cw_clean_url_custom_facet_url_unique_result($custom_facet_url, $clean_urls, $custom_facet_url_id);

        }
 
		if (empty($result)) {
			if (!empty($custom_facet_url_id)) {
				cw_array2update(
					"clean_urls_custom_facet_urls",
					array(
						'custom_facet_url'  	=> $custom_facet_url,
                        'description'           => addslashes($description),
                        'title'                 => addslashes($title) 
					),
					"url_id = $custom_facet_url_id"
				);
                db_query("delete from $tables[clean_urls_custom_facet_urls_options] where url_id = $custom_facet_url_id");
                $attribute_value_ids_by_options = explode('###', $attribute_value_ids); 
                foreach ($clean_urls_by_options as $o_idx => $cu_v) {
                    cw_array2insert("clean_urls_custom_facet_urls_options", 
                                    array('url_id'=>$custom_facet_url_id,
                                          'attribute_value_ids'=>$attribute_value_ids_by_options[$o_idx], 
                                          'clean_urls'=>$cu_v
                                    )); 
                }

				$top_message = array(
					'content' => cw_get_langvar_by_name("txt_custom_facet_url_updated"),
					'type' => 'I'
				);
			} else {
				$custom_facet_url_id = cw_array2insert(
					"clean_urls_custom_facet_urls",
					array(
						'custom_facet_url'  	=> $custom_facet_url,
                        'description'           => addslashes($description),
                        'title'                 => addslashes($title)
					)
				);

                db_query("delete from $tables[clean_urls_custom_facet_urls_options] where url_id = $custom_facet_url_id");
                $clean_urls_by_options = explode('###', $clean_urls);

                foreach ($clean_urls_by_options as $cu_k => $cu_v) {
                    $clean_urls_by_options[$cu_k] = str_replace("?/","|",trim($cu_v, "?/"));
                } 

                $attribute_value_ids_by_options = explode('###', $attribute_value_ids);
                foreach ($clean_urls_by_options as $o_idx => $cu_v) {
                    cw_array2insert("clean_urls_custom_facet_urls_options", 
                                    array('url_id'=>$custom_facet_url_id,
                                          'attribute_value_ids'=>$attribute_value_ids_by_options[$o_idx],
                                          'clean_urls'=>$cu_v
                    )); 
                }

				$top_message = array(
					'content' => cw_get_langvar_by_name("txt_custom_facet_url_added"),
					'type' => 'I'
				);
			}
			
            if (!empty($custom_facet_url_id) && !empty($file_upload_data) && is_array($file_upload_data)) {
              $is_image_uploaded_and_saved = false;
              if (cw_image_check_posted($file_upload_data['facet_categories_images'])) {
                if (cw_image_save($file_upload_data['facet_categories_images'], array('id' => $custom_facet_url_id))) $is_image_uploaded_and_saved = true;
              }
              if (!$is_image_uploaded_and_saved) {
				cw_add_top_message('Image cannot be saved', 'E');
              }
            }			
			
		} else {
			$replace = $result['type'] == 1 ? 'Custom clean url' : 'Clean urls combination';
			$content = str_replace('{{paramname}}', $replace, cw_get_langvar_by_name("lbl_error_param_unique", null, false, true));
			$custom_facet_url_name = cw_clean_url_get_custom_facet_url_name($result['id']);
			$replaced = "<a href='index.php?target=custom_facet_urls&mode=details&custom_facet_url_id=" . $result['id'] . "'>" . $custom_facet_url_name . "</a>";
			$content= str_replace('{{entrylink}}', $replaced, $content);
			$top_message = array(
				'content' => $content,
				'type' => 'E'
			);
		}
		$custom_facet_url_data['attribute_value_ids'] = $attribute_value_ids;
		$custom_facet_url_data['custom_facet_url'] = $custom_facet_url;
        $custom_facet_url_data['description'] = $description;
        $custom_facet_url_data['title'] = $title;   
		$param = !empty($custom_facet_url_id) ? 'details&custom_facet_url_id=' . $custom_facet_url_id : 'add';
		cw_header_location('index.php?target=custom_facet_urls&mode=' . $param);
	}

	if ($action == 'delete_image' && $custom_facet_url_id) {
		cw_image_delete($custom_facet_url_id, 'facet_categories_images');
		cw_header_location("index.php?target=$target&mode=$mode&custom_facet_url_id=$custom_facet_url_id");
	}



	$custom_facet_url = "";
	if (!empty($custom_facet_url_id)) {
		$result = cw_query_first("
			SELECT *
			FROM $tables[clean_urls_custom_facet_urls]
			WHERE url_id = '$custom_facet_url_id'
		");
         
		$custom_facet_url = $result['custom_facet_url'];
        $description = $result['description'];          
        $title = $result['title']; 

        $attribute_value_ids_list = cw_query("select attribute_value_ids from $tables[clean_urls_custom_facet_urls_options] where url_id='$custom_facet_url_id'");   
        $attribute_value_ids_data = array();
        foreach ($attribute_value_ids_list as $avi_v) {
            $attribute_value_ids_data = array_merge($attribute_value_ids_data, explode(',',$avi_v['attribute_value_ids'])); 
        }
	} else {
		$attribute_value_ids_data = explode(',', str_replace('###',',',$custom_facet_url_data['attribute_value_ids']));
		$custom_facet_url = $custom_facet_url_data['custom_facet_url'];
        $description = $result['description'];
        $title = $result['title']; 

	}

	$custom_facet_image = cw_image_get('facet_categories_images', $custom_facet_url_id);
	$custom_facet_url_data = array();
	$smarty->assign('attributes_options', cw_clean_url_get_attributes_options($attribute_value_ids_data));
	$smarty->assign('custom_facet_url_id', $custom_facet_url_id);
	$smarty->assign('custom_facet_url', $custom_facet_url);
    $smarty->assign('description', $description);
    $smarty->assign('title', $title); 
	$smarty->assign('custom_facet_image', $custom_facet_image);
	$smarty->assign('mode', $mode);
	$smarty->assign('main', 'custom_facet_url');
} else {

	if ($action == 'delete' && !empty($to_delete)) {
		foreach ($to_delete as $url_id => $v) {
			cw_clean_url_custom_facet_url_delete($url_id);
		}
		$top_message = array(
			'content' => cw_get_langvar_by_name("txt_selected_custom_facet_urls_deleted", null, false, true),
			'type' => 'I'
		);
		cw_header_location('index.php?target=custom_facet_urls');
	}

	if ($action == 'reset') {
		unset($search_data['custom_facet_urls']);
	}

	if ($action == 'search_facet') {
		$search_data['custom_facet_urls']['attribute_option'] = $attribute_option;
		$search_data['custom_facet_urls']['substring'] = $attribute_option_substring;
	}

    if ($sort && in_array($sort, array_keys($facet_urls_sort_options))) {
        $search_data['custom_facet_urls']['sort_field'] = $facet_urls_sort_options[$sort];
	}

    if (isset($sort_direction)) {
        $search_data['custom_facet_urls']['sort_direction'] = $sort_direction;
	}

    $search_data['custom_facet_urls']['page'] = $page;

	$conditions = array();
	if ($search_data['custom_facet_urls']['substring']) {
		$substring = $search_data['custom_facet_urls']['substring'];
		$conditions[] = "($tables[clean_urls_custom_facet_urls].custom_facet_url LIKE '%$substring%' OR 
                          $tables[clean_urls_custom_facet_urls_options].clean_urls LIKE '%$substring%')";
	}
	if ($search_data['custom_facet_urls']['attribute_option']) {
		foreach ($search_data['custom_facet_urls']['attribute_option'] as $opt) {
			$conditions[] = "($tables[clean_urls_custom_facet_urls_options].attribute_value_ids LIKE '%,$opt,%'
				OR $tables[clean_urls_custom_facet_urls_options].attribute_value_ids LIKE '$opt,%'
				OR $tables[clean_urls_custom_facet_urls_options].attribute_value_ids LIKE '%,$opt'
				OR $tables[clean_urls_custom_facet_urls_options].attribute_value_ids LIKE '$opt')";
		}
	}

    $where = "WHERE $tables[clean_urls_custom_facet_urls].url_id=$tables[clean_urls_custom_facet_urls_options].url_id";

	if (!empty($conditions)) {
		$where .= " AND " . implode(" AND ", $conditions);
	}
    $where .= " GROUP BY $tables[clean_urls_custom_facet_urls_options].url_id ";


	$total_items = cw_clean_url_get_custom_facet_urls_count($where);
	$navigation = cw_core_get_navigation($target, $total_items, $page);
	$navigation['script'] = "index.php?target=$target";
	if (!empty($search_data['custom_facet_urls']['sort_field'])) {
		$navigation['script'] .= "&sort_field=" . $search_data['custom_facet_urls']['sort_field'];
	}
	if ($search_data['custom_facet_urls']['sort_direction'] != "") {
		$navigation['script'] .= "&sort_direction=" . $search_data['custom_facet_urls']['sort_direction'];
	}

	if (!empty($search_data['custom_facet_urls']['sort_field'])) {
		$orderby = "ORDER BY " . $search_data['custom_facet_urls']['sort_field'];
		if (!empty($search_data['custom_facet_urls']['sort_direction'])) {
			$orderby .= $search_data['custom_facet_urls']['sort_direction'] ? " DESC" : " ASC";
		}
	}

	$limit = "LIMIT $navigation[first_page], $navigation[objects_per_page]";
	$custom_facet_urls = cw_clean_url_get_custom_facet_urls($where, $orderby, $limit);

    $smarty->assign('navigation', $navigation);
    $smarty->assign('custom_facet_urls', $custom_facet_urls);
    $smarty->assign('filter_options', cw_clean_url_get_used_attributes_options($search_data['custom_facet_urls']['attribute_option']));
    $smarty->assign('search_prefilled', $search_data['custom_facet_urls']);

    $smarty->assign('main', 'custom_facet_urls');
}
