<?php
function smarty_function_tunnel($params, &$smarty) {
    global $cw_allowed_tunnels;

    if (empty($params['func'])) {
        $smarty->trigger_error("tunnel: missing 'func' parameter");
        return;
    }

    if (!in_array($params['func'], $cw_allowed_tunnels, true)) {
        $smarty->trigger_error("tunnel: function $params[func] is not allowed for call from templates",E_USER_ERROR);
        return;
    }
 
    if ($params['load']) cw_load($params['load']);

    $assign = $params['assign'];
    $func = $params['func'];
    if ($params['via'] == 'cw_call') {
        // order and number of params is important for cw_call()
        // we accept only paramX as params
        $func_params = array();
        foreach($params as $k=>$v) {
            if (strpos($k,'param')!==false) $func_params[$k] = $v;
        }
        ksort($func_params);
        $result = cw_call($params['func'], $func_params);
    } else {
        unset($params['load'],$params['func'],$params['assign']);
        $result = cw_func_call($func, $params);
    }
   
    if ($assign)
        $smarty->assign($assign, $result);
    else
        echo $result;
}
