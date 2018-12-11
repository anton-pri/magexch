<?php
// Init
global $cw_attributes;
$cw_attributes = cw_call('cw_attributes_init');

// "price" is fixed core attribute, remember its id as const
define('PRICE_ATTRIBUTE_ID', cw_call('cw_attributes_filter',array(array('field'=>'price','addon'=>'core'), true,'attribute_id')) );


function cw_attributes_init() {
    global $tables, $config, $current_language;
    global $cw_attributes;

    $language = $current_language?$current_language:$config['default_customer_language'];

    $fields = $from_tbls = $query_joins = $where = $groupbys = $having = $orderbys = array();
    
    $from_tbls['a'] = 'attributes';
    $fields[] = 'a.attribute_id as id';
    $fields[] = 'a.*';
    
    $fields[] = "ifnull(al.name, a.name) as name";
    $query_joins['al'] = array(
        'tblname' => 'attributes_lng',
        'on' => "al.attribute_id = a.attribute_id and al.code = '$language'",
        'only_select' => 1,
    );  
    $orderbys[] = 'a.orderby';
    $search_query = cw_db_generate_query($fields, $from_tbls, $query_joins, $where, $groupbys, $having, $orderbys);

    $attributes['all'] = cw_query_hash($search_query,'id',false, false);
    
    $ref_keys = array('field','type','item_type','addon','active','pf_is_use','is_show');
    
    foreach ($attributes['all'] as $id=>$att) {
        if ($att['item_type'] == 'P') {
            $classes = cw_query_column("SELECT attribute_class_id FROM $tables[attributes_classes_assignement] WHERE attribute_id = '$id'");
            foreach ($classes as $c) {
                $attributes['attribute_class_id'][$c][$id] = &$attributes['all'][$id];
            }
        }
        foreach ($ref_keys as $field) {
            $attributes[$field][$att[$field]][$id] = &$attributes['all'][$id];
        }
    }
    
    // Init global var
    $cw_attributes = $attributes;
    
    return $attributes;
    
}

/*
 * Find attributes matched criteria
 * (array) $filter_data
 * e.g. array('field'=>'domains', 'item_type'=>'P')
 * 
 * Returns array or null
 */
function cw_attributes_filter($filter_data, $only_first = false, $field = null) {
    global $cw_attributes;
    
    // Extract first filter criterium
    reset($filter_data);
    list($key, $value) = each($filter_data);
    array_shift($filter_data);
    
    if ($key == 'attribute_id') $result = array($value=>&$cw_attributes['all'][$value]);
    else $result = $cw_attributes[$key][$value];
        
    // Intersect with other criteria
    foreach ($filter_data as $k=>$v) {
        if (is_array($result) && is_array($cw_attributes[$k][$v])) {
            $result = array_intersect_key($result, $cw_attributes[$k][$v]);
        } else {
            $result = null;
            break;
        }
    }
    
    if (!is_array($result)) {
        if ($only_first && $field) return '';
        return array();
    }
    
    $result = array_map(function($v){return $v;},$result); // Return copy without references
    
    if ($only_first) {
        $result = array_shift($result);
        if ($field) return $result[$field];
        return $result;
    }
    
    if ($field) return array_column($result, $field);
    return $result;
}


function cw_attributes_get_types() {
    return array('text', 'textarea', 'integer', 'decimal', 'selectbox', 'multiple_selectbox', 'date', 'yes_no', 'hidden');
}

function cw_attributes_cleanup_field($field) {
    return strtolower(preg_replace('/[^A-Za-z0-9_]/', '', $field));
}

function cw_attributes_cleanup($item_id, $item_type, $language = null, $attribute_id = 0) {
    global $tables;
    if (!is_array($item_id)) $item_id = array($item_id);

    db_query("delete from $tables[attributes_values] where item_id in ('".implode("', '", $item_id)."') and item_type='$item_type'".($attribute_id?" and attribute_id='$attribute_id'":'').(isset($language)?" and code='$language'":''));
}

function cw_attributes_delete($attribute_id) {
    global $tables;

    db_query("delete from $tables[attributes] where attribute_id='$attribute_id'");
    db_query("delete from $tables[attributes_lng] where attribute_id='$attribute_id'");
    db_query("delete from $tables[attributes_values] where attribute_id='$attribute_id'");
    db_query("delete from $tables[attributes_classes_assignement] where attribute_id='$attribute_id'");
    $default = cw_query_column("select attribute_value_id from $tables[attributes_default] where attribute_id='$attribute_id'");
    cw_call('cw_attributes_delete_values', array($default));
    cw_call('cw_image_delete_all', array('attributes_images', "id = '$attribute_id'"));
    
    cw_call('cw_attributes_init');
}

