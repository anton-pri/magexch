<?php

if (!isset($addons['promotion_suite'])) {
    return;
}
    
if (AREA_TYPE != 'A') {
	return;
}

global $mode, $offer_id, $bonus_id, $cond_id, $action;
global $file_upload_data;

$mode = (string)$mode;
$action = (string)$action;
$offer_id = (int)$offer_id;
$bonus_id = (int)$bonus_id;
$cond_id = (int)$cond_id;


$addon_modes = array(
);

$addon_actions = array(
    'update'		=> 'ps_update',
    'add'			=> 'ps_modify',
    'delete'		=> 'ps_delete',
    'details'		=> 'ps_details',
    'modify'		=> 'ps_modify',
	'form'			=> 'ps_form',
	'delete_image'	=> 'ps_delete_image',
    'coupons'		=> 'ps_get_coupons_ajax',
    'zones'		    => 'ps_get_zones_ajax',
    'attributes'	=> 'ps_get_attr_ajax'
);


global $available_fields, $optional_fields, $skip_striptags_fields, $date_fields;

$available_fields = array(
    'offer_id'		=> 'int',
	'title'			=> 'string',
    'description'	=> 'string',
    'position'		=> 'int',
    'priority'		=> 'int',
    'startdate'		=> 'int',
	'enddate'		=> 'int',
	'exclusive'		=> 'bool',
    'active'		=> 'bool',
    'repeatable'    => 'int',
);
$optional_fields = array('image', 'active', 'position', 'exclusive', 'priority');
$date_fields = array('startdate', 'enddate');
$skip_striptags_fields = array('description');

	
cw_load('image', 'attributes');
cw_image_clear(array(PS_IMG_TYPE));

$location[] = array(cw_get_langvar_by_name('lbl_ps_manage_offers'), '');
    
$smarty->assign('main', 'promosuite');
$smarty->assign('mode', $mode);



if (!function_exists('sort_zone_elements')) {
    function sort_zone_elements ($a, $b) {
        static $sort_order;
    
        $sort_order = array_flip(array("C","S","G","T","Z","A"));
    
        if ($sort_order[$a['element_type']] > $sort_order[$b['element_type']])
            return 1;
        else
            return 0;
    }
}



if (empty($action) || !isset($addon_actions[$action]) || !function_exists($addon_actions[$action])) {
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    	$smarty->assign('action', 'list');
        return cw_call('ps_show'); // default action
    }
    
}

$smarty->assign('action', $action);
return cw_call($addon_actions[$action], array($offer_id));





function ps_show() {
    global $available_fields, $optional_fields, $tables, $smarty, $top_message, $target;

    $offers = array();
    
    $fields = $from_tbls = $query_joins = $where = $groupbys = $having = $orderbys = array();
    
    $from_tbls[] = 'ps_offers';
    $fields = array_keys($available_fields);
    $where[] = 1;
    $orderbys[] = 'position';
    $orderbys[] = 'priority';
    $orderbys[] = 'offer_id';
    
    $search_query_count = cw_db_generate_query('count(offer_id)',  $from_tbls, $query_joins, $where, $groupbys, $having, array(), 0);
    $search_query = cw_db_generate_query($fields, $from_tbls, $query_joins, $where, $groupbys, $having, $orderbys);

    $total_items_res_id = db_query($search_query_count);
    $number_offers = db_num_rows($total_items_res_id);
    
    if (empty($number_offers)) {
        return null;
    }
    
    global $navigation, $page;
    
    $navigation = cw_core_get_navigation($target, $number_offers, $page);
    $limit_str = " LIMIT $navigation[first_page], $navigation[objects_per_page]";
    
    
    $offers = cw_query($search_query . $limit_str);

    if (empty($offers)) {
    	return null;
    }
    
    $offers = array_map(
        create_function('$elm', '$elm["description"] = strip_tags($elm["description"]); return $elm;'), 
        $offers
    );
    
    $smarty->assign('ps_offers', cw_stripslashes($offers));
    
    $navigation['script'] = 'index.php?target='.$target;

    $smarty->assign('navigation', $navigation);
    
}





function ps_update() {
    global $tables, $top_message, $available_fields, $optional_fields, $skip_striptags_fields;

    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    	ps_redirect();
    }
    
    global $ps_offers;

    if (empty($ps_offers) || !is_array($ps_offers)) {
        ps_redirect();
    }


    $offer_ids = array_unique(array_map('ps_process_ids', array_keys($ps_offers)));
    $offer_ids_query = implode('\', \'', $offer_ids);


    $offer_ids = cw_query_column('SELECT `offer_id` FROM ' . $tables['ps_offers'] . ' WHERE ' . 'offer_id IN (\'' . $offer_ids_query . '\')');
    
    if (empty($offer_ids)) {
        ps_redirect();
    }


    if (isset($available_fields['offer_id'])) {
        unset($available_fields['offer_id']);
    }

    $error = null;

    foreach ($offer_ids as $offer_id) {

        $data = array();
        $additional_lang_data = array();

        if (!isset($ps_offers[$offer_id])) {
            continue;
        }

        foreach ($available_fields as $field => $field_type) {
            if (isset($ps_offers[$offer_id][$field])) {

                $result = settype($ps_offers[$offer_id][$field], $field_type);
                if ($result === false) {
                    $error = 'msg_ps_incorrect_field_type';
                    $additional_lang_data = array('field_name' => $field . ' offer ID: ' . $offer_id);
                    break(2);
                }

                if (empty($ps_offers[$offer_id][$field])) {
                    if (in_array($field, $optional_fields)) {
                        $data[$field] = null;
                    }
                } else {
                    if ($field_type == 'string' && !in_array($field, $skip_striptags_fields)) {
                        $ps_offers[$offer_id][$field] = cw_strip_tags($ps_offers[$offer_id][$field]);
                    }
                    $data[$field] = & $ps_offers[$offer_id][$field];
                }
            } else {
                if ($field_type == 'bool') {
                    $data[$field] = 0;
                }
            }
        }


        if (!empty($data)) {
            cw_array2update($tables['ps_offers'], cw_addslashes($data), 'offer_id = \'' . $offer_id . '\'');
        }
    }

    $top_message = array('content' => cw_get_langvar_by_name('msg_ps_updated_succes'), 'type' => 'I');

    if (!empty($error)) {
        $top_message = array('content' => cw_get_langvar_by_name($error, $additional_lang_data), 'type' => 'E');
    }
	
	cw_cache_clean('shipping_rates');

    ps_redirect();
}




function ps_delete() {
    global $tables, $top_message;

	if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    	ps_redirect();
    }
    
	global $offer_ids;
	
    if (empty($offer_ids) || !is_array($offer_ids)) {
        ps_redirect();
    }

    $offer_ids = array_unique(array_map('ps_process_ids', array_keys($offer_ids)));
    $offer_ids_query = implode('\', \'', $offer_ids);


    db_query("DELETE FROM $tables[ps_offers] WHERE offer_id IN ('" . $offer_ids_query . "')");
    db_query("DELETE FROM $tables[ps_bonuses] WHERE offer_id IN ('" . $offer_ids_query . "')");
    db_query("DELETE FROM $tables[ps_bonus_details] WHERE offer_id IN ('" . $offer_ids_query . "')");
    db_query("DELETE FROM $tables[ps_conditions] WHERE offer_id IN ('" . $offer_ids_query . "')");
    db_query("DELETE FROM $tables[ps_cond_details] WHERE offer_id IN ('" . $offer_ids_query . "')");
    
    foreach ($offer_ids as $offer_id) {
        cw_image_delete($offer_id, PS_IMG_TYPE);
    }
    
    cw_attributes_cleanup($offer_ids, PS_ATTR_ITEM_TYPE);
    
    $top_message['content'] = cw_get_langvar_by_name('msg_ps_deleted');
	
	cw_cache_clean('shipping_rates');
	
    ps_redirect();
}




