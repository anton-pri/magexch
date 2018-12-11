<?php
namespace cw\addon_skeleton;

/** =============================
 ** Addon functions, API
 ** =============================
 **/

/**
 * what function does
 * documentation according 
 * http://manual.phpdoc.org/HTMLSmartyConverter/HandS/phpDocumentor/tutorial_phpDocumentor.pkg.html
 * 
 * example of basic @param usage
 * 
 * @param datatype $paramname description
 * @param datatype1|datatype2 $paramname description
 * @return array
 */
function get_by_id($id) {
	global $tables;
	$entry = cw_query_first("SELECT * FROM $tables[addon_skeleton] WHERE id='$id'");
	return $entry;
}


/** =============================
 ** Hooks
 ** =============================
 **/

/**
 * what function does
 * 
 * @see POST hook for cw_original_hooked_function
 * ...
 */
function cw_original_hooked_function($param1, $param2) {
	$return = cw_get_return(); // Get return of previous hook
	
	// Do something usgin $return
	
	return $return; // Do not forget return same value
}

/**
 * ...
 * @see PRE hook for cw_original_hooked_function
 * ...
 */
function my_pre_hook($params) {
	
	// modify $params
	// make $result
	
	return new EventReturn($result, $params); // Replace input param for main function
}


/** =============================
 ** Events handlers
 ** =============================
 **/

/**
 * what function does
 * 
 * @see event on_login
 * 
 * @param integer $customer_id - logged in customer_id
 * @param char(1) $area - area
 * @param integer $on_register - 1|0 context where login occured, during registration or by returning customer
 * 
 * @return null
 */
function on_login($customer_id, $area, $on_register=0) {
	// Do something after login
}

