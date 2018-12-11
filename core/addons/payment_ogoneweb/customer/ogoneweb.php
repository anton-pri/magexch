<?php
namespace cw\payment_ogoneweb;

// use $mode and $action params to define subject and action to call

$action_function = $mode;

if (in_array($action_function, array('cancel','exception'))) $action_function = 'decline';

// Default action
if (empty($action_function) || !function_exists('cw\\'.addon_name.'\\'.$action_function)) {
    $action_function = 'wrong_call';
}

// Call action
$action_result = cw_call('cw\\'.addon_name.'\\'.$action_function);

// Action can return instance of Error via error() function
// see docs/core.error.txt
if (is_error($action_result)) {
    cw_add_top_message($action_result->getMessage(), 'E');
    if (constant('OGONE_DBG')) cw_log_add('ogone', array('controller error',$action_result->getMessage(), $request_prepared), false);
}

exit();

return $action_result;

/* ================================================================================== */
/* Actions */

/**
 * Payment accepted
 */
function accept() {
    global $tables, $request_prepared;
    
    if (empty($request_prepared['secureid'])) {
        return error('Invalid secureid'); // return Error instance
    }

    $skey = $request_prepared['secureid']; // secureid - ref_id in table payment_data

    $payment_data = cw_call('cw_payment_get_data', array($skey));

    cw_call('cw_payment_put_data', array($skey, array('state'=>'END','status'=>'accept')));

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

    if (constant('OGONE_DBG')) cw_log_add('ogone', array('accept',$payment_data), false);

    cw_call('cw_payment_stop', array($payment_data));

}

/**
 * Payment declined
 */
function decline() {
    global $request_prepared;
    
    if (empty($request_prepared['secureid'])) {
        return error('Invalid secureid'); // return Error instance
    }
    
    $skey = $request_prepared['secureid']; // secureid - ref_id in table payment_data
    
    $payment_data = cw_call('cw_payment_get_data', array($skey));
    cw_call('cw_payment_put_data', array($skey, array('state'=>'END','status'=>$request_prepared['mode'])));

    $payment_data['sess_id'] = $payment_data['session_id'];

    $payment_data['bill_error'] = $request_prepared['mode'];
    
    if ($request_prepared['mode'] == 'decline')
        $payment_data['reason'] = 'Acquirer rejects the authorisation more than the maximum of authorised tries (mode=decline)';
    if ($request_prepared['mode'] == 'cancel')
        $payment_data['reason'] = 'Customer cancels the payment (mode=cancel)';
    if ($request_prepared['mode'] == 'exception')
        $payment_data['reason'] = 'The payment result is uncertain. (mode=exception)';

    if (constant('OGONE_DBG')) cw_log_add('ogone', array($request_prepared['mode'],$payment_data), false);

    $payment_data['billmes'].= " (Return code: ".$request_prepared['mode']."; CW: ".$payment_data['reason'].")";

    $payment_data['code'] = 2;
        
    cw_call('cw_payment_put_data', array($skey, array('billmes'=>$payment_data["billmes"])));

    if (constant('OGONE_DBG')) cw_log_add('ogone', array('decline end',$payment_data), false);

    $return = cw_call('cw_payment_check_results', array($payment_data));

    if (constant('OGONE_DBG')) cw_log_add('ogone', array('decline stop',$return), false);

    cw_call('cw_payment_stop', array($return));

}

/**
 * Callback handler for server-to-server callback
 * 
 * Callback URL for setting in OGONE backend is
 * http://domain.com/cw_dir/index.php?target=ogoneweb&mode=callback
 */
