<?php
namespace cw\api_server;

/** =============================
 ** Addon functions, API
 ** =============================
 **/


/**
 * get current API call data
 */
function api_data_get($name) {
    global $api_data;

    if (is_array($api_data)) return $api_data[$name];
    return null;
}

/**
 * set current API call data
 */
function api_data_set($name, $value) {
    global $api_data;

    $api_data[$name] = $value;

    return $value;
}

/**
 * check if key is allowed for this API method
 * must be hooked by addon with API implementation
 */
function api_is_key_valid($key, $method) {
    return null;
}


/**
 * return api secret for this API
 * must be hooked by addon with API implementation
 * 
 * @return  null    - by default or if addon does not handle this method
 *          false   - for public method without signature
 *          string  - api secret word for protected/encrypted API
 * 
 */
function api_secret_get($key, $method) {
    return null;
}