function cw_attributes_delete_values($att_value_ids) {
    global $tables;

	foreach ($att_value_ids as $id) {
		$att_id = cw_query_first_cell("select attribute_id from $tables[attributes_default] where attribute_value_id = '$id'");
		db_query("delete from $tables[attributes_values] where attribute_id = '$att_id' and value='$id'");
	}
	
    db_query("delete from $tables[attributes_default] where attribute_value_id in ('".implode("', '", $att_value_ids)."')");
    db_query("delete from $tables[attributes_default_lng] where attribute_value_id in ('".implode("', '", $att_value_ids)."')");
}

# kornev, save only a few attributes and do not rewrite the whole list;
# kornev, mostly this function should be used by addons for some particular attributes update
# $params[item_id]
# $paramsitem_type]
# $params[attributes] = array(field => value)
function cw_attributes_save_attribute($params, $return) {
    global $tables;

# kornev, ability to redefine attributes in addon
    if ($return) $params['attributes'] = array_merge($params['attributes'], $return);
    extract($params);

    if (is_array($attributes))
        foreach($attributes as $fld=>$v) {
            $attribute_id = cw_query_first_cell("select attribute_id from $tables[attributes] where field='$fld'");
            if (!$attribute_id) continue;
            cw_attributes_cleanup($item_id, $item_type, null, $attribute_id);

            if (!is_array($v)) $v = array($v);
            foreach($v as $value)
                cw_array2insert('attributes_values', array('item_id' => $item_id, 'item_type' => $item_type, 'attribute_id' => $attribute_id, 'value' => $value));
        }

    return $params['attributes'];
}

# kornev, save all of the attributes with the rewrite/default values if requires
# params
# $item_id
# $item_type
# $attributes
# $language - language to store the text attributes
function cw_attributes_save($item_id, $item_type, $attributes, $language = null, $extra = array()) {
    global $config, $current_language, $tables;

    $all_languages = cw_func_call('cw_core_get_available_languages');

    if ($extra['update_posted_only']) 
        $prefilled_attributes = $attributes;

    $attributes = cw_func_call('cw_attributes_get', array('item_id' => $item_id, 'item_type' => $item_type, 'prefilled' => $attributes, 'is_default' => $extra['is_default']));

    if ($extra['update_posted_only']) {
        $_attributes = array();
        foreach ($prefilled_attributes as $attr_name => $posted_attr) {
            $_attributes[$attr_name] = $attributes[$attr_name];    
        }
        $attributes = $_attributes;  
    }

# kornev, cannot make it - because the hidden attributes;

    $language = $language?$language:$current_language;
# kornev, store the default values with '' language - this way we are able to select it in the right way
//    if ($language == $config['default_admin_language']) $language = '';

    if (is_array($attributes))
        foreach($attributes as $v) {
# kornev, do not update the hidden values here, the hidden values have to be updated by cw_attributes_save_attribute
            if ($v['type'] == 'hidden') continue;
# kornev, only the text data is multi-lng - clean it with language
            if (in_array($v['type'], array('text', 'textarea')))
                cw_attributes_cleanup($item_id, $item_type, $language, $v['attribute_id']);
            else
                cw_attributes_cleanup($item_id, $item_type, null, $v['attribute_id']);

            foreach($v['values'] as $value) {
# kornev, the text attribute might be multilng on this stage
# kornev, the selectbox are store the attribute value # - so it's translated on the set attribute stage
                if (in_array($v['type'], array('text', 'textarea'))) {
                    cw_array2insert('attributes_values', array('item_id' => $item_id, 'item_type' => $item_type, 'attribute_id' => $v['attribute_id'], 'value' => $value, 'code' => $language));
# kornev, we should check the amount of the values - because the multi-lng values might be required to save as default
                    if (count($all_languages) != cw_query_first_cell("select count(*) from $tables[attributes_values] where item_id = '$item_id' and item_type = '$item_type' and attribute_id = '$v[attribute_id]'"))
                        foreach($all_languages as $code => $tmp) {
                            if (!cw_query_first_cell("select count(*) from $tables[attributes_values] where item_id = '$item_id' and item_type = '$item_type' and attribute_id = '$v[attribute_id]' and code = '$code'")) {
                                $dfv = cw_call('cw_attributes_get_attribute_default_value', array('attribute_id' => $v['attribute_id'], 'language' => $code));
                                if ($dfv[0]['value'])
                                    cw_array2insert('attributes_values', array('item_id' => $item_id, 'item_type' => $item_type, 'attribute_id' => $v['attribute_id'], 'value' => $dfv[0]['value'], 'code' => $code));
                            }
                        }
                }
                else {
                    cw_array2insert('attributes_values', array('item_id' => $item_id, 'item_type' => $item_type, 'attribute_id' => $v['attribute_id'], 'value' => $value));
                }
            }
        }

    return $attributes;
}

