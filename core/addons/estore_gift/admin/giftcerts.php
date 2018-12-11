<?php
global $app_dir, $app_skin_dir, $app_main_dir, $tables, $config, $smarty;

cw_load('map', 'doc');
$top_message = &cw_session_register('top_message');

if ($REQUEST_METHOD == "POST") {

	if (
		$action == "add_gc" 
		|| $action == "modify_gc" 
		|| $action == "preview"
	) {
		$fill_error = (empty($purchaser) || empty($recipient_to));
	    $amount_error = (
	    	($amount < $config['estore_gift']['min_gc_amount']) 
	    	|| ($config['estore_gift']['max_gc_amount'] > 0 && $amount > $config['estore_gift']['max_gc_amount'])
	    );

		if (
            $config['estore_gift']['allow_customer_select_tpl'] != 'Y'
            || cw_gift_wrong_template($gc_template)
        ) {
            $gc_template = $config['estore_gift']['default_giftcert_template'];
        }

        $gc_template = stripslashes($gc_template);

	    if ($send_via == "E") {
	        // Send via Email
	        $fill_error = ($fill_error || empty($recipient_email));

			$giftcert = array(
				'purchaser'			=> stripslashes($purchaser),
				'recipient'			=> stripslashes($recipient_to),
				'message' 			=> stripslashes($message),
				'amount' 			=> $amount,
				'debit' 			=> $amount,
				'send_via' 			=> $send_via,
				'tpl_file' 			=> stripslashes($gc_template),
            	'recipient_email' 	=> $recipient_email
			);
	    }
	    else {
	        // Send via Postal Mail
	        $has_states = (cw_query_first_cell("SELECT display_states FROM $tables[map_countries] WHERE code = '" . $recipient['country'] . "'") == 'Y');
	        $fill_error = (
	        	$fill_error 
	        	|| empty($recipient_firstname) 
	        	|| empty($recipient_lastname) 
	        	|| empty($recipient_address) 
	        	|| empty($recipient_city) 
	        	|| empty($recipient_zipcode) 
	        	|| (empty($recipient['state']) && $has_states) 
	        	|| empty($recipient['country']) 
	        );
	
	        $giftcert = array (
	            "purchaser" 			=> stripslashes($purchaser),
	            "recipient" 			=> stripslashes($recipient_to),
	            "message" 				=> stripslashes($message),
	            "amount" 				=> $amount,
	        	"debit" 				=> $amount,
	            "send_via" 				=> $send_via,
	            "recipient_firstname" 	=> stripslashes($recipient_firstname),
	            "recipient_lastname" 	=> stripslashes($recipient_lastname),
	            "recipient_address" 	=> stripslashes($recipient_address),
	            "recipient_city" 		=> stripslashes($recipient_city),
	            "recipient_zipcode" 	=> $recipient_zipcode,
	            "recipient_state" 		=> $recipient['state'],
	            "recipient_statename" 	=> cw_get_state($recipient['state'], $recipient['country']),
	            "recipient_country" 	=> $recipient['country'],
	            "recipient_countryname" => cw_get_country($recipient['country']),
	            "recipient_phone" 		=> $recipient_phone,
	            "tpl_file" 				=> $gc_template
	        );
	    }

		if (!$fill_error || $amount_error) {

			if ($action != 'preview') {
				$db_gc = $giftcert;

				foreach ($db_gc as $k=>$v) {
					$db_gc[$k] = addslashes($v);
				}
			}

			if ($action == "add_gc") {
				$db_gc['gc_id'] = $gc_id = cw_gift_get_gcid();
				$db_gc['status'] = 'P';
				$db_gc['add_date'] = time();

				cw_array2insert('giftcerts', $db_gc);

				$top_message['content'] = cw_get_langvar_by_name("msg_adm_gc_add");
			}
			elseif ($action == "preview") {
				$giftcert['recipient_statename'] = cw_get_state($recipient['state'], $recipient['country']);
				$giftcert['recipient_countryname'] = cw_get_country($recipient['country']);
				$giftcert['gc_id'] = $gc_id;
				$smarty->assign('giftcerts', array($giftcert));

				header("Content-Type: text/html");
				header("Content-Disposition: inline; filename=giftcertificates.html");

				$_tmp_smarty_debug = $smarty->debugging;
				$smarty->debugging = false;

			    if (!empty($gc_template)) {
	                $css_file = preg_replace('/\.tpl$/', '.css', $gc_template);
	                $css_fullpath = $app_dir . $app_skin_dir . '/addons/estore_gift/' . $css_file;

	                if (
	                    file_exists($css_fullpath)
	                    && $css_file != $gc_template
	                ) {
	                    $smarty->assign('css_file', $css_file);
	                }
	            }

				cw_display("addons/estore_gift/gc_admin_print.tpl", $smarty);
				$smarty->debugging = $_tmp_smarty_debug;
				exit;
			}
			elseif ($gc_id) {
				cw_array2update('giftcerts', $db_gc, "gc_id='$gc_id'");
				$top_message['content'] = cw_get_langvar_by_name("msg_adm_gc_upd");
			}

			cw_header_location("index.php?target=giftcerts");
		}
		else {
			$top_message['content'] = cw_get_langvar_by_name("err_filling_form");
			$top_message['type'] = "E";
			$smarty->assign('amount_error', $amount_error);
		}
	}
	elseif ($action != 'print' && $action != 'delete') {
		global $to_customer;
		$to_customer = $config['default_admin_language'];

        if ($status) {

	        foreach($status as $gc_id => $val) {
	            $res = cw_query_first("SELECT * FROM $tables[giftcerts] WHERE gc_id='$gc_id'");

				if ($val=="A" && $val!=$res['status']) {
	                cw_gift_send_gc($config['Company']['orders_department'], $res);
				}
				db_query("UPDATE $tables[giftcerts] SET status='$val' WHERE gc_id='$gc_id'");
			}
		}

		$top_message['content'] = cw_get_langvar_by_name("msg_adm_gcs_upd");
		cw_header_location("index.php?target=giftcerts");
	}
}