function ps_details($offer_id) {
    global $tables, $top_message, $smarty, $available_fields;
	
    if (empty($offer_id)) {
    	ps_redirect();
    }
    
    $offer_id = (int)$offer_id;
    
	if ($_SERVER['REQUEST_METHOD'] != 'GET') {
        ps_redirect($offer_id);
    }
    
    $offer_data = cw_query_first('SELECT `' . implode('`, `', array_keys($available_fields)) . '` FROM ' . $tables['ps_offers'] . ' WHERE offer_id = \'' . $offer_id . '\'');

    if (empty($offer_data) || !is_array($offer_data)) {
        ps_redirect();
    }
    
    $offer_data['image'] = cw_image_get(PS_IMG_TYPE, $offer_id);

    $smarty->assign('offer_data', cw_stripslashes($offer_data));
    
    
    // bonuses
    $sess_bonuses =& cw_session_register('_ps_bonus');
    $bonuses = ps_get_offer_bonuses($offer_id);
    
    if (!is_array($bonuses) || empty($bonuses)) {
        $bonuses = array();
    }
    
    if (!empty($sess_bonuses)) {
        if (is_array($sess_bonuses)) {
            $bonuses = array_merge($bonuses, $sess_bonuses);
            $smarty->assign('not_sav_bons', $sess_bonuses);
        }
        cw_session_unregister('_ps_bonus');
    }
    
    $smarty->assign('ps_bonus', $bonuses);
    
    $bonus_types = array_flip(array_keys($bonuses));
    
    $bonus_types = array_map(
        create_function('$elm', 'return 1;'), 
        $bonus_types
    );
    
    if (empty($bonus_types)) {
        $bonus_types = array();
    }
    
    $sess_bonus_types =& cw_session_register('_ps_bonuses');
    $sess_cond_types =& cw_session_register('_ps_conditions');
    
    if (!empty($sess_bonus_types)) {
        if (is_array($sess_bonus_types)) {
            $bonus_types = array_merge($bonus_types, $sess_bonus_types);
        }
        cw_session_unregister('_ps_bonuses');
    }
    
    $smarty->assign('ps_bonuses', $bonus_types);
    
    
    // conditions
    $sess_conditions =& cw_session_register('_ps_conds');
    $conditions = ps_get_offer_conditions($offer_id);
    
    if (!is_array($conditions) || empty($conditions)) {
        $conditions = array();
    }
    
    if (!empty($sess_conditions)) {
        if (is_array($sess_conditions)) {
            $conditions = array_merge($conditions, $sess_conditions);
            $smarty->assign('not_sav_conds', $sess_conditions);
        }
        cw_session_unregister('_ps_conds');
    }
    
    $smarty->assign('ps_conds', $conditions);

    $cond_types = array_flip(array_keys($conditions));
    
    $cond_types = array_map(
        create_function('$elm', 'return 1;'), 
        $cond_types
    );
    
    if (empty($cond_types)) {
        $cond_types = array();
    }
    
    if (!empty($sess_cond_types)) {
        if (is_array($sess_cond_types)) {
            $cond_types = array_merge($cond_types, $sess_cond_types);
        }
        cw_session_unregister('_ps_conditions');
    }
    
    $smarty->assign('ps_conditions', $cond_types);
    
	cw_load('attributes');
    $ps_attr = cw_call('cw_attributes_filter', array(array('addon'=>'','item_type' => 'P')));
    
    $smarty->assign('ps_attr', $ps_attr);
    $smarty->assign('ps_coupons', ps_get_coupons());
    $smarty->assign('ps_mans', ps_get_manufacturers());
    $smarty->assign('ps_zones', ps_get_zones());
    $smarty->assign('ps_memberships', cw_get_memberships());
    
    global $js_tab;
    if (isset($js_tab)) {
        $smarty->assign('js_tab', (string)$js_tab);
    }
    
    global $app_catalogs, $target, $mode;
    $smarty->assign('ps_url_get_coupons', "$app_catalogs[admin]/index.php?target=$target&mode=$mode&action=coupons&offer_id=$offer_id&js_tab=ps_offer_bonuses");
    $smarty->assign('ps_url_add_coupon', "$app_catalogs[admin]/index.php?target=coupons");
    
    $smarty->assign('ps_url_get_zones', "$app_catalogs[admin]/index.php?target=$target&mode=$mode&action=zones&offer_id=$offer_id&js_tab=ps_offer_conditions");
    $smarty->assign('ps_url_add_zone', "$app_catalogs[admin]/index.php?target=shipping_zones&mode=add");
    
    
    //attributes
    global $attributes;
    global $edited_language;
    $attributes = cw_func_call('cw_attributes_get', array('item_id' => $offer_id, 'item_type' => PS_ATTR_ITEM_TYPE, 'prefilled' => $attributes));
    $smarty->assign('attributes', $attributes); 
}




function ps_modify($offer_id) {
    global $tables, $top_message, $js_tab;
    
    $offer_id = (int)$offer_id;
    
    $default_tab = 'ps_offer_details';
    $_action = 'details';
    
    if (empty($offer_id)) {
        $_action = 'form';
    }
    
    if (empty($js_tab)) {
        $js_tab = $default_tab;
    }
    
	if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    	ps_redirect($offer_id, $_action, $js_tab);
    }
    
    if (!empty($offer_id)) {
        $offer_id = cw_query_first_cell('SELECT offer_id FROM ' . $tables['ps_offers'] . ' WHERE offer_id = \'' . $offer_id . '\'');
    
        if (empty($offer_id)) {
            ps_redirect();
        }
    }
    
    
    $entities = array('details', 'bonuses', 'conditions');
    $cw_prefix = __FUNCTION__ . '_';
    
    $errors = array();
    
    foreach ($entities as $entity) {
        $cw_tocall = $cw_prefix . $entity; 
        if (function_exists($cw_tocall)) {
            
            $errors[$entity] = cw_call($cw_tocall, array($offer_id));
            
            if ($entity == 'details' && is_numeric($errors[$entity][0]) && empty($offer_id)) {
                $offer_id = $errors[$entity][0];
                $_action = 'details';
            }
        } else {
            $errors[$entity] = array(false, 'A function "' . $cw_tocall . '" related to "' . $entity . '" is not defeined');
        }
    }
    
    $error = null;
    
    foreach ($errors as $entity => $error_data) {
        if ($error_data[0] != false) {
            continue;
        }
        
        if (!empty($error)) {
            $error .= "<br />\n";
        }
        $error .= $error_data[1];
    }
    

    if (!empty($error)) {
        $top_message = array('content' => $error, 'type' => 'E');
        ps_redirect($offer_id, $_action, $js_tab);
    }
    
    $top_message = array('content' => cw_get_langvar_by_name('msg_ps_updated_succes'), 'type' => 'I');

	cw_cache_clean('shipping_rates');
    
    ps_redirect($offer_id, $_action, $js_tab);
}





