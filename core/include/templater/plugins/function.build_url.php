<?php
/**
 * function build correct url from base URL and GET parameters
 * get parameters can be passed separately as params of {build_url} tag or as array in parameter "params"
 * 
 * @param url - base url
 * @param assign - optional name of smarty var
 * @param params - array of get params
 * other params considered as part of "params"
 * 
 * @example {build_url url='http://www.domain.com/cw/index.php?target=orders' mode='list' page='1' params=$array_of_get_params assign='order_url'}
 */
function smarty_function_build_url($params, &$smarty) {

    $base_url   = $params['url'];

    $get_params = $params['params'];

    $assign     = $params['assign'];

    $force_sign = $params['force_sign'];

    cw_unset($params,'url','params','assign','force_sign');

    foreach ($params as $k => $v) {
        $get_params[$k] = $v;
    }

    $url = cw_core_assign_addition_params($base_url, $get_params);
    
    if ($force_sign) {
        if (strpos($url,'?')===false) $url .= '?';
        else $url .= '&';
    }

    if (empty($assign)) return $url;

    $smarty->assign($assign, $url);

    return null;
}