# kornev, params
# $item_id, $item_type, $prefilled = array(), $is_default = false, $attribute_class_id = 0, $is_show = null, $attribute_fields = array()) {
# $params[language]
function cw_attributes_get($params, $return) {
    global $tables, $current_language;

    extract($params);

    if (empty($attribute_class_ids) && !empty($attribute_class_id))
        $attribute_class_ids = array($attribute_class_id);

# kornev, TOFIX lng param
    $language = $language?$language:$current_language;

    if ($attribute_fields) {
# kornev, selecte only a few fields
        $att_ids = cw_query_column($sql="select a.attribute_id from $tables[attributes] as a where field in ('".implode("', '", $attribute_fields)."') and a.item_type='$item_type'");
    } elseif ($attribute_addons) {
        $att_ids = cw_query_column("select a.attribute_id from $tables[attributes] as a where addon in ('".implode("', '", $attribute_addons)."') and a.item_type='$item_type'");
    }
    elseif ($item_type == 'P') {
# kornev, try to find it
        $all_attribute_class_ids = cw_func_call('cw_items_attribute_classes_get', array('item_id'=>$item_id, 'item_type' => 'P'));

        if (!empty($all_attribute_class_ids)) 
        $attribute_class_condition = " and aca.attribute_class_id in (".implode(",",$all_attribute_class_ids).")"; 

        if (empty($attribute_class_ids)) $attribute_class_ids = $all_attribute_class_ids;
# kornev, for the products we should unset the not-assigned attributes;
# kornev, also we should apply the default class on the new product
        if ($is_default || empty($attribute_class_ids))
            $att_ids = cw_query_column($sql="select a.attribute_id from $tables[attributes] as a, $tables[attributes_classes] as ac, $tables[attributes_classes_assignement] as acm where ac.is_default = 1 and acm.attribute_class_id=ac.attribute_class_id and acm.attribute_id=a.attribute_id and a.item_type='$item_type'");
        else
            $att_ids = cw_query_column($sql="select a.attribute_id from $tables[attributes] as a, $tables[attributes_classes] as ac, $tables[attributes_classes_assignement] as acm where ac.attribute_class_id in ('".implode("', '",$attribute_class_ids)."') and acm.attribute_class_id=ac.attribute_class_id and acm.attribute_id=a.attribute_id and a.item_type='$item_type'");

        if (!$att_ids) $att_ids = array();
    }

    $where_is_show_addon = empty($attribute_fields)?" or (a.addon != '' && a.is_show_addon=1)":'';

    $attributes = cw_query($sql="select a.*, ifnull(al.name, a.name) as name, IFNULL(lng1.value, a.addon) as addon_lng, IFNULL(aca.attribute_class_id, 0) as attr_class_id, ac.name as attr_class_name from $tables[attributes] as a left join  $tables[attributes_lng] as al on a.attribute_id=al.attribute_id and al.code='$language' left join $tables[languages] as lng1 ON lng1.code = '$language' and lng1.name = CONCAT('addon_name_', a.addon) left join $tables[addons] as m on m.addon=a.addon left join $tables[attributes_classes_assignement] as aca on a.attribute_id=aca.attribute_id $attribute_class_condition left join $tables[attributes_classes] as ac on ac.attribute_class_id=aca.attribute_class_id where (m.active or m.addon is null or a.addon='core') ".(isset($is_show)?"and is_show='$is_show' and a.active='$is_show'":'')." and item_type='$item_type' ".(($item_type == 'P')?" and (a.attribute_id in ('".implode("', '", $att_ids)."') $where_is_show_addon)":($att_ids?" and a.attribute_id in ('".implode("', '", $att_ids)."')":''))." group by a.attribute_id order by addon, attr_class_id, orderby");

# kornev, multi-lng, '' - because the selectboxes
    $values = cw_query_hash($sql="select av.attribute_id, ifnull(lng.value, av.value) as value from $tables[attributes_values] as av left join $tables[attributes_values] as lng on lng.item_id=av.item_id and lng.attribute_id=av.attribute_id and lng.item_type=av.item_type and lng.code='$language' where av.item_id='$item_id' and av.item_type='$item_type' and av.code in ('', '$language')", 'attribute_id', true, true);

# kornev, we need to receive the attributes by 'field' in key - it's more suitable in most of the cases for addons
    $return = array();

    if ($attributes)
        foreach($attributes as $k=>$v) {

# kornev, in the default values we are store the option for the selectbox and the default values
            $attributes[$k]['default_value'] = cw_call('cw_attributes_get_attribute_default_value', array('attribute_id' => $v['attribute_id'], 'language' => $language));
            if ($is_default) {
# kornev, if the default should be used, but the prefilled is already set - use it
# kornev, mostly it means that the fields was filled partially we have to fix the other
                if ($prefilled[$v['field']]) {
                    $return[$attributes[$k]['field']] = $attributes[$k];
                    continue;
                }
                if (in_array($v['type'], array('selectbox', 'multiple_selectbox')) && is_array($attributes[$k]['default_value'])) {
                    foreach($attributes[$k]['default_value'] as $dfv)
                        if ($dfv['is_default']) $prefilled[$v['field']][] = $dfv['value'];
                }
                else
                    $prefilled[$v['field']] = $attributes[$k]['default_value'];
            }
# kornev, extract images for customer area
# kornev, we need to extract it for the $values only
            if (in_array($v['type'], array('selectbox', 'multiple_selectbox')) && is_array($attributes[$k]['default_value']) && is_array($values[$v['attribute_id']]))
                foreach($attributes[$k]['default_value'] as $dfv) {
                    $select_values[$dfv['attribute_value_id']] = $value;
                    if ($dfv['image_id'] && in_array($dfv['value'], $values[$v['attribute_id']])) {
                        $image = cw_image_get('attributes_images', $dfv['image_id']);
                        $image['alt'] = $dfv['value'];
                        $attributes[$k]['images'][$dfv['value']] = $image;
                    }
                }

            if (is_array($prefilled) && count($prefilled)) {
//            $attributes[$k]['values'] = $prefilled[$v['field']];
                if ($attributes[$k]['type'] == 'date' && !empty($prefilled[$v['field']]['Date_Month'])) {
                    $prefilled[$v['field']] = mktime(0, 0, 0, $prefilled[$v['field']]['Date_Month'], $prefilled[$v['field']]['Date_Day'], $prefilled[$v['field']]['Date_Year']);
                }
                if (is_array($prefilled[$v['field']])) {
                    $attributes[$k]['values'] = $prefilled[$v['field']];
                    $attributes[$k]['value'] = array_pop($prefilled[$v['field']]);
                }
                else {
                    $attributes[$k]['value'] = $prefilled[$v['field']];
                    $attributes[$k]['values'] = array($prefilled[$v['field']]);
                }
            }
            elseif (is_array($values[$v['attribute_id']])) {
                $attributes[$k]['values'] = $values[$v['attribute_id']];
                $attributes[$k]['value'] = array_pop($values[$v['attribute_id']]);
            }
            if (!is_array($attributes[$k]['values'])) $attributes[$k]['values'] = array();


            if (empty($attributes[$k]['addon']) && in_array($v['type'], array('selectbox', 'multiple_selectbox')) && !empty($attributes[$k]['default_value'])) {
                //check if current values of extra fields are valid options
                $values_are_valid = true;
                if (is_array($attributes[$k]['values'])) { 
                    foreach ($attributes[$k]['values'] as $atv_k => $atv) {
                        $atr_value_found = false;
                        foreach ($attributes[$k]['default_value'] as $dfv) {
                            if ($dfv['attribute_value_id'] == $atv) { 
                                $atr_value_found = true;
                                break; 
                            } 
                        }  
                        if (!$atr_value_found) {
                            $values_are_valid = false;
                        }
                    }
                } else {
                    $values_are_valid = false;
                }
                if (!$values_are_valid) { 
                    $attributes[$k]['values'] = array();
                    foreach ($attributes[$k]['default_value'] as $dfv) { 
                        if ($dfv['is_default'] == 1) {
                            $attributes[$k]['values'][] = $dfv['attribute_value_id'];
                            $attributes[$k]['default_values_selected'] = 'Y';
                        } 
                    }
                }     
            }


# kornev, for the selectbox, multi-selectbox the text values are stored in the default_values (required by multi-lng) - need to find that
            $attributes[$k]['values_str']  = $attributes[$k]['values'];

            $select_values = array();
            if (in_array($v['type'], array('selectbox', 'multiple_selectbox'))) {
                foreach($attributes[$k]['default_value'] as $dfv)
                    $select_values[$dfv['attribute_value_id']] = $dfv['value'];
                foreach($attributes[$k]['values_str'] as $vid=>$vv)
                    $attributes[$k]['values_str'][$vid] = $select_values[$vv];
            }

            $return[$attributes[$k]['field']] = $attributes[$k];
        }
    return $return;
}

