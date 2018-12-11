<?php
if (defined('IS_ROBOT') && constant('IS_ROBOT')) return false;

$logged_data = array();
$logged_data['date'] = (intval($_SERVER['REQUEST_TIME'])>0)?$_SERVER['REQUEST_TIME']:time();
$logged_data['cwsid'] = $cwsid;
$logged_data['customer_id'] = $customer_id;
$logged_data['REQUEST_METHOD'] = $_SERVER['REQUEST_METHOD'];
$logged_data['REQUEST_URI'] = $_SERVER['REQUEST_URI'];
$logged_data['GET_POST'] = (!empty($_GET))?$_GET:$_POST;

$remove_params = array("card_type","card_name","card_number","card_expire_Month","card_expire_Year","card_cvv2","password");
if (!empty($logged_data['GET_POST'])) {
    $_GET_POST = array();
    $__GET_POST = array();
    foreach ($logged_data['GET_POST'] as $gp_k=>$gp_val) {
        if (!in_array($gp_k, $remove_params)) { 
            $_GET_POST[$gp_k]=$gp_val;
            $__GET_POST[$gp_k]=$gp_val;      
        } else {
            $_GET_POST[$gp_k] = "_deleted_"; 
        }
    }
    $_get_post_ser = serialize($__GET_POST);
    $clean_entire = false;
    foreach ($remove_params as $bad_str) {
        if (strpos($_get_post_ser, $bad_str) !== false) {
            $clean_entire = true; 
            break;
        } 
    }
    if ($clean_entire)
       $logged_data['GET_POST'] = array();
    else
       $logged_data['GET_POST'] = $_GET_POST; 
}

if ($target == 'index' && !empty($cat) && $page_code == '') 
    $page_code = 'category';
$logged_data['target_code'] = $target.(!empty($page_code)?"/$page_code":'');

$logged_data['HTTP_REFERER'] = $_SERVER['HTTP_REFERER'];
$logged_data['REDIRECT_URL'] = $_SERVER['REDIRECT_URL'];

$logged_data['current_area'] = $current_area;

foreach ($logged_data as $lg_data_k=>$lg_data_v) {
    if (is_array($lg_data_v)) {
        if (!empty($lg_data_v))
            $logged_data[$lg_data_k] = serialize($lg_data_v);
        else
            $logged_data[$lg_data_k] = '';
    }
    $logged_data[$lg_data_k] = addslashes($logged_data[$lg_data_k]);
}

cw_call_delayed('cw_array2insert', array('logged_data',$logged_data));

$logged_data_session = &cw_session_register("logged_data_session", array());
if (empty($logged_data_session) || $logged_data_session['cwsid'] != $cwsid) {
    $logged_data_session['cwsid'] = $cwsid;
    $logged_data_session['SERVER'] = array();

    $useful_server_fields = array('HTTP_USER_AGENT', 'HTTP_COOKIE', 'SERVER_ADDR', 'REMOTE_ADDR');

    foreach ($_SERVER as $lds_s_k => $lds_s_v) {
        if (in_array($lds_s_k, $useful_server_fields)) {
            $logged_data_session['SERVER'][$lds_s_k] = $lds_s_v;
        }
    }

    $logged_data_session['user_account'] = $user_account;
    $logged_data_session['customer_id'] = $customer_id;

    foreach ($logged_data_session as $lg_data_k=>$lg_data_v) {
        if (is_array($lg_data_v)) {
            if (!empty($lg_data_v))
                $logged_data_session[$lg_data_k] = serialize($lg_data_v);
            else
                $logged_data_session[$lg_data_k] = '';
        }
        $logged_data_session[$lg_data_k] = addslashes($logged_data_session[$lg_data_k]);
    }
   // cw_array2insert('logged_data_sessions',$logged_data_session, true);
}

unset($logged_data, $logged_data_session);
