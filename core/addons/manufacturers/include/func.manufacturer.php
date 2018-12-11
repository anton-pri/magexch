<?php
function cw_manufacturer_get_list($where = '', $orderby = 'manufacturer') {
    global $tables, $current_language;

    $manufacturers = cw_query("select $tables[manufacturers].*, IFNULL($tables[manufacturers_lng].manufacturer, $tables[manufacturers].manufacturer) as manufacturer, IFNULL($tables[manufacturers_lng].descr, $tables[manufacturers].descr) as descr from $tables[manufacturers] LEFT JOIN $tables[manufacturers_lng] ON $tables[manufacturers].manufacturer_id = $tables[manufacturers_lng].manufacturer_id AND $tables[manufacturers_lng].code = '$current_language'".($where?"where $where":"")." order by $orderby");

    return $manufacturers;
}

# kornev, params
# $params[info_type] & 2 - products counter
function cw_manufacturer_search($params, $return = null) {
    extract($params);

    global $tables, $current_language, $target;

    $fields = $from_tbls = $query_joins = $where = $groupbys = $having = $orderbys = array();
   
# kornev, merge standart and additional variables
    if ($return)
    foreach ($return as $saname => $sadata)
        if (isset($$saname) && is_array($$saname) && empty($$saname)) $$saname = $sadata;

    $from_tbls[] = 'manufacturers';
    $query_joins['manufacturers_lng'] = array(
        'on' => "$tables[manufacturers_lng].manufacturer_id = $tables[manufacturers].manufacturer_id and $tables[manufacturers_lng].code='$current_language'",
    );

    if ($info_type & 2) {
        $attribute_id = cw_call('cw_attributes_get_attribute_by_field', array('field' => 'manufacturer_id'));
        $query_joins['product_counter'] = array(
            'tblname' => 'attributes_values',
            'on' => "product_counter.value = $tables[manufacturers].manufacturer_id AND product_counter.attribute_id=$attribute_id",
            'parent' => 'manufacturers',
            'only_select' => 1,
        );
        $fields[] = "count(product_counter.item_id) as products_count";
    }
  
    $groupbys[] = "$tables[manufacturers].manufacturer_id";

    $fields[] = "$tables[manufacturers].*";
    $fields[] = "IFNULL($tables[manufacturers_lng].manufacturer, $tables[manufacturers].manufacturer) as manufacturer";
    $fields[] = "IFNULL($tables[manufacturers_lng].descr, $tables[manufacturers].descr) as descr";

    $where[] = 1;
    if ($data['substring'])
        $where[] = "(IFNULL($tables[manufacturers_lng].manufacturer, $tables[manufacturers].manufacturer) like '%$data[substring]%' or IFNULL($tables[manufacturers_lng].descr, $tables[manufacturers].descr) like '%$data[substring]%')";

    if (isset($data['avail']))
        $where[] = "$tables[manufacturers].avail = '$data[avail]'";

    if (isset($data['featured']))
        $where[] = "$tables[manufacturers].featured = '$data[featured]'";

    if (!empty($data['sort_field'])) {
        $direction = $data['sort_direction'] ? 'DESC' : 'ASC';

        switch ($data['sort_field']) {
            case '':
                $sort_string = "manufacturer $direction";
                break;
            default:
                $orderbys[] = $data['sort_field'].' '.$direction;
        }
    }
    else
        $orderbys[] = 'manufacturer';

    $search_query_count = cw_db_generate_query('count(*)',  $from_tbls, $query_joins, $where, $groupbys, $having, array(), 0);
    $search_query = cw_db_generate_query($fields, $from_tbls, $query_joins, $where, $groupbys, $having, $orderbys);
    $total_items_res_id = db_query($search_query_count);
    $total_items = db_num_rows($total_items_res_id);


    $page = $data['page'];
    if ($data['count'])
        return $total_items;
    elseif($data['limit'])
        $limit_str = " LIMIT $data[limit]";
    elseif ($data['all'])
        $limit_str = '';
    else {
        $navigation = cw_core_get_navigation($target, $total_items, $page);
        $limit_str = " LIMIT $navigation[first_page], $navigation[objects_per_page]";
    }

    $manufacturers = cw_query($search_query.$limit_str);

    if ($info_type & 1 && $manufacturers)
    foreach($manufacturers as $k=>$v)
        $manufacturers[$k]['image'] = cw_image_get('manufacturer_images', $v['manufacturer_id']);

    return array($manufacturers, $navigation);
}

