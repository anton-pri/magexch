<?php
function cw_payment_delete($payment_id) {
    global $tables;

    db_query("delete from $tables[payment_methods] where payment_id='$payment_id'");
    db_query("delete from $tables[payment_methods_lng] where payment_id='$payment_id'");
    db_query("delete from $tables[payment_quotes] where payment_id='$payment_id'");
    db_query("delete from $tables[payment_methods_memberships] where payment_id='$payment_id'");
    db_query("delete from $tables[payment_settings] where payment_id='$payment_id'");
    
    if ($tables['payments_shippings'])
        db_query("delete from $tables[payments_shippings] where payment_id='$payment_id'");

    cw_attributes_cleanup($payment_id, 'G');
}

# info_type
# 1 - get shipping info
function cw_payment_search($params, $return = null) {
    extract($params);

    global $tables, $current_language, $addons, $cart;

    $fields = $from_tbls = $query_joins = $where = $groupbys = $having = $orderbys = array();

# kornev, merge standart and additional variables
    if ($return)
    foreach ($return as $saname => $sadata)
        if (isset($$saname) && is_array($$saname) && empty($$saname)) $$saname = $sadata;

    $language = $language?$language:$current_language;

    $from_tbls[] = 'payment_methods';
    $fields[] = "$tables[payment_methods].*";
    $query_joins['payment_methods_lng'] = array(
        'on' => "$tables[payment_methods_lng].payment_id = $tables[payment_methods].payment_id AND $tables[payment_methods_lng].code = '$language'",
        'only_select' => 1,
    );
    $fields[] = "IFNULL($tables[payment_methods_lng].title, $tables[payment_methods].title) as title";
    $fields[] = "IFNULL($tables[payment_methods_lng].descr, $tables[payment_methods].descr) as descr";

    $where = array();
    if (isset($data['active']))
        $where[] = "active='$data[active]'";

    if ($data['type']) 
        $where[] = "(payment_operations & $data[type] or payment_operations & 3)";

    if ($data['quotes'] == 1)
        $where[] = "is_quotes = 1";
    elseif ($data['quotes'] == 2)
        $where[] = "is_web = 1";
    elseif ($data['quotes'] == 3)
        $where[] = "is_quotes != 1";

    if ($data['total'])
        $where[] = "(min_limit = 0 or min_limit < '$total') and (max_limit = 0 or max_limit > '$total')";

    if (isset($data['membership_id'])) {
        $query_joins['payment_methods_memberships'] = array(
            'on' => "$tables[payment_methods_memberships].payment_id = $tables[payment_methods].payment_id and $tables[payment_methods_memberships].membership_id = '$membership_id'",
            'is_inner' => 1,
        );
    }

    $orderbys[] = "$tables[payment_methods].orderby";

    $search_query = cw_db_generate_query($fields, $from_tbls, $query_joins, $where, $groupbys, $having, $orderbys);

    $payment_methods = cw_query($search_query);
    
    if (empty($payment_methods)) $payment_methods=null;

    if ($info_type & 1 && is_array($payment_methods))
    foreach($payment_methods as $k=>$v)
        $payment_methods[$k]['shippings'] = cw_query_column("select shipping_id from $tables[payments_shippings] where payment_id='$v[payment_id]'");

    if (is_array($payment_methods)) $payment_methods[0]['is_default'] = 1;

    // if used quote
    if (
    	$addons['quote_system']
    	&& isset($cart['info']['quote_doc_id']) 
    	&& !empty($cart['info']['quote_doc_id']) 
    	&& isset($cart['info']['payment_id'])
    ) {
    	foreach ($payment_methods as $method) {
    		if ($method['payment_id'] == $cart['info']['payment_id']) {
    			return array($method);
    		}
    	}
    	return array();
    }

    return $payment_methods;
}

