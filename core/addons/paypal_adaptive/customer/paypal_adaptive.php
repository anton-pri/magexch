<?php
    cw_load('http', 'paypal');

#   $txt = "transaction%5B0%5D.is_primary_receiver=false&log_default_shipping_address_in_transaction=false&transaction%5B0%5D.receiver=us_seller%40shabaev.com&action_type=PAY_PRIMARY&ipn_notification_url=http%3A//www.shabaev.com/ipmail.php&transaction%5B1%5D.paymentType=SERVICE&transaction%5B0%5D.amount=USD+17.00&charset=windows-1252&transaction_type=Adaptive+Payment+PAY&transaction%5B1%5D.id_for_sender_txn=5CA35707JY234151P&transaction%5B0%5D.invoiceId=293%2C294%2110&transaction%5B1%5D.is_primary_receiver=true&notify_version=UNVERSIONED&cancel_url=http%3A//printdrop.desk.local/index.php%3Ftarget%3Dcart&transaction%5B1%5D.status_for_sender_txn=Completed&transaction%5B1%5D.receiver=dmitriy-facilitator%40shabaev.com&verify_sign=An5ns1Kso7MWUdW4ErQKJJJ4qi4-Afg2OCG.qUDQGhplM45KX-0N5YvM&sender_email=us_buyer%40shabaev.com&fees_payer=EACHRECEIVER&return_url=http%3A//printdrop.desk.local/index.php%3Ftarget%3Dorder-message%26doc_ids%3D293%2C294&transaction%5B0%5D.paymentType=SERVICE&memo=293%2C294%2110&transaction%5B1%5D.amount=USD+17.00&reverse_all_parallel_payments_on_error=false&transaction%5B1%5D.pending_reason=NONE&pay_key=AP-45A76721F5609124X&transaction%5B1%5D.id=4TD03090TA894444N&transaction%5B0%5D.pending_reason=NONE&transaction%5B1%5D.invoiceId=293%2C294%2110&status=INCOMPLETE&transaction%5B1%5D.status=Completed&test_ipn=1&payment_request_date=Sun+Sep+14+07%3A15%3A23+PDT+2014";

    if(empty($_POST))
        $_POST = $HTTP_POST_VARS;

    $skey = $_POST["tracking_id"];
    $data = cw_call('cw_payment_get_data', array($skey));
    $bill_output['sess_id'] = $data['session_id'];
    $bill_output['skey']    = $skey;

    $https_success = true;
    $https_msg = "";
    $txinfo = "";

    # do PayPal (IPN) background request...
    $txt = file_get_contents("php://input");
    $pp_host = ($config['paypal_adaptive']['test_mode'] == 'N' ? "www.paypal.com" : "www.sandbox.paypal.com");
    list($a,$result) = cw_https_request("POST","https://$pp_host:443/cgi-bin/webscr?cmd=_notify-validate", array($txt), "");
    $is_verified = preg_match("/VERIFIED/i", $result);

    if (empty($a)) {
        // HTTPS client error
        $https_success = false;
        $https_msg = $result;
    }

