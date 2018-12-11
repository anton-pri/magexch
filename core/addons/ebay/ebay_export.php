<?php
namespace CW\ebay;

/* ======================================== */
/*
 * Controller init
 *
 */

cw_load('product', 'category', 'config', 'export');

global $mode;

if (empty($mode)) {
    $mode = 'index';
}

if (!in_array($mode, array('index', 'export', 'add_data', 'delete_file'), true))
    return false;

$smarty->assign('main', 'ebay_export');

// Call corresponding action
cw_call('CW\ebay\\' . $mode);

return true;

/* ======================================== */
/*
 * Implementation of actions
 *
 */

// Show export page
function index() {
    global $smarty, $config, $user_account, $tables;

    $smarty->assign('ebay_file_name', 'ebay_export_' .date('Ymd') . '_' . date('His'));
    $smarty->assign('ebay_location', cw_ebay_get_location());
    
    // ebay categories
    $smarty->assign('ebay_categories', cw_ebay_get_categories_list());
    
    $smarty->assign('ebay_avail_export_files', cw_ebay_get_avail_export_files_list());
}

function export() {
    global $REQUEST_METHOD, $smarty, $config, $addons, $top_message, $customer_id, $tables;
    global $mode, $action, $ebay_config;

    if ($REQUEST_METHOD != 'POST') cw_header_location('index.php?target=' . addon_target);

    $result = cw_ebay_check_fields($_POST);
    
    if (count($result)) {
    	$top_message = array('content' => implode("<br />", $result), 'type' => 'E');
    	cw_header_location('index.php?target=' . addon_target);
    }
    
    if (empty($_POST['file_name'])) {
    	$_filename = addon_files_location_path . 'ebay_export_' .date('Ymd') . '_' . date('His') . '.csv';
    }
    else {
    	$_filename = addon_files_location_path . $_POST['file_name'] . '.csv';
    }
    $_copy_filename = $_filename;

    if (
    	($filename = cw_allow_file($_filename, true))
    	&& $file = cw_fopen($_filename, 'w', true)
    ) {
	    $pids = cw_call('cw_objects_get_list_ids', array('P'));

	    if (empty($pids)) {
			$pids = cw_query_column("SELECT product_id FROM $tables[products] WHERE status=1"); // Very bad. Use API
	    }

	    if ($pids) {	
	        $ebay_config = cw_array_merge($ebay_config, $config['ebay'], $_POST);
	        cw_config_update('ebay', $_POST);


	        $data = array();
	        $header_put = false;
	        $count_files = 0;
	
	        foreach($pids as $v) {
	            $variants = array();
	            $prod = cw_func_call('cw_product_get', array('id' => $v, 'info_type' => 8|64|128|256|512|2048));	
	
	            $attr = cw_query_hash("SELECT a.field, av.value
	                    FROM $tables[attributes_values] av, $tables[attributes] a
	                    WHERE av.item_id=$v AND av.item_type='P' 
	                    	AND a.attribute_id=av.attribute_id", 'field', false, true); // very bad. Use API

	            if ($prod['is_variants']) {
	                $variants = cw_call('cw_get_product_variants', array($v));
	            }
	            else {
	                $variants[0] = $prod;
	            }

	            foreach ($variants as $var) {	
	                $var = cw_array_merge($var, $attr);

					$ebay_category = cw_ebay_get_category_value($prod['category_id'], $ebay_config['ebay_category']);
					
					if (!$ebay_category) {
						fclose($file);
						$top_message = array('content' => 'For <a href="index.php?target=categories&mode=edit&cat=' . $prod['category_id'] . '">Main category</a> for the product "' . $prod['product'] . '" option "Category" does not set.', 'type' => 'E');
						cw_header_location('index.php?target='.addon_target);
					}

	                if (!$header_put) {
	                	// Set smart headers
		                $data = array(
		                    'Action'            		=> $ebay_config['ebay_action'],
		                    'ImmediatePayRequired=' . ($ebay_config['ebay_immediate_pay_required'] == 'Y' ? "1" : "0") 	=> "",
		                    'Category'					=> $ebay_category,
		                    'ConditionID'				=> cw_ebay_get_condition_value($var, $ebay_config['ebay_condition_id']),
		                    'Description'				=> substr(nl2br($var['descr']), 0, 500000),
		                    'Duration=' . $ebay_config['ebay_duration'] 												=> "",
		                    'Format=' . $ebay_config['ebay_format']														=> "",
		                    'Location=' . $ebay_config['ebay_location']													=> "",
		                    'PayPalAccepted=' . ($ebay_config['ebay_paypal_accepted'] == 'Y' ? "1" : "0")				=> "",
		                    'PayPalEmailAddress'		=> $ebay_config['ebay_paypal_accepted'] == 'Y' ? $ebay_config['ebay_paypal_email_address'] : "",
		                    'Quantity'					=> $var['avail'],
		                    'Title'						=> substr($var['product'], 0, 80),
		                    'DispatchTimeMax=' . $ebay_config['ebay_dispatch_time_max']									=> "",
		                    'ReturnsAcceptedOption=' . $ebay_config['ebay_returns_accepted_option']						=> "",
		                    'StartPrice'				=> $var['price']
		                );
	                }
	                else {
		                $data = array(
		                    'Action'            		=> $ebay_config['ebay_action'],
		                    'ImmediatePayRequired'		=> "",
		                    'Category'					=> $ebay_category,
		                    'ConditionID'				=> cw_ebay_get_condition_value($var, $ebay_config['ebay_condition_id']),
		                    'Description'				=> substr(nl2br($var['descr']), 0, 500000),
		                    'Duration'					=> "",
		                    'Format'					=> "",
		                    'Location'					=> "",
		                    'PayPalAccepted'			=> "",
		                    'PayPalEmailAddress'		=> $ebay_config['ebay_paypal_accepted'] == 'Y' ? $ebay_config['ebay_paypal_email_address'] : "",
		                    'Quantity'					=> $var['avail'],
		                    'Title'						=> substr($var['product'], 0, 80),
		                    'DispatchTimeMax'			=> "",
		                    'ReturnsAcceptedOption'		=> "",
		                    'StartPrice'				=> $var['price']
		                );
	                }

	                if (!$header_put) {
	                    fputcsv($file, array_keys($data), ",");
	                    $header_put = true;
	                }

	                fputcsv($file, $data, ",");

	                // A single file cannot exceed 15 MB
	                if (filesize($_filename) > 14680064) {
	                	fclose($file);
	                	
	                	$new_filename = str_replace(".csv", "_" . $count_files . ".csv", $_copy_filename);
	                	rename($_filename, $new_filename);
	                	
	                	$count_files++;
	                	$_filename = str_replace(".csv", "_" . $count_files . ".csv", $_copy_filename);
	                	
	                	$file = cw_fopen($_filename, 'w', true);
	                	$header_put = false;
	                }
	            }
	        }
	    }

		fclose($file);
	    $top_message = array('content' => 'File <b>'.$_filename.'</b> successfully created');
	}

    cw_header_location('index.php?target='.addon_target);
}

// Add ConditionIDs by Category
function add_data() {
	global $top_message;

	$result = cw_ebay_add_data_from_file();

	$top_message = $result;
	cw_header_location('index.php?target=' . addon_target);
}

function delete_file() {
	global $current_location;

	cw_load('files');
	
	$_file = trim($_GET['file']);
	$_filename = addon_files_location_path . $_file;

	if (!empty($_file) && cw_allow_file($_filename)) {	
		unlink($_filename);
	}
	
	$content = "No files";
	$files = cw_ebay_get_avail_export_files_list();
	
	if (count($files)) {
		$content = "";

		foreach ($files as $file) {
			$content .= '<a href="' . $file['path'] . '">' . $file['name'] . '</a>&nbsp;<a href="javascript:delete_export_file(\'' . $file['name'] . '\');"><img src="' . $current_location . '/skins/images/delete_cross.gif"></a><br>';
		}
	}
	
	cw_add_ajax_block(array(
		'id' 		=> 'export_files_container',
		'action' 	=> 'update',
		'content' 	=> $content
    ));
}
/* ======================================== */