function cw_payment_get($payment_id, $language = '') {
    global $tables;

    $language = $language?$language:$current_language;

    $payment = cw_query_first("select pm.*, ifnull(pml.title, pm.title) as title from $tables[payment_methods] as pm left join $tables[payment_methods_lng] as pml on pm.payment_id=pml.payment_id and pml.code='$language' where pm.payment_id='$payment_id' order by pm.orderby, pm.title");
    $payment['web'] = cw_query_first("select ps.* from $tables[payment_settings] as ps where ps.payment_id='$payment_id'");
    $payment['quotes'] = cw_query("select * from $tables[payment_quotes] where payment_id='$payment_id'");

    $payment['memberships'] = cw_query_key("select membership_id from $tables[payment_methods_memberships] where payment_id = '$payment_id'");
    $payment['shippings'] = cw_query_key("select shipping_id from $tables[payments_shippings] where payment_id = '$payment_id'"); 

    return $payment;
}


function cw_get_urlencoded_doc_ids ($doc_ids) {
    if (!is_array($doc_ids))
        return '';

    return urlencode(join (",", $doc_ids));
}

function cw_check_webinput($check_php = 1) {
    global $config, $tables;

    $ip = $_SERVER['REMOTE_ADDR'];
    $allow_ip = $config['Security']['allow_ips'];

    if ($allow_ip) {
        $not_found = true;
        $a = explode(',',$allow_ip);
        foreach ($a as $v) {
            list($aip, $amsk) = explode('/',trim($v));

            # Cannot use 0x100000000 instead 4294967296
            $amsk = 4294967296 - ($amsk ? pow(2,(32-$amsk)) : 1);

            if ((ip2long($ip) & $amsk) == ip2long($aip)) {
                $not_found = false;
                break;
            }
        }

        return ($not_found ? "err" : "pass");
    }

    return "pass";
}

function cw_payment_header() {
    global $smarty;

    echo cw_display('customer/payment/payment_wait.tpl', $smarty, false);

    if (!defined("NO_RSFUNCTION"))
        register_shutdown_function("cw_payment_footer");
}

# Display payment page footer
function cw_payment_footer() {
    global $smarty;

    if (defined("DISP_PAYMENT_FOOTER"))
        return false;

    echo cw_display('customer/payment/payment_wait_end.tpl', $smarty, false);
    define("DISP_PAYMENT_FOOTER", true);
}

function cw_payment_create_form($params) {
    extract($params);

    $method = strtolower($method) == 'get'?'get':'post';

    $button_title = cw_get_langvar_by_name("lbl_submit", array(), false, true);
    $script_note = cw_get_langvar_by_name("txt_script_payment_note", array("payment" => $name), false, true);
    $noscript_note = cw_get_langvar_by_name("txt_noscript_payment_note", array("payment" => $name, "button" => $button_title), false, true);
    ?>
<form action="<?php echo $url; ?>" method="<?php echo $method; ?>" name="process">
<?php
    foreach($fields as $fn => $fv) {
?>  <input type="hidden" name="<?php echo $fn; ?>" value="<?php echo htmlspecialchars($fv); ?>" />
<?php
    }
?>
<center id="text_box">
<noscript>
<?php echo $noscript_note; ?><br />
<input type="submit" value="<?php echo $button_title; ?>">
</noscript>
</center>
</form>
<script type="text/javascript">
<!--
if (document.getElementById('text_box'))
    document.getElementById('text_box').innerHTML = "<?php echo strtr($script_note, array('\\'=>'\\\\',"'"=>"\\'",'"'=>'\\"',"\r"=>'\\r',"\n"=>'\\n','</'=>'<\/')); ?>";
document.process.submit();
-->
</script>
    <?php
}

#
# Check IP
#
function cw_is_valid_ip($ip) {
    return (bool)preg_match("/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/", trim($ip));
}

#
# Get valid IP
#
function cw_get_valid_ip($ip) {
    return cw_is_valid_ip($ip) ? $ip : '127.0.0.1';
}

#
# Check payment activity
#
function cw_is_active_payment($php_script) {
    global $tables;

    $cnt = cw_query_first_cell("SELECT COUNT($tables[payment_web].processor) FROM $tables[payment_web], $tables[payment_methods] WHERE $tables[payment_web].processor = '".addslashes($php_script)."' AND $tables[payment_web].payment_id = $tables[payment_methods].payment_id AND $tables[payment_methods].active = 1");
    return ($cnt > 0);

}

