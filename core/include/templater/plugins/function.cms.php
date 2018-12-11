<?php

function smarty_function_cms($params, &$smarty) {

  global $tables, $domain_attributes, $mobile_attributes;
  global $config, $identifiers, $current_area, $addons;
  global $current_language, $product_id, $code;

  global $attributes;

  if (empty($addons['cms'])) return false;

  if (!empty($params['bannercode'])) 
      $params['service_code'] = $params['bannercode'];

  if (empty($params['service_code'])) return '';

  $current_date = time();
  $output = '<!-- {cms service_code="'.$params['service_code'].'"} -->';

    // MDM
    $mysql_contentsection_domains_conditions = '';
    if (!empty($domain_attributes['AB']) && ($conditions = cw_md_get_available_domains()) !== false) {
    	
	    $mysql_contentsection_domains_conditions = "INNER JOIN $tables[attributes_values] av
	        ON ab.contentsection_id=av.item_id
		    AND av.attribute_id = '" . $domain_attributes['AB'] . "'
		    AND av.value in " . $conditions;
    }

    // Mobile
    if (
    	isset($mobile_attributes['AB'])
    	&& !empty($mobile_attributes['AB']['attribute_id'])
    	&& !empty($mobile_attributes['AB']['values'])
    ) {
	    $mysql_contentsection_domains_conditions .= " INNER JOIN $tables[attributes_values] avm
	        ON ab.contentsection_id=avm.item_id
		    AND avm.attribute_id = '" . $mobile_attributes['AB']['attribute_id'] . "'
		    AND avm.value in (" . implode(",", $mobile_attributes['AB']['values']). ")";
	}

  $query = "SELECT ab.contentsection_id, " .
             "ab.service_code, " .
             "ab.content, " .
             "ab.url, " .
             "ab.type, " .
             "ab.target, " .
             "ab.start_date, " .
             "ab.end_date, " .
             "ab.skin, " .
             "ab.parse_smarty_tags " .
           "FROM $tables[cms] AS ab $mysql_contentsection_domains_conditions " .
           "WHERE ab.service_code = '".addslashes($params['service_code'])."' " .
             "AND IF(ab.start_date > 0, ab.start_date <= '".$current_date."', 1) " .
             "AND IF(ab.end_date > 0, ab.end_date >= '".$current_date."', 1) " .
             "AND ab.active = 'Y' " .
	  		 ($code == 404 ? "AND ab.display_on_404 = 'Y' " : "AND ab.display_on_404 = 'N' ") .
           "ORDER BY ab.orderby ASC";
  $contentsections = cw_query($query);

  $is_editable = !empty($identifiers['A']) && $config['cms']['allow_edit_from_customer_area']=='Y';

  if (!$is_editable && (empty($contentsections) || !is_array($contentsections))) return $output.'<!-- NO cms with service_code="'.$params['service_code'].'" -->';

  $first_contentsection = $contentsections[0];
  if (!empty($params['skin']))
    $skin = $params['skin'];
  else   
    $skin = $first_contentsection['skin'];

  $allowed_contentsections = array();

  foreach ($contentsections as $contentsection_data) {
	  
	// Check attributes restriction
      if (!empty($attributes) && !empty($product_id)) {
          $_allowed_attributes = cw_query("select object_id as attribute_id, value_id, operation, value from $tables[cms_restrictions] where contentsection_id='".$contentsection_data['contentsection_id']."' AND object_type='A'");
          if (!empty($_allowed_attributes) && is_array($_allowed_attributes)) {

              $allowed_attributes = array();
              foreach ($_allowed_attributes as $_allowed_attribute) { 
                  if (!isset($allowed_attributes[$_allowed_attribute['attribute_id']]))
                      $allowed_attributes[$_allowed_attribute['attribute_id']] = array();

                  $allowed_attributes[$_allowed_attribute['attribute_id']][$_allowed_attribute['value_id']] = $_allowed_attribute;
              } 
              $contentsection_attributes = array();
              $attributes_conflict = false;
              foreach ($attributes as $attribute_name => $attribute_data) {           
                  if (!isset($allowed_attributes[$attribute_data['attribute_id']])) continue;
                  $_attributes_conflict = false;
                  if (isset($allowed_attributes[$attribute_data['attribute_id']][0])) {
                      if ($allowed_attributes[$attribute_data['attribute_id']][0]['operation'] == 'bt') {
                          $between_values = explode(",",$allowed_attributes[$attribute_data['attribute_id']][0]['value']); 
                          if (count($between_values) != 2) continue;
                          $_attributes_conflict = ($attribute_data['value'] < min($between_values) || $attribute_data['value'] > max($between_values));
                      } elseif ($allowed_attributes[$attribute_data['attribute_id']][0]['operation'] == 'in') {
                          $in_values = explode(",",$allowed_attributes[$attribute_data['attribute_id']][0]['value']);
                          if (!count($in_values)) continue;
                          $_attributes_conflict = !in_array($attribute_data['value'], $in_values);
                      } elseif ($allowed_attributes[$attribute_data['attribute_id']][0]['operation'] == 'eq') {
                          $_attributes_conflict = ($attribute_data['value'] != $allowed_attributes[$attribute_data['attribute_id']][0]['value']);
                      } elseif ($allowed_attributes[$attribute_data['attribute_id']][0]['operation'] == 'lt') {
                          $_attributes_conflict = ($attribute_data['value'] >= $allowed_attributes[$attribute_data['attribute_id']][0]['value']);  
                      } elseif ($allowed_attributes[$attribute_data['attribute_id']][0]['operation'] == 'le') {
                          $_attributes_conflict = ($attribute_data['value'] > $allowed_attributes[$attribute_data['attribute_id']][0]['value']);
                      } elseif ($allowed_attributes[$attribute_data['attribute_id']][0]['operation'] == 'gt') {
                          $_attributes_conflict = ($attribute_data['value'] <= $allowed_attributes[$attribute_data['attribute_id']][0]['value']);
                      } elseif ($allowed_attributes[$attribute_data['attribute_id']][0]['operation'] == 'ge') {
                          $_attributes_conflict = ($attribute_data['value'] < $allowed_attributes[$attribute_data['attribute_id']][0]['value']);
                      }
                      $values_intersection = array(0);
                  } else {
                      $values_intersection = array_intersect($attribute_data['values'], array_keys($allowed_attributes[$attribute_data['attribute_id']]));   
                      $_attributes_conflict = empty($values_intersection);
                  }

                  if (!$_attributes_conflict) {
                      //prepare restricted attributes for display
                      $contentsection_attributes[$attribute_data['attribute_id']] = array('attribute_data' => $attribute_data, 'values' =>array());
                      foreach ($values_intersection as $value) {
                          $contentsection_attributes[$attribute_data['attribute_id']]['values'][$value] = 
                              $allowed_attributes[$attribute_data['attribute_id']][$value];
                      }
                  }

                  if ($_attributes_conflict) $attributes_conflict = true;
              }
              if ($attributes_conflict || empty($contentsection_attributes)) continue;
              $contentsection_data['attributes'] = $contentsection_attributes;
          }
      } 

    // Collect all restrictions validation result. All must return (bool)true to show section.
    // Listen to on_cms_check_restrictions event with your handlers and return false or true.
    $cms_restrictions = cw_event('on_cms_check_restrictions', array($contentsection_data), array());
    $is_valid = true;
    foreach ($cms_restrictions as $rest) {
        $is_valid &= $rest;
    }
    if (!$is_valid) continue;

		// Load image
      if (true || $contentsection_data['type'] == 'image') {
		cw_load('image');
        $contentsection_data['image'] = cw_image_get('cms_images', $contentsection_data['contentsection_id']);
      }

		// Load alt lang
      $contentsection_alt_languages = cw_query_first("SELECT name, url, content FROM $tables[cms_alt_languages] WHERE contentsection_id = '".$contentsection_data['contentsection_id']."' AND code = '".$current_language."'");
      if (!empty($contentsection_alt_languages) && is_array($contentsection_alt_languages)) {
        $contentsection_data['url']     = $contentsection_alt_languages['url'];
        $contentsection_data['name']    = $contentsection_alt_languages['name'];
        $contentsection_data['content'] = $contentsection_alt_languages['content'];
      }

		// Update counter
      $count = intval(cw_query_first_cell("SELECT abuc.count FROM $tables[cms_user_counters] AS abuc WHERE abuc.contentsection_id = '".$contentsection_data['contentsection_id']."'"));
      $count++;
      $is_entry_exists = intval(cw_query_first_cell("SELECT COUNT(*) FROM $tables[cms_user_counters] WHERE contentsection_id = '".$contentsection_data['contentsection_id']."'"));
      if ($is_entry_exists) {
        cw_array2update('cms_user_counters', array('count' => $count), "contentsection_id = '".$contentsection_data['contentsection_id']."'");
      }
      else {
        cw_array2insert(
          'cms_user_counters',
          array(
            'count'     => '1',
            'contentsection_id' => $contentsection_data['contentsection_id'],
          ),
          true
        );
      }

      $allowed_contentsections[] = $contentsection_data;
    }

    if (intval($params['pick_random']) > 0 && count($allowed_contentsections) > 1) {
        $random_select_count = min(count($allowed_contentsections)-1, intval($params['pick_random']));
        if ($random_select_count > 0) {
            shuffle($allowed_contentsections);  
            $allowed_contentsections = array_slice($allowed_contentsections, 0, $random_select_count);
        }
    }

    $smarty->assign('contentsections', $allowed_contentsections);
    $smarty->assign('cs_skin', $skin);
	$smarty->assign('service_code', $params['service_code']);
    $smarty->assign('preload_popup', $params['preload_popup']);
    $smarty->assign('page_link_override', $params['page_link_override']);
	
    return $output."\n".$smarty->fetch('addons/cms/skins/content.tpl');

}

?>