function cw_manufacturer_get_list_smarty() {
    global $tables;

    $ret = cw_func_call('cw_manufacturer_search', array('data' => array('all' => 1)));
    return $ret[0];
}

function cw_manufacturer_delete($manufacturer_id) {
    global $tables;

    db_query("delete from $tables[manufacturers] where manufacturer_id='$manufacturer_id'");
    db_query("delete from $tables[manufacturers_lng] where manufacturer_id='$manufacturer_id'");
    $id = cw_call('cw_attributes_get_attribute_by_field', array('field' => 'manufacturer_id'));
    db_query("delete from $tables[attributes_values] where attribute_id='$id' and value='$manufacturer_id'");
    cw_call('cw_attributes_cleanup', array($manufacturer_id,'M'));

    cw_image_delete($manufacturer_id, 'manufacturer_images');
    
    cw_event('on_manufacturer_delete', array($manufacturer_id));
}

function cw_manufacturer_get_menu($is_image = 0) {
    global $tables, $config, $current_language;

    $all = false;
    if ($config['manufacturers']['manufacturers_limit'] == 0 || $config['manufacturers']['view_list_manufacturer'] == 0) $all = true;
    if ($config['manufacturers']['view_list_manufacturer'] == 1) $is_image = 1;
    list($manufacturers, $navigation) = cw_func_call('cw_manufacturer_search', array('data' => array('avail' => 1, 'limit' => $config['manufacturers']['manufacturers_limit'], 'all'=>$all), 'info_type' => $is_image));
    
    return $manufacturers;
}

function cw_manufacturer_get_smarty($params) {
    return cw_func_call('cw_manufacturer_get', array('manufacturer_id' => $params['manufacturer_id']));
}

# $params[manufacturer_id]
function cw_manufacturer_get($params, $return = null) {
    extract($params);
    global $tables, $current_language;

    $fields = $from_tbls = $query_joins = $where = array();
# kornev, merge standart and additional variables
    if ($return)
    foreach ($return as $saname => $sadata)
        if (isset($$saname) && is_array($$saname) && empty($$saname)) $$saname = $sadata;

    $from_tbls[] = 'manufacturers';
    $fields[] = "$tables[manufacturers].*";

    $lang = $lang?$lang:$current_language;
    $query_joins['manufacturers_lng'] = array(
        'on' => "$tables[manufacturers_lng].manufacturer_id = $tables[manufacturers].manufacturer_id AND $tables[manufacturers_lng].code = '$lang'",
    );
    $fields[] = "IFNULL($tables[manufacturers_lng].manufacturer, $tables[manufacturers].manufacturer) as manufacturer";
    $fields[] = "IFNULL($tables[manufacturers_lng].descr, $tables[manufacturers].descr) as descr";

    $query_joins['manufacturer_images'] = array(
        'on' => "$tables[manufacturer_images].id = $tables[manufacturers].manufacturer_id",
    );
    $fields[] = "IF($tables[manufacturer_images].id IS NULL, '', 'Y') as is_image";

    $where[] = "$tables[manufacturers].manufacturer_id = '$manufacturer_id'";
    
    if (isset($avail)) {
    	$where[] = "$tables[manufacturers].avail = '$avail'";
    }

    $search_query = cw_db_generate_query($fields, $from_tbls, $query_joins, $where, $groupbys, $having, $orderbys);
    $manuf = cw_query_first($search_query);
    if (!$manuf) return false;
    
    if (!empty($manuf['url']) && strpos($manuf['url'], 'www.') === 0) {
    	$manuf['url'] = 'http://' . $manuf['url'];
    }

    $manuf['image'] = cw_image_get('manufacturer_images', $manufacturer_id);

    return $manuf;
}