function ps_form() {
	global $smarty;
	
	if ($_SERVER['REQUEST_METHOD'] != 'GET') {
		ps_redirect(0, 'form', 'details');
	}
	
	$_offer_data =& cw_session_register('_offer_data');
    if (!empty($_offer_data)) {
        $smarty->assign('offer_data', $_offer_data);
        cw_session_unregister('_offer_data');
    }
    
    $_ps_bonuses =& cw_session_register('_ps_bonuses');
    if (!empty($_ps_bonuses)) {
        $smarty->assign('ps_bonuses', $_ps_bonuses);
        cw_session_unregister('_ps_bonuses');
    }
    
    $_ps_bonus =& cw_session_register('_ps_bonus');
    if (!empty($_ps_bonus)) {
        $smarty->assign('ps_bonus', $_ps_bonus);
        cw_session_unregister('_ps_bonus');
    }
    
    
    $_ps_conditions =& cw_session_register('_ps_conditions');
    if (!empty($_ps_conditions)) {
        $smarty->assign('ps_conditions', $_ps_conditions);
        cw_session_unregister('_ps_conditions');
    }
    
    $_ps_conds =& cw_session_register('_ps_conds');
    if (!empty($_ps_conds)) {
        $smarty->assign('ps_conds', $_ps_conds);
        cw_session_unregister('_ps_conds');
    }
    
    $smarty->assign('ps_coupons', ps_get_coupons());
    $smarty->assign('ps_mans', ps_get_manufacturers());
    $smarty->assign('ps_zones', ps_get_zones());
    $smarty->assign('ps_memberships', cw_get_memberships());

    
    global $js_tab;
    if (isset($js_tab)) {
        $smarty->assign('ps_offer_js_tab', (string)$js_tab);
    }
    
    global $app_catalogs, $target, $mode;
    $smarty->assign('ps_url_get_coupons', "$app_catalogs[admin]/index.php?target=$target&mode=$mode&action=coupons&offer_id=&js_tab=ps_offer_bonuses");
    $smarty->assign('ps_url_add_coupon', "$app_catalogs[admin]/index.php?target=coupons");
    
    $smarty->assign('ps_url_get_zones', "$app_catalogs[admin]/index.php?target=$target&mode=$mode&action=zones&offer_id=&js_tab=ps_offer_conditions");
    $smarty->assign('ps_url_add_zone', "$app_catalogs[admin]/index.php?target=shipping_zones&mode=add");
    
	global $file_upload_data;
	if (!empty($file_upload_data) && isset($file_upload_data[PS_IMG_TYPE]) && is_array($file_upload_data[PS_IMG_TYPE])) {
   		$file_upload_data[PS_IMG_TYPE]['is_redirect'] = false;
    }
    
    //attributes
    global $attributes;
    global $edited_language;
    $attributes = cw_func_call('cw_attributes_get', array('item_id' => 0, 'item_type' => PS_ATTR_ITEM_TYPE, 'prefilled' => $attributes));
    $smarty->assign('attributes', $attributes);
	
}



function ps_delete_image($offer_id) {
	global $top_message;

    $_action = 'details';
    $_js_tab = 'ps_offer_details';
    
	$offer_id = (int)$offer_id;
    
    if (empty($offer_id)) {
    	ps_redirect();
    }
    
	if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    	ps_redirect($offer_id, $_action, $_js_tab);
    }
    
    cw_image_delete($offer_id, PS_IMG_TYPE);
    $top_message = array('content' => cw_get_langvar_by_name('msg_ps_updated_succes'), 'type' => 'I');

    ps_redirect($offer_id, $_action, $_js_tab);
}



function ps_process_ids($id) {
    return (int) $id;
}




function ps_redirect($offer_id = null, $action = null, $js_tab = null) {
    global $app_catalogs, $target, $mode;
    
	$offer_condition = null;
    if (!is_null($offer_id) && $offer_id === 0) {
	    $offer_condition = '&offer_id=';
    }
    if (!empty($offer_id)) {
    	$offer_condition = '&offer_id=' . (int)$offer_id;
    }
    
    $action_condition = null;
    if (!empty($action)) {
    	$action_condition = '&action=' . (string)$action;
    }
    
	$js_tab_condition = null;
    if (!empty($js_tab)) {
    	$js_tab_condition = '&js_tab=' . (string)$js_tab;
    }

    cw_header_location("$app_catalogs[admin]/index.php?target=$target&mode=$mode$action_condition$offer_condition$js_tab_condition");
}





function ps_modify_details($offer_id) {
    global $tables, $available_fields, $optional_fields, $skip_striptags_fields, $date_fields;
    
    /*if (empty($offer_id)) {
        return array(false, 'Offer Id was not provided');
    }*/
    
    global $offer_data;

    if (empty($offer_data) || !is_array($offer_data)) {
        return array(true, null);
    }
    
    $error = null;
    $data = array();

    $excl_from_base_list = array('offer_id');
    foreach ($excl_from_base_list as $field) {
	    if (isset($available_fields[$field])) {
	        unset($available_fields[$field]);
	    }
    }

    $additional_lang_data = array();
    
	foreach ($date_fields as $field) {
    	if (isset($offer_data[$field]) && !empty($offer_data[$field])) {
    		$offer_data[$field] = cw_core_strtotime($offer_data[$field]);
    	}
    }

    foreach ($available_fields as $field => $field_type) {
        if (isset($offer_data[$field])) {

            $result = settype($offer_data[$field], $field_type);
            if ($result === false) {
                $error = 'msg_ps_incorrect_field_type';
                $additional_lang_data = array('field_name' => $field);
                break;
            }
            
            if ($field == 'description') {
                if ($offer_data[$field] == '<p>&#160;</p>') {
                    $offer_data[$field] = null;
                }
            }

            if (empty($offer_data[$field])) {
                if (in_array($field, $optional_fields)) {
                    $data[$field] = null;
                } else {
                    $error = 'msg_ps_empty_fields';
                    break;
                }
            } else {
                
                if ($field_type == 'string' && !in_array($field, $skip_striptags_fields)) {
                    $offer_data[$field] = cw_strip_tags($offer_data[$field]);
                }
                $data[$field] = & $offer_data[$field];
            }
        } else {
            if ($field_type == 'bool') {
                $data[$field] = 0;
            } else {
                if (in_array($field, $optional_fields)) {
                    $data[$field] = null;
                } else {
                    $error = 'msg_ps_empty_fields';
                    break;
                }
            }
        }
    }
    
    $sess_offer_data = &cw_session_register('_offer_data');
    
    $GLOBALS['_offer_data'] = & $offer_data;
    cw_session_register('_offer_data');
    

    if (!empty($error)) {
        return array(false, cw_get_langvar_by_name($error, $additional_lang_data));
    }
    
    global $attributes;
    $data['attributes'] = $attributes;
    
    $error = cw_error_check($data, array(), PS_ATTR_ITEM_TYPE);
    //cw_attributes_check($array_to_check['attribute_class_id'], $array_to_check['attributes'], $attributes_type, $index)
    if (!empty($error)) {
        return array(false, $error);
    }
    
    global $file_upload_data;
    
    if (empty($offer_id)) {
        
        if (empty($data)) {
            return array(false, null);
        }
        
        $offer_id = cw_array2insert($tables['ps_offers'], cw_addslashes($data));
        
        $is_image = false;
        
        if (!empty($file_upload_data) && isset($file_upload_data[PS_IMG_TYPE]) && is_array($file_upload_data[PS_IMG_TYPE])) {
        	$is_image = true;
        	if (isset($sess_offer_data['image']) && !empty($sess_offer_data['image'])) {
        		$file_upload_data[PS_IMG_TYPE]['is_redirect'] = false;
        	}
        	$GLOBALS['_offer_data']['image'] = $file_upload_data[PS_IMG_TYPE];
        	$file_upload_data[PS_IMG_TYPE]['is_redirect'] = false;
        }
    

        if (!empty($offer_id)) {
			
        	if ($is_image == true) {
	    		$image_posted = cw_image_check_posted($file_upload_data[PS_IMG_TYPE]);
		    	if ($image_posted) {
					$image_id = cw_image_save($file_upload_data[PS_IMG_TYPE], array('alt' => $data['title'], 'id' => $offer_id));
	        	}
			}
        }
        
    } else {
        
    	$image_id = 0;
    	
    	if (!empty($file_upload_data) && isset($file_upload_data[PS_IMG_TYPE]) && is_array($file_upload_data[PS_IMG_TYPE])) {
        	$image_posted = cw_image_check_posted($file_upload_data[PS_IMG_TYPE]);
    	   	if ($image_posted) {
    			$image_id = cw_image_save($file_upload_data[PS_IMG_TYPE], array('alt' => $data['title'], 'id' => $offer_id));
           	}
    	}
        
        if (empty($data) && empty($image_id)) {
            $error = 'msg_ps_nothing_to_update';
            return array(false, cw_get_langvar_by_name($error, $additional_lang_data));
        }
        
        cw_array2update($tables['ps_offers'], cw_addslashes($data), 'offer_id = \'' . $offer_id . '\'');
    }
    
    cw_call('cw_attributes_save', array('item_id' => $offer_id, 'item_type' => PS_ATTR_ITEM_TYPE, 'attributes' => $attributes));
    
    cw_session_unregister('_offer_data');
    
    return array($offer_id, null);
    
}



