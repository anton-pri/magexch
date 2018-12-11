<?php
function smarty_function_load_head_resource($params, &$smarty) {
    global $cw_head_tag_load_resources;

    if (!isset($cw_head_tag_load_resources))
        $cw_head_tag_load_resources = array();

    if (empty($params['file'])) {
        $smarty->trigger_error("load_head_resource: missing 'file' parameter");
        return;
    }

    $res_type = !empty($params['type'])?$params['type']:'js';

    $cw_head_tag_load_resources[$params['file']] = $res_type;
}
