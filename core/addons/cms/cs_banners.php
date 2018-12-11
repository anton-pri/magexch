<?php

$contentsections_filter = &cw_session_register('contentsections_filter');

cw_load('image','attributes');

$page = intval($page);

if (empty($mode)) $mode = 'list';

if ($mode == 'list') $contentsections_filter = array();

$redirect_link = 'index.php?target=cms'.(empty($mode) ? '' : '&mode='.$mode).(empty($page) ? '' : '&page='.$page).(empty($cs_type) ? '' : '&cs_type='.$cs_type);

if ($REQUEST_METHOD == 'POST') {
  switch ($action) {
    case 'delete_contentsections':
      if (!empty($delete_contentsections) && is_array($delete_contentsections)) {
        $delete_contentsections = array_keys($delete_contentsections);
        db_query("DELETE FROM $tables[cms] WHERE contentsection_id IN ('".implode("','", $delete_contentsections)."')");
        db_query("DELETE FROM $tables[cms_restrictions] WHERE contentsection_id IN ('".implode("','", $delete_contentsections)."')");
        db_query("DELETE FROM $tables[cms_user_counters] WHERE contentsection_id IN ('".implode("','", $delete_contentsections)."')");
        db_query("DELETE FROM $tables[cms_alt_languages] WHERE contentsection_id IN ('".implode("','", $delete_contentsections)."')");

          foreach ($delete_contentsections as $contentsection_id) {
          cw_image_delete($contentsection_id, 'cms_images');
          cw_call('cw_attributes_cleanup', array($contentsection_id,'AB'));
        }        
      }
      break;
    case 'update_filters':
      if (!empty($filter) && is_array($filter)) {
        $date_fields = array (
            0 => array('start_filter_date' => 0, 'end_filter_date' => 1),
        );
        cw_core_process_date_fields($filter, $date_fields);
        $contentsections_filter = array(
          'type'           => $filter['type'], 
          'name'           => stripslashes($filter['name']),
          'target'         => $filter['target'],
          'url'            => stripslashes($filter['url']),
          'skin'           => $filter['skin'],
          'service_code'   => stripslashes($filter['service_code']),
          'start_date'     => intval($filter['start_filter_date']),
          'end_date'       => intval($filter['end_filter_date']),
          'sort_field'     => 'service_code',
          'sort_direction' => 0,
          'attributes'     => $attributes,
          'offers'			=> $content_section['offers'],
        );
        if (!empty($content_section_clean_urls) && is_array($content_section_clean_urls)) {
          $contentsections_filter['clean_urls'] = array();
          foreach ($content_section_clean_urls as $cs_filter_cu) {
              $filter_clean_url = trim($cs_filter_cu['value']);
              if (!empty($filter_clean_url)) 
                  $contentsections_filter['clean_urls'][] = $filter_clean_url;
          }
        }
        if (!empty($restricted_attributes)) {
           $contentsections_filter['restricted_attributes'] = $restricted_attributes;
        }
      }
      break;
    case 'update_contentsections':
      if (!empty($contentsections) && is_array($contentsections)) {
        foreach ($contentsections as $contentsection_id => $data) {
          $data = array_map('trim', $data);
          $data['active']  = empty($data['active']) ? 'N' : 'Y';
          $data['orderby'] = intval($data['orderby']);
          $data['show_limit']   = intval($data['show_limit']);
          cw_array2update('cms', $data, "contentsection_id = '".$contentsection_id."'");

          $alt_lang_cms_exists = cw_query_first_cell("SELECT contentsection_id FROM $tables[cms_alt_languages] WHERE contentsection_id = '$contentsection_id' AND code='$current_language'");
          if (empty($alt_lang_cms_exists)) {
            $main_data = cw_query_first("SELECT name, url, content FROM $tables[cms] WHERE contentsection_id = '$contentsection_id'");
            $data = array(
            'contentsection_id' => $contentsection_id,
            'code'     => $current_language,
            'name'     => $main_data['name'],
            'url'      => $main_data['url'],
            'content'  => addslashes($main_data['content']),
            );
            cw_array2insert('cms_alt_languages', $data, true);
          }
        }
      }
      break;
  }
  cw_header_location($redirect_link);
}

# Prepare search condition for content sections 
$contentsection_conditions = array();
$mysql_contentsection_conditions = '';

if (in_array($cs_type, array('html','image','staticpage','staticpopup'))) 
  $contentsections_filter['type'] = array($cs_type);

