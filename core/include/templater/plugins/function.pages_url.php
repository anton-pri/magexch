<?php
function smarty_function_pages_url($params, &$smarty) {
   
    if ($params['assign']) 
        $smarty->assign($params['assign'], cw_call('cw_core_get_html_page_url', array($params)));
    else
        return cw_call('cw_core_get_html_page_url', array($params));
}
