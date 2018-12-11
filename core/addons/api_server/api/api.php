<?php
namespace cw\api_server;

$api_server_version = $request_prepared['api_server_version'];

if (empty($api_server_version)) $api_server_version = 1;

$action_function = 'api_call_'.$api_server_version;

// Call action
$action_result = cw_call('cw\\'.addon_name.'\\'.$action_function);

cw_call('cw\\'.addon_name.'\api_prepare_output_'.$api_server_version, array($action_result));
exit();

/* ================================================================================== */

/* Actions */

/**
 * request data
 * [api_key]    => API key
 * [api_sign]   => optional for public API; required for signed/encrypted API. It is md5(request+api_secret). Never pass api_secret with API request
 * [api_server_version] => version of communication protocol with API server
 * [version]    => version of requested api method
 * [encrypted]  => true|1 for encrypted requests
 * [method]     => requested API method
 * [request]    => JSON encoded request data (can be encypted, api_sign must be calculated after encryption)
 * 
 * response
 * @see function api_prepare_output
 */
function api_call_1() {

    global $request_prepared;
    
    $api_key    = api_data_set('api_key',$request_prepared['api_key']);
    $api_method = api_data_set('method', cw_core_identifier($request_prepared['method']));
    $version = api_data_set('version', $request_prepared['version']);

    api_data_set('api_server_version', $request_prepared['api_server_version']);

    $api_sign   = $request_prepared['api_sign'];
    
    if (!$api_key) {
        return error('api_key is empty');
    }
    
    $api_is_key_valid = cw_call('cw\api_server\api_is_key_valid', array($api_key, $api_method));
    if (!$api_is_key_valid || is_error($api_is_key_valid)) {
        return is_error($api_is_key_valid)?$api_is_key_valid:error('api_key is not valid');
    }
    
    if (empty($api_method)) {
        return error('API method is not specified');
    }
    
    $api_secret = cw_call('cw\api_server\api_secret_get', array($api_key, $api_method));
    if ($api_secret !== false && empty($api_sign)) {
        return error('This API method is not public - api_sign required');
    }
    
    if ($api_sign) {
        if (empty($api_secret)) {
            return error('Request is signed with unknown API secret');
        }
        $is_valid_sign = $api_sign == md5($request_prepared['request'].$api_secret);
        if (empty($api_secret)) {
            return error('Invalid api_sign');
        }
      
        api_data_set('is_signed', $is_valid_sign);
    }

    $request_prepared['request'] = stripslashes($request_prepared['request']);
    
    if (!empty($request_prepared['encrypted'])) {
        if (empty($api_secret)) {
            return error('Request is encrypted with unknown API secret');
        }
        api_data_set('is_encrypted', $is_valid_sign);
        $request =  mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $api_secret, $request_prepared['request'], MCRYPT_MODE_ECB);
        if (empty($request)) {
            return error('Cannot decrypt request');
        }
        api_data_set('request', json_decode(trim($request), true));
    } else {
        api_data_set('request', json_decode(trim($request_prepared['request']), true));
    }

    $result = cw_call('cw\api_server\api_method_exec_'.$api_method, array($api_method));
    
    if (empty($result)) {
        return error('Method '.$api_method.' is not implemented');
    }
    
    return $result;

}

/* Service functions */
/**
 * Response format - JSON encoded array:
 * [api_key]    => API key
 * [api_sign]   => signature if request was signed
 * [version]    => version of requested api method
 * [encrypted]  => 1 for encrypted requests
 * [method]     => requested API method
 * [status]     => success|error
 * [message]    => status message
 * [response]   => (string) JSON encoded response data (can be encypted)
 */
function api_prepare_output_1($result) {
    
    $out = array();
    $out['api_key'] = api_data_get('api_key');
    $out['method']  = api_data_get('method');
    $out['version'] = api_data_get('version');    
    $out['status'] = 'success';
    
    if (is_error($result)) {
        $out['status'] = 'error';
        $out['message'] = $result->getMessage();
        $result = null;
        cw_log_add('API',$out);
    }

    $api_secret = cw_call('cw\api_server\api_secret_get', array($out['api_key'], $out['method']));
    $result = json_encode($result);
    
    if (api_data_get('is_encrypted')) {
        $result = mcrypt_encrypt(MCRYPT_RIJNDAEL_256,$api_secret,$result,MCRYPT_MODE_ECB);
        $out['encrypted'] = 1;
    }
    
    if (api_data_get('is_signed')) {
        $out['api_sign'] = md5($result.$api_secret);
    }
    
    $out['response'] = $result;
    
    header('Content-type: application/json');
    echo json_encode($out);
    exit();
}