function cw_payment_get_methods($params) {
    global $tables, $current_language;

    extract($params);

    $payment_methods = cw_query_first("SELECT ps.processor, pm.*, pm.title as title_orig, IFNULL(pml.title, pm.title) as title, IFNULL(pml.descr, pm.descr) as descr FROM $tables[payment_methods] as pm LEFT JOIN $tables[payment_methods_lng] as pml on pml.payment_id = pm.payment_id and pml.code = '$current_language', $tables[payment_settings] as ps WHERE ps.payment_id=pm.payment_id and pm.payment_id='$payment_id'");

    if(!check_payment_method($payment_id)) return false;

    return $payment_methods;
}

function check_payment_method($payment_id) {
    global $tables, $cart, $config;

    $required_shipping_id = array();
    if ($config['Appearance']['show_cart_summary'] == 'Y' && is_array($cart['shipping_arr'])) {
        $required_shipping_id = $cart['shipping_arr'];
    }
    elseif($cart['shipping_id']) 
        $required_shipping_id = array($cart['shipping_id']);

    if (count($required_shipping_id)) {
        $count = cw_query_first_cell("select count(*) from $tables[payments_shippings] where payment_id='$payment_id'");
        if (!$count) return true;
        $count = cw_query_first_cell("select count(*) from $tables[payments_shippings] where payment_id='$payment_id' and shipping_id in ('".implode(', ', $required_shipping_id)."')");
            if ($count != count($required_shipping_id)) return false;
    }

    return true;
}

function cw_payment_run_processor() {
    return array();
}

