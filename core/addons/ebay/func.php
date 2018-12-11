<?php
namespace CW\ebay;

function cw_ebay_add_data_from_file() {
	global $tables;

	$path = addon_files_location_path . addon_conditions_data_file_name;

	if (!file_exists($path) || !is_readable($path)) {
		return array('content' => 'File "' . addon_conditions_data_file_name . '" does not exist or not readable.', 'type' => 'E');
	}

	if (($handle = fopen($path, "r")) !== FALSE) {
		$attribute_id = cw_query_first_cell("SELECT attribute_id FROM $tables[attributes] WHERE field = 'ebay_category' AND addon = 'ebay'");

		if (empty($attribute_id)) {
			fclose($handle);
			return array('content' => 'Error in attribute "ebay Category". Wrong parameter ID.', 'type' => 'E');
		}

		db_query("DELETE FROM $tables[attributes_default] WHERE attribute_id = " . $attribute_id);

		// Prepare attributes data
		$attributes = array();
		$attributes[""] =  
			array(
				"value" 	=> "-- Inherited from parent --", 
				"value_key" => 0
			);
			
		while (($data = fgetcsv($handle)) !== FALSE) {
			$category_id 	= intval($data[0]);

			if (!is_numeric($category_id) || $category_id <= 0) continue;

			$category_name 	= addslashes(trim($data[1]));

			$attributes[$category_name] =  
				array(
					"value" 	=> $category_name, 
					"value_key" => $category_id
				);
		}
		fclose($handle);

		if (count($attributes)) {
			ksort($attributes);
			$orderby = 0;
			// Add to cw_attributes_default
			foreach ($attributes as $attribute) {
				cw_array2insert(
					"attributes_default", 
					array(
						"value" 		=> $attribute["value"], 
						"value_key" 	=> $attribute["value_key"], 
						"attribute_id" 	=> $attribute_id,
						"orderby" 		=> $orderby++,
						"active" 		=> 1
					)
				);
			}
		}
	}
	else {
		return array('content' => 'File "' . addon_conditions_data_file_name . '" not readable.', 'type' => 'E');
	}
	
	return array('content' => 'The operation completed successfully.', 'type' => 'I');
}

function cw_ebay_get_categories_list() {
	global $tables;

    $ebay_categories[] = array("id" => "", "name" => "Not specified");
    $attribute_id = cw_query_first_cell("SELECT attribute_id FROM $tables[attributes] WHERE field = 'ebay_category' AND addon = 'ebay'");
    
    if (!empty($attribute_id)) {
    	$categories = cw_query("SELECT value_key, value FROM $tables[attributes_default] WHERE attribute_id = $attribute_id ORDER BY value");
    	
    	if (!empty($categories)) {
    		$ebay_categories = array();

    		foreach ($categories as $category) {
                $_cats = explode("|", $category['value']);
    			$ebay_categories[] = array("id" => $category['value_key'], "name" =>  $_cats[0]);
    		}
    	}
    }
    
    return $ebay_categories;
}

function cw_ebay_get_condition_value($var, $default_value) {
	global $tables;

	if (
		isset($var['ebay_condition_id']) 
		&& is_numeric($var['ebay_condition_id'])
	) {
		$condition_value = cw_query_first_cell("SELECT value_key FROM $tables[attributes_default] WHERE attribute_value_id = " . $var['ebay_condition_id']);
		
		if (empty($condition_value)) {
			return $default_value;
		}
	}
	else {
		return $default_value;
	}
	
	return $condition_value;
}

function cw_ebay_get_category_value($category_id, $default_value) {
	global $tables;
	
	if (empty($category_id)) {
		return empty($default_value) ? FALSE : $default_value;
	}
	
	$attr = cw_query_hash("SELECT a.field, av.value
		                    FROM $tables[attributes_values] av, $tables[attributes] a
		                    WHERE av.item_id=$category_id 
		                    	AND av.item_type='C' 
		                    	AND a.attribute_id=av.attribute_id
		                    	AND a.field='ebay_category'", 'field', false, true);
	
	$ebay_category = FALSE;
	if (isset($attr['ebay_category']) && !empty($attr['ebay_category'])) {
		$ebay_category = $attr['ebay_category'];
	}
	else {
		// Get parent categories
		$parent_categories = array();
		cw_category_generate_path($category_id, $parent_categories);
		
		if (count($parent_categories)) {
			
			foreach ($parent_categories as $parent_category_id) {
				$attr = cw_query_hash("SELECT a.field, av.value
					                    FROM $tables[attributes_values] av, $tables[attributes] a
					                    WHERE av.item_id=$parent_category_id 
					                    	AND av.item_type='C' 
					                    	AND a.attribute_id=av.attribute_id
					                    	AND a.field='ebay_category'", 'field', false, true);

				if (isset($attr['ebay_category']) && !empty($attr['ebay_category'])) {
					$ebay_category = $attr['ebay_category'];
					break;
				}
			}
		}
	}

	if ($ebay_category) {
		$category_value = cw_query_hash("SELECT attribute_value_id, value_key FROM $tables[attributes_default] WHERE attribute_value_id = " . $ebay_category, 'attribute_value_id', false, true);
		
		if (empty($category_value[$ebay_category])) {
			return empty($default_value) ? FALSE : $default_value;
		}
		
		return $category_value[$ebay_category];
	}
	
	return empty($default_value) ? FALSE : $default_value;
}

function cw_ebay_get_location() {
    global $config;
    
    if (!empty($config['ebay']['ebay_location'])) {
    	$location = $config['ebay']['ebay_location'];
    }
    else {
    	$location = isset($config['Company']['zipcode']) ? $config['Company']['zipcode'] : $config['Company']['city'] . "," . $config['Company']['state'] . "," . $config['Company']['country'];
    }
    
    return $location;
}

function cw_ebay_check_fields($fields) {
	cw_load('mail');
	
	$errors = array();

	if (
		empty($fields['ebay_duration']) 
		|| !is_numeric($fields['ebay_duration'])
		|| strlen($fields['ebay_duration']) > 3
	) {
		$errors[] = 'Error in field "Duration".';
	}

	if (
		empty($fields['ebay_location']) 
		|| strlen($fields['ebay_location']) > 45
	) {
		$errors[] = 'Error in field "Location".';
	}

	if (
		$fields['ebay_paypal_accepted'] == 'Y' 
		&& !cw_check_email($fields['ebay_paypal_email_address'])
	) {
		$errors[] = 'Error in field "PayPal Email address".';
	}
	
	return $errors;
}

function cw_ebay_get_avail_export_files_list() {
	global $current_location; 

	cw_load('files');
	
	$files_list = array();
	$files = cw_files_get_dir(trim(addon_files_location_path, "/"));

	if (is_array($files)) {
		
		foreach ($files as $file) {
            $paths = explode("/", $file);
			$file_name = array_pop($paths);
			$files_list[] = array("path" => $current_location . "/" . $file, "name" => $file_name);
		}
	}
	
	return $files_list;
}