function ps_modify_bonuses($offer_id) {
    global $tables, $bonus_names;
    global $ps_bonuses, $ps_bonus;
    
    if (empty($offer_id)) {
        $GLOBALS['_ps_bonuses'] = & $ps_bonuses;
        cw_session_register('_ps_bonuses');
        
        $GLOBALS['_ps_bonus'] = & $ps_bonus;
        cw_session_register('_ps_bonus');
        
        return array(true, null);
        //return array(false, 'Offer Id was not provided');
    }
    
    db_query("DELETE FROM $tables[ps_bonuses] WHERE offer_id = '$offer_id'");
    db_query("DELETE FROM $tables[ps_bonus_details] WHERE offer_id = '$offer_id'");
    
    
    $available_fields = array(
        'bonus_id'		=> 'int',
    	'offer_id'		=> 'int',
    	'type'			=> 'string',
        'apply'		    => 'int',
        'coupon'		=> 'string',
    	'discount'		=> 'float',
        'disctype'		=> 'int'
    );
    
    $excl_from_base_list = array('bonus_id');
    foreach ($excl_from_base_list as $field) {
	    if (isset($available_fields[$field])) {
	        unset($available_fields[$field]);
	    }
    }
    
    $optional_fields = array('discount', 'disctype');
    $date_fields = array();
    $skip_striptags_fields = array();
    
    if (empty($ps_bonuses) || !is_array($ps_bonuses)) {
        return array(true, null);
    }
    
    $available_btypes = array(PS_DISCOUNT, PS_FREE_PRODS, PS_FREE_SHIP, PS_COUPON);
    
    $bonuses = array();
    
    foreach ($ps_bonuses as $bonus_type => $trash) {
        if (!isset($ps_bonus[$bonus_type]) || empty($ps_bonus[$bonus_type]) || !in_array($bonus_type, $available_btypes)) {
            unset($ps_bonuses[$bonus_type]);
        } else {
            $bonuses[$bonus_type] = $ps_bonus[$bonus_type];
        }
    }
    
    unset($ps_bonus);
    
    if (empty($ps_bonuses) || empty($bonuses)) {
        return array(true, null);
    }
    
    $GLOBALS['_ps_bonuses'] = & $ps_bonuses;
    cw_session_register('_ps_bonuses');
    
    
    $errors = array();
    
    $tmp_optional_fields = $optional_fields;
    
    foreach ($bonuses as $bonus_type => $input_data) {
        
        $optional_fields = $tmp_optional_fields;
        
        $additional_lang_data = array();
        $pids = $cids = array();
        
        $input_data['offer_id'] = $offer_id;
        $input_data['type'] = $bonus_type;
        
        
        if ($bonus_type != PS_COUPON) {
            $input_data['coupon'] = 1;
            
            if ($input_data['apply'] == PS_APPLY_PRODS || $bonus_type == PS_FREE_PRODS) {
                
                if ((!isset($input_data['products']) && !isset($input_data['cats'])) 
                    || (empty($input_data['products']) && empty($input_data['cats']))) {
                    $additional_lang_data = array('bonus' => cw_get_langvar_by_name($bonus_names[$bonus_type]));
                    $errors[] = cw_get_langvar_by_name('msg_ps_bonus_incorrect', $additional_lang_data);
                    continue;
                }
                
                if (isset($input_data['products']) && !empty($input_data['products'])) {
                    
                    $products_data = array();
                    
                    foreach ($input_data['products'] as $product_data) {
                        $product_data['id'] = trim($product_data['id']);
                        $products_data[$product_data['id']] = $product_data['quantity'];
                    }
                    
                    $pids = array_keys($products_data);
                    $pids = cw_query_column("SELECT product_id as id FROM $tables[products] WHERE product_id IN ('" . implode("','", $pids) . "')");
                    
                }
                
                
                if (isset($input_data['cats']) && !empty($input_data['cats'])) {
                    
                    $cats_data = array();
                    
                    foreach ($input_data['cats'] as $cat_data) {
                        $cat_data['id'] = trim($cat_data['id']);
                        $cats_data[$cat_data['id']] = $cat_data['quantity'];
                    }
                    
                    $cids = array_keys($cats_data);
                    $cids = cw_query_column("SELECT category_id as id FROM $tables[categories] WHERE category_id IN ('" . implode("','", $cids) . "')");
                    
                }
                
                if (empty($pids) && empty($cids)) {
                    $additional_lang_data = array('bonus' => cw_get_langvar_by_name($bonus_names[$bonus_type]));
                    $errors[] = cw_get_langvar_by_name('msg_ps_bonus_incorrect', $additional_lang_data);
                    continue;
                }
                
                
            }
        }
        
        if ($bonus_type != PS_DISCOUNT && $bonus_type != PS_FREE_SHIP) {
            $input_data['discount'] = $input_data['disctype'] = null;
        } elseif ($bonus_type == PS_FREE_SHIP) {
			$input_data['disctype'] = null;
		} else {
            $optional_fields = array();
        }
        
        
        if (in_array($bonus_type, array(PS_FREE_PRODS, PS_COUPON))) {
            $optional_fields[] = 'apply';
        }
        
        
        $data = array();

    	foreach ($date_fields as $field) {
        	if (isset($input_data[$field]) && !empty($input_data[$field])) {
        		$input_data[$field] = cw_core_strtotime($input_data[$field]);
        	}
        }
    
        $error = null;
        
        foreach ($available_fields as $field => $field_type) {
            if (isset($input_data[$field])) {
    
                $result = settype($input_data[$field], $field_type);
                if ($result === false) {
                    $error = 'msg_ps_incorrect_field_type';
                    $additional_lang_data = array('field_name' => $field);
                    break;
                }
    
                if (empty($input_data[$field])) {
                    if (in_array($field, $optional_fields)) {
                        $data[$field] = null;
                    } else {
                        $additional_lang_data = array('bonus' => cw_get_langvar_by_name($bonus_names[$bonus_type]));
                        $error = 'msg_ps_bonus_incorrect';
                        break;
                    }
                } else {
                    if ($field_type == 'string' && !in_array($field, $skip_striptags_fields)) {
                        $input_data[$field] = cw_strip_tags($input_data[$field]);
                    }
                    $data[$field] = & $input_data[$field];
                }
            } else {
                if ($field_type == 'bool') {
                    $data[$field] = 0;
                } else {
                    
                    if (in_array($field, $optional_fields)) {
                        $data[$field] = null;
                    } else {
                        $additional_lang_data = array('bonus' => cw_get_langvar_by_name($bonus_names[$bonus_type]));
                        $error = 'msg_ps_bonus_incorrect';
                        break;
                    }
                    
                }
            }
        }
        
    
        if (!empty($error)) {
            $errors[] = cw_get_langvar_by_name($error, $additional_lang_data);
            continue;
        }
        
            
    	if (empty($data)) {
    	    continue;
        }
        
        $bonus_id = cw_array2insert($tables['ps_bonuses'], cw_addslashes($data));
        
        if ($bonus_type == PS_FREE_SHIP) {
            foreach ($input_data['methods'] as $trash=>$shipping_id) {
                    $data = array(
                        'offer_id' => $offer_id,
                        'bonus_id' => $bonus_id,
                        'object_id' => $shipping_id,
                        'object_type' => PS_OBJ_TYPE_SHIPPING
                    );
                    cw_array2insert($tables['ps_bonus_details'], cw_addslashes($data));
            }
        }
        
        if ($bonus_type != PS_COUPON) {
            
            if ($input_data['apply'] == PS_APPLY_PRODS || $bonus_type == PS_FREE_PRODS) {
                
                if (!empty($pids)) {
                    
                    $data = array();
                    
                    $data['offer_id'] = $offer_id;
                    $data['bonus_id'] = $bonus_id;
                    
                    foreach ($pids as $pid) {
                        $data['object_id'] = $pid;
                        $data['object_type'] = PS_OBJ_TYPE_PRODS;
                        $data['quantity'] = $products_data[$pid];
                        if (empty($data['quantity'])) {
                            $data['quantity'] = 1;
                        }
                        cw_array2insert($tables['ps_bonus_details'], cw_addslashes($data));
                    }
                    
                }
                
                if (!empty($cids)) {
                    
                    $data = array();
                    
                    $data['offer_id'] = $offer_id;
                    $data['bonus_id'] = $bonus_id;
                    
                    foreach ($cids as $cid) {
                        $data['object_id'] = $cid;
                        $data['object_type'] = PS_OBJ_TYPE_CATS;
                        $data['quantity'] = $cats_data[$cid];
                        if (empty($data['quantity'])) {
                            $data['quantity'] = 1;
                        }
                        cw_array2insert($tables['ps_bonus_details'], cw_addslashes($data));
                    }
                    
                }
                
            }
        }
        
        unset($bonuses[$bonus_type]);
        
    }
    
    if (!empty($bonuses)) {
        $GLOBALS['_ps_bonus'] = & $bonuses;
        cw_session_register('_ps_bonus');
    }
    
    if (!empty($errors)) {
        $error = implode("<br />\n", $errors);
        return array(false, $error);
    }
    
    return array(true, null);
    
}