# kornev,
# $params[field] - field of the attribute
# return attribute_id if $field is scalar
# return array of attribute_ids if $field is array
function cw_attributes_get_attribute_by_field($field) {
    global $tables;

    if ($ret=cw_get_return()) return $ret; // pre-hook can override this function for certain field by returning own value

    if (!is_array($field)) $fields = array($field);
    else $fields = $field;
    $ret = array();
    foreach ($fields as $f) {
        $att = cw_call('cw_attributes_filter',array(array('field'=>$f), true));
        if ($att) $ret[$att['field']] = $att['attribute_id'];
    }

    if (!is_array($field)) return array_pop($ret);
    return $ret;
}

function cw_attributes_get_attributes_by_field($params, $return = null) {
    global $tables;

    if (isset($return)) return $return;
    $conditions = array('field'=>$params['field']);
    if (isset($params['active'])) $conditions['active'] = intval($param['active']);

    $ret = cw_call('cw_attributes_filter',array($conditions));
    $ret = array_column($ret,'attribute_id','item_type');
    return $ret;
}

function cw_attributes_get_attribute_default_value($attribute_id, $language=null) {

    global $tables, $current_language;
    $language = $language?$language:$current_language;
    return cw_query("select ad.*, ifnull(adl.value, ad.value) as value from $tables[attributes_default] as ad left join $tables[attributes_default_lng] as adl on ad.attribute_value_id=adl.attribute_value_id and adl.code='$language' where ad.attribute_id='$attribute_id' order by ad.orderby, ad.attribute_value_id");
}

