<?php
function cw_ab_get_contentsection($contentsection_id, $get_attributes = true) {

  global $tables, $current_language;

  $query = "SELECT * FROM $tables[cms] WHERE contentsection_id = '".intval($contentsection_id)."'";
  $contentsection = cw_query_first($query);
  if (!empty($contentsection) && is_array($contentsection)) {
    $contentsection_alt_languages = cw_query_first("SELECT name, alt, url, content FROM $tables[cms_alt_languages] WHERE contentsection_id = '".intval($contentsection_id)."' AND code = '".$current_language."'");
    if (!empty($contentsection_alt_languages) && is_array($contentsection_alt_languages)) {
      $contentsection['name']    = $contentsection_alt_languages['name'];
      $contentsection['url']     = $contentsection_alt_languages['url'];
      $contentsection['content'] = $contentsection_alt_languages['content'];
    }
  }

  if ($contentsection['type'] == 'image') {
    $contentsection['image'] = cw_image_get('cms_images', $contentsection_id);
  }

  if ($get_attributes)
      $contentsection['attributes'] = cw_func_call('cw_attributes_get', array('item_id' => $contentsection_id, 'item_type' => 'AB'));

  return $contentsection; 

  $contentsection['attributes'] = $attributes;

  return $contentsection;

}


#
# This function converts search masks with
# metasymbols ? and * to equivalent MysSQL
# search mask.
#
function cw_ab_convert_search_mask_to_mysql($mask) {
  $mask = strval($mask);
  if (strlen($mask) == 0) return '%';
  $mask = str_replace(array('*', '?'), array('%', '_'), $mask);
  return $mask;
}

function cw_ab_get_cms_restrictions($contentsection_id, $object_type, $object_name='') {
	global $tables;
	$contentsection_id = intval($contentsection_id);

	$rename_object = '';
	if (!empty($object_name)) $rename_object = " as $object_name";

	$result = cw_query($s="SELECT object_id $rename_object, operation, value_id, value FROM $tables[cms_restrictions] WHERE contentsection_id=$contentsection_id AND object_type='$object_type'");

        return $result;	
}

function cw_ab_get_cms_categories($contentsection_id = 0) {

	$categories = cw_ab_get_cms_restrictions($contentsection_id,'C','category_id');
	return array_column($categories,'category_id');

}

function cw_ab_get_cms_categories_ex($contentsection_id = 0) {

	$categories = cw_ab_get_cms_restrictions($contentsection_id,'CX','category_id');
	return array_column($categories,'category_id');

}


function cw_ab_get_cms_products($contentsection_id = 0) {

	return cw_ab_get_cms_restrictions($contentsection_id,'P','id');

}

// To addons
function cw_ab_get_cms_manufacturers($contentsection_id) {

  global $tables;

//  $manufacturers = cw_ab_get_cms_restrictions($contentsection_id,'M','manufacturer_id');
//  $manufacturers = array_column($manufacturers,'manufacturer_id');
  
  if (empty($contentsection_id)) {
    $query = "SELECT m.manufacturer_id, m.manufacturer, '' AS selected " .
             "FROM $tables[manufacturers] AS m " .
             "ORDER BY m.manufacturer ASC";
  }
  else {
    $query = "SELECT m.manufacturer_id, m.manufacturer, IF(ISNULL(abm.object_id), '', 1) AS selected " .
             "FROM $tables[manufacturers] AS m " .
             "LEFT JOIN $tables[cms_restrictions] AS abm " .
             "ON m.manufacturer_id = abm.object_id AND abm.object_type='M' AND abm.contentsection_id = '".$contentsection_id."' " .
             "ORDER BY m.manufacturer ASC";
  }

  return cw_query($query);
}

function cw_ab_check_clean_url($url, $attr_ids){
    global $tables;

    $data =  cw_query("SELECT item_id FROM $tables[attributes_values] WHERE value = '$url' AND attribute_id IN (".implode(", ", $attr_ids).") ");

    if(!$data)
        return 0;
    else
        return 1;

}

function cw_ab_get_cms_clean_urls($contentsection_id) {

    global $tables;
    static $clean_url_atts;

    $contentsection_id = intval($contentsection_id);
    
    $urls = cw_ab_get_cms_restrictions($contentsection_id,'URL');
    if (is_null($clean_url_atts)) $clean_url_atts = cw_call('cw_attributes_get_attributes_by_field', array(array('field'=>'clean_url')));
    
    foreach ($urls as $k=>$v) {
		$urls[$k]['valid_url'] = cw_call('cw_ab_check_clean_url', array($v['value'], $clean_url_atts));
	}

	return $urls;

}

function cw_ab_get_cms_restrict_attributes($contentsection_id) {

    global $tables;
    
    $contentsection_id = intval($contentsection_id);

    $_restricted_attributes = cw_ab_get_cms_restrictions($contentsection_id,'A','attribute_id');
    $restricted_attributes = array();
    foreach ($_restricted_attributes as $attr_entry) {
        $restricted_attributes[] = array(
            'attribute_id' => $attr_entry['attribute_id'],
            'operation' => $attr_entry['operation'],
            'value' => array(($attr_entry['value_id']>0)?$attr_entry['value_id']:$attr_entry['value'])
        ); 
    }
    return $restricted_attributes;
}

