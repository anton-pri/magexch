<?php
namespace cw\DataScraper;


function cw_datascraper_get_table_fields($tbl_name) {
    global $tables, $ds_table_fields;

    if (!isset($tables['datascraper_'.$tbl_name]))
        $tables['datascraper_'.$tbl_name] = 'cw_datascraper_'.$tbl_name;
    
    $tbl_fields = cw_check_field_names(array(), $tables['datascraper_'.$tbl_name]);

    $columns = array();

    if (!isset($ds_table_fields[$tbl_name]))
        $ds_table_fields[$tbl_name] = array();

    foreach ($tbl_fields as $field_name) {
        if (in_array($field_name, array_keys($ds_table_fields[$tbl_name]))) {
            if ($ds_table_fields[$tbl_name][$field_name]['disabled']) continue;
            $new_column = $ds_table_fields[$tbl_name][$field_name];
            $new_column['field'] = $field_name;
        } else { 
            $new_column = array(
                'field' => $field_name
            );  
        }
        $columns[] = $new_column;
    }
    return $columns;
}

function cw_datascraper_rebuild_tables($site_id = 0) {
    global $tables;

    if ($site_id) {
        $sites_limit = " AND siteid='$site_id'";
    } 

    $field_types_sql = array(
        'integer' => "int(11) NOT NULL DEFAULT 0",
        'decimal' => "decimal(12,2) NOT NULL DEFAULT 0",
        'image' => "varchar(255) NOT NULL DEFAULT ''", 
        'text' => "text NOT NULL DEFAULT ''",
        'default' => "varchar(255) NOT NULL DEFAULT ''" 
    );

    $sites2update = cw_query("SELECT * FROM $tables[datascraper_sites_config] WHERE 1 $sites_limit");
    foreach ($sites2update as $sites_data) {
        $site_fields = cw_query_hash("SELECT * FROM $tables[datascraper_attributes] WHERE site_id='$sites_data[siteid]'", 'name', 0, 0);
//print_r($site_fields);
        $site_results_tbl_orig = $tables['datascraper_result_values'].$sites_data['siteid'];
        $res_tbl_exists = cw_query_first("SELECT *  FROM information_schema.tables WHERE table_name = '$site_results_tbl_orig' LIMIT 1");
        if ($res_tbl_exists) {
            $site_results_tbl = $site_results_tbl_orig.'_new'; 
        } else {
            $site_results_tbl = $site_results_tbl_orig;
        } 
        $fields_defs = array();
        $fields_defs[] = "result_id int(11) NOT NULL AUTO_INCREMENT";  
        $fields_defs[] = "url varchar(255) NOT NULL DEFAULT ''";  
        $fields_defs[] = "date_scraped int(11) NOT NULL DEFAULT 0";
        foreach ($site_fields as $fname => $fdata) {
            if (in_array($fdata['type'], array_keys($field_types_sql)))  
                $_ftype = $field_types_sql[$fdata['type']]; 
            else 
                $_ftype = $field_types_sql['default'];

            $fields_defs[] = "`$fname` $_ftype";
        }
        $fields_defs[] = "PRIMARY KEY(result_id)"; 
         
        $create_table_qry = "CREATE TABLE `$site_results_tbl` (".implode(', ', $fields_defs).");"; 
//print($create_table_qry); 
        db_query("DROP TABLE IF EXISTS `$site_results_tbl`");
        db_query($create_table_qry);

        $common_fields = array();

        if ($site_results_tbl != $site_results_tbl_orig) { 
            $old_res_vals_fields = cw_check_field_names(array(), $site_results_tbl_orig);      
            $new_res_vals_fields = cw_check_field_names(array(), $site_results_tbl);
            $common_fields = array_intersect($old_res_vals_fields, $new_res_vals_fields);
/*
print_r(array('common'=>$common_fields));
print_r(array('old_res_vals_fields'=>$old_res_vals_fields));
print_r(array('new_res_vals_fields'=>$new_res_vals_fields));
*/

            $move_data_qry = "INSERT INTO `$site_results_tbl` (`".implode("`,`", $common_fields)."`) SELECT `".implode("`,`", $common_fields)."` FROM `$site_results_tbl_orig`";
            db_query($move_data_qry); 
            db_query("DROP TABLE IF EXISTS `$site_results_tbl_orig`");
            db_query("RENAME TABLE `$site_results_tbl` TO `$site_results_tbl_orig`");
        }
        print("Rebuilt table for site <b>$sites_data[name]</b>,".((!empty($common_fields))?(" saved values of fields: `".implode("`,`", $common_fields)."`"):" table <b>$site_results_tbl</b> is built from scratch")." <br>\n<br>\n");
    }

}

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