function cw_payment_check_results($payment_data) {
    $log_payment_failure = false;
    if (!empty($payment_data['sess_id'])) {
	    if (cw_check_webinput()=="err") {
		    $log_payment_failure = true;

    		if ($payment_data['code']==1) {
	    		$__transaction_status = "successful";
		    	$payment_data['code'] = 3;
    		} elseif ($payment_data['code']==3)
	    		$__transaction_status = "queued";
		    else
			    $__transaction_status = "declined";

    		$payment_data['billmes'] = "Gateway reported of $__transaction_status transaction but it's response came from the IP that is not specified in the list of valid IPs: ".cw_get_valid_ip($_SERVER['REMOTE_ADDR'])."\n-- response ----\n".$payment_data['billmes'];
	    }
    	$sessurl = APP_SESSION_NAME."=".$payment_data['sess_id']."&";

	    cw_session_id($payment_data['sess_id']);
    }
    else $sessurl = '';

    $cart = &cw_session_register('cart', array());
    $secure_oid = &cw_session_register("secure_oid");

    $bill_error = $reason = ''; 
    $fatal = false;

    if (!empty($payment_data)) $saved_payment_data = $payment_data;
    else $saved_payment_data = false;

    if (empty($secure_oid)) {
    	$bill_error = cw_get_langvar_by_name("lbl_error_ccprocessor_error");
	    $payment_data['billmes'] = "error: your order was lost";
    	$reason = $payment_data['billmes'];
	    $fatal = true;
    }
    elseif (empty($cart) && empty($payment_data['skey'])) {
    	$bill_error = cw_get_langvar_by_name("lbl_error_ccprocessor_error");
	    $payment_data['billmes'] = "Error: Your cart was lost";
    	$reason = $payment_data['billmes'];
	    $fatal = true;
    }
    elseif ($payment_data['code'] == 3) {
    	$reason = $payment_data['billmes'];
    }
    elseif ($payment_data['code'] == 2) {
    	$bill_error = cw_get_langvar_by_name("lbl_error_ccprocessor_error");
	    $reason = $payment_data['billmes'];
    }
    elseif ($payment_data['code'] == 1) {
    	if (isset($payment_return) && !empty($payment_return) && $payment_data['code'] != 2) {
	    	if (isset($payment_return['total'])) {
    			$sum = 0;
	    		foreach ($secure_oid as $_oid) {
		    		$o = cw_order_data($_oid);
			    	$sum += $o['order']['total'];
    			}

	    		if ($sum != doubleval($payment_return['total'])) {
		    		$payment_data['code'] = 2;
			    	$payment_data['billmes'] .= "; Payment amount mismatch.";
    			}
	    	}
    
	    	if (
		    	$payment_data['code'] != 2 &&
			    isset($payment_return['currency']) &&
    			isset($payment_return['_currency']) &&
	    		!empty($payment_return['_currency']) &&
		    	$payment_return['currency'] != $payment_return['_currency']
    		) {
	    		$payment_data['code'] = 2;
		    	$payment_data['billmes'] .= "; Payment amount mismatch.";
    		}
	    }

    	if ($payment_data['code'] == 1)
	    	$payment_data['billmes'] = "Approved: ".$payment_data['billmes'];
	    else {
    		$bill_error = cw_get_langvar_by_name("lbl_error_ccprocessor_error");
	    	$reason = $payment_data['billmes'];
		    $payment_data['billmes'] = "Declined: ".$payment_data['billmes'];
    	}
    }
    else {
	    # unavailable
    	$bill_error = cw_get_langvar_by_name("lbl_error_ccprocessor_unavailable");
	    $payment_data['billmes'] = "Error: Payment gateway is unavailable";
    }

    if (!$fatal) {
    	cw_load('doc');
        $status_after_capture = cw_call('cw_payment_doc_status_after_capture', array($payment_data));
	    $order_status = ($bill_error) ? "F" : (($payment_data['code'] == 3) ? "Q" : $status_after_capture);

        if (in_array($order_status, array('P', 'Q', $status_after_capture)) && !empty($payment_data['is_preauth'])) {
            $order_status = 'A'; // Authorized
        }

    	if ($payment_data['code'] == 1 || $payment_data['code'] == 3) {
	    	if (empty($payment_data['skey'])) $cart = array();
	    }

    	$advinfo = array();
	    $advinfo[] = "Reason: ".$payment_data['billmes'];
    	if ($payment_data['avsmes']) $advinfo[] = "AVS info: ".$payment_data['avsmes'];
	    if ($payment_data['cvvmes']) $advinfo[] = "CVV info: ".$payment_data['cvvmes'];

    	if (isset($cmpi_result)) {
	    	$advinfo[] = "3-D Secure Transaction:";
		    if (isset($cmpi_result['Enrolled'])) {
			    $advinfo[] = "  TransactionId: ".$cmpi_result['TransactionId'];
    			$advinfo[] = "  Enrolled: ".$cmpi_result['Enrolled'];
	    	} else {
		    	$advinfo[] = "  PAResStatus: ".$cmpi_result['PAResStatus'];
			    $advinfo[] = "  PAResStatusDesc: ".$cmpi_result['PAResStatusDesc'];
    			$advinfo[] = "  CAVV: ".$cmpi_result['Cavv'];
	    		$advinfo[] = "  SignatureVerification: ".$cmpi_result['SignatureVerification'];
		    	$advinfo[] = "  Xid: ".$cmpi_result['Xid'];
			    $advinfo[] = "  EciFlag: ".$cmpi_result['EciFlag'];
    		}
	    	if (!empty($cmpi_result['ErrorNo']))
		    	$advinfo[] = "  ErrorNo: ".$cmpi_result['ErrorNo'];
    		if (!empty($cmpi_result['ErrorDesc']))
	    		$advinfo[] = "  ErrorDesc: ".$cmpi_result['ErrorDesc'];
    	}

	    cw_call('cw_doc_change_status', array($secure_oid, $order_status, join("\n", $advinfo)));
    }

    if (!empty($payment_data['extra_order_data'])) {
    
        foreach($secure_oid as $oid) {
           cw_call('cw_doc_place_extras_data', array($oid,$payment_data['extra_order_data']));
        }

        unset($payment_data['extra_order_data']);

    }


    cw_session_unregister("secure_oid");
    cw_session_save();

    return array(
        'bill_error' => $bill_error,
        'sessurl' => $sessurl,
        'reason' => $reason,
        'doc_ids' => $secure_oid,
    );
}

