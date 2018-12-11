<?php
    define('PP_STD_DBG', 0);
    
    cw_load('http', 'paypal', 'payment', 'doc');

    global $REQUEST_METHOD, $request_prepared, $config, $tables;
/**
 * Successful return from PayPal
 */
 
if (constant('PP_STD_DBG')) cw_log_add('paypal_std', array('start',$REQUEST_METHOD,$request_prepared), false);

if (isset($request_prepared['mode']) && $request_prepared['mode'] == 'success') {

    $skey = $request_prepared['secureid']; // secureid - ref_id in table payment_data

    $payment_data = cw_call('cw_payment_get_data', array($skey));
    
    cw_call('cw_payment_put_data', array($skey, array('state'=>'END','status'=>'success')));
    
    if (empty($payment_data['doc_ids'])) {
        
        $payment_data['bill_error'] = 'callback error';
        $payment_data['reason']     = 'Order is not found in stored payment data';
        
        cw_call('cw_payment_put_data', array($skey, array(
            'state' => 'END',
            'status'=> $payment_data['bill_error'],
            'reason'=> $payment_data['reason'])));
        
    } else {
		// If user returns to site successfully before callback (or callback missed) - switch status from I to Q	
		$doc_ids = cw_query_column("SELECT doc_id FROM $tables[docs] WHERE status='I' AND doc_id IN ('" . implode("','", $payment_data['doc_ids']) . "')");
        cw_call('cw_doc_change_status', array($doc_ids, 'Q'));
    }
    
    if (constant('PP_STD_DBG')) cw_log_add('paypal_std', array('success',$payment_data), false);
        
    cw_call('cw_payment_stop', array($payment_data));
    exit();
}

/**
 * Cancel return from PayPal
 */
if (isset($request_prepared['mode']) && $request_prepared['mode'] == 'cancel') {
    
    $skey = $request_prepared['secureid']; // secureid - ref_id in table payment_data
    $payment_data = cw_call('cw_payment_get_data', array($skey));
    cw_call('cw_payment_put_data', array($skey, array('state'=>'END','status'=>'canceled')));
    
    $payment_data['bill_error'] = 'cancel';
    $payment_data['reason']     = 'Canceled by the user';
    
    if (!empty($payment_data['doc_ids'])) {
       cw_call('cw_doc_change_status', array($payment_data['doc_ids'], 'F'));
    }  

    if (constant('PP_STD_DBG')) cw_log_add('paypal_std', array('cancel',$payment_data), false);
        
    cw_call('cw_payment_stop', array($payment_data));
    exit();
    
}

/**
 * Callback from PayPal
 */

