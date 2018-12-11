<?php
function cw_clean_url_adjust($string)
{

    if (!is_string($string)) {
        return NULL;
    }

    $string = trim($string);

    if (zerolen($string)) {
        return '';
    }
    //$string = str_replace("'",'',$string);
    $string = preg_replace('/\&(?!#[0-9]+;)(?!#x[0-9a-f]+;)/', '-and-', preg_replace('/\&amp;/', '-and-', $string));

    return preg_replace('/-$/', '', preg_replace('/[-]+/', '-', preg_replace('/[^a-zA-Z0-9._-]/', '-', $string)));
}

# kornev, params
# $params['var'] - item type
function cw_clean_url_get_html_page_url($params) {
    global $app_web_dir, $tables, $current_language, $config;

    $language = !empty($current_language) ? $current_language : $config['default_customer_language'];

    $pages_types = array(
        'product'       => array('item_type' => 'P',  'item_id' => 'product_id'),
        'index'         => array('item_type' => 'C',  'item_id' => 'cat'),
        'manufacturers' => array('item_type' => 'M',  'item_id' => 'manufacturer_id'),
        'pages'         => array('item_type' => 'AB', 'item_id' => 'page_id'),
        'cms'           => array('item_type' => 'AB', 'item_id' => 'contentsection_id'),
        'search'        => array('item_type' => 'Q',  'item_id' => '0')
    );
    $pt = $pages_types[$params['var']];
    if ($pt) {
		$att = cw_call('cw_attributes_filter', array(array('field'=>'clean_url','item_type'=>$pt['item_type']), true));
		$url = cw_call('cw_attribute_get_value',array($att['attribute_id'], $params[$pt['item_id']], $language));
        $url = cw_clean_url_adjust($url);
        $clean_url_html_attribute_ids = cw_call('cw_clean_url_html_attribute_ids', array());

        if (in_array($att['attribute_id'], $clean_url_html_attribute_ids) && !empty($url)) 
            $url .= ".html";

        if ($url) {
            unset($params[$pt['item_id']]);
            return cw_core_assign_addition_params($app_web_dir . '/' . $url, $params, array(), $params['delimiter']);
        }
    }
    else {  // owner clean urls
        $url = cw_clean_url_get_redirect_url_in_owner_urls('target=' . $params['var'], $language);
        if ($url) {
            unset($params[$pt['item_id']]);
            return cw_core_assign_addition_params($app_web_dir . '/' . $url . '?', $params, array(), $params['delimiter']);
        }
    }

    return null;
}

function cw_clean_url_get_meta($tag) {
    global $smarty;

        cw_load('attributes');

//        $meta_fields = array('meta_title', 'meta_description', 'meta_keywords');

        $page = $smarty->_tpl_vars['main'];

        if ($page == 'subcategories') {
            $attribute_id = cw_call('cw_attributes_filter', array(array('item_type' => 'C', 'field' =>'meta_'.$tag),true,'attribute_id'));
            $meta = cw_call('cw_attribute_get_value', array($attribute_id, $smarty->_tpl_vars['current_category']['category_id']));
        }
        elseif ($page == 'product') {
            $attribute_id = cw_call('cw_attributes_filter', array(array('item_type' => 'P', 'field' =>'meta_'.$tag),true,'attribute_id'));
            $meta = cw_call('cw_attribute_get_value', array($attribute_id, $smarty->_tpl_vars['product']['product_id']));
        }
        elseif ($page == 'manufacturer_products') {
            $attribute_id = cw_call('cw_attributes_filter', array(array('item_type' => 'M', 'field' =>'meta_'.$tag),true,'attribute_id'));
            $meta = cw_call('cw_attribute_get_value', array($attribute_id, $smarty->_tpl_vars['manufacturer']['manufacturer_id']));
        }


    return !empty($meta)?$meta:null;
}

function cw_clean_url_check_and_generate($cleanurl, $params, $language) {
    global $tables;

    $result_clean_url = $clean_url = cw_clean_url_adjust($cleanurl);
    $index = 1;
    while (true) {
        $duplicate = cw_clean_url_check_is_duplicate(
            $result_clean_url,
            $params['item_id'],
            $params['item_type'],
            $params['attribute_id']
        );
        if (!$duplicate) break;
        $result_clean_url = $clean_url . '-' . $index;
        $index++;
    }

    // get current url for matching

    $current_clean_url = cw_call('cw_attribute_get_value',array($params['attribute_id'], $params['item_id'], $language));

    // save old url if it was changed
    if ($result_clean_url !== $current_clean_url && !empty($current_clean_url)) {
        cw_clean_url_add_to_history(
            $current_clean_url,
            $result_clean_url,
            $params['item_id'],
            $params['item_type'],
            $params['attribute_id']
        );
    }

    return $result_clean_url;
}

// check new url and save it
function cw_clean_url_check_url_and_save_value($clean_url, $item_id, $item_type, $attribute_id, $language) {
    global $tables;

    $clean_url = cw_clean_url_check_and_generate(
        $clean_url,
        array('item_id' => $item_id, 'item_type' => $item_type, 'attribute_id' => $attribute_id),
        $language
    );

    // Delete old attributes
    db_query(
        "DELETE from $tables[attributes_values]
        WHERE attribute_id = '$attribute_id'
            AND item_id = '$item_id'
            AND item_type = '$item_type'"
    );

    cw_array2insert(
        "attributes_values",
        array(
            'attribute_id'  => $attribute_id,
            'item_id'       => $item_id,
            'item_type'     => $item_type,
            'value'         => $clean_url,
            'code'          => $language
        )
    );

    return $clean_url;
}

# kornev, generate the clean url as it's required;
function cw_clean_url_attributes_save($item_id, $item_type, $attributes, $language = null, $extra = array()) {
    global $tables, $current_language, $config;
    
# kornev, we don't need this function if the clean url is not in the return - because it's partial update in this case
# kornev, and we will re-generate the url wrongly
    if (!isset($attributes['clean_url'])) return null;
    
    $language = !empty($current_language) ? $current_language : $config['default_customer_language'];

    $fields = array(
        'P' => array('field' => 'product', 'table' => $tables['products'], 'key' => 'product_id'),
        'C' => array('field' => 'category', 'table' => $tables['categories'], 'key' => 'category_id'),
        'M' => array('field' => 'manufacturer', 'table' => $tables['manufacturers'], 'key' => 'manufacturer_id'),
        'AB' => array('field' => 'name', 'table' => $tables['cms'], 'key' => 'contentsection_id'),
    );
    if (!$fields[$item_type]) return null;

    $clean_url = trim($attributes['clean_url']);
    if (!$clean_url) {
        $tp = $fields[$item_type];
        $clean_url = cw_clean_url_adjust(cw_query_first_cell("select {$tp['field']} from {$tp['table']} where {$tp['key']} = '{$item_id}'"));
    }

    $att = cw_call('cw_attributes_filter', array(array('addon'=>'clean_urls','item_type' => $item_type,'field' => 'clean_url'), true));
    $params = array(
        'attribute_id' => $att['attribute_id'],
        'item_type' => $item_type,
        'item_id' => $item_id,
    );
    $attributes['clean_url'] = cw_clean_url_check_and_generate($clean_url, $params, $language);

    // replace $attributes['clean_url'] in input params
    return new EventReturn($attributes, array($item_id, $item_type, $attributes, $language, $extra));
}

// get uniting attribute with item_type 'PA'
function cw_clean_url_get_uniting_attribute_id() {
	$att = cw_call('cw_attributes_filter', array(array('addon'=>'clean_urls','item_type' => clean_urls_attributes_item_type,'field' => 'clean_url'), true));
    return $att['attribute_id'];
}

// get attribute values attribute with item_type 'AV'
function cw_clean_url_get_attribute_values_attribute_id() {
	$att = cw_call('cw_attributes_filter', array(array('addon'=>'clean_urls','item_type' => clean_urls_attributes_values_item_type,'field' => 'clean_url'), true));
    return $att['attribute_id'];
}