function cw_manufacturers_is_manufacturer_attribute($params) {
    if ($params['attribute']['field'] == 'manufacturer_id') return true;
    return false;
}

# kornev, set product filter names
function cw_manufacturers_product_search($params, $return) {

    if ($return[2])
    foreach($return[2] as $k=>$v) {
        if ($v['field'] == 'manufacturer_id') {

			// This approach with all manufacturers in cache better works for huge amount of manufacturers over 1K
			$all_manufacturers = cw_cache_get(null,'manufacturers_all');
			if (empty($all_manufacturers)) {
				$all_manufacturers = cw_query_hash("SELECT manufacturer_id, manufacturer FROM cw_manufacturers",'manufacturer_id',false,true);
				cw_cache_save($all_manufacturers,null,'manufacturers_all');
			}
			
            if ($v['values'])
            foreach($v['values'] as $kk=>$vv) {
                if (!isset($all_manufacturers[$vv['id']])) unset($return[2][$k]['values'][$kk]);
                else $return[2][$k]['values'][$kk]['name'] = $all_manufacturers[$vv['id']];
            }
            if (!$return[2][$k]['values']) unset($return[2][$k]);
            break;
        } 
    }

    if ($return[0]) {
        global $tables;
        $attribute_id = cw_call('cw_attributes_get_attribute_by_field', array('field' => 'manufacturer_id'));
        if ($attribute_id) {
            foreach ($return[0] as $k => $v) {
                $product_id = $v['product_id'];
                $manufacturer = cw_query_first_cell("
                    SELECT m.manufacturer
                    FROM $tables[manufacturers] m
                    LEFT JOIN $tables[attributes_values] av ON av.value = m.manufacturer_id
                    WHERE av.item_id = '$product_id' AND av.attribute_id = '$attribute_id' AND av.item_type = 'P'
                ");
                $return[0][$k]['manufacturer'] = $manufacturer;
            }
        }
    }
    return $return;
}

function cw_manufacturers_product_get($params, $return) {
	global $tables;

	if (!empty($params['id'])) {
		$product_id = intval($params['id']);
		$attribute_id = cw_call('cw_attributes_get_attribute_by_field', array('field' => 'manufacturer_id'));
		if ($attribute_id) {
			$manufacturer = cw_query_first_cell("
				SELECT m.manufacturer
				FROM $tables[manufacturers] m
				LEFT JOIN $tables[attributes_values] av ON av.value = m.manufacturer_id
				WHERE av.item_id = '$product_id' AND av.attribute_id = $attribute_id AND av.item_type = 'P'
			");
			$return['manufacturer'] = $manufacturer;
		}
	}

	return $return;
}

// change search query params for order search
function cw_manufacturers_prepare_search_orders($data, $docs_type, &$fields, &$query_joins, &$where, &$groupbys, &$having, &$orderbys) {
    global $tables;

    if ($data['search_sections']['tab_search_orders_advanced'] && !empty($data['advanced']['manufacturer_id'])){
        $attribute_id = cw_call('cw_attributes_get_attribute_by_field', array('field' => 'manufacturer_id'));
        $query_joins['attributes_values']['on'] = "$tables[attributes_values].item_id = $tables[docs_items].product_id";
        $where[] = "$tables[attributes_values].attribute_id = ".$attribute_id;
        $where[] = "$tables[attributes_values].value IN ('".implode("', '", $data['advanced']['manufacturer_id'])."')";
    }
}

function cw_manufacturers_prepare_search_users($data, &$fields, &$from_tbls, &$query_joins, &$where, &$groupbys, &$having, &$orderbys) {
    global $tables;

    if (!empty($data['orders']['manufacturer_id'])) {
        $query_joins['docs_user_info'] = array(
           "on" => "$tables[docs_user_info].customer_id = $tables[customers].customer_id",
           'parent' => 'customers',
           'is_inner' => 1,
       );

       if (empty($query_joins['docs']))
           $query_joins['docs'] = array(
               "on" => "$tables[docs].doc_info_id = $tables[docs_user_info].doc_info_id",
               'parent' => 'docs_user_info',
               'is_inner' => 1,
           );

       $query_joins['doc_history_manufacturers'] = array(
           "on" => "$tables[doc_history_manufacturers].doc_id = $tables[docs].doc_id".
           " and $tables[doc_history_manufacturers].manufacturer_id in ('".implode("','", $data['orders']['manufacturer_id'])."')",
           'parent' => 'docs',
           'is_inner' => 1,
       );

/* this code works on currently existing products only
       $query_joins['di_manufacturers'] = array(
           "tblname" => 'docs_items',
           "on" => "di_manufacturers.doc_id = $tables[docs].doc_id",
           'parent' => 'docs',
           'is_inner' => 1,
       );

       $manufacturers_attribute_id = cw_call('cw_attributes_get_attribute_by_field', array('field' => 'manufacturer_id'));
       $query_joins['av_manufacturers'] = array(
            "tblname" => 'attributes_values',
            'on' => "av_manufacturers.item_id = di_manufacturers.product_id and av_manufacturers.item_type = 'P' and av_manufacturers.attribute_id = '$manufacturers_attribute_id' and av_manufacturers.value in ('".implode("','", $data['orders']['manufacturer_id'])."')",
            'parent' => 'di_manufacturers',
            'is_inner' => 1
       );
*/
    }
}

 function cw_manufacturers_doc_get_extras_data($doc_id) {
     global $tables;

     $return = cw_get_return();
     $attribute_id = cw_call('cw_attributes_get_attribute_by_field', array('field' => 'manufacturer_id'));
     $return['manufacturers'] = cw_query_first_cell(
                                "SELECT  GROUP_CONCAT(manufacturer) as manufacturers
                                 FROM $tables[manufacturers]
                                 INNER JOIN $tables[attributes_values] on $tables[attributes_values].value = $tables[manufacturers].manufacturer_id
                                 INNER JOIN $tables[docs_items]  on $tables[docs_items].product_id = $tables[attributes_values].item_id
                                 WHERE cw_docs_items.doc_id = '".$doc_id."'
                                 AND cw_attributes_values.attribute_id = ".$attribute_id);

     $return['manufacturers'] = str_replace(',', ', ', $return['manufacturers']);

    return $return;
}

function cw_manufacturers_on_cms_check_restrictions_M($data) {
	global $tables, $product_id;
	static $manufacturer_attribute_id;
	
	if (is_null($manufacturer_attribute_id)) {
        $manufacturer_attribute_id = cw_call('cw_attributes_get_attribute_by_field', array('manufacturer_id'));
    }
	
      if (isset($product_id)) {
		$allowed_manufacturer_ids = cw_ab_get_cms_restrictions($data['contentsection_id'],'M','manufacturer_id');
        if (empty($allowed_manufacturer_ids)) return true;
		$allowed_manufacturer_ids = array_column($allowed_manufacturer_ids,'manufacturer_id');
        $product_manufacturer_id = cw_query_first_cell("SELECT value FROM $tables[attributes_values] WHERE item_id = '".$product_id."' and item_type='P' AND attribute_id='$manufacturer_attribute_id'");
        if (!empty($allowed_manufacturer_ids) && is_array($allowed_manufacturer_ids) && !empty($product_manufacturer_id)) {
          if (!in_array($product_manufacturer_id, $allowed_manufacturer_ids)) return false;
        }
      }
      return true;
 }

function cw_manufacturers_get_last_title_part($location) {
    $last_title_part = cw_get_return();
    global $smarty;
    $main = $smarty->_tpl_vars['main'];
    if ($main == 'manufacturer_products') {
        $last_title_part = implode(' | ',array($last_title_part, cw_get_langvar_by_name('lbl_manufacturers'))); 
    }

    return $last_title_part;
}