if ($REQUEST_METHOD == "POST" && isset($request_prepared['payment_type']) && isset($request_prepared['custom'])) {
    
    if (isset($request_prepared['mc_gross'])) {
        $payment_return = array(
            "total" => $request_prepared['mc_gross']
        );
    }

    // It is possible to point this script as notify_url for paypal_pro too, e.g. index.php?target=paypal&notify_from=pro
    if (!empty($request_prepared['notify_from']) && $request_prepared['notify_from'] == 'pro') {
        $_processor = 'paypal_pro';
    }
    else {
        $_processor = 'paypal';
    }

    $skey                   = $request_prepared["custom"];

    $payment_data           = cw_call('cw_payment_get_data', array($skey));
    $bill_output['sess_id'] = $payment_data['session_id'];

    $cur                    = $config['paypal']['pp_currency'];
    $testmode               = $config['paypal']['test_mode'];

    $pp_host                = ($testmode == 'N' ? "www.paypal.com" : "www.sandbox.paypal.com");

    $payment_status         = $request_prepared['payment_status'];

    $https_success = true;
    $https_msg = "";

    if ($config['paypal_solution'] != 'uk') {
        # do PayPal (IPN) background request...
        $post = array();
        foreach ($_POST as $key => $val)
            $post[] = "$key=".stripslashes($val);

        list($a,$result) = cw_https_request("POST","https://$pp_host:443/cgi-bin/webscr?cmd=_notify-validate", $post);
        $is_verified = preg_match("/VERIFIED/i", $result);

        if (empty($a)) {
            // HTTPS client error
            $https_success = false;
            $https_msg = $result;
        }

    } else {
        $is_verified = true;
    }

    cw_call('cw_payment_put_data', array($skey, array('state'=>'END','status'=>$payment_status,'is_callback'=>true,'is_verified'=>$is_verified)));

    if (!$https_success) {
        $bill_message = "Queued: HTTPS client error ($https_msg).";
        $bill_output["code"] = 3;
    } elseif (!$is_verified) {
        $bill_output["code"] = 2;
        $bill_message = "Declined (invalid request)";

    } elseif (strcasecmp($payment_status,"Completed")==0 || strcasecmp($payment_status, "Pending")==0) {

        $bill_output["code"] = 2;
        if (!strcasecmp($payment_status, "Pending")) {
            $bill_message = "Queued";
            $bill_output["code"] = 3;
            
            if (constant('PP_STD_DBG')) cw_log_add('paypal_std', array('callback pending',$transaction_entity), false);

            # It is pre-authorization response
            if ($transaction_entity == 'auth') {
                if ($_processor == 'paypal') {
                    $bill_output['is_preauth'] = true;
                    $bill_output['extra_order_data'] = array(
                        'paypal_type' => 'USSTD',
                        'paypal_txnid' => $txn_id,
                        'capture_status' => 'A',
                        'transaction_amount' => $request_prepared['mc_gross'],
                    );
                } else {
                    exit;
                }
            }

        } elseif (strcasecmp($payment_status,"Completed")==0 && ($doc_ids = cw_paypal_get_capture_orderid($auth_id))) {

            # Order(s) captured on PayPal backend
//            $total = cw_query_first_cell("SELECT SUM(di.total) FROM $tables[docs] d, $tables[docs_info] di WHERE d.doc_info_id=di.doc_info_id AND di.doc_id IN ('" . implode("','", $doc_ids) . "')");
            if ($cur == $request_prepared['mc_currency']) {
                cw_call('cw_doc_change_status',      array($doc_ids, cw_call('cw_payment_doc_status_after_capture', array())));
                foreach ($doc_ids as $doc_id) {
                    cw_call('cw_doc_place_extras_data',  array($doc_id, array('capture_status'=>'C','captured_amount' => $request_prepared['mc_gross'])));
                }
            }
            if (constant('PP_STD_DBG')) cw_log_add('paypal_std', array('callback Completed',$doc_ids), false);
            
            exit;

        } elseif ($cur != $request_prepared['mc_currency']) {
            
            $bill_message = "Declined: Payment amount mismatch: wrong order currency ( ".$cur." <> ".$request_prepared['mc_currency']." ).";

        } elseif ($is_verified) {
            
            $bill_output["code"] = 1;
            $bill_output['extra_order_data'] = array(
                    'paypal_type' => 'USSTD',
                    'paypal_txnid' => $txn_id,
                    'capture_status' => 'C',
                    'transaction_amount' => $request_prepared['mc_gross'],
             );
            $bill_message = "Accepted";
            
        } else {
            $bill_message = "Declined (processor error)";
        }

/* kornev - it shouldn't be there 
        $_oids = explode("|", cw_query_first_cell("SELECT trstat FROM $tables[cc_pp3_data] WHERE ref='".$skey."'"));
        array_shift($_oids);
        if (!empty($_oids)) {
            foreach($_oids as $_oid)
                cw_paypal_update_order($_oid);
        }
*/

    } elseif (strcasecmp($payment_status, "Voided")==0 && ($doc_ids = cw_paypal_get_capture_orderid($auth_id))) { 

        # Order(s) voided on PayPal backend

        cw_call('cw_doc_change_status', array($doc_ids, 'D'));
        foreach ($doc_ids as $doc_id) {
            cw_call('cw_doc_place_extras_data',  array($doc_id, array('capture_status'=>'V')));
        }

        if (constant('PP_STD_DBG')) cw_log_add('paypal_std', array('callback Voided',$doc_ids), false);

        exit;


    } elseif (strcasecmp($payment_status, "Refunded")==0) {
        # Register Refund transaction
        if (!empty($parent_txn_id)) {
            $doc_ids = cw_payment_get_docs_by_transaction($parent_txn_id);
            foreach ($doc_ids as $doc_id) {
                cw_call('cw_doc_place_extras_data',  array($doc_id, array('capture_status'=>'R','refund_txnid'=>$txn_id)));
            }
        }
        
        if (constant('PP_STD_DBG')) cw_log_add('paypal_std', array('callback Refunded',$doc_ids), false);

        exit;

    } else {
        $bill_message = "Declined";
        $bill_output["code"] = 2;
    }

    $bill_output["billmes"] = "$bill_message Status: $payment_status (TransID #$txn_id)";
    if (!empty($pending_reason))
        $bill_output["billmes"] .= " Reason: $pending_reason";

    cw_call('cw_payment_put_data', array($skey, array('billmes'=>$bill_output["billmes"])));

    if (constant('PP_STD_DBG')) cw_log_add('paypal_std', array('callback end',$bill_output), false);

    $return = cw_call('cw_payment_check_results', array($bill_output));
    
    if (constant('PP_STD_DBG')) cw_log_add('paypal_std', array('callback stop',$return), false);

    cw_call('cw_payment_stop', array($return));
}