// update clean urls for product option field and attribute field and values
function cw_clean_url_attributes_create_attribute($params, $return) {
    global $tables, $current_language, $config;

    $attribute_id = empty($params['attribute_id']) ? $return : $params['attribute_id'];    //editable attribute
    $language = $params['language'];
    $data = $params['data'];    // cw_attributes.*, default_value
    /*type = "text", "textarea", "integer", "decimal", "selectbox", "multiple_selectbox", "date", "yes_no", "hidden"*/

    if (
        $data['item_type'] != 'P'
        || empty($attribute_id)
        || empty($data['type'])
    ) {
        return $return;
    }

    cw_load('attributes');

    $language = !empty($language) ? $language : (!empty($current_language) ? $current_language : $config['default_customer_language']);
    $main_attribute_id = cw_clean_url_get_uniting_attribute_id();
    $attribute_values_attribute_id = cw_clean_url_get_attribute_values_attribute_id();

    // Save clean url for attribute field
    $clean_url_field = !empty($data['clean_url_field']) ? $data['clean_url_field'] : $data['name'];
    $clean_url_field = trim($clean_url_field);
    if (!$clean_url_field) {
        $result = cw_func_call(
            "cw_attributes_get_attribute",
            array('attribute_id' => $attribute_id, 'language' => $language)
        );
        $clean_url_field = cw_clean_url_adjust($result['name']);
    }
    else {
        $clean_url_field = cw_clean_url_adjust($clean_url_field);
    }

    cw_clean_url_check_url_and_save_value(
        $clean_url_field,
        $attribute_id,
        clean_urls_attributes_item_type,
        $main_attribute_id,
        $language
    );

    // Save clean urls for attribute values
    if (in_array($data['type'], array('selectbox','multiple_selectbox','integer','decimal'))) {
		$key_names = array(
			'integer'	=> 'default_values_select',
			'decimal'	=> 'default_values_select',
			'selectbox' => 'default_values_select',
			'multiple_selectbox' => 'default_values_multiselect'
		);
		$key_name = $key_names[$data['type']];


		if (!empty($data[$key_name]) && is_array($data[$key_name])) {
			$attribute_values = cw_query(
				"SELECT attribute_value_id FROM $tables[attributes_default]
            	WHERE attribute_id = '$attribute_id' ORDER BY attribute_value_id"
			);

			// unshift array for equal with $data arrays
			array_unshift($attribute_values, '');

			// change array for equal with $data arrays
			$fixed_attribute_values = array();
			// get existing ids
			$exist_ids = array_filter($data[$key_name . '_id']);
			if (!empty($exist_ids)) {
				$fixed_attribute_values = $attribute_values;
				if (!empty($data[$key_name][0])) {
					// get element for new value with 0 key in array
					$zero_key = max(array_keys($exist_ids)) + 1;
					// set element with $zero_key on 0 position
					if (isset($fixed_attribute_values[$zero_key])) {
						$fixed_attribute_values[0] = $fixed_attribute_values[$zero_key];
						array_splice($fixed_attribute_values, $zero_key, 1);
					}
				}
			} else {
				$position = 1;
				foreach ($data[$key_name] as $_k => $_v) {
					if ($_k == 0 && !empty($data[$key_name][$_k])) {
						// change array for equal with $data arrays
						// get existing ids
						$exist_ids = array_filter($data[$key_name . '_id']);
						// get element for new value with 0 key in array
						$zero_key = max(array_keys($exist_ids)) + 1;
						// set element with $zero_key on 0 position
						if (isset($attribute_values[$zero_key])) {
							$fixed_attribute_values[$_k] = $attribute_values[$zero_key];
							array_splice($attribute_values, $zero_key, 1);
						}
					} else {
						$fixed_attribute_values[$_k] = $attribute_values[$position];
						$position++;
					}
				}
			}

            foreach ($data[$key_name] as $k => $v) {
				if (!empty($v) && $data[$key_name . '_active'][$k]) {
                    $attribute_value_id = !empty($data[$key_name . '_id'][$k])
                        ? $data[$key_name . '_id'][$k]
                        : $fixed_attribute_values[$k]['attribute_value_id'];

					if (!empty($attribute_value_id)) {
                        $clean_url_value = !empty($data[$key_name . '_cleanurl'][$k])
                            ? $data[$key_name . '_cleanurl'][$k]
                            : $clean_url_field . "-" . $v;

                        cw_clean_url_check_url_and_save_value(
                            $clean_url_value,
                            $attribute_value_id,
                            clean_urls_attributes_values_item_type,
							$attribute_values_attribute_id,
                            $language
                        );
                    }
                }
            }
        }
    }
    elseif ($data['type'] == 'text' || $data['type'] == 'textarea') {

        if (!empty($data['default_value'])) {
            $clean_url_value = !empty($data['clean_url_value_field'])
                ? $data['clean_url_value_field']
                : $clean_url_field . "-" . $data['default_value'];
            // attribute id for default value
            $attribute_value_id = $data['attribute_value_id'];
            if (empty($attribute_value_id)) {
                $attribute_value_id = cw_query_first_cell(
                    "SELECT attribute_value_id FROM $tables[attributes_default]
                    WHERE attribute_id = '$attribute_id'"
                );
            }

            if (
                !empty($clean_url_value)
                && !empty($attribute_value_id)
                && $data['active']
            ) {
                cw_clean_url_check_url_and_save_value(
                    $clean_url_value,
                    $attribute_value_id,
					clean_urls_attributes_values_item_type,
					$attribute_values_attribute_id,
                    $language
                );
            }
        }
    }

    return $return;
}

// post save attribute method, called on product options update
// update clean urls for product options values
function cw_clean_url_attributes_save_attribute($params, $return) {
    global $tables, $current_language, $config;

    $productid = $params['item_id'];
    $item_type = $params['item_type'];
    $attributes = $params['attributes'];

    if (
        empty($productid)
        || $item_type != 'P'
        || empty($attributes)
    ) {
        return $return;
    }
	
	$clean_url_attribute = cw_call('cw_attributes_filter', array(array('field'=>'clean_url','item_type'=>clean_urls_attributes_values_item_type), true));
	$clean_url_attribute_id = $clean_url_attribute['attribute_id'];
    if (is_array($attributes) && $clean_url_attribute_id) {
        $language = !empty($current_language) ? $current_language : $config['default_customer_language'];
        foreach ($attributes as $field => $values) {
			$attribute = cw_call('cw_attributes_filter', array(array('field'=>$field, 'item_type'=>'P'), true));
            if (!$attribute) continue;
            $attribute_id = $attribute['attribute_id']; 
            $attribute_type = $attribute['type'];
            $attribute_name = $attribute['name'];

            if (!in_array($attribute_type, array("text", "textarea", "selectbox", "multiple_selectbox"))) continue;

            if (!is_array($values)) {
                $values = array($values);
            }

            foreach ($values as $attribute_value_id) {
                $attribute_default_value = cw_query_first_cell(
                    "SELECT value FROM $tables[attributes_default]
                    WHERE attribute_id = '$attribute_id' AND attribute_value_id = '$attribute_value_id' AND active = 1"
                );
                if (!empty($attribute_default_value)) {
                    cw_clean_url_check_url_and_save_value(
                        $attribute_name . '-' . $attribute_default_value,
                        $attribute_value_id,
                        clean_urls_attributes_values_item_type,
                        $clean_url_attribute_id,
                        $language
                    );
                }
            }
        }
    }

    return $return;
}

function cw_clean_url_manufacturer_delete($manufacturer_id) {
    global $tables;
    
    $return = cw_get_return();

    db_query('DELETE FROM ' . $tables['clean_urls_history'] . ' WHERE item_id = "' . $manufacturer_id . '" AND item_type="M"');
    
    return $return;
}