# kornev
# $params[attribute_id]
# $params[language] - language to select
function cw_attributes_get_attribute($params, $return = null) {
    global $cw_attributes;
    global $tables, $current_language;

    $language = $params['language']?$params['language']:$current_language;
    $attribute_id = $params['attribute_id'];
    
    $attribute = $cw_attributes['all'][$attribute_id];

    $dv = cw_call('cw_attributes_get_attribute_default_value', array($attribute_id,$language));
    if (in_array($attribute['type'], array('selectbox', 'multiple_selectbox'))) {
        $attribute['default_value'] = $dv; // TOFIX. Wrong approach, default value only one
        $attribute['default_values'] = $dv;
    } else {
        $attribute['default_value'] = $dv[0]['value']; // Consider default value as scalar
        $attribute['default_values'] = $dv;
    }

    return $attribute;
}

# kornev, params
# data - the data to search
# $params[language]
function cw_attributes_search($params, $return = null) {
    extract($params);

    global $tables, $current_language, $target;

    $language = $language?$language:$current_language;

    $fields = $from_tbls = $query_joins = $where = $groupbys = $having = $orderbys = array();

# kornev, merge standart and additional variables
    if ($return)
        foreach ($return as $saname => $sadata)
            if (isset($$saname) && is_array($$saname) && empty($$saname)) $$saname = $sadata;

    $from_tbls[] = 'attributes';

    $groupbys[] = "$tables[attributes].attribute_id";

    $fields[] = "$tables[attributes].*";

    $fields[] = "ifnull($tables[attributes_lng].name, $tables[attributes].name) as name";
    $query_joins['attributes_lng'] = array(
        'on' => "$tables[attributes_lng].attribute_id = $tables[attributes].attribute_id and $tables[attributes_lng].code = '$language'",
        'only_select' => 1,
    );

    $where[] = 1;
    if ($data['substring'])
        $where[] = "($tables[attributes].name like '%$data[substring]%' or $tables[attributes].field like '%$data[substring]%')";

    if (isset($data['addon'])){
        if ($data['addon'] && is_array($data['addon'])) {
            $where[] = "$tables[attributes].addon IN ('".implode("', '", $data['addon'])."')";
        } else {
            $where[] = "$tables[attributes].addon = '$data[addon]'";
        }
    }

    if (isset($data['active']))
        $where[] = "$tables[attributes].active = '$data[active]'";

    if (isset($data['is_show']))
        $where[] = "$tables[attributes].is_show = '$data[is_show]'";

    if (isset($data['pf_is_use']))
        $where[] = "$tables[attributes].pf_is_use = '$data[pf_is_use]'";

    if (isset($data['item_type'])) {
        if (is_array($data['item_type'])) {
            $where[] = "$tables[attributes].item_type IN ('".implode("', '", $data['item_type'])."')";
        } else {
            $where[] = "$tables[attributes].item_type = '$data[item_type]'";
        }
    }

    if (isset($data['class'])) {
        $query_joins['attributes_classes_assignement'] = array(
            'on' => "$tables[attributes_classes_assignement].attribute_id = $tables[attributes].attribute_id",
        );
        if (isset($data['class'][0])) {
            $where[] ="($tables[attributes_classes_assignement].attribute_class_id IN ('".implode("', '", $data['class'])."') OR $tables[attributes_classes_assignement].attribute_class_id IS NULL )";
        } else
            $where[] = "$tables[attributes_classes_assignement].attribute_class_id IN ('".implode("', '", $data['class'])."')";
    }

    if (!empty($data['sort_field'])) {
        $direction = $data['sort_direction'] ? 'DESC' : 'ASC';
        $orderbys[] = $data['sort_field'].' '.$direction;
    }
    else
        $orderbys[] = 'name';

    $search_query_count = cw_db_generate_query('count(*)',  $from_tbls, $query_joins, $where, $groupbys, $having, array(), 0);
    $search_query = cw_db_generate_query($fields, $from_tbls, $query_joins, $where, $groupbys, $having, $orderbys);
    $total_items_res_id = db_query($search_query_count);
    $total_items = db_num_rows($total_items_res_id);
    $navigation = cw_core_get_navigation($target, $total_items, $data['page']);
    $limit_str = " LIMIT $navigation[first_page], $navigation[objects_per_page]";
    if ($data['all'])
        $limit_str = '';

    $attributes = cw_query($search_query . $limit_str);

    return array($attributes, $navigation);
}

