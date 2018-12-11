<?php
/**
 * Implementation of basic echo API
 */
namespace cw\api_server\echo_api;

// Echo API available with any key
function api_is_key_valid($key, $method) {
    if ($method == 'echo' && !empty($key)) return true;
    
    return null;
}

// Echo API is public
function api_secret_get($key, $method) {
    if ($method == 'echo') return false;
    
    return null;
}

// Echo API main function
function api_method_exec($method) {
    
    $response = cw_call('cw\api_server\api_data_get', array('request'));
    
    return $response;
}
