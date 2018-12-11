<?php
// Common namespace

/**
 * This function can be used to call API server
 * 
 * @param array $params      - reqiust data (see doc to understand necessary structure)
 * @param string $api_url    - URL where API server runs
 * @param string $api_secret - optional secret word for secure api methods. secret is not sent with request, API server should store and know your secret by your API key
 * @param bool $encrypted    - encrypt request
 * 
 * @return array $response - full response including API server data and requested method data
 */
function cw_api_server_call($params, $api_url, $api_secret='', $encrypted=false) {
    global $config;
    cw_load('http');

    if (!is_array($params) || empty($params)) return error('Empty params for api call');
    if (empty($params['method'])) return error('"method" is required for api call');

// API Server version
    $params['api_server_version'] = 1;

// Encrypt & Sign
    $params['request'] = json_encode($params['request']);

    if (!empty($api_secret)) {
        if ($encrypted) {
            $params['request'] = mcrypt_encrypt(MCRYPT_RIJNDAEL_256,$api_secret,$params['request'],MCRYPT_MODE_ECB);
            $params['encrypted'] = 1;
        }       

        $params['api_sign'] = md5($params['request'].$api_secret);
    }
    $params_str = http_build_query($params);
   
    if (strpos($api_url,'http')!==0) {
        $api_url = 'http://'.$api_url; // protocol is required for correct work of parse_url()
    }
    $server = parse_url($api_url,PHP_URL_HOST);
    $script = str_replace(array('http://','https://',$server),'',$api_url);

    // return array(0=>head, 1=>body, 2=>cookie)
    $return = cw_http_post_request($server,$script,$params_str);
    
// if (strpos($return[0]['CONTENT-TYPE'],'application/json')===false) return error('API Server is not available');
    
    $return = json_decode(trim($return[1]),true);

     if ($return['api_sign']) {
        if (md5($return['response'].$api_secret)!=$return['api_sign']) {
            $return['status'] = 'error';
            $return['message'] = 'Response sign is invalid';
        }
    }
    if ($return['encrypted']) {
        $return['response'] = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $api_secret,$return['response'], MCRYPT_MODE_ECB);
    }
    
    $return['response'] = json_decode($return['response'],true);
    
    return $return;   
 
}