if (!empty($contentsections_filter) && is_array($contentsections_filter)) {

  if (!empty($contentsections_filter['type'])) {
    $contentsection_conditions[] = "ab.type in ('".implode("','", $contentsections_filter['type'])."')";
  }

  if (strlen($contentsections_filter['service_code']) > 0) {
    $mask = cw_ab_convert_search_mask_to_mysql($contentsections_filter['service_code']);
    $contentsection_conditions[] = "ab.service_code LIKE '".addslashes($mask)."'";
  }

  if (strlen($contentsections_filter['name']) > 0) {
    $mask = cw_ab_convert_search_mask_to_mysql($contentsections_filter['name']);
    $contentsection_conditions[] = "ab.name LIKE '".addslashes($mask)."'";
  }

  if (!empty($contentsections_filter['target'])) {
    $contentsection_conditions[] = "ab.target in ('".implode("','", $contentsections_filter['target'])."')";
  }
/*alt tag is attribute now, rewrite search condition accordingly
  if (strlen($contentsections_filter['alt']) > 0) {
    $mask = cw_ab_convert_search_mask_to_mysql($contentsections_filter['alt']);
    $contentsection_conditions[] = "ab.alt LIKE '".addslashes($mask)."'";
  }
*/
  if (strlen($contentsections_filter['url']) > 0) {
    $mask = cw_ab_convert_search_mask_to_mysql($contentsections_filter['url']);
    $contentsection_conditions[] = "ab.url LIKE '".addslashes($mask)."'";
  }

  if (!empty($contentsections_filter['skin'])) {
    $contentsection_conditions[] = "ab.skin in ('".implode("','", $contentsections_filter['skin'])."')";
  }

  if (!empty($contentsections_filter['start_date'])) {
    $contentsection_conditions[] = "ab.start_date >= '".intval($contentsections_filter['start_date'])."'";
  }
  if (!empty($contentsections_filter['end_date'])) {
    $contentsection_conditions[] = "ab.end_date <= '".intval($contentsections_filter['end_date'])."'";
  }

  if (!empty($contentsections_filter['clean_urls'])) {
      $clean_url_inner_join = " INNER JOIN $tables[cms_restrictions] as rest ON rest.contentsection_id=ab.contentsection_id AND rest.object_type='URL' AND rest.value IN ('".implode("','", $contentsections_filter['clean_urls'])."')";
  }
  
  if (!empty($contentsections_filter['offers'])) {
      $clean_url_inner_join = " INNER JOIN $tables[cms_restrictions] as rest ON rest.contentsection_id=ab.contentsection_id AND rest.object_type='PS' AND rest.object_id IN ('".implode("','", $contentsections_filter['offers'])."')";
  }
  
  if (!empty($contentsections_filter['restricted_attributes'])) {
      $restricted_attributes_inner_joins = array();
      $inn_j_tab_cnt = 0; 
      foreach ($contentsections_filter['restricted_attributes'] as $attr_id => $attr_options) {
          if (is_array($attr_options)) {
              foreach ($attr_options as $attribute_value_id) {
                  $restricted_attributes_inner_joins[] = "INNER JOIN $tables[cms_restrictions] cms_a$inn_j_tab_cnt ON cms_a$inn_j_tab_cnt.contentsection_id=ab.contentsection_id AND cms_a$inn_j_tab_cnt.object_type='A' AND cms_a$inn_j_tab_cnt.object_id = '$attr_id' AND cms_a$inn_j_tab_cnt.value_id = '$attribute_value_id'";
                  $inn_j_tab_cnt++;
              }   
          } elseif (!empty($attr_options)) {
              $restricted_attributes_inner_joins[] = "INNER JOIN $tables[cms_restrictions] cms_a$inn_j_tab_cnt ON cms_a$inn_j_tab_cnt.contentsection_id=ab.contentsection_id AND cms_a$inn_j_tab_cnt.object_type='A' AND cms_a$inn_j_tab_cnt.object_id = '$attr_id' AND cms_a$inn_j_tab_cnt.value LIKE '%$attr_options%'";
              $inn_j_tab_cnt++;
          } 
      }
      if (!empty($restricted_attributes_inner_joins)) 
          $restricted_attributes_inner_join = implode(" ", $restricted_attributes_inner_joins);
  } 

  if (!empty($contentsection_conditions)) {
    $mysql_contentsection_conditions = implode(' AND ', $contentsection_conditions);
  }
}

if (empty($contentsections_filter) || empty($contentsections_filter['sort_field'])) {
  $contentsections_filter = array(
    'sort_field'     => 'service_code',
    'sort_direction' => 0
  );
}

$allowed_sort_fields = array('service_code', 'name', 'url', 'start_date', 'end_date', 'show_limit', 'viewed', 'clicked', 'orderby', 'type');