function cw_attributes_get_all_for_products($is_show=null) {
    $filter = array('addon'=>'','item_type' => 'P');
    if (!is_null($is_show)) $filter['is_show'] = intval($is_show);
    $attributes = cw_call('cw_attributes_filter', array($filter));
    return $attributes;
}

function cw_attributes_get_all_classes_for_products() {
    list($attributes, $navigation) = cw_func_call('cw_attributes_classes_search', array('data' => array('all' => 1)));
    return $attributes;
}

function cw_attributes_classes_search($params, $return = null) {
    extract($params);

    global $tables, $current_language, $target;

    $fields = $from_tbls = $query_joins = $where = $groupbys = $having = $orderbys = array();

# kornev, merge standart and additional variables
    if ($return)
        foreach ($return as $saname => $sadata)
            if (isset($$saname) && is_array($$saname) && empty($$saname)) $$saname = $sadata;

    $from_tbls[] = 'attributes_classes';

    $groupbys[] = "$tables[attributes_classes].attribute_class_id";

    $fields[] = "$tables[attributes_classes].*";

    $where[] = 1;
    if ($data['substring'])
        $where[] = "$tables[attributes_classes].name like '%$data[substring]%'";

    if (!empty($data['sort_field'])) {
        $direction = $data['sort_direction'] ? 'DESC' : 'ASC';
        $orderbys[] = $data['sort_field'].' '.$direction;
    }
    else
        $orderbys[] = 'name';

    $search_query_count = cw_db_generate_query('count(*)',  $from_tbls, $query_joins, $where, $groupbys, $having, array(), 0);
    $search_query = cw_db_generate_query($fields, $from_tbls, $query_joins, $where, $groupbys, $having, $orderbys);

    $total_items_res_id = db_query($search_query_count);
    $total_items = db_num_rows($total_items_res_id);

    $navigation = cw_core_get_navigation($target, $total_items, $data['page']);
    $limit_str = " LIMIT $navigation[first_page], $navigation[objects_per_page]";
    if ($data['all'])
        $limit_str = '';

    $attributes_classes = cw_query($search_query.$limit_str);

    return array($attributes_classes, $navigation);
}

function cw_attributes_check($class_id, &$attributes, $item_type, $index) {
    global $tables;

    $error = array();
# kornev, we should split the custom and the addon attributes
# customer attributes are applied for some of the objects only, depending on attributes class
# the addon attribute is applied to all of the objects, no matter which class assigned to object. It's true for the products only for now
    if ($item_type == 'P')
        $pa = cw_query_column("select a.attribute_id from $tables[attributes] as a, $tables[attributes_classes_assignement] as acm where acm.attribute_class_id='$class_id' and (acm.attribute_id=a.attribute_id or a.addon != '') and a.item_type='$item_type'");

# kornev, the addon should be enabled
    $required = cw_query("select a.field, a.name, a.type, a.is_required from $tables[attributes] as a left join $tables[addons] as m on m.addon=a.addon where (m.addon is null or m.active = 1) and item_type='$item_type'".($item_type == 'P'?"and attribute_id in ('".implode("', '", $pa)."')":''));

    if ($required)
        foreach($required as $v) {
            if ($v['type'] == 'integer') $attributes[$v['field']] = intval($attributes[$v['field']]);
            elseif ($v['type'] == 'decimal') $attributes[$v['field']] = floatval($attributes[$v['field']]);
            elseif ($v['type'] == 'date') $attributes[$v['field']] = mktime(0, 0, 0, $attributes[$v['field']]['Date_Month'], $attributes[$v['field']]['Date_Day'], $attributes[$v['field']]['Date_Year']);

            if ($v['is_required'] && empty($attributes[$v['field']])) {
                $lng = cw_get_langvar_by_name('err_field_attribute', array('field' => $v['name']), false, true);
                $error[] =  $index++.'. '.$lng;
            }
        }
    return $error;
}

