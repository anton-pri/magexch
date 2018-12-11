<?php
/*
 * Export objects set
 */

// add objects to set
function cw_objects_add_to_set($objects, $type='P') {
	global $tables, $customer_id;
	
	if (
		is_array($objects)
		&& count($objects)
	) {
		$objects = array_unique($objects);
		$objects = array_filter($objects, function ($object) {
			return (!empty($object) && is_numeric($object)); 
		}); 
		
		if (count($objects)) {

			foreach ($objects as $object) {
				
                cw_array2insert(
                    "objects_set", 
                    array(
                        "object_id" 	=> $object, 
                        "customer_id" 	=> $customer_id, 
                        "set_type" 		=> $type
                    ),
                    true
                );
				
			}
			return TRUE;
		}
	}
	
	return FALSE;
}

// deleted objects from set
function cw_objects_delete_from_set($objects, $type='P') {
	global $tables, $customer_id;
	
	if (
		is_array($objects)
		&& count($objects)
	) {
		$objects = array_unique($objects);
		$objects = array_filter($objects, function ($object) {
			return (!empty($object) && is_numeric($object)); 
		}); 
		
		if (count($objects)) {
			$query = "DELETE FROM $tables[objects_set] 
						WHERE object_id IN (" . implode(',', $objects) . ") 
							AND customer_id = $customer_id 
							AND set_type = '$type'";
			db_query($query);
			return TRUE;
		}
	}
	
	return FALSE;
}

// get list of objects ids
function cw_objects_get_list_ids($type='P') {
	global $tables, $customer_id;
	
	$objects = array();

	$result = cw_query("SELECT object_id 
							FROM $tables[objects_set] 
							WHERE customer_id = $customer_id 
								AND set_type = '$type'");
	
	if (
		is_array($result)
		&& count($result)
	) {
		foreach ($result as $object) {
			$objects[] = $object['object_id'];
		}
	}
	
	return $objects;
}

// get count
function cw_objects_get_count($type='P') {
	global $tables, $customer_id;
	
	return cw_query_first_cell("SELECT count(object_id) 
									FROM $tables[objects_set] 
									WHERE customer_id = $customer_id 
										AND set_type = '$type'");
}

// reset
function cw_objects_reset($type='P') {
	global $tables, $customer_id;

	$query = "DELETE FROM $tables[objects_set] WHERE customer_id = $customer_id AND set_type = '$type'";

	return db_query($query) !== FALSE;
}

// check if object_id exists in set
function cw_objects_check_exist($object, $type='P') {	
	global $tables, $customer_id;
	
	if (is_numeric($object)) {	
		$query = "SELECT object_id 
					FROM $tables[objects_set] 
					WHERE object_id = $object 
						AND customer_id = $customer_id 
						AND set_type = '$type'";
		return cw_query_first_cell($query) !== FALSE;
	}
	
	return FALSE;
}