// post hook for cw_category_delete
// find all ophaned categories records in history and clean them
function cw_clean_url_category_delete($cat, $is_show_process = false) {
    global $tables, $config;

    $return = cw_get_return();

	$orphaned_cats = cw_query_column("SELECT u.item_id FROM $tables[clean_urls_history] u 
	LEFT JOIN $tables[categories] c ON c.category_id=u.item_id WHERE u.item_type='C' AND c.category_id IS NULL");

    if ($cat!=0) $orphaned_cats[] = $cat;

    if (!empty($subcats) && is_array($subcats)) {
        db_exec("DELETE FROM $tables[clean_urls_history] WHERE item_type = 'C' AND item_id IN (?)", array($orphaned_cats));
    }

    return $return;
}

// Pre-hook on cw_attributes_delete()
// delete current clean url and all history
function cw_clean_url_attributes_delete($attribute_id) {
    global $tables;

    if (empty($attribute_id)) {
        return $attribute_id;
    }

    $main_attribute_id = cw_clean_url_get_uniting_attribute_id();
    $attribute_values_attribute_id = cw_clean_url_get_attribute_values_attribute_id();

    // delete urls from history
    db_query(
        "DELETE FROM $tables[clean_urls_history]
		WHERE item_id = '$attribute_id'
            AND (
            	(item_type = '" . clean_urls_attributes_item_type . "'
            		AND attribute_id = '$main_attribute_id')
            	OR (item_type = '" . clean_urls_attributes_values_item_type . "'
            		AND attribute_id = '$attribute_values_attribute_id')
            )"
    );

    // delete clean urls for attribute name field
    cw_call('cw_attributes_cleanup', array($attribute_id, clean_urls_attributes_item_type));
    cw_call('cw_attributes_cleanup', array($attribute_id, clean_urls_attributes_values_item_type));
}

// Pre-hook on cw_attributes_delete_values()
// Delete all history and clean URLs for attribute defaults 
function cw_clean_url_attributes_delete_values($att_value_ids) {
    global $tables;
    db_query("
		DELETE FROM $tables[clean_urls_history]
		WHERE item_id IN ('".implode("', '", $att_value_ids)."') AND item_type = '" . clean_urls_attributes_item_type . "'
    ");
    cw_call('cw_attributes_cleanup', array($att_value_ids, clean_urls_attributes_item_type));
    db_query("
    	DELETE FROM $tables[clean_urls_history]
    	WHERE item_id IN ('".implode("', '", $att_value_ids)."') AND item_type = '" . clean_urls_attributes_values_item_type . "'
    ");
    cw_call('cw_attributes_cleanup', array($att_value_ids, clean_urls_attributes_values_item_type));
}

function cw_clean_url_attributes_get_attribute($params, $return) {
    global $tables;

    $attribute_id = $params['attribute_id'];    //editable attribute
    $language = $params['language'];
    $main_attribute_id = cw_clean_url_get_uniting_attribute_id();
	$attribute_values_attribute_id = cw_clean_url_get_attribute_values_attribute_id();

    if (!empty($attribute_id)) {
		$clean_url_value = cw_call('cw_attribute_get_value',array($main_attribute_id, $attribute_id, $language));
        $return['clean_url_field'] = $clean_url_value;
    }

    if (in_array($return['type'], array('selectbox', 'multiple_selectbox','integer','decimal'))) {

        if (is_array($return['default_values'])) {

            foreach ($return['default_values'] as $default_key => $default_value) {
				$clean_url_value = cw_call('cw_attribute_get_value',array($attribute_values_attribute_id, $default_value['attribute_value_id'], $language));
                $return['default_values'][$default_key]['cleanurl'] = $clean_url_value;
            }
        }
    }
    elseif (in_array($return['type'], array('text', 'textarea'))) {
        $clean_url_value = cw_query_first_cell(
            "SELECT av.value
            FROM $tables[attributes_values] av
            LEFT JOIN $tables[attributes_default] ad ON ad.attribute_value_id = av.item_id
            WHERE ad.attribute_id = '$attribute_id'
                AND av.code = '$language'
                AND av.item_type = '" . clean_urls_attributes_values_item_type . "'"
        );
        $return['clean_url_value_field'] = $clean_url_value;
    }

    return $return;
}

// get current url by url from history
function cw_clean_url_get_url_by_history_url($url) {
	global $tables, $current_language, $config;

	$result = cw_query_first(
		"SELECT *
		FROM $tables[clean_urls_history]
		WHERE url = '" . addslashes($url) . "'"
    );

	if (!empty($result)) {
		$language = !empty($current_language) ? $current_language : $config['default_customer_language'];
		$att = cw_call('cw_attributes_filter', array(array('field'=>'clean_url','item_type'=>$result['item_type']), true));
		$current_url = cw_call('cw_attribute_get_value', array($att['attribute_id'],$result['item_id']),$language);

		if (!empty($current_url)) {
			return $current_url;
		}
	}
	
	return $url;
}

// check url in history
// return TRUE or FALSE
function cw_clean_url_check_url_is_in_history($url) {
	global $tables;

	$count = cw_query_first_cell(
		"SELECT count(*)
		FROM $tables[clean_urls_history]
		WHERE url = '" . addslashes($url) . "'"
	);
	
	if ($count) {
		return TRUE;
	}
	
	return FALSE;
}

// check url in history and in current clean urls for other items
// return TRUE or FALSE
function cw_clean_url_check_is_duplicate($url, $item_id, $item_type, $attribute_id) {
	global $tables;

   $clean_url_attribute_ids = cw_call('cw_attributes_filter', array(array('field'=>'clean_url', 'addon'=>'clean_urls'), false, 'attribute_id'));

	// check if url is duplicate for other clean url attributes
	$count = cw_query_first_cell(
		"SELECT count(*) 
		FROM $tables[attributes_values] 
		WHERE value = '" . addslashes($url) . "'
            AND (attribute_id IN ('".join("','",$clean_url_attribute_ids)."'))
		    AND (item_id != '$item_id' OR attribute_id != '$attribute_id')"
	);

	if ($count) {
		return TRUE;
	}

	// check if url is duplicate for history clean urls
	$count = cw_query_first_cell(
		"SELECT count(*)
		FROM $tables[clean_urls_history]
		WHERE url = '" . addslashes($url) . "'
            AND (item_id != '$item_id' OR attribute_id != '$attribute_id')"
	);

	if ($count) {
		return TRUE;
	}

	return FALSE;
}
 
// add old url to history
function cw_clean_url_add_to_history($old_url, $new_url, $item_id, $item_type, $attribute_id) {
	global $tables;

	// delete new url from history if present
	cw_clean_url_delete_url_by_data($new_url, $item_id, $item_type, $attribute_id);

	// add old url to history for current item
	cw_array2insert(
		'clean_urls_history', 
		array(
			'url'		    => $old_url,
			'item_id'	    => $item_id,
			'item_type'	    => $item_type,
			'attribute_id'	=> $attribute_id,
			'ctime'		    => time()
		)
	);
}

// get url history list for item
function cw_clean_url_get_url_list_from_history($item_id, $item_type) {
	global $tables;
	
	if (empty($item_id) || empty($item_type)) {
		return array();
	}
	
	$urls = cw_query(
		"SELECT id, url
		FROM $tables[clean_urls_history]
		WHERE item_id = '$item_id' AND item_type = '$item_type'
		ORDER BY ctime"
	);
	
	return $urls;
}

// delete url from history by id
function cw_clean_url_delete_url_by_id($url_id) {
	global $tables;

	// delete url from history
	db_query("DELETE FROM $tables[clean_urls_history] WHERE id = '$url_id'");
}

// delete url from history by url, item_id and item_type
function cw_clean_url_delete_url_by_data($url, $item_id, $item_type, $attribute_id) {
	global $tables;

	// delete new url from history if present
	db_query(
		"DELETE FROM $tables[clean_urls_history] 
		WHERE url = '$url'
            AND item_id = '$item_id'
            AND item_type = '$item_type'
            AND attribute_id = '$attribute_id'"
	);
}

// delete url from history and return item_id and item_type
function cw_clean_url_delete_url_by_id_and_get_data($url_id) {
	global $tables;
	
	$result = array(
		'item_id'	=> 0,
		'item_type'	=> ''
	);

	if (!empty($url_id)) {
		$data = cw_query_first(
			"SELECT item_id, item_type
			FROM $tables[clean_urls_history]
			WHERE id = '$url_id'"
		);
		
		if (!empty($data)) {
			$result['item_id'] 		= $data['item_id'];
			$result['item_type'] 	= $data['item_type'];
		}

		cw_clean_url_delete_url_by_id($url_id);
	}

	return $result;
}

// get additional query from url
function cw_clean_url_get_additional_query($url_query, $data) {

    if (empty($url_query) || empty($data)) {
        return '';
    }

    $params_check = array();
    foreach ($data as $key => $value) {
        $params_check[] = $key;
        $params_check[] = $value[0];
    }

    $result = array();
    $parts = explode("&", trim($url_query));

    if (!empty($parts)) {

        foreach ($parts as $part) {
            $params = explode("=", $part);

            $check = $params[0];
            if ($params[0] == 'target') {
                $check = $params[1];
            }

            if (
                !empty($params)
                && !empty($params[0])
                && !in_array($check, $params_check)
            ) {
                $result[] = $params[0] . "=" . $params[1];
            }
        }
    }

    if (!empty($result)) {
        return "?" . implode("&", $result);
    }
    else {
        return "";
    }
}

/**
 * 
 * @param array $params - additional function params 
 *  [ns] - navigation script
 *  [clean_urls] - array of clean urls can be supplied by pre-hooks to build final url
 * @param array $query - query params
 * @param bool $full - add or not navigation script to returned url
 * 
 * @return string url
 */
function cw_clean_url_generate_filter_url($params, $query, $full = true) {
    global $tables, $current_language, $config, $current_location, $current_host_location;
    global $cw_attributes;

    $main_attribute_id = cw_clean_url_get_uniting_attribute_id();
	$attribute_values_attribute_id = cw_clean_url_get_attribute_values_attribute_id();
    $language = !empty($current_language) ? $current_language : $config['default_customer_language'];
    
    if (empty($query) || !is_array($query)) {
        return '';
    }
    $addition_params = array();
    $clean_urls =  array();
    if (!empty($params['clean_urls'])) $clean_urls = $params['clean_urls'];
	foreach ($query as $varname=>$element) {
      if (is_array($element)) {
        ksort($element); // Sort by attribute_id. Maybe it is not the best key for sorting, but it gives same attributes order each time [CP-650]
        foreach ($element as $attribute_id => $attribute_value) {

			if ($attribute_id == 'price') {
				// If price is shown as ranges, then process it as usual attribute
				$a = cw_call('cw_attributes_filter',array(array('field'=>'price'), true));
				if (in_array($a['pf_display_type'], array('P','E','W','G'))) {
					$attribute_id = $a['attribute_id'];
				}
			}

            if (is_numeric($attribute_id)) {
				$att = $cw_attributes['all'][$attribute_id];
				
                $attribute_type = ($att['active']==1 && $att['pf_is_use']==1)?$att['type']:false;

                if (is_array($attribute_value)) $attribute_first_value = reset($attribute_value);
                
                if (in_array($attribute_type, array("selectbox", "multiple_selectbox"))
					||  (in_array($attribute_type, array("decimal", "integer")) 
						&& in_array($att['pf_display_type'], array('P','E','W','G'))) ) {
                    
                    if (is_numeric($attribute_first_value)) {
						$clean_url = cw_call('cw_attribute_get_value', array($attribute_values_attribute_id, $attribute_first_value, $language));

                        if (!empty($clean_url)) {
                            $clean_urls[] = $clean_url;
                        }
                    }
                    else {
						$clean_url = cw_call('cw_attribute_get_value', array($attribute_values_attribute_id, $attribute_id, $language));

                        if (!empty($clean_url)) {
                            $clean_urls[] = $clean_url . '-' . $attribute_first_value;
                        }
                    }
                }
                elseif (in_array($attribute_type, array("text", "date", "yes_no", "decimal", "integer"))) {
					$clean_url = cw_call('cw_attribute_get_value', array($main_attribute_id, $attribute_id, $language));

                    if (!empty($clean_url)) {

                        if (isset($attribute_value['min']) && isset($attribute_value['max'])) {
                            $clean_urls[] = $clean_url . '-' . $attribute_value['min'] . '-' . $attribute_value['max'];
                        }
                        else {
                            $clean_urls[] = $clean_url . '-' . $attribute_first_value;
                        }
                    }
                }
                elseif (in_array($attribute_type, array("manufacturer-selector"))) {
					$clean_url = cw_call('cw_attribute_get_value', array($main_attribute_id, $attribute_id, $language));
					$att = cw_call('cw_attributes_filter', array(array('field'=>'clean_url','item_type'=>'M','addon'=>'clean_urls'), true));
					$clean_url_value = cw_call('cw_attribute_get_value', array($att['attribute_id'], $attribute_first_value, $language));

                    if (!empty($clean_url) && !empty($clean_url_value)) {
                        $clean_urls[] = $clean_url . '-' . $clean_url_value;
                    }
                }
            }
            elseif ($attribute_id == 'price') {  // price
				$att = cw_call('cw_attributes_filter', array(array('field'=>'price','pf_is_use'=>'1'), true));
                $attribute_id = $att['attribute_id'];
				$clean_url = cw_call('cw_attribute_get_value', array($main_attribute_id, $attribute_id, $language));
                
                if (
                    !empty($clean_url)
                    && isset($attribute_value['min'])
                    && isset($attribute_value['max'])
                ) {
                    $clean_urls[] = $clean_url . '-' . $attribute_value['min'] . '-' . $attribute_value['max'];
                }
            }
            elseif ($attribute_id == 'substring') {  //substring
                $clean_urls[] = 'substring-' . $attribute_value;
            }
        }
      } else {
        $addition_params[$varname] = $element;
      }
    }

    if (!empty($clean_urls)) {
		if ($full) {
			$url_info = parse_url($params['ns']);
			$redirect_url = cw_clean_url_get_redirect_url_in_owner_urls($url_info['query'], $language);

			if (!empty($redirect_url)) {
				return trim($current_location . '/' . $redirect_url, '/') . '/' . implode('/', $clean_urls).'?'.http_build_query($addition_params);
			}
			else {
				return '/' . trim($url_info['path'], '/') . '/' . implode('/', $clean_urls).'?'.http_build_query($addition_params);
			}
		} else {
			return implode('/', $clean_urls).'?'.http_build_query($addition_params);
		}
    }

    return '';
}

function cw_clean_url_product_navigation_filter_url($params) {

    if (!$params['ns']) return '';
    
    if ($params['reset']) {
        if ($pos = strpos($params['ns'],'/search/')) return substr($params['ns'],0,$pos).'/search';
        if ($pos = strpos($params['ns'],'?')) return substr($params['ns'],0,$pos);
        return $params['ns'];
    }

    if (!$params['is_selected']) {
        $url_info = parse_url($params['ns']);

        parse_str($url_info['query'], $arr);

        $arr['att'][$params['att_id']][0] = $params['value_id'];
        $clean_url = cw_call('cw_clean_url_generate_filter_url', array($params, $arr));

        if ($clean_url) {
            return $clean_url;
        }
        else {
            return $params['ns'] . '&att[' . $params['att_id'] . '][]=' . $params['value_id'];
        }
    }

    if (!$params['value_selected']) return $params['ns'];

    $url_info = parse_url($params['ns']);

    parse_str($url_info['query'], $arr);

    if (!isset($arr['att'][$params['att_id']])) return $params['ns'];

    if (is_array($arr['att'][$params['att_id']])) {
        foreach($arr['att'][$params['att_id']] as $k=>$v)  {
            if (in_array($v, $params['value_selected'])) unset($arr['att'][$params['att_id']], $k);
        }
    }
    else {
        unset($arr['att'][$params['att_id']]);
    }

    $clean_url = cw_call('cw_clean_url_generate_filter_url', array($params, $arr));

    if ($clean_url) {
        return $clean_url;
    }
    else {
        $has_additional_query = FALSE;

        if (is_array($arr)) {

            foreach ($arr as $_v) {

                if (!empty($_v)) {
                    $has_additional_query = TRUE;
                    break;
                }
            }
        }
        return $url_info['path'] . ($has_additional_query ? '?' . http_build_query($arr) : '');
    }
}

/**************** CLEAN URLS LIST *****************/
// get all clean urls
function cw_clean_url_get_clean_urls_list_data($get_count=FALSE, $where="", $orderby="", $limit="") {
    global $tables;

    $result = cw_query("
        SELECT " . ($get_count ? "count(*) as c" : "
                av.*,
                IF (a.item_type = 'C', c.category,
                    IF (a.item_type = 'M', m.manufacturer,
                        IF (a.item_type = 'P', pr.product,
                            IF (a.item_type = 'AB', p.name, '')))
                ) AS entity,
                IF (a.item_type = 'C', CONCAT('index.php?target=index&cat=', av.item_id),
                    IF (a.item_type = 'M', CONCAT('index.php?target=manufacturers&manufacturer_id=', av.item_id),
                        IF (a.item_type = 'P', CONCAT('index.php?target=product&product_id=', av.item_id),
                            IF (a.item_type = 'AB', CONCAT('index.php?target=pages&page_id=', av.item_id), av2.value)))
                ) AS to_url,
                IF (a.item_type = 'C', 'Category',
                    IF (a.item_type = 'M', 'Manufacturer',
                        IF (a.item_type = 'P', 'Product',
                            IF (a.item_type = 'AB', 'Page', 'Owner')))
                ) AS type
            ") . "
        FROM $tables[attributes_values] av
        LEFT JOIN $tables[attributes] a ON a.attribute_id = av.attribute_id
        LEFT JOIN $tables[categories] c ON c.category_id = av.item_id
        LEFT JOIN $tables[manufacturers] m ON m.manufacturer_id = av.item_id
        LEFT JOIN $tables[products] pr ON pr.product_id = av.item_id
        LEFT JOIN $tables[cms] p ON p.contentsection_id = av.item_id AND p.type = 'staticpage'
        LEFT JOIN $tables[attributes_values] av2 ON av2.item_id = av.item_id AND av.item_type = 'O' AND av2.item_type = 'OS'
        WHERE a.active = 1 AND a.field = 'clean_url' AND av.item_type IN ('AB', 'C', 'M', 'P', 'O')
        $where
        $orderby
        $limit
    ");

    if ($get_count) {
        return $result[0]["c"];
    }

    return $result;
}

// get all clean urls from history
function cw_clean_url_get_clean_urls_history_list_data($orderby="") {
    global $tables;

    $result = cw_query("
        SELECT h.*,
            IF (h.item_type = 'C', c.category,
                IF (h.item_type = 'M', m.manufacturer,
                    IF (h.item_type = 'P', pr.product, p.name))
            ) AS entity,
            IF (h.item_type = 'C', CONCAT('index&cat=', h.item_id),
                IF (h.item_type = 'M', CONCAT('manufacturers&manufacturer_id=', h.item_id),
                    IF (h.item_type = 'P', CONCAT('product&product_id=', h.item_id), CONCAT('pages&page_id=', h.item_id)))
            ) AS to_url,
            IF (h.item_type = 'C', 'Category',
                IF (h.item_type = 'M', 'Manufacturer',
                    IF (h.item_type = 'P', 'Product',
                    	IF (h.item_type = 'PA', 'Attribute',
                    		IF (h.item_type = 'AV', 'Attributes values', 'Page'))))
            ) AS type
        FROM $tables[clean_urls_history] h
        LEFT JOIN $tables[categories] c ON c.category_id = h.item_id
        LEFT JOIN $tables[manufacturers] m ON m.manufacturer_id = h.item_id
        LEFT JOIN $tables[products] pr ON pr.product_id = h.item_id
        LEFT JOIN $tables[cms] p ON p.contentsection_id = h.item_id AND p.type = 'staticpage'
        WHERE h.item_type IN ('AB', 'C', 'M', 'P', 'PA', 'AV')
        $orderby
    ");

    return $result;
}

// get redirect url from static url in owner urls
function cw_clean_url_get_redirect_url_in_owner_urls($query, $language) {
    global $tables;

    $redirect_url = '';

    if (empty($query)) {
        return $redirect_url;
    }

    parse_str($query, $arr);

    if (isset($arr['att'])) {
        unset($arr['att']);
        $query = http_build_query($arr);
    }

    if (empty($query)) {
        return $redirect_url;
    }

    $query = db_escape_string($query);

    $data = cw_query_first("
        SELECT item_id, item_type
        FROM $tables[attributes_values]
        WHERE value LIKE '%" . $query . "' AND code = '$language' AND item_type = 'OS'
    ");

    if (!empty($data)) {
        $redirect_url = cw_query_first_cell("
            SELECT value
            FROM $tables[attributes_values]
            WHERE item_id = '$data[item_id]' AND item_type = 'O' AND code = '$language'
        ");
    }

    return $redirect_url;
}

/* Events handlers */
function cw_clean_url_on_product_delete($product_id) {
    global $tables;

    db_query('DELETE FROM ' . $tables['clean_urls_history'] . ' WHERE item_id = "' . $product_id . '" AND item_type="P"');
}

/********************** CUSTOM FACET URLS **************************/

/**
 * check if data is unique and return result
 *
 * @param $custom_clean_url
 * @param $clean_urls
 * @param int $exclude_url_id
 * @return array|int
 */
function cw_clean_url_custom_facet_url_unique_result($custom_clean_url, $clean_urls, $exclude_url_id=0) {
	global $tables;

	if (empty($clean_urls)) {
		return array(
			'type' => 1
		);
	}

	$where = "";
	if ($exclude_url_id) {
		$where = "AND url_id <> '$exclude_url_id'";
	}

	$result = cw_query_first_cell("
		SELECT url_id
		FROM $tables[clean_urls_custom_facet_urls]
		WHERE custom_facet_url = '$custom_clean_url' $where
	");

	if ($result) {
		return array(
			'type' => 1,
			'id' => $result
		);
	}

	$result = false;
	$urls = explode('/', trim($clean_urls, '/'));
	if (!empty($urls)) {
		$url_ids = cw_query("
			SELECT url_id
			FROM $tables[clean_urls_custom_facet_urls_options]
			WHERE clean_urls LIKE '%$urls[0]%' $where
		");

		if (!empty($url_ids)) {
			foreach ($url_ids as $url_id) {
				$test_urls = cw_query_first_cell("
					SELECT clean_urls
					FROM $tables[clean_urls_custom_facet_urls_options]
					WHERE url_id = '$url_id[url_id]'
				");
				$test_urls = explode('/', trim($test_urls, '/'));

				if (!array_diff($urls, $test_urls) && !array_diff($test_urls, $urls)) {
					$result = $url_id['url_id'];
					break;
				}
			}
		}
	}

	if ($result) {
		return array(
			'type' => 2,
			'id' => $result
		);
	}

	return 0;
}

/**
 * get attributes options
 *
 * @param array $attribute_value_ids
 * @return array
 */
function cw_clean_url_get_attributes_options($attribute_value_ids = array()) {
	cw_load('attributes');

	list ($attributes, $navigation) = cw_func_call(
		'cw_attributes_search',
		array(
			'data' => array(
				'all' => 1,
				'active' => 1,
				'pf_is_use' => 1,
				'type' => 'P',
				'sort_field' => 'orderby, name',
			)
		)
	);

	$attributes_options = array();
	if (is_array($attributes) && count($attributes)) {
		foreach ($attributes as $value) {
			$default_values = cw_call(
				'cw_attributes_get_attribute_default_value',
				array(
					'attribute_id' => $value['attribute_id']
				)
			);
			$options = array();
			$attributes_selected = false;
			if (is_array($default_values) && count($default_values)) {
				foreach ($default_values as $v) {
					$parameters = array();
					$parameters['att'][$v['attribute_id']][0] = $v['attribute_value_id'];
					$clean_url = cw_call('cw_clean_url_generate_filter_url', array(array(), $parameters, false));
					if (!empty($v['value']) && !empty($clean_url)) {
						$checked = in_array($v['attribute_value_id'], $attribute_value_ids);
						if ($checked) {
							$attributes_selected = true;
						}
						$options[] = array(
							'attribute_value_id' => $v['attribute_value_id'],
							'name' => $v['value'],
							'clean_url' => $clean_url,
							'checked' => $checked
						);
					}
				}
			}

			if (count($options)) {
				$attributes_options[] = array(
					'attribute_id' => $value['attribute_id'],
					'name' => $value['name'],
					'options' => $options,
					'selected' => $attributes_selected
				);
			}
		}
	}

	return $attributes_options;
}

/**
 * get only used options for filter
 *
 * @param $attribute_option
 * @return array
 */
function cw_clean_url_get_used_attributes_options($attribute_option) {
	global $tables;
	cw_load('attributes');

	$data = cw_query("SELECT attribute_value_ids FROM $tables[clean_urls_custom_facet_urls_options]");
	if (empty($data)) {
		return array();
	}
	$attribute_value_ids = array();
	foreach ($data as $item) {
		$attribute_value_ids = array_merge($attribute_value_ids, explode(',', $item['attribute_value_ids']));
	}
	$attribute_value_ids = array_unique($attribute_value_ids);

	list ($attributes, $navigation) = cw_func_call(
		'cw_attributes_search',
		array(
			'data' => array(
				'all' => 1,
				'active' => 1,
				'pf_is_use' => 1,
				'type' => 'P',
				'sort_field' => 'orderby, name',
			)
		)
	);

	$attributes_options = array();
	if (is_array($attributes) && count($attributes)) {
		foreach ($attributes as $value) {
			$default_values = cw_call(
				'cw_attributes_get_attribute_default_value',
				array(
					'attribute_id' => $value['attribute_id']
				)
			);
			$options = array();
			if (is_array($default_values) && count($default_values)) {
				foreach ($default_values as $v) {
					$parameters = array();
					$parameters['att'][$v['attribute_id']][0] = $v['attribute_value_id'];
					$clean_url = cw_call('cw_clean_url_generate_filter_url', array(array(), $parameters, false));
					if (!empty($v['value']) && !empty($clean_url)) {
						if (in_array($v['attribute_value_id'], $attribute_value_ids)) {
							$options[] = array(
								'attribute_value_id' => $v['attribute_value_id'],
								'name' => $v['value'],
								'clean_url' => $clean_url,
								'checked' => in_array($v['attribute_value_id'], (array)$attribute_option)
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
			}
		}
	}

	return $attributes_options;
}

/**
 * @param $where
 * @return bool
 */
function cw_clean_url_get_custom_facet_urls_count($where) {
	global $tables;

    $_res = db_query("
		SELECT *
		FROM $tables[clean_urls_custom_facet_urls_options], $tables[clean_urls_custom_facet_urls] 
		$where
	");
    $total_items = db_num_rows($_res);

    return $total_items;
}

/**
 * @param $where
 * @param $orderby
 * @param $limit
 * @return array
 */
function cw_clean_url_get_custom_facet_urls($where, $orderby, $limit) {
	global $tables, $current_language;

	$data = cw_query("
		SELECT $tables[clean_urls_custom_facet_urls].*
		FROM $tables[clean_urls_custom_facet_urls_options], $tables[clean_urls_custom_facet_urls]
		$where
		$orderby
		$limit
	");

	foreach ($data as $k => $v) {
        $attribute_value_ids_list = cw_query("select attribute_value_ids from $tables[clean_urls_custom_facet_urls_options] where url_id='$v[url_id]'");
        $data[$k]['options_combinations'] = array();
        foreach ($attribute_value_ids_list as $avi_v) {
		    $attribute_value_ids = explode(',',$avi_v['attribute_value_ids']);
		    if (!empty($attribute_value_ids)) {
			    $items = array();
  			    foreach ($attribute_value_ids as $attribute_value_id) {
				    $items[] = cw_query_first_cell("
					    SELECT IFNULL(adl.value, ad.value)
					    FROM $tables[attributes_default] AS ad
					    LEFT JOIN $tables[attributes_default_lng] as adl ON ad.attribute_value_id = adl.attribute_value_id
						    AND adl.code = '$current_language'
					    WHERE ad.attribute_value_id = '$attribute_value_id'
				    ");
			    }
			    $data[$k]['options_combinations'][] = implode("/", $items);
            }
        }
        $data[$k]['options_combination'] = implode("<br>", $data[$k]['options_combinations']);

        $clean_urls_list = cw_query("select clean_urls from $tables[clean_urls_custom_facet_urls_options] where url_id='$v[url_id]'");
        $clean_urls_data = array();
        foreach ($clean_urls_list as $cu_v) 
            $clean_urls_data[] = $cu_v['clean_urls']; 

        $data[$k]['clean_urls'] = implode('<br>', $clean_urls_data);
        $data[$k]['multi_url_count'] = count($clean_urls_list);
	}

	return $data;
}

/**
 * @param $url_id
 */
function cw_clean_url_custom_facet_url_delete($url_id) {
	global $tables;

	if (!empty($url_id) && is_numeric($url_id)) {
		db_query("DELETE FROM $tables[clean_urls_custom_facet_urls] WHERE url_id = '$url_id'");
        db_query("DELETE FROM $tables[clean_urls_custom_facet_urls_options] WHERE url_id = '$url_id'"); 
	}
}

/**
 * get clean urls combination for custom facet url
 *
 * @param $url
 * @return bool
 */
function cw_clean_url_get_custom_facet_url($url) {
	global $tables;

	if (!empty($url)) {
		$result = cw_query_first("
			SELECT fu.*, GROUP_CONCAT(fuo.clean_urls SEPARATOR '/') as clean_urls
			FROM $tables[clean_urls_custom_facet_urls] fu, $tables[clean_urls_custom_facet_urls_options] fuo
			WHERE fu.custom_facet_url = '".db_escape_string($url)."' and fu.url_id=fuo.url_id
			GROUP BY fu.url_id
		");

		if (!empty($result)) {
			return $result;
		}
	}

	return false;
}

/**
 * get custom facet url name by id
 *
 * @param $url_id
 * @return bool
 */
function cw_clean_url_get_custom_facet_url_name($url_id) {
	global $tables;

	if (!empty($url_id)) {
		$result = cw_query_first_cell("
			SELECT custom_facet_url
			FROM $tables[clean_urls_custom_facet_urls]
			WHERE url_id = '$url_id'
		");

		if (!empty($result)) {
			return $result;
		}
	}

	return false;
}

/**
 * get custom facet url for clean urls combination
 *
 * @param $urls
 * @param $converted_clean_urls - earlier converted clean urls
 * @return bool|string
 */
function cw_clean_url_get_custom_facet_url_from_combination($urls) {
    global $tables, $current_language, $config, $smarty;

    $language = !empty($current_language) ? $current_language : $config['default_customer_language'];

    if (!empty($urls)) {
        $urls = array_map('db_escape_string', $urls);

        $clean_urls_data = cw_query_hash("SELECT cav.*, 
                                                 IF(cav.item_type='AV',ad.value,m.manufacturer) as title, 
                                                 IF(cav.item_type='AV',ad.description,m.descr) as description,
                                                 IF(cav.item_type='AV',a_av.attribute_id,-1000) as attr_id,
                                                 IF(cav.item_type='AV',a_av.pf_orderby,-1000) as cu_pos 
                                          FROM $tables[attributes_values] cav 
                                          INNER JOIN $tables[attributes] a ON 
                                              a.attribute_id=cav.attribute_id AND 
                                              a.addon='clean_urls' AND 
                                              a.field='clean_url' AND 
                                              a.item_type IN ('M','AV') AND 
                                              a.active=1 
                                          LEFT JOIN $tables[attributes_default] ad ON 
                                              ad.attribute_value_id = cav.item_id 
                                          LEFT JOIN $tables[manufacturers] m ON 
                                              cav.item_id=m.manufacturer_id
                                          LEFT JOIN $tables[attributes] a_av ON 
                                              ad.attribute_id = a_av.attribute_id  
                                          WHERE cav.value IN ('".implode("','", $urls)."') ORDER BY cu_pos",
                           'value',false);

        $clean_urls = array_keys($clean_urls_data);

        if (empty($clean_urls)) return;

        $not_used_urls = array_diff($urls, $clean_urls);

        $clean_urls_query = array();
        foreach ($clean_urls as $cu_str) {
            $clean_urls_query[] = "$tables[clean_urls_custom_facet_urls_options].clean_urls LIKE '%|$cu_str|%'";
            $clean_urls_query[] = "$tables[clean_urls_custom_facet_urls_options].clean_urls LIKE '$cu_str|%'";
            $clean_urls_query[] = "$tables[clean_urls_custom_facet_urls_options].clean_urls LIKE '%|$cu_str'";
            $clean_urls_query[] = "$tables[clean_urls_custom_facet_urls_options].clean_urls = '$cu_str'";
        }
        if (!empty($clean_urls_query))
            $clean_urls_query_str = " AND (".implode(' OR ', $clean_urls_query).")";

        $result = cw_query($s = "
            SELECT $tables[clean_urls_custom_facet_urls].*, $tables[clean_urls_custom_facet_urls_options].clean_urls, $tables[clean_urls_custom_facet_urls_options].attribute_value_ids 
            FROM $tables[clean_urls_custom_facet_urls_options], $tables[clean_urls_custom_facet_urls]
            WHERE $tables[clean_urls_custom_facet_urls_options].url_id = $tables[clean_urls_custom_facet_urls].url_id $clean_urls_query_str");

        $exact_match = array();

        if (!empty($result)) {
            foreach ($result as $url) {
                $facet_urls = explode('|', $url['clean_urls']);
                if (count($facet_urls) != count($clean_urls)) continue;
                $match_diff = array_diff($facet_urls, $clean_urls);
                if (empty($match_diff)) {
                    $exact_match = $url;
                }
            }
        }
        if (!empty($exact_match)) {
            $exact_match['type'] = 'best_match';
            $smarty->assign('current_facet_category', $exact_match);
        } else {
            $titles = array();
            $descriptions = array();
            $custom_facet_urls = cw_query_hash("SELECT 
                                             $tables[clean_urls_custom_facet_urls].*, 
                                             $tables[clean_urls_custom_facet_urls_options].clean_urls, 
                                             $tables[clean_urls_custom_facet_urls_options].attribute_value_ids 
                                           FROM $tables[clean_urls_custom_facet_urls_options], $tables[clean_urls_custom_facet_urls]
                                           WHERE 
                                             $tables[clean_urls_custom_facet_urls_options].url_id = $tables[clean_urls_custom_facet_urls].url_id 
                                           AND $tables[clean_urls_custom_facet_urls_options].clean_urls IN ('".implode("','", $clean_urls)."')",
                                         'clean_urls',false);

            $clean_url_attribute_value_ids = array();

            foreach ($clean_urls_data as $clean_url => $cu_data) {
                if (!empty($custom_facet_urls[$clean_url])) {

                    if (!empty($custom_facet_urls[$clean_url]['title']))
                        $titles[] = $custom_facet_urls[$clean_url]['title'];

                    if (!empty($custom_facet_urls[$clean_url]['description']))
                        $descriptions[] = $custom_facet_urls[$clean_url]['description'];

                } else {

                    if (!empty($cu_data['title']))
                        $titles[] = $cu_data['title'];

                    if (!empty($cu_data['description']))
                        $descriptions[] = $cu_data['description'];

                }
                if ($cu_data['item_type'] == 'AV')
                    $clean_url_attribute_value_ids[] = $cu_data['item_id'];

            }

            $synth_facet_category = array("description"=>implode("<br />", $descriptions), "title"=>implode(" ",$titles));

            $synth_facet_category['type'] = 'synth';
            $synth_facet_category['clean_urls'] = $clean_urls;

            $synth_facet_category['clean_url_attribute_value_ids'] = $clean_url_attribute_value_ids;

            if (count($clean_urls) > count($synth_facet_category['clean_url_attribute_value_ids']))
                unset($synth_facet_category['clean_url_attribute_value_ids']);
            else
                $synth_facet_category['clean_url_attribute_value_ids'] = implode(',', $synth_facet_category['clean_url_attribute_value_ids']);

            $smarty->assign('current_facet_category', $synth_facet_category);
        }

        if (!empty($exact_match['custom_facet_url']))  {
            return implode('/', $not_used_urls) . '/' . $exact_match['custom_facet_url'];
        }

    }

    return false;
}


/*
 * return custom_facet_urls info for given manufacturer
 */
function cw_clean_url_manufacturers_categories($manufacturer_id) {
	global $tables;
	$manufacturer_id = intval($manufacturer_id);
	$fcats = cw_query("SELECT fu.*, mc.pos
		FROM $tables[manufacturers_categories] mc,  $tables[clean_urls_custom_facet_urls] fu
		WHERE mc.manufacturer_id='$manufacturer_id' AND mc.url_id=fu.url_id ORDER BY mc.pos");
	
	foreach ($fcats as $k=>$fcat) {
		$fcats[$k]['title'] = cw_clean_url_get_custom_facet_url_title($fcat['url_id']);
	}
	
	return $fcats;
}

/*
 * return facet category title - imploded attribute values
 * 
 * @params:
 * (int|string) url 
 * 		numeric url considered as url_id
 * 		string url considered as custom_facet_url
 * @return:
 *  (string) title
 */
function cw_clean_url_get_custom_facet_url_title($url) {
	global $tables;
	
	$field = (is_numeric($url)?'url_id':'custom_facet_url');
	$url = addslashes($url);
	
	$att_ids = cw_query_first_cell("SELECT fuo.attribute_value_ids 
		FROM  $tables[clean_urls_custom_facet_urls] fu, $tables[clean_urls_custom_facet_urls] fuo
		WHERE fu.$field='$url' AND fu.url_id=fuo.url_id");

	$titles = cw_query_column("SELECT ad.value as title
			FROM $tables[attributes] a, $tables[attributes_default] ad
			WHERE ad.attribute_value_id IN ($att_ids) AND
				ad.attribute_id = a.attribute_id
			ORDER BY a.pf_orderby, ad.orderby");
	return implode(' ', $titles);
}

/**
 * Event handler.
 * Check if current page meets CMS url restriction
 * 
 * @see event on_cms_check_restrictions
 * 
 * @return bool
 */
function cw_clean_url_on_cms_check_restrictions_URL($data) {
	 global $tables, $app_web_dir, $request_prepared;
	 
	$current_destination = str_replace($app_web_dir.'/','', $request_prepared['REDIRECT_URL']);
	$urls = cw_ab_get_cms_clean_urls($data['contentsection_id']);
	if (!empty($urls) && is_array($urls)) {
		if(!cw_query_first_cell("SELECT count(*) FROM $tables[cms_restrictions] WHERE contentsection_id = '".$data['contentsection_id']."' AND object_type='URL' AND value='".$current_destination."'")){
			return false;
		}
	}
	return true;

}

/**
 * Event handler.
 * Update clean urls for attribute values
 * 
 * @see event on_after_attribute_options_modify_att_options as on_after_<target>_<action> in cw.core.php:cw_header_location();
 * 
 */
function cw_clean_url_on_attribute_options() {
	global $posted_data, $edited_language;
	
	$attribute_id = $_POST['attribute_id'];
	
	if ($attribute_id) {
		$attribute = cw_func_call('cw_attributes_get_attribute', array('attribute_id' => $attribute_id, 'language' => $edited_language));
		foreach($attribute as $field => $value) {
			if (!is_array($value) && !isset($posted_data[$field])) $posted_data[$field] = addslashes($value);
		}
		cw_func_call('cw_clean_url_attributes_create_attribute', array('attribute_id' => $posted_data['attribute_id'], 'data'=> $posted_data, 'language' => $edited_language));
	}

}
/**
 * Translate dynamic/mixed URL into SEO friendly clean URL
 * 
 * @param string $url - mixed url
 * eg http://domain.com/cw/index.php?target=search&mode=search&att[A][]=AV&att[B][]=value&att[C][]=CV&page=2&sort=name
 * 
 * @return string $url - dynamic url 
 * http://domain.com/cw/CustomFacetUrl/AttributeA/AttributeB-value?page=2&sort=name
 */
 
function cw_clean_url_get_seo_url($mixed_url) {
    global $current_language, $tables;
    
    cw_load('attributes');
    
    $url = parse_url($mixed_url);
    //if (empty($url['query'])) return rtrim($mixed_url,'?'); // URL does not contain any dynamic part - return as is.
    
    $dynamic_url = cw_clean_url_get_dynamic_url($mixed_url); // Make absolute dynamic url from mixed seo/dynamic 

    $url = parse_url($dynamic_url);
    parse_str($url['query'], $params);
    
    $clean_urls = explode('/',$url['path']);
    if (count($clean_urls)) {
        foreach($clean_urls as $k=>$u) {
            if ($u=='' || $u=='index.php') unset($clean_urls[$k]);
        }
    }
    
    // Translate custom dynamic URL into custom static URL
    // Currently the only get params are translated
    $attributeO  = cw_call('cw_attributes_filter',array(array('field'=>'clean_url','item_type'=>'O'), true, 'attribute_id'));
    $all_dynamic_urls = cw_query("
    SELECT item_id, value 
    FROM $tables[attributes_values] 
    WHERE
        attribute_id = '$attributeO' AND
        code = '$current_language' AND
        item_type = 'OS'");
    foreach ($all_dynamic_urls as $dynamic_url) {
        $parsed_dynamic_url = parse_url($dynamic_url['value']);
        if (empty($parsed_dynamic_url['query'])) continue;
        parse_str($parsed_dynamic_url['query'], $_paramsC);
        $params_match = true;
        foreach ($_paramsC as $k=>$v) {
            $params_match &= ($params[$k] == $v);
        }
        if ($params_match) { // All get params exist and match
            $static_url = cw_query_first_cell("
            SELECT value
            FROM $tables[attributes_values]
            WHERE 
                attribute_id='$attributeO' AND 
                item_type='O' AND
                item_id = '".$dynamic_url['item_id']."'
            ");
            if ($static_url) {
                // Add clean URL and unset matched get params
                $clean_urls[] = $static_url;
                foreach ($_paramsC as $k=>$v) {
                    unset($params[$k]);
                }
            }
        }
    }

    // Translate clean urls by type
    // Order of params is important - clean_urls parts will be added in this order
    $param_types  = array(
        'page_id' => 'AB',
        'cat'=> 'C',
        'manufacturer_id' => 'M',
        'product_id' => 'P',
    );
    foreach ($param_types as $k=>$it) {
        if (isset($params[$k])) {
            $v = $params[$k];
            $att = cw_call('cw_attributes_filter', array(array('field'=>'clean_url','item_type'=>$param_types[$k]), true));
            $_clean_url = cw_call('cw_attribute_get_value',array($att['attribute_id'], $v));
            if ($_clean_url) {
                $clean_urls[] = $_clean_url;
                unset($params[$k]);
                unset($params['target']);
            }
        }
    }
    // Special case for target=search&mode=search
    if (isset($params['target']) && $params['target'] == 'search'
        && isset($params['mode']) && $params['mode'] == 'search') {
            $clean_urls[] = 'search';
            unset($params['mode'], $params['target']);
    }

    // Translate custom facet urls and complex att urls
    if ($params['att']) {
        $att_clean_url = cw_call('cw_clean_url_generate_filter_url', array(array(), array('att'=>$params['att']), false));
        if (!empty($att_clean_url)) {
            list($att_clean_url) = explode('?',$att_clean_url);
            $clean_urls = array_merge($clean_urls,explode('/',$att_clean_url));
            unset($params['att']);
        }
    }
    
 
    $url['query'] = urldecode(http_build_query($params));
    $scheme   = isset($url['scheme']) ? $url['scheme'] . '://' : '';
    $host     = isset($url['host']) ? $url['host'] : ''; 
    $path     = !empty($clean_urls)? '/'.join('/', $clean_urls):'';
    $query    = !empty($url['query']) ? '?' . $url['query'] : '';
      
    return $scheme.$host.$path.$query;    
    
}


/**
 * Translate seo/mixed URL to dynamic
 * 
 * @param string $url - mixed url
 *  http://domain.com/cw/CustomFacetUrl/AttributeA/AttributeB-value?page=2&sort=name
 * 
 * @return string $url - dynamic url 
 * http://domain.com/cw/index.php?target=search&mode=search&att[A][]=AV&att[B][]=value&att[C][]=CV&page=2&sort=name
 */
function cw_clean_url_get_dynamic_url($mixed_url) {
    global $tables, $app_web_dir;
    cw_load('attributes');
    $clean_urls = $params0 = $paramsC = $paramsF = $paramsS = $params1 = array();
    
    $url = parse_url($mixed_url);
    parse_str($url['query'], $params0);
    $clean_url = $url['path'];

    if (strlen($app_web_dir) && strpos($url['path'],$app_web_dir)===0) {
        $clean_url = substr($url['path'], strlen($app_web_dir));
    }

    $clean_url = trim($clean_url, '/');
    if (!empty($clean_url)) $clean_urls = explode('/', $clean_url);

   // Not recognized parts of clean URL can be processed by addons to convert into dynamic params
   // Addons must delete or transform recognized clean url parts in $clean_urls (passed by reference).
   // Please take care about other handlers by using  cw_get_return() in begining
   $paramsAddons = cw_event('on_get_dynamic_url', array(&$clean_urls));
   if (!is_array($paramsAddons)) $paramsAddons = array();

    // Translate history URLs to actual
    if (count($clean_urls)) {
        foreach($clean_urls as $k=>$u) {
            $clean_urls[$k] = cw_clean_url_get_url_by_history_url($u);
        }
    }
    
    // Translate custom defined urls
    if (count($clean_urls)) {
        $attributeO  = cw_call('cw_attributes_filter',array(array('field'=>'clean_url','item_type'=>'O'), true));
        //$attributeOS = cw_call('cw_attributes_filter',array(array('field'=>'clean_url','item_type'=>'OS'), true));
        $_clean_urls = array();

        foreach($clean_urls as $k=>$u) {
            $static_url =  cw_query_first_cell("
            SELECT item_id
            FROM $tables[attributes_values]
            WHERE attribute_id='$attributeO[attribute_id]' AND item_type='O' AND
            value = '".db_escape_string($u)."'
            ");
            if ($static_url) {
                $dynamic_url = cw_query_first_cell("
                SELECT value
                FROM $tables[attributes_values]
                WHERE attribute_id='$attributeO[attribute_id]' AND item_type='OS' AND
                item_id = $static_url ");
                
                $parsed_dynamic_url = parse_url($dynamic_url);
                parse_str($parsed_dynamic_url['query'], $_paramsC);
                $paramsC = array_merge($paramsC, $_paramsC);
                unset($clean_urls[$k]);
                $_clean_urls = array_merge($_clean_urls, explode('/', trim($parsed_dynamic_url['path'],'/')));
            }
        }
        $clean_urls = array_merge($clean_urls, $_clean_urls);
        unset($_clean_urls);
    }
    
   if (count($clean_urls)) {
        foreach($clean_urls as $k=>$u) {
            if ($u=='' || $u=='index.php') unset($clean_urls[$k]);
        }
   }
    // Translate clean urls
    if (count($clean_urls)) {
        $item_type_params = array(
            'P' => array('target' => 'product', 'param' => 'product_id'),
            'C' => array('target' => 'index', 'param' => 'cat'),
            'M' => array('target' => 'manufacturers', 'param' => 'manufacturer_id'),
            'AB' => array('target' => 'pages', 'param' => 'page_id'),
        );
        
        $attributes = cw_call('cw_attributes_filter', array(array('field'=>'clean_url')));
        $att_ids = array_column($attributes,'attribute_id'); // All attribute ids which mean clean urls

        foreach($clean_urls as $k=>$u) {
            $data = cw_query_first("SELECT item_id, attribute_id, item_type FROM $tables[attributes_values]
            WHERE attribute_id IN (".join(',',$att_ids).") AND
            value = '".db_escape_string($u)."'");
            
            $found = true;
            
            if ($item_type_params[$data['item_type']]) {
                $params1['target'] = $item_type_params[$data['item_type']]['target'];
                $params1[$item_type_params[$data['item_type']]['param']] = $data['item_id'];
            } elseif ($data['item_type']=='Q') {
                $params1['target'] = 'search';
                $params1['mode'] = 'search';
            } elseif ($data['item_type'] == 'AV') {
                
                $attribute_value_id = $data['item_id'];

                $attribute_id = cw_query_first_cell(
                    "SELECT attribute_id
                    FROM $tables[attributes_default]
                    WHERE attribute_value_id = '$attribute_value_id'"
                );

                if (!empty($attribute_id)) {
                    $params1['att'][$attribute_id][$data['item_id']] = $data['item_id'];
                }
     
            } else {
                $found = false;
            }
            
            if ($found) {
                unset($clean_urls[$k]);
            }
        }
    }
    
   // Translate custom facet urls
   if (count($clean_urls)) {
        foreach($clean_urls as $k=>$u) {
            $value_ids = cw_query_first_cell("
                SELECT GROUP_CONCAT(fuo.attribute_value_ids SEPARATOR ',') as ids
                FROM $tables[clean_urls_custom_facet_urls] fu, $tables[clean_urls_custom_facet_urls_options] fuo
                WHERE fu.custom_facet_url = '".addslashes($u)."' and fu.url_id=fuo.url_id
                GROUP BY fu.url_id
            ");
            if ($value_ids) {
                $att_params = cw_query("SELECT attribute_id, attribute_value_id 
                    FROM $tables[attributes_default] WHERE attribute_value_id IN ($value_ids)");
                foreach ($att_params as $att_param) {
                    $paramsF['att'][$att_param['attribute_id']][$att_param['attribute_value_id']] = $att_param['attribute_value_id'];
                }
                unset($clean_urls[$k]);
            }
        }
   }
   
   // Translate complex URL with attribute and value
   if (count($clean_urls)) {
        $attributePA = cw_call('cw_attributes_filter', array(array('field'=>'clean_url','item_type'=>'PA'),true));
        foreach($clean_urls as $k=>$u) {
            // Detect data
            $clean_url_values = array();    // values for range or select attributes
            $clean_url_parts = explode('-', $u);
            
            if (count($clean_url_parts) == 1) continue;
            
            // for substring use special code
            if ($clean_url_parts[0] == 'substring') {
                unset($clean_url_parts[0]);
                $paramsS['att']['substring'] = implode('-', $clean_url_parts);
                unset($clean_urls[$k]);
                continue;
            }
            
            do {
                $data = cw_query_first(
                    "SELECT item_id, item_type
                    FROM $tables[attributes_values]
                    WHERE attribute_id = $attributePA[attribute_id] AND 
                    value = '" . addslashes(implode('-', $clean_url_parts)) . "'");

                if (empty($data)) {
                    array_unshift($clean_url_values, array_pop($clean_url_parts));
                }
            } while (empty($data) && count($clean_url_parts));
            
            if (empty($data)) continue;

            $attribute = cw_call('cw_attributes_filter', array(array('attribute_id'=>$data['item_id']),true));

            if (in_array($attribute['type'], array("decimal", "integer"))) {
                $paramsS['att'][$attribute['attribute_id']]['min'] = $clean_url_values[0];
                $paramsS['att'][$attribute['attribute_id']]['max'] = $clean_url_values[1];
            } elseif ($attribute['type'] == "manufacturer-selector") {
                $attributeM = cw_call('cw_attributes_filter', array(array('field'=>'clean_url','item_type'=>'M'),true));
                $manufacturer_item_id = cw_query_first_cell(
                    "SELECT av.item_id
                    FROM $tables[attributes_values] av
                    WHERE av.attribute_id=$attributeM[attribute_id] 
                        AND av.value = '" . addslashes(implode('-', $clean_url_values)) . "'"
                );
                $paramsS['att'][$attribute['attribute_id']][$manufacturer_item_id] = $manufacturer_item_id;
            }
            else {
                $paramsS['att'][$attribute['attribute_id']][] = implode('-', $clean_url_values);
            }

            unset($clean_urls[$k]);
        }
    }

   $result = array_replace_recursive($paramsC, $paramsF, $params1, $paramsS, (array)$paramsAddons,$params0);
   
   if (isset($result['att']) && is_array($result['att'])) {
   foreach ($result['att'] as $attr_id => $a) {
       $result['att'][$attr_id] = array_filter($a);
   }
   }
   //cw_var_dump($result,$paramsC, $paramsF, $params1, $paramsS, $paramsAddons, $params0);
   $url['query'] = urldecode(http_build_query($result));
  $scheme   = isset($url['scheme']) ? $url['scheme'] . '://' : '';
  $host     = isset($url['host']) ? $url['host'] : ''; 
  $path     = isset($url['path']) ? ((!empty($app_web_dir) && strpos($url['path'],$app_web_dir)===0)?$app_web_dir:'').join('/',$clean_urls).'/index.php' : '';
  $query    = isset($url['query']) ? '?' . $url['query'] : '';
  
   return $scheme.$host.$path.$query;

}

function cw_clean_url_alt_tags($item_id, $image_type) {
    global $tables;

    $result = cw_query_first_cell("select av.value from $tables[attributes_values] av, $tables[attributes] a where a.addon='clean_urls' and a.attribute_id=av.attribute_id and a.field='".$image_type."_alt' and av.item_id='$item_id'");

    if (empty($result)) {

        $result = cw_call('cw_clean_url_default_alt_tags', array($item_id, $image_type));

    }

    return $result;
}

function cw_clean_url_default_alt_tags($item_id, $image_type) {

    global $smarty, $user_account;

    $result = '';

    if (in_array($image_type, array('products_images_thumb', 'products_images_det'))) {

        $product = $smarty->_tpl_vars['product'];

        if (empty($product) || $product['product_id'] != $item_id) 
            $product = cw_func_call('cw_product_get', array('id' => $item_id, 'user_account' => $user_account, 'info_type' => 0));   
 
        if (!empty($product))                  
            $result = $product['product'];

    }
    return $result;
}

/**
 * Mass clean url creation
 * !!! it removes all clean URLs history and current URLs for specified types
 * 
 * @param array $item_types_param = any combination ['P','C','M','AB','PA','AV']
 *  AV (Attributes Values) can be generated only together with PA (Product Attributes) - ['PA','AV']
 *
 */
function cw_clean_url_generate_all($item_types_param = array()) {
    global $current_language, $tables;
    
    $fields = array(
        'P' => array('field' => 'product', 'table' => $tables['products'], 'key' => 'product_id'),
        'C' => array('field' => 'category', 'table' => $tables['categories'], 'key' => 'category_id'),
        'M' => array('field' => 'manufacturer', 'table' => $tables['manufacturers'], 'key' => 'manufacturer_id'),
        'AB' => array('field' => 'name', 'table' => $tables['cms'], 'key' => 'contentsection_id'),
        'PA' => array('field' => 'name', 'table' => $tables['attributes'], 'key' => 'attribute_id'),
        'AV' => array('field' => 'value', 'table' => $tables['attributes_default'], 'key' => 'attribute_value_id', 'extra_fields' => 'attribute_id'),

    );
    
    if (!empty($item_types_param)) {
        $item_types = array_intersect(array_keys($fields), $item_types_param);
    } else {
        $item_types =  array_keys($fields);
    }

    // First we need to clean all old URLs
    foreach ($item_types as $item_type) {
        $clean_url_attribute_id = cw_call('cw_attributes_filter', array(array('field'=>'clean_url', 'addon'=>'clean_urls','item_type' => $item_type), true, 'attribute_id'));
        db_query("DELETE FROM $tables[clean_urls_history] WHERE item_type='$item_type'");
        db_query("DELETE FROM $tables[attributes_values] WHERE attribute_id='$clean_url_attribute_id'");
    }
    
    // Then we can generate new URLs
    foreach ($item_types as $item_type) {

        $clean_url_attribute_id = cw_call('cw_attributes_filter', array(array('field'=>'clean_url', 'addon'=>'clean_urls','item_type' => $item_type), true, 'attribute_id'));

        $tp = $fields[$item_type];

        $items = cw_query("SELECT {$tp['key']}, {$tp['field']} ".($tp['extra_fields']?", {$tp['extra_fields']}":"")." FROM {$tp['table']}");
        foreach ($items as $item) {
            $item_id = $item[$tp['key']];
            $name = $item[$tp['field']];
/*
            if ($item_type == 'AV') {
                $parent_attribute_id = $item[$tp['extra_fields']];
                $name = $log['PA'][$parent_attribute_id].'-'.$name;
            }
*/
            $saved_clean_url = cw_call('cw_clean_url_check_url_and_save_value', array($name, $item_id, $item_type, $clean_url_attribute_id, $current_language));
            $log[$item_type][$item_id] =  $saved_clean_url;
        }

    }
    
//    cw_log_add('clean_url_generate_all', $log);
    return $log;
}

function cw_clean_url_get_html_redirect_types() {
    global $config, $tables;

    $result = array();
    foreach ($config['clean_urls'] as $name => $value) {
        if ($value == 'Y' && strpos($name, 'clean_urls_allow_html_') !== false)   {
            $result[] = str_replace('clean_urls_allow_html_','', $name);
        }  
    }

    return $result;
}

function cw_clean_url_html_attribute_ids() {
    global $tables;

    $html_redirect_types = cw_call('cw_clean_url_get_html_redirect_types', array());

//    $result = array('32','35');

    $result = cw_query_column("select attribute_id from $tables[attributes] where addon='clean_urls' and item_type in ('".implode("','", $html_redirect_types)."') and field='clean_url'"); 

    return $result;
}

function cw_clean_url_html_is_redirect($item_type, $clean_url) {
    $html_redirect_types = cw_call('cw_clean_url_get_html_redirect_types', array()); 

    $result = (in_array($item_type, $html_redirect_types) && (strtolower(substr($clean_url, -4)) != ".htm") && (strtolower(substr($clean_url, -5)) != ".html")); 

    return $result;  
}