function cw_payment_stop($payment_data) {
    global $app_catalogs, $customer_id;
    
    cw_load('cart_process');

    $cart = &cw_session_register('cart', array());
    $top_message = &cw_session_register('top_message');

    if ($payment_data['bill_error']) {
        $top_message = array('type' => 'E', 'content' => $payment_data['bill_error'].' '.$payment_data['reason']);
	    $request = $app_catalogs['customer'].'/index.php?target=cart&mode=checkout';
    }
    else {
        $_doc_ids = cw_get_urlencoded_doc_ids($payment_data['doc_ids']);
	    $request = $app_catalogs['customer']."/index.php?target=order-message&doc_ids=".$_doc_ids;
    	$cart = array();
	    cw_session_save();
	    cw_save_customer_cart($customer_id, $cart);

	if (constant('OGONE_DBG')) cw_log_add('ogone', array('cw_payment_stop START', compact('customer_id', 'cart')), false);
	if (constant('PP_STD_DBG')) cw_log_add('paypal_std', array('cw_payment_stop START', compact('customer_id', 'cart')), false);

        global $current_area, $identifiers, $APP_SESS_ID;
        if ($identifiers[$current_area]['customer_to_merge']>0) {
            cw_load('user');
            cw_call('cw_user_merge', array($customer_id, $identifiers[$current_area]['customer_to_merge']));
            cw_func_call('cw_user_delete', array('customer_id' => $customer_id, 'send_mail' => false));
            $request .= '&skey='.cw_call('cw_doc_security_key',array($payment_data['doc_ids'],$APP_SESS_ID));
            cw_event('on_pre_logout');
            cw_unset($identifiers, $current_area);
            cw_event('on_logout');
        }
    }

    if (constant('OGONE_DBG')) cw_log_add('ogone', array('cw_payment_stop END', compact('payment_data', 'customer_id', 'cart')), false);
    if (constant('PP_STD_DBG')) cw_log_add('paypal_std', array('cw_payment_stop END', compact('payment_data', 'customer_id', 'cart')), false);

    cw_header_location($request);
}

function cw_payment_get_label($params, $return) {
    global $tables, $current_language;
    if ($return) return $return;
    return cw_query_first_cell("select ifnull(pml.title, pm.title) as title from $tables[payment_methods] as pm left join $tables[payment_methods_lng] as pml on pm.payment_id=pml.payment_id and pml.code='$current_language' where pm.payment_id='".$params['payment_id']."'");
}

function cw_payment_interruption() {
    global $APP_SESS_ID, $tables;
    db_query("delete from $tables[payment_data] where session_id='$APP_SESS_ID'");
}

function cw_payment_get_session($params) {
    global $tables;
    return cw_query_first_cell($sql="select session_id from $tables[payment_data] where ref_id='$params[key]'");
}

function cw_payment_start() {
    global $tables, $APP_SESS_ID;

    while (true) {
        $unique_id = md5(uniqid(rand()));
        db_query("insert into $tables[payment_data] (ref_id, session_id) values ('$unique_id', '$APP_SESS_ID')");
        if (db_affected_rows() > 0) break;
    }

    return $unique_id;
}

function cw_payment_get_data($ref_id) {
    global $tables;

    $data = cw_query_first("select session_id, data from $tables[payment_data] where ref_id='$ref_id'");
    if (!$data) return array();

    $ret = array('session_id' => $data['session_id']);
    if ($data['data']) {
        $uns_data = unserialize($data['data']);
        if (is_array($uns_data)) $ret += $uns_data;
    }

    return $ret;
}