function ps_get_offer_bonuses($offer_id = null) {
    global $tables;
    
    $result = array();
    
    $offer_id = (int)$offer_id;
    
    if (empty($offer_id)) {
        return array();
    }
    
    $bonuses = cw_query("SELECT * FROM $tables[ps_bonuses] WHERE offer_id = '$offer_id'");
    $bonus_details = cw_query("SELECT * FROM $tables[ps_bonus_details] WHERE offer_id = '$offer_id'");
    
    $_bonus_details = array();
    
    if (!empty($bonus_details) && is_array($bonus_details)) {
        
        $products = array();
        $cats = array();
        
        foreach ($bonus_details as $details) {
            
            switch ($details['object_type']) {
                case PS_OBJ_TYPE_PRODS:
                    $products[$details['object_id']] = null;
                    break;
                
                case PS_OBJ_TYPE_CATS:
                    $cats[$details['object_id']] = null;
                    break;
                    
                default:
                break;
            }
            
        }
        
        
        if (!empty($products)) {
            $products = cw_query_hash("SELECT product_id, product FROM $tables[products] WHERE product_id IN ('" . implode("', '", array_keys($products)) . "')", 'product_id', false, true);
        }
        
        if (!empty($cats)) {
            $cats = cw_query_hash("SELECT category_id, category FROM $tables[categories] WHERE category_id IN ('" . implode("', '", array_keys($cats)) . "')", 'category_id', false, true);
        }
        
        
        foreach ($bonus_details as $details) {
            
            if (!isset($_bonus_details[$details['bonus_id']]['products'])) {
                $_bonus_details[$details['bonus_id']]['products'] = array();
            }
            
            if (!isset($_bonus_details[$details['bonus_id']]['cats'])) {
                $_bonus_details[$details['bonus_id']]['cats'] = array();
            }
            
            switch ($details['object_type']) {
                case PS_OBJ_TYPE_PRODS:
                    $_bonus_details[$details['bonus_id']]['products'][] = array('id' => $details['object_id'], 'name' => $products[$details['object_id']], 'quantity' => $details['quantity']);
                    break;
                
                case PS_OBJ_TYPE_CATS:
                    $_bonus_details[$details['bonus_id']]['cats'][] = array('id' => $details['object_id'], 'name' => $cats[$details['object_id']], 'quantity' => $details['quantity']);
                    break;
                case PS_OBJ_TYPE_SHIPPING:
                    $_bonus_details[$details['bonus_id']]['methods'][$details['object_id']] = $details['object_id'];
                    break;
                    
                default:
                break;
            }
            
        }
        
    }
    
    $bonus_details = $_bonus_details;
    unset($_bonus_details);
    
    
    if (empty($bonuses) || !is_array($bonuses)) {
        return array();
    }
    
    foreach ($bonuses as $bonus) {
        $result[$bonus['type']] = $bonus;
        if ($bonus['type'] == PS_FREE_SHIP) {
            $result[$bonus['type']]['methods'] = $bonus_details[$bonus['bonus_id']]['methods'];
        }
        if ($bonus['type'] != PS_COUPON && ($bonus['apply'] == PS_APPLY_PRODS || $bonus['type'] == PS_FREE_PRODS)) {
            $result[$bonus['type']]['products'] = $bonus_details[$bonus['bonus_id']]['products'];
            $result[$bonus['type']]['cats'] = $bonus_details[$bonus['bonus_id']]['cats'];
        }
    }

    return $result;
    
}




function ps_get_coupons() {
    global $tables, $addons;
    
    if (!isset($addons['discount_coupons']) || empty($addons['discount_coupons'])) {
        return array();
    }
    
    $coupons = cw_query("SELECT * FROM $tables[discount_coupons] WHERE status = 1");
    
    if (empty($coupons) || !is_array($coupons)) {
        return array();
    }
    
    return $coupons;
}



function ps_get_coupons_ajax() {
    global $smarty, $ps_type;
    
    $smarty->assign('ps_coupons', ps_get_coupons());
    $smarty->assign('ps_type', $ps_type);
    //$smarty->fetch('addons/promotion_suite/admin/coupons.tpl', null, null, true);
    cw_display('addons/promotion_suite/admin/coupons.tpl', $smarty);
    exit(0);
}



function ps_get_manufacturers() {
    global $tables, $addons;
    
    if (!isset($addons['manufacturers']) || empty($addons['manufacturers'])) {
        return array();
    }
    
    $manufacturers = cw_query("SELECT * FROM $tables[manufacturers]");
    
    if (empty($manufacturers) || !is_array($manufacturers)) {
        return array();
    }
    
    return $manufacturers;
}