if (isset($sort_direction) && in_array($sort_direction, array(0, 1))) {
  $contentsections_filter['sort_direction'] = $sort_direction;
}

if (isset($sort) && in_array($sort, $allowed_sort_fields)) {
  $contentsections_filter['sort_field'] = $sort;
}

$mysql_sort_string = '`'.$contentsections_filter['sort_field'].'`'.($contentsections_filter['sort_direction'] ? ' DESC' : ' ASC');

$attributes = cw_func_call('cw_attributes_get', array('item_id' => 0, 'item_type' => 'AB', 'language' => $current_language));

// Search by attributes params
$mysql_contentsection_attr_conditions = "";
if (!empty($contentsections_filter['attributes']) && is_array($contentsections_filter['attributes'])) {	
	$jc = 1;
	
	foreach ($contentsections_filter['attributes'] as $field => $value) {
		$attribute_id = 0;
		$attribute_values = array();
		$attribute = cw_func_call('cw_attributes_get_attributes_by_field', array('active' => 1, 'field' => $field));
		
		if (!empty($attribute['AB'])) {
			$attribute_id = $attribute['AB'];
		}
		
		if (!empty($value)) {
			is_array($value) ? $attribute_values = array_merge($attribute_values, $value): $attribute_values[] = $value;
			// save selected values
			if (isset($attributes[$field])) {
				$attributes[$field]['values'] = is_array($value) ? $value : array($value);
			}
		}

		$attribute_values = array_unique($attribute_values);
	
		if (!empty($attribute_id) && !empty($attribute_values)) {
		    $mysql_contentsection_attr_conditions .= " INNER JOIN $tables[attributes_values] av$jc
		    ON ab.contentsection_id=av$jc.item_id
		    AND av$jc.item_type = 'AB'
		    AND av$jc.attribute_id = " . $attribute_id . "
		    AND av$jc.value in (" . implode(",", $attribute_values) . ")";
		    $jc++;
		}		
	}
}

  $query = "SELECT ab.contentsection_id, " .
             "ab.service_code, " .
             "IF(ISNULL(abal.content), ab.content, abal.content) AS content, " .
             "ab.type, " .
             "IF(ISNULL(abal.name), ab.name, abal.name) AS name, " .
             "ab.target, " .
             "IF(ISNULL(abal.url), ab.url, abal.url) AS url, " .
             "ab.show_limit, " .
             "ab.active, " .
             "ab.orderby, " .
             "ab.start_date, " .
             "ab.end_date, " .
             "abuc.count AS viewed, " .
             "abuc.clicked AS clicked " .
           "FROM $tables[cms] AS ab $mysql_contentsection_attr_conditions " .
           $clean_url_inner_join .
           $restricted_attributes_inner_join .
           "LEFT JOIN $tables[cms_user_counters] AS abuc " .
             "ON ab.contentsection_id = abuc.contentsection_id " .
           "LEFT JOIN $tables[cms_alt_languages] AS abal " .
             "ON ab.contentsection_id = abal.contentsection_id AND abal.code = '".$current_language."' " .
           "WHERE ".(strlen(trim($mysql_contentsection_conditions)) > 0 ? trim($mysql_contentsection_conditions) : ' 1 ') .
           " GROUP BY ab.contentsection_id " .
           "ORDER BY ".$mysql_sort_string;
//print($query);
$contentsections_result = db_query($query);
$total_items = db_num_rows($contentsections_result);
db_free_result($contentsections_result);
$navigation = cw_core_get_navigation($target, $total_items, $page);
$navigation['script'] = 'index.php?target='.$target.(empty($mode) ? '' : '&mode='.$mode).(empty($cs_type) ? '' : '&cs_type='.$cs_type);
$contentsections = cw_query($query." LIMIT $navigation[first_page], $navigation[objects_per_page]");

$skins = cw_files_get_dir($app_dir.'/skins/addons/cms/skins',2);
$skins = array_map('basename', $skins);
$smarty->assign('skins', $skins);

$smarty->assign('attributes', $attributes);
$smarty->assign('contentsections', $contentsections);
$smarty->assign('contentsections_filter', $contentsections_filter);
$smarty->assign('navigation', $navigation);
$smarty->assign('page', $page);
$smarty->assign('mode', $mode);

$smarty->assign('cs_type', $cs_type);

$smarty->assign('filter_restricted_attributes', cw_cms_get_used_attributes_options($contentsections_filter['restricted_attributes']));

$smarty->assign('current_main_dir', 'addons');
$smarty->assign('current_section_dir', 'cms');
$smarty->assign('main', 'cs_banners');