function cw_attributes_group_update($ge_id, $item_id, $item_type, $fields) {
    global $tables;

    $attributes = cw_func_call('cw_attributes_get', array('item_id' => $item_id, 'item_type' => $item_type));
    $att = $att_ids = array();
    if ($attributes)
        foreach($attributes as $attribute)
            if ($fields[$attribute['field']]) {
                $att[] = $attribute;
                $att_ids[] = $attribute['attribute_id'];
            }

    if (count($att))
        while ($id = cw_group_edit_each($ge_id, 1, $item_id)) {
            db_query("delete from $tables[attributes_values] where item_id = '$id' and item_type='$item_type' and attribute_id in ('".implode("', '", $att_ids)."')");

            foreach($att as $v) {
                foreach($v['values'] as $value) {
                    cw_array2insert('attributes_values', array('item_id' => $id, 'item_type' => $item_type, 'attribute_id' => $v['attribute_id'], 'value' => $value));
                }
            }

        }
}

function cw_attributes_check_code($val, $k, $data) {
    global $tables;

    $parsed_val = cw_call('cw_attributes_cleanup_field', array('field' => $val));

    $is_error = empty($val) || cw_query_first_cell($sql="select count(*) from $tables[attributes] where field='$val' and attribute_id != '$data[attribute_id]'") || $parsed_val != $val;
    return $is_error;
}

function cw_attributes_set_default_class($params, $return = null) {
    global $tables;
    extract($params);
    db_query("update $tables[attributes_classes] set is_default=0");
    db_query("update $tables[attributes_classes] set is_default=1 where attribute_class_id='$attribute_class_id'");
}

function cw_attributes_get_class($params, $return = null) {
    global $tables;
    extract($params);

    $class = cw_query_first("select * from $tables[attributes_classes] where attribute_class_id='$attribute_class_id'");
    if ($class)
        $class['attributes'] = cw_query_column("select attribute_id from $tables[attributes_classes_assignement] where attribute_class_id='$attribute_class_id' order by assignment_id asc");
    return $class;
}

function cw_attributes_class_delete($params, $return = null) {
    global $tables;
    extract($params);

    db_query("delete from $tables[attributes_classes_assignement] where attribute_class_id='$attribute_class_id'");
    db_query("delete from $tables[attributes_classes] where attribute_class_id='$attribute_class_id'");
//  db_query("update $tables[products] set attribute_class_id=0 where attribute_class_id='$attribute_class_id'");
    db_query("delete from $tables[items_attribute_classes] where attribute_class_id='$attribute_class_id'");
}

# $params[data]
# $params[attribute_id]
# $params[language]
function cw_attributes_update_lng($params, $return) {
    extract($params);

    global $current_language;

    $language = $language?$language:$current_language;

    $data['code'] = $language;
    $data['attribute_id'] = $attribute_id;
    cw_array2insert('attributes_lng', $data, 1);
}

function cw_attributes_update_default_value($attribute_id, $data, $language=null) {

    global $current_language, $config;

    $language = $language?$language:$current_language;

    $data['attribute_id'] = $attribute_id;
    $data_lng = $data;
    if ($language != $config['default_admin_language']) cw_unset($data, 'value');

    if ($data['attribute_value_id'])
        cw_array2update('attributes_default', $data, "attribute_value_id='$data[attribute_value_id]'");
    else
        $data_lng['attribute_value_id'] = cw_array2insert('attributes_default', $data);

    $data_lng['code'] = $language;
    cw_array2insert('attributes_default_lng', $data_lng, 1);

    return $data_lng['attribute_value_id'];
}