function ps_modify_conditions($offer_id) {
    global $tables, $cond_names;
    global $ps_conditions, $ps_conds;

    if (empty($offer_id)) {
        
        $GLOBALS['_ps_conditions'] = & $ps_conditions;
        cw_session_register('_ps_conditions');
        
        $GLOBALS['_ps_conds'] = & $ps_conds;
        cw_session_register('_ps_conds');
        
        return array(true, null);
        //return array(false, 'Offer Id was not provided');
    }

    db_query("DELETE FROM $tables[ps_conditions] WHERE offer_id = '$offer_id'");
    db_query("DELETE FROM $tables[ps_cond_details] WHERE offer_id = '$offer_id'");
    
    $available_fields = array(
        'cond_id'		=> 'int',
    	'offer_id'		=> 'int',
    	'type'			=> 'string',
    	'coupon'		=> 'string'
    );
    
    $excl_from_base_list = array('cond_id');
    foreach ($excl_from_base_list as $field) {
	    if (isset($available_fields[$field])) {
	        unset($available_fields[$field]);
	    }
    }
    
    $optional_fields = array('coupon');
    $date_fields = array();
    $skip_striptags_fields = array();
 
     
    
    if (empty($ps_conditions) || !is_array($ps_conditions)) {
        return array(true, null);
    }
    
    $available_ctypes = array(
        PS_TOTAL,
        PS_SHIP_ADDRESS,
        PS_SPEC_PRODUCTS,
        PS_WEIGHT,
        PS_MEMBERSHIP,
        PS_USE_COUPON,
        PS_COOKIE,
    );
    
    $conditions = array();
    
    foreach ($ps_conditions as $cond_type => $trash) {
        if (
            !isset($ps_conds[$cond_type])
            || empty($ps_conds[$cond_type])
            || !in_array($cond_type, $available_ctypes)
        ) {
            unset($ps_conditions[$cond_type]);
        }
        else {
            $conditions[$cond_type] = $ps_conds[$cond_type];
        }
    }
    
    unset($ps_conds);
    
    if (empty($ps_conditions) || empty($conditions)) {
        return array(true, null);
    }
    
    $GLOBALS['_ps_conditions'] = & $ps_conditions;
    cw_session_register('_ps_conditions');
    
    $errors = array();
    
    $tmp_optional_fields = $optional_fields;

    foreach ($conditions as $cond_type => $input_data) {

        $optional_fields = $tmp_optional_fields;
        
        $additional_lang_data = array();
        $pids = $cids = $mids = array();
        
        $input_data['offer_id'] = $offer_id;
        $input_data['type'] = $cond_type;
        
        if ($cond_type == PS_SPEC_PRODUCTS) {
            
            if ((!isset($input_data['products']) && !isset($input_data['cats']) && !isset($input_data['mans']) && !isset($input_data['attr'])) 
                || (empty($input_data['products']) && empty($input_data['cats']) && empty($input_data['mans']) && empty($input_data['attr']))) {
                $additional_lang_data = array('cond' => cw_get_langvar_by_name($cond_names[$cond_type]));
                $errors[] = cw_get_langvar_by_name('msg_ps_cond_incorrect', $additional_lang_data);
                continue;
            }
            
            // Prepare products data
            if (isset($input_data['products']) && !empty($input_data['products'])) {
                
                $products_data = array();
                
                foreach ($input_data['products'] as $product_data) {
                    $product_data['id'] = trim($product_data['id']);
                    $products_data[$product_data['id']] = $product_data['quantity'];
                }
                
                $pids = array_keys($products_data);
                $pids = cw_query_column("SELECT product_id as id FROM $tables[products] WHERE product_id IN ('" . implode("','", $pids) . "')");
                
            }
            
            // Prepare categories data
            if (isset($input_data['cats']) && !empty($input_data['cats'])) {
                
                $cats_data = array();
                
                foreach ($input_data['cats'] as $cat_data) {
                    $cat_data['id'] = trim($cat_data['id']);
                    $cats_data[$cat_data['id']] = $cat_data['quantity'];
                }
                
                $cids = array_keys($cats_data);
                $cids = cw_query_column("SELECT category_id as id FROM $tables[categories] WHERE category_id IN ('" . implode("','", $cids) . "')");
                
            }
            
            // Prepare attributes data
            if (isset($input_data['attr']) && !empty($input_data['attr'])) {
                
                $attr_data = array();
                
                foreach ($input_data['attr'] as $a_data) {
                    $attr_data[trim($a_data['attribute_id'])] = array(
						'quantity'=> $a_data['quantity'],
						'value' => current($a_data['value']),
						'operation' => $a_data['operation']
						);
                }
                
                $attrids = array_keys($attr_data);
                $attrids = cw_query_column("SELECT attribute_id as id FROM $tables[attributes] WHERE attribute_id IN ('" . implode("','", $attrids) . "')");
                
            }
            // Prepare manufacturers data            
            if (isset($input_data['mans']) && !empty($input_data['mans'])) {
                
                $mans_data = array();
                
                foreach ($input_data['mans'] as $man_data) {
                    $man_data['id'] = trim($man_data['id']);
                    $mans_data[$man_data['id']] = $man_data['quantity'];
                }
                
                $mids = array_keys($mans_data);
                $mids = cw_query_column("SELECT manufacturer_id as id FROM $tables[manufacturers] WHERE manufacturer_id IN ('" . implode("','", $mids) . "')");
                
            }
            
            
            if (empty($pids) && empty($cids) && empty($mids) && empty($attrids)) {
                $additional_lang_data = array('cond' => cw_get_langvar_by_name($cond_names[$cond_type]));
                $errors[] = cw_get_langvar_by_name('msg_ps_cond_incorrect', $additional_lang_data);
                continue;
            }
                
        
        }
        elseif ($cond_type == PS_SHIP_ADDRESS) {
            
            if (!isset($input_data['zones']) || empty($input_data['zones'])) {
                $additional_lang_data = array('cond' => cw_get_langvar_by_name($cond_names[$cond_type]));
                $errors[] = cw_get_langvar_by_name('msg_ps_cond_incorrect', $additional_lang_data);
            }
            
            
            $zone_ids = array_map(
                create_function('$id', 'return trim($id);'), 
                $input_data['zones']
            );
            
            $zone_ids = cw_query_column("SELECT zone_id as id FROM $tables[zones] WHERE zone_id IN ('" . implode("','", $zone_ids) . "')");
            
            if (empty($zone_ids) || !is_array($zone_ids)) {
                $additional_lang_data = array('cond' => cw_get_langvar_by_name($cond_names[$cond_type]));
                $errors[] = cw_get_langvar_by_name('msg_ps_cond_incorrect', $additional_lang_data);
            }
            
        }
        elseif ($cond_type == PS_TOTAL || $cond_type == PS_WEIGHT) {

            if (
                !isset($input_data['from'])
                || intval($input_data['from']) < 0
                || !isset($input_data['till'])
                || intval($input_data['till']) < 0
                || (intval($input_data['till']) < intval($input_data['from'])
                    && intval($input_data['till']) != 0
                    && intval($input_data['from']) != 0
                )
            ) {
                $additional_lang_data = array('cond' => cw_get_langvar_by_name($cond_names[$cond_type]));
                $errors[] = cw_get_langvar_by_name('msg_ps_cond_incorrect', $additional_lang_data);
            }
        }

        $data = array();

    	foreach ($date_fields as $field) {
        	if (isset($input_data[$field]) && !empty($input_data[$field])) {
        		$input_data[$field] = cw_core_strtotime($input_data[$field]);
        	}
        }
    
        $error = null;

        foreach ($available_fields as $field => $field_type) {
            if (isset($input_data[$field])) {
    
                $result = settype($input_data[$field], $field_type);
                if ($result === false) {
                    $error = 'msg_ps_incorrect_field_type';
                    $additional_lang_data = array('field_name' => $field);
                    break;
                }
    
                if (empty($input_data[$field])) {
                    if (in_array($field, $optional_fields)) {
                        $data[$field] = null;
                    } else {
                        $additional_lang_data = array('cond' => cw_get_langvar_by_name($cond_names[$cond_type]));
                        $error = 'msg_ps_cond_incorrect';
                        break;
                    }
                } else {
                    if ($field_type == 'string' && !in_array($field, $skip_striptags_fields)) {
                        $input_data[$field] = cw_strip_tags($input_data[$field]);
                    }
                    $data[$field] = & $input_data[$field];
                }
            } else {
                if ($field_type == 'bool') {
                    $data[$field] = 0;
                } else {
                    
                    if (in_array($field, $optional_fields)) {
                        $data[$field] = null;
                    } else {
                        $additional_lang_data = array('cond' => cw_get_langvar_by_name($cond_names[$cond_type]));
                        $error = 'msg_ps_cond_incorrect';
                        break;
                    }
                    
                }
            }
        }


        if (!empty($error)) {
            $errors[] = cw_get_langvar_by_name($error, $additional_lang_data);
            continue;
        }


    	if (empty($data)) {
    	    continue;
        }

        if ($cond_type == PS_WEIGHT || $cond_type == PS_TOTAL) {
            $data['total'] = intval($input_data['from']);
            $cond_id = cw_array2insert($tables['ps_conditions'], cw_addslashes($data));
            $data['total'] = intval($input_data['till']);
            $cond_id2 = cw_array2insert($tables['ps_conditions'], cw_addslashes($data));
        }
        else {
            $cond_id = cw_array2insert($tables['ps_conditions'], cw_addslashes($data));
        }
        
        
        if ($cond_type == PS_SPEC_PRODUCTS) {
            // Save products to condition details
            if (!empty($pids)) {
                
                $data = array();
                
                $data['offer_id'] = $offer_id;
                $data['cond_id'] = $cond_id;
                
                foreach ($pids as $pid) {
                    $data['object_id'] = $pid;
                    $data['object_type'] = PS_OBJ_TYPE_PRODS;
                    $data['quantity'] = $products_data[$pid];
                    if (empty($data['quantity'])) {
                        $data['quantity'] = 1;
                    }
                    cw_array2insert($tables['ps_cond_details'], cw_addslashes($data));
                }
                
            }
            
            // Save categories to condition details
            if (!empty($cids)) {
                
                $data = array();
                
                $data['offer_id'] = $offer_id;
                $data['cond_id'] = $cond_id;
                
                foreach ($cids as $cid) {
                    $data['object_id'] = $cid;
                    $data['object_type'] = PS_OBJ_TYPE_CATS;
                    $data['quantity'] = $cats_data[$cid];
                    if (empty($data['quantity'])) {
                        $data['quantity'] = 1;
                    }
                    cw_array2insert($tables['ps_cond_details'], cw_addslashes($data));
                }
                
            }
            
            // Save attributes to condition details
            if (!empty($attrids)) {
                
                $data = array();
                
                $data['offer_id'] = $offer_id;
                $data['cond_id'] = $cond_id;
                
                foreach ($attrids as $aid) {
                    $data['object_id'] = $aid;
                    $data['object_type'] = PS_OBJ_TYPE_ATTR;
                    $data['quantity'] = $attr_data[$aid]['quantity'];
                    $data['param1'] = $attr_data[$aid]['value'];
                    $data['param2'] = $attr_data[$aid]['operation'];
                    if (empty($data['quantity'])) {
                        $data['quantity'] = 1;
                    }
                    cw_array2insert($tables['ps_cond_details'], cw_addslashes($data));
                }
			}
			
            // Save manufacturers to condition details
            if (!empty($mids)) {
                
                $data = array();
                
                $data['offer_id'] = $offer_id;
                $data['cond_id'] = $cond_id;
                
                foreach ($mids as $mid) {
                    $data['object_id'] = $mid;
                    $data['object_type'] = PS_OBJ_TYPE_MANS;
                    $data['quantity'] = $mans_data[$mid];
                    if (empty($data['quantity'])) {
                        $data['quantity'] = 1;
                    }
                    cw_array2insert($tables['ps_cond_details'], cw_addslashes($data));
                }
            }
        }
        elseif ($cond_type == PS_SHIP_ADDRESS) {
            
            if (!empty($zone_ids)) {
                $data = array();
                
                $data['offer_id'] = $offer_id;
                $data['cond_id'] = $cond_id;
                
                foreach ($zone_ids as $zone_id) {
                    $data['object_id'] = $zone_id;
                    $data['object_type'] = PS_OBJ_TYPE_ZONES;
                    $data['quantity'] = null;
                    cw_array2insert($tables['ps_cond_details'], cw_addslashes($data));
                }
            }
        }
        elseif ($cond_type == PS_TOTAL || $cond_type == PS_WEIGHT) {
            $data = array();

            $data['offer_id'] = $offer_id;
            $data['cond_id'] = $cond_id;
            $data['object_id'] = 0;
            $data['object_type'] = PS_OBJ_TYPE_FROM;
            $data['quantity'] = 0;

            cw_array2insert($tables['ps_cond_details'], cw_addslashes($data));

            if (isset($cond_id2)) {
                $data['cond_id'] = $cond_id2;
                $data['object_type'] = PS_OBJ_TYPE_TILL;

                cw_array2insert($tables['ps_cond_details'], cw_addslashes($data));
            }
        }
        elseif ($cond_type == PS_COOKIE) {
            
            $data = array(
                'offer_id' => $offer_id,
                'cond_id' => $cond_id,
                'object_type' => PS_OBJ_TYPE_COOKIE,
                'param1' => serialize($input_data),
            );
            
            cw_array2insert($tables['ps_cond_details'], cw_addslashes($data));            
        }
        elseif ($cond_type == PS_MEMBERSHIP) {
            $data = array();

            $data['offer_id'] = $offer_id;
            $data['cond_id'] = $cond_id;
            $data['object_id'] = $input_data['membership'];
            $data['object_type'] = PS_OBJ_TYPE_MEMBERSHIP;
            $data['quantity'] = 0;

            cw_array2insert($tables['ps_cond_details'], cw_addslashes($data));
        }

        unset($conditions[$cond_type]);
    }
    
    
    if (!empty($conditions)) {
        $GLOBALS['_ps_conds'] = & $conditions;
        cw_session_register('_ps_conds');
    }
    
    if (!empty($errors)) {
        $error = implode("<br />\n", $errors);
        return array(false, $error);
    }
    
    return array(true, null);
    
}