function cw_ab_staticpages_get($page_id, $active, $return = null) {

    global $tables;

    $fields = $from_tbls = $query_joins = $where = array();

    if ($return)
    foreach ($return as $saname => $sadata)
        if (isset($$saname) && is_array($$saname) && empty($$saname)) $$saname = $sadata;

    $fields[] = "$tables[cms].contentsection_id as page_id";
    $from_tbls[] = 'cms';
    $fields[] = "$tables[cms].*";
    $where[] = "$tables[cms].contentsection_id='$page_id'";
   
    $current_date = time();

    $where[] = "if($tables[cms].start_date > 0, $tables[cms].start_date <= '".$current_date."', 1)";
    $where[] = "if($tables[cms].end_date > 0, $tables[cms].end_date >= '".$current_date."', 1)";


    if (isset($active))
        $where[] = "$tables[cms].active = '".($active?'Y':'N')."'";

    $search_query = cw_db_generate_query($fields, $from_tbls, $query_joins, $where, $groupbys, $having, $orderbys);

    return cw_query_first($search_query);
}

function cw_cms_get_used_attributes_options($selected_filter) {
    global $tables;

    $all_attribute_ids = cw_query_column("select distinct(object_id) as attribute_id from $tables[cms_restrictions] where object_type='A'");

    if (empty($all_attribute_ids))
        return array();

    list ($attributes, $navigation) = cw_func_call(
        'cw_attributes_search',
        array(
            'data' => array(
                'all' => 1,
                'active' => 1,
                'type' => 'P',
                'sort_field' => 'orderby, name',
            )
        )
    );

    $attributes_options = array();

    if (is_array($attributes) && count($attributes)) {
        foreach ($attributes as $value) {
            if (!in_array($value['attribute_id'], $all_attribute_ids)) continue; 
 
            $default_values = cw_call(
                'cw_attributes_get_attribute_default_value',
                array(
                    'attribute_id' => $value['attribute_id']
                )
            );
            $options = array();
            $attribute_value_ids = cw_query_column("select value_id from $tables[cms_restrictions] where object_id='$value[attribute_id]' AND object_type='A'");
            if (is_array($default_values) && count($default_values) && !empty($attribute_value_ids)) {
                foreach ($default_values as $v) {
                    if (!empty($v['value'])) {
                        if (in_array($v['attribute_value_id'], $attribute_value_ids)) {
                            $options[] = array(
                                'attribute_value_id' => $v['attribute_value_id'],
                                'name' => $v['value'],
                                'checked' => in_array($v['attribute_value_id'], (array)$selected_filter[$value['attribute_id']])
                            );
                        }
                    }
                }
            }
            if (count($options)) {
                $attributes_options[] = array(
                    'attribute_id' => $value['attribute_id'],
                    'name' => $value['name'],
                    'options' => $options
                );
            } elseif ($value['type'] == 'text') {
                $attributes_options[] = array(
                    'attribute_id' => $value['attribute_id'],
                    'name' => $value['name'],
                    'type' => $value['type'],
                    'value' => $selected_filter[$value['attribute_id']]
                ); 
            }
        }
    }

    return $attributes_options;


}

function cw_cms_get_meta($tag) {
    global $smarty, $tables;

    cw_load('attributes');

    $page = $smarty->_tpl_vars['main'];

    if ($page == 'pages') {
        $attribute_id = cw_call('cw_attributes_filter', array(array('item_type' => 'AB', 'field' =>'meta_'.$tag),true,'attribute_id'));
        $meta = cw_call('cw_attribute_get_value', array($attribute_id, $smarty->_tpl_vars['page_data']['contentsection_id']));
    } elseif ($page == "help") {

        $section = $smarty->_tpl_vars['section'];

        //attempt to find related cms entry
        $related_page = $smarty->_tpl_vars['page_data']; 
        if (!empty($related_page)) {
            $attribute_id = cw_call('cw_attributes_filter', array(array('item_type' => 'AB', 'field' =>'meta_'.$tag),true,'attribute_id'));
            $meta = cw_call('cw_attribute_get_value', array($attribute_id, $related_page['contentsection_id']));
        }
    }

    return !empty($meta)?$meta:$return;
}


function cw_cms_get_staticpages() {
    global $tables;

    $fields = $from_tbls = $query_joins = $where = array();

    if ($return)
    foreach ($return as $saname => $sadata)
        if (isset($$saname) && is_array($$saname) && empty($$saname)) $$saname = $sadata;

    $fields[] = "$tables[cms].contentsection_id";
    $fields[] = "IFNULL($tables[cms_alt_languages].name,$tables[cms].name) as name";
    $fields[] = "$tables[cms].type";
    $fields[] = "IF($tables[cms].active='Y',1,0) as active";
    $fields[] = "$tables[cms].orderby";

    $from_tbls[] = 'cms';

    $query_joins['cms_alt_languages'] = array(
        'on' => "$tables[cms_alt_languages].contentsection_id = $tables[cms].contentsection_id",
    );

    $where[] = "$tables[cms].type='staticpage'";

    $orderbys[] = "name DESC, $tables[cms].orderby DESC"; 
  
    $search_query = cw_db_generate_query($fields, $from_tbls, $query_joins, $where, $groupbys, $having, $orderbys);
    return cw_query($search_query);


}