function callback() {
    global $request_prepared, $config;
    
    list($errerr,$staerr) = ogone_get_errerr();

    if (defined('OGONE_DBG')) {
        cw_log_add('ogone', array('callback POST',$request_prepared), false);
    }

    $skey                   = $request_prepared["COMPLUS"];
    $payment_data           = cw_call('cw_payment_get_data', array($skey));

    if (empty($payment_data))
        return error('Order is not found in stored payment data');

    $STATUS = $request_prepared["STATUS"];
    $PAYID = $request_prepared["PAYID"];
    $ACCEPTANCE = $request_prepared["ACCEPTANCE"];
    $PM = $request_prepared["PM"];
    $BRAND = $request_prepared["BRAND"];
    $CARDNO = $request_prepared["CARDNO"];
    $NCERROR = $request_prepared["NCERROR"];
    $SHASIGN = $request_prepared["SHASIGN"];

    $bill_output['sess_id'] = $payment_data['session_id'];
    $bill_output['billmes'] = (empty($staerr[$STATUS]) ? "Status code: ".$STATUS : $staerr[$STATUS]);

    if ($STATUS == '5' || $STATUS== '9') {
        $bill_output['code']=1;
        $bill_output['billmes'].= " (PayID: ".$PAYID.")";
        $bill_output['billmes'].= " (ACCEPTANCE: ".$ACCEPTANCE.")";
        $bill_output['billmes'].= " (".$PM.": ".$BRAND." ".$CARDNO.")";

    } elseif ($STATUS == '4' || $STATUS == '41' || $STATUS == '51' || $STATUS == '91') { // Order stored => queued status
        $bill_output['code']=3;
        $bill_output['billmes'].= " (PayID: ".$PAYID.")";
        $bill_output['billmes'].= " (ACCEPTANCE: ".$ACCEPTANCE.")";
        $bill_output['billmes'].= " (".$PM.": ".$BRAND." ".$CARDNO.")";
    } else {
        $bill_output['code']=2;
    }

    if ($NCERROR) {
        $errc = (empty($errerr[$NCERROR]) ? "Error Code: ".$NCERROR : $errerr[$NCERROR]);
        $bill_output['billmes'].= " (".$errc.") ";
    }

    if (isset($request_prepared['amount'])) {
        $payment_return = array(
            'total' => $request_prepared['amount']
        );

        if (isset($request_prepared['currency'])) {
            $payment_return['currency'] = $request_prepared['currency'];
            $payment_return['_currency'] = $config['payment_ogoneweb']['ogoneweb_cur'];
        }
    }

    /**
     * Security Checking for Post-payment feedback (CALLBACK)
     */
    $is_fake_callback = false;
    $pp_merch   = $config['payment_ogoneweb']['ogoneweb_pspid'];
    $pp_secret  = $config['payment_ogoneweb']['ogoneweb_sign'];
    $pp_curr    = $config['payment_ogoneweb']['ogoneweb_cur'];
    $pp_tp      = trim($config['payment_ogoneweb']['ogoneweb_tp']);

    #Level 1 Security Checking
    if (empty($PAYID))
        $is_fake_callback = true;

    #Level 3 Security Checking
    if (!empty($SHASIGN) && !$is_fake_callback) {

        // Generate SHAsignature based on _POST var
        $sha1 = ogone_generate_signature($_POST, 'associative_array', $pp_secret);


        if ($sha1 != $SHASIGN)
            $is_fake_callback = true;
    }
    
    if (defined('OGONE_DBG')) {
        cw_log_add('ogone', array('signature'=>$sha1,'is_fake_callback'=>$is_fake_callback), false);
    }
    
    if (!$is_fake_callback) {
        
        cw_call('cw_payment_put_data', array($skey, array('billmes'=>$bill_output["billmes"])));

        if (constant('OGONE_DBG')) cw_log_add('ogone', array('callback end',$bill_output), false);

        $return = cw_call('cw_payment_check_results', array($bill_output));

        if (constant('OGONE_DBG')) cw_log_add('ogone', array('callback stop',$return), false);

        cw_call('cw_payment_stop', array($return));
        
    } else {
        
        if (constant('OGONE_DBG')) cw_log_add('ogone', array('callback fake',$bill_output), false);
        return error('Security tests were not passed');
        
    }
}


