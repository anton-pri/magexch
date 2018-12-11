<?php

function smarty_function_edit_on_place($p, $smarty) {
    global $REQUEST_URI;
    
    static $start;
    
    if (empty($p['table']) || empty($p['field']) || ($p['pk'].$p['where'])=='') {
        return '';
    }
    
    $cw_tokens = &cw_session_register('cw_tokens',array());
    
    $group = md5($REQUEST_URI);
    
    if (is_null($start) && !defined('IS_AJAX')) {
        cw_unset_tokens_group($group);
        $start = true;
    }
    
    $t = md5(cw_core_microtime().serialize($p).rand(1,100));
    
    $cw_tokens[$group][$t] = $t;
    $cw_tokens[$group]['time'] = CURRENT_TIME;
    
    $cw_tokens[$t] = array(
        'group' => $group,
        'table' => $p['table'],
        'field' => $p['field'],
        'pk'    => $p['pk'],
        'where' => $p['where'],
        'value' => $p['value'],
        'handler' => $p['handler'],
        'time'  => CURRENT_TIME
    );

    if ($p['token_only']) 
        return $t;
    else
        return 'class="edit_on_place" token="'.$t.'"';
        

}

function cw_unset_tokens_group($group) {
    
    $cw_tokens = &cw_session_register('cw_tokens',array());
    
    if (isset($cw_tokens[$group])) {
        foreach ($cw_tokens[$group] as $t) {
            unset($cw_tokens[$t]);
        }
        unset($cw_tokens[$group]);
    }
    
    if (rand(1,20) == 20) {
        // every Xth call delete old tokens
        foreach ($cw_tokens as $t=>$v) {
            if ((CURRENT_TIME-$v['time']) > (60 * 20)) {
                unset($cw_tokens[$t]);
            }
        }
    }
}