function ps_get_offer_conditions($offer_id = null) {
    global $tables;
    
    $result = array();
    
    $offer_id = (int)$offer_id;
    
    if (empty($offer_id)) {
        return array();
    }
    
    $conditions = cw_query("SELECT * FROM $tables[ps_conditions] WHERE offer_id = '$offer_id'");
    $condition_details = cw_query("SELECT * FROM $tables[ps_cond_details] WHERE offer_id = '$offer_id'");
    
    $_condition_details = array();
    
    if (!empty($condition_details) && is_array($condition_details)) {
        
        $products = array();
        $cats = array();
        $mans = array();
        $zones = array();
        
        foreach ($condition_details as $details) {
            
            switch ($details['object_type']) {
                case PS_OBJ_TYPE_PRODS:
                    $products[$details['object_id']] = null;
                    break;
                
                case PS_OBJ_TYPE_CATS:
                    $cats[$details['object_id']] = null;
                    break;
                    
                case PS_OBJ_TYPE_MANS:
                    $mans[$details['object_id']] = null;
                    break;
                    
                case PS_OBJ_TYPE_ATTR:
                    $attr[$details['object_id']] = null;
                    break;                    
                    
                case PS_OBJ_TYPE_ZONES:
                    $zones[$details['object_id']] = null;
                    break;
                    
                default:
                break;
            }
            
        }
        
        
        if (!empty($products)) {
            $products = cw_query_hash("SELECT product_id, product FROM $tables[products] WHERE product_id IN ('" . implode("', '", array_keys($products)) . "')", 'product_id', false, true);
        }

        if (!empty($cats)) {
            $cats = cw_query_hash("SELECT category_id, category FROM $tables[categories] WHERE category_id IN ('" . implode("', '", array_keys($cats)) . "')", 'category_id', false, true);
        }
        
        if (!empty($mans)) {
            $mans = cw_query_hash("SELECT manufacturer_id, manufacturer FROM $tables[manufacturers] WHERE manufacturer_id IN ('" . implode("', '", array_keys($mans)) . "')", 'manufacturer_id', false, true);
        }
        
        if (!empty($zones)) {
            $zones = cw_query_hash("SELECT zone_id, zone_name FROM $tables[zones] WHERE zone_id IN ('" . implode("', '", array_keys($zones)) . "')", 'zone_id', false, true);
        }
        
        
        foreach ($condition_details as $details) {
            
            if (!isset($_condition_details[$details['cond_id']]['products'])) {
                $_condition_details[$details['cond_id']]['products'] = array();
            }
            
            if (!isset($_condition_details[$details['cond_id']]['cats'])) {
                $_condition_details[$details['cond_id']]['cats'] = array();
            }
            
            if (!isset($_condition_details[$details['cond_id']]['mans'])) {
                $_condition_details[$details['cond_id']]['mans'] = array();
            }

            if (!isset($_condition_details[$details['cond_id']]['attr'])) {
                $_condition_details[$details['cond_id']]['attr'] = array();
            }
            
            if (!isset($_condition_details[$details['cond_id']]['zones'])) {
                $_condition_details[$details['cond_id']]['zones'] = array();
            }
            
            switch ($details['object_type']) {
                case PS_OBJ_TYPE_PRODS:
                    $_condition_details[$details['cond_id']]['products'][] = array('id' => $details['object_id'], 'name' => $products[$details['object_id']], 'quantity' => $details['quantity']);
                    $products[$details['object_id']] = null;
                    break;
                
                case PS_OBJ_TYPE_CATS:
                    $_condition_details[$details['cond_id']]['cats'][] = array('id' => $details['object_id'], 'name' => $cats[$details['object_id']], 'quantity' => $details['quantity']);
                    $cats[$details['object_id']] = null;
                    break;
                    
                case PS_OBJ_TYPE_MANS:
                    $_condition_details[$details['cond_id']]['mans'][] = array('id' => $details['object_id'], 'name' => $mans[$details['object_id']], 'quantity' => $details['quantity']);
                    $mans[$details['object_id']] = null;
                    break;

                case PS_OBJ_TYPE_ATTR:
                    $_condition_details[$details['cond_id']]['attr'][] = array('id' => $details['object_id'], 'quantity' => $details['quantity'], 'cd_id'=>$details['cd_id']);
                    $attr[$details['object_id']] = null;
                    break;
                                        
                case PS_OBJ_TYPE_ZONES:
                    $_condition_details[$details['cond_id']]['zones'][$details['object_id']] = array('id' => $details['object_id'], 'name' => $zones[$details['object_id']], 'quantity' => $details['quantity']);
                    $zones[$details['object_id']] = null;
                    break;

                case PS_OBJ_TYPE_FROM:
                    $_condition_details[$details['cond_id']] = 'from';
                    break;

                case PS_OBJ_TYPE_TILL:
                    $_condition_details[$details['cond_id']] = 'till';
                    break;

                case PS_OBJ_TYPE_MEMBERSHIP:
                    $_condition_details[$details['cond_id']]['membership'] = $details['object_id'];
                    break;
                
                case PS_OBJ_TYPE_COOKIE:
                    $_condition_details[$details['cond_id']] = (array)unserialize($details['param1']);
                    break;
                                        
                default:
                break;
            }
        }
    }

    $condition_details = $_condition_details;
    unset($_condition_details);

    if (empty($conditions) || !is_array($conditions)) {
        return array();
    }

    $save_weight_fields = $save_total_fields = array();
    
    foreach ($conditions as $condition) {
        $result[$condition['type']] = $condition;
        
        if ($condition['type'] == PS_SPEC_PRODUCTS) {
            $result[$condition['type']]['products'] = $condition_details[$condition['cond_id']]['products'];
            $result[$condition['type']]['cats'] = $condition_details[$condition['cond_id']]['cats'];
            $result[$condition['type']]['mans'] = $condition_details[$condition['cond_id']]['mans'];
            $result[$condition['type']]['attr'] = $condition_details[$condition['cond_id']]['attr'];
            
        }
        elseif ($condition['type'] == PS_SHIP_ADDRESS) {
            $result[$condition['type']]['zones'] = $condition_details[$condition['cond_id']]['zones'];
        }
        elseif ($condition['type'] == PS_WEIGHT) {
            $field = $condition_details[$condition['cond_id']];
            $save_weight_fields[$condition['type']][$field] = $condition['total'];
            $result[$condition['type']] = array_merge($result[$condition['type']], $save_weight_fields[$condition['type']]);
        }
        elseif ($condition['type'] == PS_TOTAL) {
            $field = $condition_details[$condition['cond_id']];
            $save_total_fields[$condition['type']][$field] = $condition['total'];
            $result[$condition['type']] = array_merge($result[$condition['type']], $save_total_fields[$condition['type']]);
        }
        elseif ($condition['type'] == PS_MEMBERSHIP) {
            $result[$condition['type']]['membership'] = $condition_details[$condition['cond_id']]['membership'];
        }
        elseif ($condition['type'] == PS_COOKIE) {
            $result[$condition['type']] = array_merge($condition,$condition_details[$condition['cond_id']]);
        }
    }

    return $result;
    
}