# kornev, params
# $params[attribute_id]
# $params[data]
# $params[language]
function cw_attributes_create_attribute($params, $return) {
    global $current_language, $config, $tables;

    extract($params);

    $language = $language?$language:$current_language;

    $lng_data = $data;
    if ($attribute_id && $language != $config['default_admin_language'])
        cw_unset($data, 'name');

    $data['field'] = cw_call('cw_attributes_cleanup_field', array($data['field']));

    if ($attribute_id) {
        $attribute_id = $data['attribute_id'];
        cw_array2update('attributes', $data, "attribute_id='$attribute_id'");
    }
    else
        $attribute_id = cw_array2insert('attributes', $data, 1);

    cw_func_call('cw_attributes_update_lng', array('attribute_id' => $attribute_id, 'data' => $lng_data, 'language' => $language));

    if (in_array($data['type'], array('selectbox', 'multiple_selectbox'))) {
# kornev, we should remove not updated attributes;
        if ($config['edit_attribute_options_together'] == 'Y') {
            $existing = cw_query_key("select attribute_value_id from $tables[attributes_default] where attribute_id='$attribute_id'");
            if (is_array($data['default_value']))
                foreach($data['default_value'] as $v) {
                    cw_call('cw_attributes_update_default_value', array('attribute_id' => $attribute_id, 'data' => $v, 'language' => $language));
                    if ($v['attribute_value_id']) unset($existing[$v['attribute_value_id']]);
                }
            if (count($existing)) 
                cw_call('cw_attributes_delete_values', array(array_keys($existing)));
        }
    }
    else {
        $counter = cw_query_first_cell("select count(*) from $tables[attributes_default] where attribute_id='$attribute_id'");
        if ($counter == 0) cw_array2insert('attributes_default', array('attribute_id' => $attribute_id));

        $data['attribute_value_id'] = cw_query_first_cell("select attribute_value_id from $tables[attributes_default] where attribute_id='$attribute_id' and is_default=1");
        $data['value'] = $data['default_value'] ? $data['default_value'] : $data['value'];
        $data['facet'] = $data['default_values']['facet'];
        $data['description'] = $data['default_values']['description'];
        $data['is_default'] = 1;
        unset($data['default_values']);
# kornev, the text attribute might be multilng, for the other attributes - set the default lng
        if (in_array($data['type'], array('text', 'textarea')))
            cw_call('cw_attributes_update_default_value', array($attribute_id, $data, $language));
        else
            cw_call('cw_attributes_update_default_value', array($attribute_id, $data, $config['default_admin_language']));
    }

    cw_call('cw_attributes_init');

    return $attribute_id;
}

// TODO: Rename to cw_attributes_get_by_addon
function cw_get_attributes_by_addon($addon) {
    
    return cw_call('cw_attributes_filter', array(array('addon'=>$addon)));

}

/**
 * @param $attribute_value_id
 * @return bool|string
 */
function cw_attributes_get_value_by_attribute_value_id($attribute_value_id) {
    global $tables;

    if (!$attribute_value_id) {
        return false;
    }

    return cw_query_first_cell("
        SELECT value
        FROM $tables[attributes_default]
        WHERE attribute_value_id = '" . $attribute_value_id . "'
    ");
}

/* 
 * get attribute value for item
 */
function cw_attribute_get_value($attribute_id, $item_id, $language=null) {
    global $tables, $current_language, $cw_attributes, $config;

    $language = $language?$language:(!empty($current_language) ? $current_language : $config['default_customer_language']);
    
    $attribute = $cw_attributes['all'][$attribute_id];
    $value = cw_query_first_cell("SELECT value FROM $tables[attributes_values] WHERE item_id='$item_id' AND attribute_id='$attribute_id' AND code IN ('$language','')");
    if (in_array($attribute['type'], array('selectbox','multi-selectbox'))) {
        // replace reference to value ID by real value
        return cw_attributes_get_value_by_attribute_value_id($value);
    }
    return $value;
}

/**
 * @return bool
 */
function cw_attributes_get_default_attribute_class_id() {
    global $tables;

    return cw_query_first_cell("
        SELECT attribute_class_id
        FROM $tables[attributes_classes]
        WHERE is_default = 1
    ");
}
# antonp, params
# $params[item_id]
# $params[attribute_class_ids]
# $params[item_type]
function cw_items_attribute_classes_save($params, $return) {
    global $tables;

    extract($params);

    db_query("delete from $tables[items_attribute_classes] where item_id='$item_id' and item_type='$item_type'");    

    foreach ($attribute_class_ids as $cls_id) {

        cw_array2insert('items_attribute_classes', array('item_id' => $item_id, 'attribute_class_id' => $cls_id, 'item_type' => $item_type));
    }

    return $attribute_class_ids; 
}

# antonp, params
# $params[item_id]
# $params[item_type]
function cw_items_attribute_classes_get($params, $return) {
    global $tables;

    extract($params);

    $attribute_class_ids = cw_query_column("select attribute_class_id from $tables[items_attribute_classes] where item_id = '$item_id' and item_type = '$item_type'");
    if ($item_type == 'P' && !$for_product_modify) { 
 
        $categories_attribute_class_ids = cw_query_column("select $tables[items_attribute_classes].attribute_class_id from $tables[items_attribute_classes], $tables[products_categories] where $tables[products_categories].product_id = '$item_id' and $tables[products_categories].category_id = $tables[items_attribute_classes].item_id");
        if (!empty($categories_attribute_class_ids))
            $attribute_class_ids = array_merge($attribute_class_ids, $categories_attribute_class_ids);
    }

    return $attribute_class_ids;
}

#antonp
# function to sort option values in attributes selector in admin
# params: $a, $b - attributes options compared by 'value' field
function cw_attributes_default_values_selector_sort($a, $b) {
    if ($a['value'] == $b['value']) {
        return 0;
    }
    return ($a['value']  < $b['value']) ? -1 : 1;
}