/*
    >> https://developer.paypal.com/docs/classic/ipn/integration-guide/IPNandPDTVariables/
    The status of the payment. Possible values are:
    CANCELED – The Preapproval agreement was cancelled
    CREATED – The payment request was received; funds will be transferred once the payment is approved
    COMPLETED – The payment was successful
    INCOMPLETE – Some transfers succeeded and some failed for a parallel payment or, for a delayed chained payment, secondary receivers have not been paid
    ERROR – The payment failed and all attempted transfers failed or all completed transfers were successfully reversed
    REVERSALERROR – One or more transfers failed when attempting to reverse a payment
    PROCESSING – The payment is in progress
    PENDING – The payment is awaiting processing
*/
    $payment_status = $_POST["status"];

    if (!$https_success) {
        $bill_message = "Queued: HTTPS client error ($https_msg).";
        $bill_output["code"] = 3;
    } elseif (0 && !$is_verified) {
        $bill_output["code"] = 2;
        $bill_message = "Declined (invalid request)";

    } elseif (in_array($payment_status,array('CREATED','INCOMPLETE','COMPLETED','PROCESSING','PENDING'))) {

        $bill_output["code"] = $payment_status=='COMPLETED' ? 1 : 3;
	$bill_message = $payment_status=='COMPLETED' ? 'Accepted' : 'Queued';

	$tx = explode('&',urldecode($txt));
	sort($tx);
	foreach($tx as $ti => $tv)
	if(!preg_match("/^transaction\[\d+\]/",$tv))
		unset($tx[$ti]);

	$txinfo = "\n".join("\n",$tx);

	# &memo=293%2C294%2110& .. &sender_email=us_buyer%40shabaev.com& 
	cw_log_add('pp_adapt_email', array($_POST["memo"], $_POST["sender_email"]));

        $memo_parts = explode("!", $_POST["memo"]);
        if (is_array($memo_parts)) {  
            $memo_doc_ids = $memo_parts[0]; 
            if (!empty($memo_doc_ids)) {
                $memo_doc_ids_arr = explode(",", $memo_doc_ids); 
                foreach ($memo_doc_ids_arr as $memo_doc_id) {
                    cw_array2insert('paypal_adaptive_doc_accounts', array('doc_id'=>$memo_doc_id, 'email'=>$_POST["sender_email"]), true);
                }
            }  
        }
/*
        if (!strcasecmp($payment_status, "Pending")) {
            $bill_message = "Queued";
            $bill_output["code"] = 3;

            # It is pre-authorization response
            if ($transaction_entity == 'auth') {
                if ($_processor == 'ps_paypal.php') {
                    $bill_output['is_preauth'] = true;
                    $bill_output['extra_order_data'] = array(
                        "paypal_type" => "USSTD",
                        "paypal_txnid" => $txn_id,
                        "capture_status" => 'A'
                    );
            
                } else {
                    exit;
                }
            }

        } elseif (!strcasecmp($payment_status,"Completed") && ($orderids = cw_paypal_get_capture_orderid($auth_id))) {

            # Order(s) captured on PayPal backend
            $total = cw_query_first_cell("SELECT SUM(total) $tables[orders] WHERE orderid IN ('" . implode("','", $orderids) . "')");
            if ($cur == $_POST['mc_currency'] && $total == $_POST['mc_gross']) {
                cw_order_process_capture($orderids);
            }
            exit;
        } elseif ($cur != $_POST['mc_currency']) {
            $bill_message = "Declined: Payment amount mismatch: wrong order currency ( ".$cur." <> ".$_POST['mc_currency']." ).";
        } else {
            $bill_message = "Declined (processor error)";
        }
*/
/*
    } elseif (!strcasecmp($payment_status, "Voided")) { 

        # Order(s) voided on PayPal backend
        $orderids = cw_paypal_get_capture_orderid($auth_id);
        cw_order_process_void($orderids);
        exit;

    } elseif (!strcasecmp($payment_status, "Refunded")) {
        # Register Refund transaction
        if (!empty($parent_txn_id))
            cw_paypal_reg_refund($parent_txn_id, $txn_id);

        exit;
*/
    } else {
        $bill_message = "Declined";
        $bill_output["code"] = 2;
    }

    $bill_output["billmes"] = "$bill_message Status: ".$payment_status.$txinfo;
    if (!empty($pending_reason))
        $bill_output["billmes"] .= " Reason: $pending_reason";


#print "<pre>".$pp_host."<br>"; print_r(array($skey,$bill_output,$cur,$testmode,$_POST,$post,$a,$result,$bill_output));exit;
$return = cw_call('cw_payment_check_results', array($bill_output));
#mail("sdg@shabaev.com","http://printdrop.com",print_r(array($skey,$bill_output,$pp_host,$testmode,$_GET,$_POST,$txt,$post,$a,$result,$bill_output,$return),1));
cw_call('cw_payment_stop', array($return));
