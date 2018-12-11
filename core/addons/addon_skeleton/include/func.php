<?php
namespace cw\addon_skeleton;

/** =============================
 ** Addon functions, API
 ** =============================
 **/

/* Name your API function as you wish, better with some simple prefix. */

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
function sklt_get_by_id($id) {
	global $tables;
	$entry = cw_query_first("SELECT * FROM $tables[addon_skeleton] WHERE id='$id'");
	return $entry;
}


/** =============================
 ** Hooks
 ** =============================
 **/

/* Name your hooks as original functions. It is safe with namespace. 
 * Suffix 'pre' or 'post' can be added at your wish to emphasize the order of call
 */

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
function cw_original_hooked_function_pre($params) {
	
	// modify $params
	// make $result
	
	return new EventReturn($result, $params); // Replace input param for main function
}


/** =============================
 ** Events handlers
 ** =============================
 **/

/* Name your event handlers as original event name. It is safe with namespace. */


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