function ps_get_zones() {
    global $tables;
    
    $is_shipping = 1;
    $zones_condition = ' and is_shipping = 1';
    
    $zones = cw_query("SELECT $tables[zones].* FROM $tables[zones] WHERE 1 $zones_condition ORDER BY $tables[zones].zone_name");
    
    if (empty($zones) || !is_array($zones)) {
        return array();
    }
    
    
    if (!empty($zones)) {
    
        foreach ($zones as $k=>$zone) {
            if (!empty($zone['zone_cache'])) {
                $zone_cache_array = explode("-", $zone['zone_cache']);
                for ($i = 0; $i < count($zone_cache_array); $i++) {
                    if (preg_match("/^([\w])([0-9]+)$/", $zone_cache_array[$i], $match)) {
                        $zones[$k]['elements'][$i]['element_type'] = $match[1];
                        $zones[$k]['elements'][$i]['counter']= $match[2];
                        
                        if ($match[2] == 1) {
                        
                            $_element = cw_query_first_cell("SELECT field FROM $tables[zone_element] WHERE zone_id='$zone[zone_id]' AND field_type='$match[1]' LIMIT 1");
                            if ($match[1] == "C")
                                $element_name = cw_get_country($_element);
                            
                            elseif ($match[1] == "S")
                                $element_name = cw_get_state(substr($_element, strpos($_element, "_")+1), substr($_element, 0, strpos($_element, "_")));
                            
                            elseif ($match[1] == "G")
                                $element_name = cw_get_state(substr($_element, strpos($_element, "_")+1), substr($_element, 0, strpos($_element, "_")));
                            
                            else
                                $element_name = $_element;
                            
                            $zones[$k]['elements'][$i]['element_name'] = $element_name;
                        
                        }
                    }
                }

                usort($zones[$k]['elements'], "sort_zone_elements");

            }
        }
    }
    
    return $zones;
    
}



function ps_get_zones_ajax() {
    global $smarty;
    
    $smarty->assign('ps_zones', ps_get_zones());
    //$smarty->fetch('addons/promotion_suite/admin/zones.tpl', null, null, true);
    cw_display('addons/promotion_suite/admin/zones.tpl', $smarty);
    exit(0);
}

function ps_get_attr_ajax() {
	global $smarty, $tables;
	global $attribute_id, $cd_id;
	
	$value = $operation = $quantity = '';
	
	
	if (!empty($cd_id)) {
		// extract from DB certain condition details
		$cond_details = cw_query_first("SELECT * FROM $tables[ps_cond_details] WHERE cd_id='$cd_id'");
		$attribute_id = $cond_details['object_id'];
		$quantity = $cond_details['quantity'];
		$value = $cond_details['param1'];
		$operation = $cond_details['param2'];
	}
	
	if (empty($attribute_id)) return false;
	
	cw_load('attributes');
    $attribute = cw_func_call('cw_attributes_get_attribute', array('attribute_id'=>$attribute_id));
    $attribute['value'] = $value;
	$attribute['values'] = array($value);
    $smarty->assign(array(
		'attribute'=> $attribute,
		'index' => empty($cd_id)?time():'cd_id'.$cd_id,
		'quantity' => $quantity,
		'value' => $value,
		'operation' => $operation
	));
	cw_ajax_add_block(array(
		'action' => 'append',
		'id' => 'ps_attributes',
		'template' => 'addons/promotion_suite/admin/attribute_row.tpl',
	));

	return true;
}