/*
 * Whole controller called in wrong way
 */
function wrong_call() {
    return error('Incorrect Ogone callback');
}

/* Service functions */

/**
 * Data function returns array of errors codes and descriptions
 */
function ogone_get_errerr() {
    
    $errerr = array(
        '00000000' => "Automatic Address and CVC check successful ",
        '00000001' => "Automatic CVC check successful (address not checked) ",
        '00000002' => "Automatic Address check successful (CVC not checked) ",
        '00000003' => "Data Not Matched (Address and CVC) ",
        '00000004' => "Data Not Checked ",
        '00000011' => "Automatic CVC check successful (address wrong) ",
        '00000012' => "Automatic Address check successful (CVC wrong) ",
        '0020001001' => "Authorization failed, please retry ",
        '0020001002' => "Authorization failed, please retry ",
        '0020001003' => "Authorization failed, please retry ",
        '0020001004' => "Authorization failed, please retry ",
        '0020001005' => "Authorization failed, please retry ",
        '0020001006' => "Authorization failed, please retry ",
        '0020001007' => "Authorization failed, please retry ",
        '0020001008' => "Authorization failed, please retry ",
        '0020001009' => "Authorization failed, please retry ",
        '0020001010' => "Authorization failed, please retry ",
        '0030001999' => "Our payment system is currently under maintenance, please try later ",
        '0050001005' => "Expiry date error ",
        '0050001007' => "Requested Operation code not allowed ",
        '0050001008' => "Invalid delay value ",
        '0050001010' => "Input date in invalid format ",
        '0050001013' => "Unable to parse socket input stream ",
        '0050001014' => "Error in parsing stream content ",
        '0050001015' => "Currency error ",
        '0050001016' => "Transaction still posted at end of wait ",
        '0050001017' => "Sync value not compatible with delay value ",
        '0050001019' => "Transaction duplicate of a pre-existing transaction ",
        '0050001020' => "Acceptation code empty while required for the transaction ",
        '0050001024' => "Maintenance acquirer differs from original transaction acquirer ",
        '0050001025' => "Maintenance merchant differs from original transaction merchant ",
        '0050001028' => "Maintenance operation not accurate for the original transaction ",
        '0050001031' => "Host application unknown for the transaction ",
        '0050001032' => "Unable to perform requested operation with requested currency ",
        '0050001033' => "Maintenance card number differs from original transaction card number ",
        '0050001034' => "Operation code not allowed ",
        '0050001035' => "Exception occurred in socket input stream treatment ",
        '0050001036' => "Card length does not correspond to an acceptable value for the brand ",
        '0050001068' => "A technical problem occurred, please contact helpdesk ",
        '0050001069' => "Invalid check for CardID and Brand ",
        '0050001070' => "A technical problem occurred, please contact helpdesk ",
        '0050001116' => "Unknown origin IP ",
        '0050001117' => "No origin IP detected ",
        '0050001118' => "Merchant configuration problem, please contact support ",
        '10001001' => "Communication failure ",
        '10001002' => "Communication failure ",
        '10001003' => "Communication failure ",
        '10001004' => "Communication failure ",
        '10001005' => "Communication failure ",
        '20001001' => "We received an unknown status for the transaction. We will contact your acquirer and update the status of the transaction within one working day. Please check the status later. ",
        '20001002' => "We received an unknown status for the transaction. We will contact your acquirer and update the status of the transaction within one working day. Please check the status later. ",
        '20001003' => "We received an unknown status for the transaction. We will contact your acquirer and update the status of the transaction within one working day. Please check the status later. ",
        '20001004' => "We received an unknown status for the transaction. We will contact your acquirer and update the status of the transaction within one working day. Please check the status later. ",
        '20001005' => "We received an unknown status for the transaction. We will contact your acquirer and update the status of the transaction within one working day. Please check the status later. ",
        '20001006' => "We received an unknown status for the transaction. We will contact your acquirer and update the status of the transaction within one working day. Please check the status later. ",
        '20001007' => "We received an unknown status for the transaction. We will contact your acquirer and update the status of the transaction within one working day. Please check the status later. ",
        '20001008' => "We received an unknown status for the transaction. We will contact your acquirer and update the status of the transaction within one working day. Please check the status later. ",
        '20001009' => "We received an unknown status for the transaction. We will contact your acquirer and update the status of the transaction within one working day. Please check the status later. ",
        '20001010' => "We received an unknown status for the transaction. We will contact your acquirer and update the status of the transaction within one working day. Please check the status later. ",
        '20001111' => "A technical problem occurred, please contact helpdesk ",
        '20002001' => "Origin for the response of the bank can not be checked ",
        '20002002' => "Beneficiary account number has been modified during processing ",
        '20002003' => "Amount has been modified during processing ",
        '20002004' => "Currency has been modified during processing ",
        '20002005' => "No feedback from the bank server has been detected ",
        '30001001' => "Payment refused by the acquirer ",
        '30001002' => "Duplicate request ",
        '30001010' => "A technical problem occurred, please contact helpdesk ",
        '30001011' => "A technical problem occurred, please contact helpdesk ",
        '30001012' => "Card black listed - Contact acquirer ",
        '30001051' => "A technical problem occurred, please contact helpdesk ",
        '30001054' => "A technical problem occurred, please contact helpdesk ",
        '30001100' => "Unauthorized buyer's country ",
        '30001997' => "Authorization canceled by simulation ",
        '30001998' => "A technical problem occurred, please try again. ",
        '30001999' => "Your merchant's acquirer is temporarily unavailable, please try later or choose another payment method. ",
        '30002001' => "Payment refused by the financial institution ",
        '30021001' => "Call acquirer support call number. ",
        '30031001' => "Invalid merchant number. ",
        '30041001' => "Retain card. ",
        '30051001' => "Authorization declined ",
        '30071001' => "Retain card - special conditions. ",
        '30121001' => "Invalid transaction ",
        '30131001' => "Invalid amount ",
        '30141001' => "Invalid card number ",
        '30151001' => "Unknown acquiring institution. ",
        '30171001' => "Client cancellation. ",
        '30191001' => "Try again later. ",
        '30201001' => "A technical problem occurred, please contact helpdesk ",
        '30301001' => "Invalid format ",
        '30311001' => "Unknown acquirer ID. ",
        '30331001' => "Card expired. ",
        '30341001' => "Suspicion of fraud. ",
        '30381001' => "A technical problem occurred, please contact helpdesk ",
        '30401001' => "Invalid functionM. ",
        '30411001' => "Lost card. ",
        '30431001' => "Stolen card, pick up ",
        '30511001' => "Insufficient funds. ",
        '30541001' => "Card expired. ",
        '30551001' => "Invalid PIN. ",
        '30561001' => "Card not in authorizer's database. ",
        '30571001' => "Card number changed from ref. trn. ",
        '30581001' => "Transaction interdite au terminal ",
        '30591001' => "Suspicion of fraud. ",
        '30601001' => "The merchant must contact the acquirer. ",
        '30611001' => "Amount exceeds card ceiling. ",
        '30621001' => "Restricted card. ",
        '30631001' => "Security policy not respected. ",
        '30641001' => "Amount changed from ref. trn. ",
        '30681001' => "Tardy response. ",
        '30751001' => "PIN entered incorrectly too often ",
        '30761001' => "Card holder already contesting. ",
        '30771001' => "PIN entry required. ",
        '30811001' => "Message flow error. ",
        '30821001' => "Authorization center unavailable ",
        '30831001' => "Authorization center unavailable ",
        '30901001' => "Temporary system shutdown. ",
        '30911001' => "Acquirer unavailable. ",
        '30921001' => "Invalid card type for acquirer. ",
        '30941001' => "Duplicate transaction ",
        '30961001' => "Processing temporarily not possible ",
        '30971001' => "A technical problem occurred, please contact helpdesk ",
        '30981001' => "A technical problem occurred, please contact helpdesk ",
        '31011001' => "Unknown acceptance code ",
        '31021001' => "Invalid currency ",
        '31031001' => "Acceptance code missing ",
        '31041001' => "Inactive card ",
        '31051001' => "Merchant not active ",
        '31061001' => "Invalid expiration date ",
        '31071001' => "Interrupted host communication ",
        '31081001' => "Card refused ",
        '31091001' => "Invalid password ",
        '31101001' => "Plafond transaction (majore du bonus) depasse ",
        '31111001' => "Plafond mensuel (majore du bonus) depasse ",
        '31121001' => "Plafond centre de facturation depasse ",
        '31131001' => "Plafond entreprise depasse ",
        '31141001' => "Code MCC du fournisseur non autorise pour la carte ",
        '31151001' => "Numero SIRET du fournisseur non autorise pour la carte ",
        '39991001' => "A technical problem occurred, please contact helpdesk ",
        '40001001' => "A technical problem occurred, please try again. ",
        '40001002' => "A technical problem occurred, please try again. ",
        '40001003' => "A technical problem occurred, please try again. ",
        '40001004' => "A technical problem occurred, please try again. ",
        '40001005' => "A technical problem occurred, please try again. ",
        '40001006' => "A technical problem occurred, please try again. ",
        '40001007' => "A technical problem occurred, please try again. ",
        '40001008' => "A technical problem occurred, please try again. ",
        '40001009' => "A technical problem occurred, please try again. ",
        '40001010' => "A technical problem occurred, please try again. ",
        '40001012' => "Your merchant's acquirer is temporarily unavailable, please try later or choose another payment method. ",
        '40001013' => "A technical problem occurred, please contact helpdesk ",
        '40001016' => "A technical problem occurred, please contact helpdesk ",
        '40001050' => "A technical problem occurred, please contact helpdesk ",
        '50001001' => "Unknown credit card type ",
        '50001002' => "Credit card number format check failed for given credit card number. ",
        '50001003' => "Merchant data error ",
        '50001004' => "Merchant identification missing ",
        '50001005' => "Expiry date error ",
        '50001006' => "Amount is not a number ",
        '50001007' => "A technical problem occurred, please contact helpdesk ",
        '50001008' => "A technical problem occurred, please contact helpdesk ",
        '50001009' => "A technical problem occurred, please contact helpdesk ",
        '50001010' => "A technical problem occurred, please contact helpdesk ",
        '50001011' => "Brand not supported for that merchant ",
        '50001012' => "A technical problem occurred, please contact helpdesk ",
        '50001013' => "A technical problem occurred, please contact helpdesk ",
        '50001014' => "A technical problem occurred, please contact helpdesk ",
        '50001015' => "Invalid currency code ",
        '50001016' => "A technical problem occurred, please contact helpdesk ",
        '50001017' => "A technical problem occurred, please contact helpdesk ",
        '50001018' => "A technical problem occurred, please contact helpdesk ",
        '50001019' => "A technical problem occurred, please contact helpdesk ",
        '50001020' => "A technical problem occurred, please contact helpdesk ",
        '50001021' => "A technical problem occurred, please contact helpdesk ",
        '50001022' => "A technical problem occurred, please contact helpdesk ",
        '50001023' => "A technical problem occurred, please contact helpdesk ",
        '50001024' => "A technical problem occurred, please contact helpdesk ",
        '50001025' => "A technical problem occurred, please contact helpdesk ",
        '50001026' => "A technical problem occurred, please contact helpdesk ",
        '50001027' => "A technical problem occurred, please contact helpdesk ",
        '50001028' => "A technical problem occurred, please contact helpdesk ",
        '50001029' => "A technical problem occurred, please contact helpdesk ",
        '50001030' => "A technical problem occurred, please contact helpdesk ",
        '50001031' => "A technical problem occurred, please contact helpdesk ",
        '50001032' => "A technical problem occurred, please contact helpdesk ",
        '50001033' => "A technical problem occurred, please contact helpdesk ",
        '50001034' => "A technical problem occurred, please contact helpdesk ",
        '50001035' => "A technical problem occurred, please contact helpdesk ",
        '50001036' => "Card length does not correspond to an acceptable value for the brand ",
        '50001037' => "Purchasing card number for a regular merchant ",
        '50001038' => "Non Purchasing card for a Purchasing card merchant ",
        '50001039' => "Details sent for a non-Purchasing card merchant, please contact helpdesk ",
        '50001040' => "Details not sent for a Purchasing card transaction, please contact helpdesk ",
        '50001041' => "Payment detail validation failed ",
        '50001042' => "Given transactions amounts (tax,discount,shipping,net,etc) do not compute correctly together ",
        '50001043' => "A technical problem occurred, please contact helpdesk ",
        '50001044' => "No acquirer configured for this operation ",
        '50001045' => "No UID configured for this operation ",
        '50001046' => "Operation not allowed for the merchant ",
        '50001047' => "A technical problem occurred, please contact helpdesk ",
        '50001048' => "A technical problem occurred, please contact helpdesk ",
        '50001049' => "A technical problem occurred, please contact helpdesk ",
        '50001050' => "A technical problem occurred, please contact helpdesk ",
        '50001051' => "A technical problem occurred, please contact helpdesk ",
        '50001052' => "A technical problem occurred, please contact helpdesk ",
        '50001053' => "A technical problem occurred, please contact helpdesk ",
        '50001054' => "Card detection routine did not find any brand that matches ",
        '50001055' => "A technical problem occurred, please contact helpdesk ",
        '50001056' => "A technical problem occurred, please contact helpdesk ",
        '50001057' => "A technical problem occurred, please contact helpdesk ",
        '50001058' => "A technical problem occurred, please contact helpdesk ",
        '50001059' => "A technical problem occurred, please contact helpdesk ",
        '50001060' => "A technical problem occurred, please contact helpdesk ",
        '50001061' => "A technical problem occurred, please contact helpdesk ",
        '50001062' => "A technical problem occurred, please contact helpdesk ",
        '50001063' => "Card Issue Number does not correspond to range or not present ",
        '50001064' => "Start Date not valid or not present ",
        '50001066' => "Format of CVC code invalid ",
        '50001111' => "Data validation error ",
        '50001113' => "This order has already been processed ",
        '50001114' => "Error pre-payment check page access ",
        '50001115' => "Request not received in secure mode ",
        '50001116' => "Unknown IP address origin ",
        '50001117' => "NO IP address origin ",
        '50001118' => "Pspid not found or not correct ",
        '50001119' => "Password incorrect or disabled due to numbers of errors ",
        '50001120' => "Invalid currency ",
        '50001121' => "Invalid number of decimals for the currency ",
        '50001122' => "Currency not accepted by the merchant ",
        '50001123' => "Card type not active ",
        '50001124' => "Number of lines don't match with number of payments ",
        '50001125' => "Format validation error ",
        '50001126' => "Overflow in data capture requests for the original order ",
        '50001127' => "The original order is not in a correct status ",
        '50001128' => "missing authorization code for unauthorized order ",
        '50001129' => "Overflow in refunds requests ",
        '50001130' => "Error access to original order ",
        '50001131' => "Error access to original history item ",
        '50001133' => "Duplicate request ",
        '50001134' => "Authentication failed, please retry or cancel. ",
        '50001135' => "Authentication temporary unavailable, please retry or cancel. ",
        '50001136' => "Technical problem with your browser, please retry or cancel ",
        '50001137' => "Your bank access control server is temporary unavailable, please retry or cancel ",
        '50001150' => "Fraud Detection, Technical error (IP not valid) ",
        '50001151' => "Fraud detection : technical error (IPCTY unknown or error) ",
        '50001152' => "Fraud detection : technical error (CCCTY unknown or error) ",
        '60000001' => "account number unknown ",
        '60000003' => "not credited dd-mm-yy ",
        '60000005' => "name/number do not correspond ",
        '60000007' => "account number blocked ",
        '60000008' => "specific direct debit block ",
        '60000009' => "account number WKA ",
        '60000010' => "administrative reason ",
        '60000011' => "account number expired ",
        '60000012' => "no direct debit authorisation given ",
        '60000013' => "debit not approved ",
        '60000014' => "double payment ",
        '60000018' => "name/address/city not entered ",
        '60001001' => "no original direct debit for revocation ",
        '60001002' => "payer's account number format error ",
        '60001004' => "payer's account at different bank ",
        '60001005' => "payee's account at different bank ",
        '60001006' => "payee's account number format error ",
        '60001007' => "payer's account number blocked ",
        '60001008' => "payer's account number expired ",
        '60001009' => "payee's account number expired ",
        '60001010' => "direct debit not possible ",
        '60001011' => "creditor payment not possible ",
        '60001012' => "payer's account number unknown WKA-number ",
        '60001013' => "payee's account number unknown WKA-number ",
        '60001014' => "impermissible WKA transaction ",
        '60001015' => "period for revocation expired ",
        '60001017' => "reason for revocation not correct ",
        '60001018' => "original run number not numeric ",
        '60001019' => "payment ID (betalingskenmerk) incorrect ",
        '60001020' => "amount not numeric ",
        '60001021' => "amount zero not permitted ",
        '60001022' => "negative amount not permitted ",
        '60001023' => "payer and payee giro account number ",
        '60001025' => "processing code (verwerkingscode) incorrect ",
        '60001028' => "revocation not permitted ",
        '60001029' => "guaranteed direct debit on giro account number ",
        '60001030' => "NBC transaction type incorrect ",
        '60001031' => "description too large ",
        '60001032' => "book account number not issued ",
        '60001034' => "book account number incorrect ",
        '60001035' => "payer's account number not numeric ",
        '60001036' => "payer's account number not eleven-proof ",
        '60001037' => "payer's account number not issued ",
        '60001039' => "payer's account number of DNB/BGC/BLA ",
        '60001040' => "payee's account number not numeric ",
        '60001041' => "payee's account number not eleven-proof ",
        '60001042' => "payee's account number not issued ",
        '60001044' => "payee's account number unknown ",
        '60001050' => "payee's name missing ",
        '60001051' => "indicate payee's bank account number instead of 3102 ",
        '60001052' => "no direct debit contract ",
        '60001053' => "amount beyond bounds ",
        '60001054' => "selective direct debit block ",
        '60001055' => "original run number unknown ",
        '60001057' => "payer's name missing ",
        '60001058' => "payee's account number missing ",
        '60001059' => "restore not permitted ",
        '60001060' => "bank's reference (navraaggegeven) missing ",
        '60001061' => "BEC/GBK number incorrect ",
        '60001062' => "BEC/GBK code incorrect ",
        '60001087' => "book account number not numeric ",
        '60001090' => "cancelled on request ",
        '60001091' => "cancellation order executed ",
        '60001092' => "cancelled instead of bended ",
        '60001093' => "book account number is a shortened account number ",
        '60001094' => "instructing party account number not identical with payer ",
        '60001095' => "payee unknown GBK acceptor ",
        '60001097' => "instructing party account number not identical with payee ",
        '60001099' => "clearing not permitted ",
        '60001101' => "payer's account number not spaces ",
        '60001102' => "PAN length not numeric ",
        '60001103' => "PAN length outside limits ",
        '60001104' => "track number not numeric ",
        '60001105' => "track number not valid ",
        '60001106' => "PAN sequence number not numeric ",
        '60001107' => "domestic PAN not numeric ",
        '60001108' => "domestic PAN not eleven-proof ",
        '60001109' => "domestic PAN not issued ",
        '60001110' => "foreign PAN not numeric ",
        '60001111' => "card valid date not numeric ",
        '60001112' => "book period number (boekperiodenr) not numeric ",
        '60001113' => "transaction number not numeric ",
        '60001114' => "transaction time not numeric ",
        '60001115' => "transaction no valid time ",
        '60001116' => "transaction date not numeric ",
        '60001117' => "transaction no valid date ",
        '60001118' => "STAN not numeric ",
        '60001119' => "instructing party's name missing ",
        '60001120' => "foreign amount (bedrag-vv) not numeric ",
        '60001122' => "rate (verrekenkoers) not numeric ",
        '60001125' => "number of decimals (aantaldecimalen) incorrect ",
        '60001126' => "tariff (tarifering) not B/O/S ",
        '60001127' => "domestic costs (kostenbinnenland) not numeric ",
        '60001128' => "domestic costs (kostenbinnenland) not higher than zero ",
        '60001129' => "foreign costs (kostenbuitenland) not numeric ",
        '60001130' => "foreign costs (kostenbuitenland) not higher than zero ",
        '60001131' => "domestic costs (kostenbinnenland) not zero ",
        '60001132' => "foreign costs (kostenbuitenland) not zero ",
        '60001134' => "Euro record not fully filled in ",
        '60001135' => "Client currency incorrect ",
        '60001136' => "Amount NLG not numeric ",
        '60001137' => "Amount NLG not higher than zero ",
        '60001138' => "Amount NLG not equal to Amount ",
        '60001139' => "Amount NLG incorrectly converted ",
        '60001140' => "Amount EUR not numeric ",
        '60001141' => "Amount EUR not greater than zero ",
        '60001142' => "Amount EUR not equal to Amount ",
        '60001143' => "Amount EUR incorrectly converted ",
        '60001144' => "Client currency not NLG ",
        '60001145' => "rate euro-vv (Koerseuro-vv) not numeric ",
        '60001146' => "comma rate euro-vv (Kommakoerseuro-vv) incorrect ",
        '60001147' => "acceptgiro distributor not valid ",
        '60001148' => "Original run number and/or BRN are missing ",
        '60001149' => "Amount/Account number/ BRN different ",
        '60001150' => "Direct debit already revoked/restored ",
        '60001151' => "Direct debit already reversed/revoked/restored ",
        '60001153' => "Payer's account number not known "
    );

    $staerr = array(
        '0' => "In creation ",
        '1' => "Canceled by client ",
        '2' => "Authorization refused ",
        '4' => "Order stored ",
        '5' => "Authorized ",
        '51' => "Authorization waiting ",
        '52' => "Authorization not known ",
        '55' => "Stand-by ",
        '59' => "Authoriz. to get manually ",
        '6' => "Authorized and canceled ",
        '61' => "Author. deletion waiting ",
        '62' => "Author. deletion uncertain ",
        '63' => "Author. deletion refused ",
        '64' => "Authorized and canceled ",
        '7' => "Payment deleted ",
        '71' => "Payment deletion pending ",
        '72' => "Payment deletion uncertain ",
        '73' => "Payment deletion refused ",
        '74' => "Payment deleted ",
        '8' => "Refund ",
        '81' => "Refund pending ",
        '82' => "Refund uncertain ",
        '83' => "Refund refused ",
        '84' => "Payment declined by the acquirer ",
        '9' => "Payment requested ",
        '91' => "Payment processing ",
        '92' => "Payment uncertain ",
        '93' => "Payment refused ",
        '94' => "Refund declined by the acquirer ",
        '99' => "Being processed "
    );
    
    return array($errerr, $staerr);
}