function cw_payment_put_data($ref_id, $data) {
    global $tables;

    $existing_data = cw_call('cw_payment_get_data', array($ref_id));
    if (!$existing_data) return false;

    $data = $data + $existing_data;
    foreach(array('session_id', 'ref_id', 'key') as $fld) unset($data[$fld]);

    db_query("update $tables[payment_data] set data='".addslashes(serialize($data))."' where ref_id='$ref_id'");
}

function cw_payment_delete_data($params) {
    global $tables;
    db_query("delete from $tables[payment_data] where ref_id='$params[key]'");
}

function cw_payment_check_cart($params) {
    extract($params);
    if (is_array($payment_methods)) {
        foreach($payment_methods as $pd) {
            if ($pd['payment_id'] == $payment_id) return $payment_id;
        }
        return $payment_methods[0]['payment_id'];
    }
    return 0;
}

function cw_payment_checkout_login_prepare() {
    global $smarty, $user_account, $current_language;

    $cart = &cw_session_register('cart', array());

    $payment_methods = cw_func_call('cw_payment_search', array('data' => array('membership_id' => $user_account['membership_id'],'active'=>1), 'language' => $current_language, 'total' => $cart['info']['total'], 'type' => 2));
    if (!$payment_methods)
        $top_message = array('content' => cw_get_langvar_by_name('lng_no_payment_methods'), 'type' => 'E');
    $smarty->assign('payment_methods', $payment_methods);

# kornev, we should find the addon of the payment gateway and get the params
    $cart['info']['payment_id'] = cw_func_call('cw_payment_check_cart', array('payment_methods' => $payment_methods, 'payment_id' => $cart['info']['payment_id']));
    $payment_data = cw_func_call('cw_payment_get_methods', $cart['info']);
    $smarty->assign('payment_data', $payment_data);
}

function cw_payment_checkout_prepare() {
    cw_func_call('cw_payment_checkout_login_prepare');
    if ($top_message)
		cw_header_location('index.php?target=cart');
}

function cw_payment_cc_is_visa($num) {
	$first4 = 0+substr($num,0,4);
	return ($first4>=4000 && $first4<=4999);
}

function cw_payment_cc_is_mc($num) {
	$first4 = 0+substr($num,0,4);
	return ($first4>=5100 && $first4<=5999);
}

function cw_payment_cc_is_amex($num) {
	$first4 = 0+substr($num,0,4);
	return (($first4>=3400 && $first4<=3499) || ($first4>=3700 && $first4<=3799));
}

function cw_payment_cc_is_diners($num) {
	$first4 = 0+substr($num,0,4);
	return (($first4>=3000 && $first4<=3059) || ($first4>=3600 && $first4<=3699) || ($first4>=3800 && $first4<=3889));
}

function cw_payment_cc_is_dc($num) {
	$first4 = 0+substr($num,0,4);
	return ($first4==6011);
}

function cw_payment_cc_is_jcb($num) {
	$first4 = 0+substr($num,0,4);
	return ($first4>=3528 && $first4<=3589);
}

function cw_payment_cc_is_test($num,$rules) {
	$result = false;
	$num = trim($num);
	for ($ndx=0; $ndx<count($rules); ++$ndx) {
		list($hiPrefix,$loPrefix,$valLength,$issueLength,$startDateLength) = explode(',',$rules[$ndx]);
		$prefix = substr($num,0,strlen($hiPrefix));

		if ($prefix>=$hiPrefix && $prefix<=$loPrefix && strlen($num)==$valLength) {
			$result = true;
			break;
		}
	}
	return $result;
}

function cw_payment_cc_is_switch($num) {
	$rules = array("490302,490309,18,1","490335,490339,18,1","491101,491102,16,1","491174,491182,18,1","493600,493699,19,1","564182,564182,16,2","633300,633300,16,0","633301,633301,19,1","633302,633349,16,0","675900,675900,16,0","675901,675901,19,1","675902,675904,16,0","675905,675905,19,1","675906,675917,16,0","675918,675918,19,1","675919,675937,16,0","675938,675940,18,1","675941,675949,16,0","675950,675962,19,1","675963,675997,16,0","675998,675998,19,1","675999,675999,16,0");

	return is_test($num,$rules);
}