if ($action == "delete") {
	// Delete gift certificate
    if (!empty($gc_ids)) {
        $gcids = array_keys($gc_ids);
        
        // GC can be deleted if it's doc_id == 0 or is not exist
        db_query("DELETE gc 
        FROM $tables[giftcerts] gc
        LEFT JOIN $tables[docs] d ON gc.doc_id=d.doc_id
        WHERE gc.gc_id IN ('" .implode("' ,'", $gcids) . "')
            AND (gc.doc_id=0 OR d.doc_id IS NULL)");
    }
    
    cw_add_top_message(cw_get_langvar_by_name("msg_adm_gcs_del"));
	cw_header_location("index.php?target=giftcerts");
}

if (in_array($mode, array('add_gc', 'modify_gc'))) {

	if (empty($country)) {
		$country = $config['General']['default_country'];
	}

	$smarty->assign('countries', cw_map_get_countries());
	$smarty->assign('states', cw_map_get_states($country));
	$smarty->assign('gc_templates', cw_gift_get_templates($app_dir . $app_skin_dir));
	$gc_readonly = "";

	if (!empty($gc_id)) {
		$giftcert = cw_query_first("SELECT * FROM $tables[giftcerts] where gc_id='" . $gc_id . "'");

		if ($giftcert['send_via'] != "E") {
			$giftcert['recipient_statename'] = cw_get_state($giftcert['recipient_state'], $giftcert['recipient_country']);
			$giftcert['recipient_countryname'] = cw_get_country($giftcert['recipient_country']);
		}

		$smarty->assign('giftcert', $giftcert);
		$smarty->assign('gc_id', $gc_id);
		$gc_readonly = ($mode == "modify_gc" && $giftcert['status'] != "P" ? "Y" : "");
	}
}
elseif ($action == 'print') {
	$giftcerts = false;
    $gc_ids = $gc_ids_p;

	if (!empty($gc_ids) && is_array($gc_ids)) {
		$tpl_cond = (!empty($tpl_file) ? " AND tpl_file='$tpl_file'" : '');
		$giftcerts = cw_query("SELECT *, add_date FROM $tables[giftcerts] WHERE gc_id IN ('".implode("','", array_keys($gc_ids))."') ".$tpl_cond);
	}

	if (empty($giftcerts) || !is_array($giftcerts)) {
		$top_message['type'] = 'W';
		$top_message['content'] = cw_get_langvar_by_name("msg_adm_warn_gc_sel");
		cw_header_location('index.php?target=giftcerts');
	}

	foreach ($giftcerts as $k=>$v) {
		$giftcerts[$k]['recipient_statename'] = cw_get_state($v['recipient_state'], $v['recipient_country']);
		$giftcerts[$k]['recipient_countryname'] = cw_get_country($v['recipient_country']);
	}

	$smarty->assign('giftcerts', $giftcerts);

	header("Content-Type: text/html");
	header("Content-Disposition: inline; filename=giftcertificates.html");

	$_tmp_smarty_debug = $smarty->debugging;
	$smarty->debugging = false;

	if (!empty($tpl_file)) {
		$css_file = preg_replace('!\.tpl$!', '.css', $tpl_file);

		if ($css_file != $tpl_file) {
			$smarty->assign('css_file', $css_file);;
		}
	}

	cw_display("addons/estore_gift/admin/gc_admin_print.tpl",$smarty);
	$smarty->debugging = $_tmp_smarty_debug;

	exit;
}
else {
	$expired_condition = ($config['estore_gift']['gc_show_expired'] == "Y" ? "" : " and status!='E'");
	$giftcerts = cw_query("SELECT * FROM $tables[giftcerts] where 1 $expired_condition");
	

	if (is_array($giftcerts)) {

		foreach ($giftcerts as $k => $v) {

			if ($v['doc_id'] > 0) {
                $giftcerts[$k]['doc'] = cw_query_first("select * from $tables[docs] where doc_id = " . $v['doc_id']);

                /*
                $result = cw_doc_get_related($v['doc_id']);
                
                if (empty($result)) {
                    $result = cw_query_hash("select * from $tables[docs] where doc_id = " . $v['doc_id'], 'type');
                }
                $giftcerts[$k]['related_docs'] = $result;
                */
            }
		}

		$smarty->assign('giftcerts', $giftcerts);
	}
}

$smarty->assign('mode', $mode);

$smarty->assign('main', "giftcerts");
$smarty->assign('gc_readonly', $gc_readonly);

$smarty->assign('current_main_dir', 'addons/estore_gift');
$smarty->assign('current_section_dir', 'admin');

