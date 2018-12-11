<?php

function smarty_function_cw_order_statuses($params, &$smarty)
{
   
    global $tables, $config;

    $retval = "";

    $langvar_code = '{$lng.';
    $name_name = 'name';

    $statuses_list = cw_query("select os.* from $tables[order_statuses] os order by orderby, code");

    cw_event('on_prepare_statuses_list', array(&$statuses_list));

    if ($params['mode'] == "select") {

        if ($params['multiple']) { 
            $multiple_html = "multiple=\"multiple\"";
            $multiple_select_name_ext = "[]";
        }

        $retval .= "<select name=\"$params[name]$multiple_select_name_ext\" $params[extra] $multiple_html>";
        if ($params['extended'] != "")
            $retval .= "<option value=\"\"></option>";
    }

    $selected_status_codes = array();
    if (is_array($params['selected'])) {
        if ($params['normal_array']) {
            $selected_status_codes = $params['selected'];
        } else {
            $selected_status_codes = array_keys($params['selected']);
        }
    } else {
        $selected_status_codes = array($params['selected']);
    }

    foreach ($statuses_list as $st) {

        $selected_html = "";

        if (strpos($st[$name_name], $langvar_code) !== false) {
            $st[$name_name] = substr($st[$name_name], strlen($langvar_code));
            if (strpos($st[$name_name], '}') !== false) {
                $st[$name_name] = substr($st[$name_name], 0, strpos($st[$name_name], '}'));
            } 
            $st[$name_name] = cw_get_langvar_by_name($st[$name_name]);
        } elseif (strpos($st[$name_name], 'lbl_') !== false) {
            $st[$name_name] = cw_get_langvar_by_name($st[$name_name]);
        }
        
        if (intval($st['deleted'])) {
            $deleted_text = cw_get_langvar_by_name('lbl_status_deleted');
        } else {
            $deleted_text = "";
        } 

        if ($params['mode'] == "select")  {

            if (in_array($st['code'], $selected_status_codes)) 
                $selected_html = "selected=\"selected\"";

            if (!intval($st['deleted']) || !empty($selected_html)) { 
                $retval .= "<option value=\"$st[code]\" $selected_html>".$st[$name_name]."$deleted_text</option>";
            }

        } elseif ($params['mode'] == "static") {
            if (in_array($st['code'], $selected_status_codes)) {
                $retval = $st[$name_name].$deleted_text;
            }
        }
    }

    if ($params['mode'] == "select") {
        $retval .= "</select>";
    }

    return $retval;
}

/* vim: set expandtab: */

?>