function cw_payment_cc_is_solo($num) {
	$rules = array("633450,633453,16,0","633454,633457,16,0","633458,633460,16,0","633461,633461,18,1","633462,633472,16,0","633473,633473,18,1","633474,633475,16,0","633476,633476,19,1","633477,633477,16,0","633478,633478,18,1","633479,633480,16,0","633481,633481,19,1","633482,633489,16,0","633490,633493,16,1","633494,633494,18,1","633495,633497,16,2","633498,633498,19,1","633499,633499,18,1","676700,676700,16,0","676701,676701,19,1","676702,676702,16,0","676703,676703,18,1","676704,676704,16,0","676705,676705,19,1","676706,676707,16,2","676708,676711,16,0","676712,676715,16,0","676716,676717,16,0","676718,676718,19,1","676719,676739,16,0","676740,676740,18,1","676741,676749,16,0","676750,676762,19,1","676763,676769,16,0","676770,676770,19,1","676771,676773,16,0","676774,676774,18,1","676775,676778,16,0","676779,676779,18,1","676780,676781,16,0","676782,676782,18,1","676783,676794,16,0","676795,676795,18,1","676796,676797,16,0","676798,676798,19,1","676799,676799,16,0");

	return is_test($num,$rules);
}

function cw_payment_cc_is_delta($num) {
	return false;
}

function cw_payment_data_delete() {
	global $tables;
	
	// delete payment data of expired sessions
    $sess_ids = cw_query_column("SELECT pd.session_id 
		FROM $tables[payment_data] pd
		LEFT JOIN $tables[sessions_data] sd ON sd.sess_id = pd.session_id
		WHERE sd.sess_id IS NULL");
    if ($sess_ids)
		foreach($sess_ids as $sess_id)
			db_query("delete from $tables[payment_data] where session_id='$sess_id'");
}

/**
 * Check if doc is authorized via payment processor
 * 
 * @param int|array $doc - doc_id or doc data
 * 
 * @return bool
 */
function cw_payment_is_authorized($doc)
{
    global $tables;

    // $doc is doc data
    if (is_array($doc) && !empty($doc['doc_id']) && !empty($doc['status'])) {
        $status = $doc['status'];
        $doc_id = $doc['doc_id'];
    }
    
    // $doc is doc_id
    if (empty($status) && is_int($doc)) {
        $doc_id = $doc;
//        $status = cw_query_first_cell("SELECT status FROM $tables[docs] WHERE doc_id = '$doc_id'");
    }

// Order Pre-auth status is not necessary for capturing, main condition is payment capture_status below
/*
    if ($status != 'A')
        return false;
*/

    $capture_status = cw_query_first_cell("SELECT value FROM $tables[docs_extras] WHERE doc_id = '$doc_id' AND khash = 'capture_status'");

    if ($capture_status != 'A')
        return false;

    return true;
}

function cw_payment_get_docs_by_transaction($txn_id, $khash=array()) {
    global $tables;    
    
    static $default_khashes = array('paypal_txnid', 'pnref','capture_pnref');
    
    if (empty($khash)) $khash = $default_khashes;
    if (!is_array($khash)) $khash = array($khash);
    
    $res = cw_query_column("SELECT doc_id 
    FROM $tables[docs_extras]
    WHERE khash IN ('".join("','",$khash)."') AND value = '$txn_id'");

    return $res
        ? array_unique($res)
        : false;
}

/**
 * Return doc status used to mark order when it funds cuptured (Sale or Capture transaction)
 */
function cw_payment_doc_status_after_capture($payment_data) {
    return 'P';
}

/**
 * Dummy function for capture process. Payment addon must hook this function
 * 
 * @param array $order - order data
 */
function cw_payment_do_capture($order) {
    return error('Payment processing error: there is no capture handler for this payment');
}

/**
 * Dummy function for void process. Payment addon must hook this function
 */
function cw_payment_do_void($order) {
    return error('Payment processing error: there is no void handler for this payment');
}
