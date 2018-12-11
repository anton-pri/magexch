<?php
cw_load('edit_on_place');

cw_set_hook('cw_can_edit_on_place', 'cw_can_edit_on_place_productcode', EVENT_POST);

$cw_tokens = &cw_session_register('cw_tokens',array());

$token_data = $cw_tokens[$request_prepared['token']];

$error = null;

if (empty($cw_tokens[$token])) {
    $error = error('Token is wrong');
}
elseif (CURRENT_TIME-$cw_tokens[$token]['time'] > 60*30) {
    $error = error('Token expired. Please refresh the page.');
}
elseif (empty($tables[$token_data['table']])) {
    $error = error("Token error. Table \"{$token_data['table']}\" is not defined");
} else {
    $errors = cw_call('cw_can_edit_on_place', array($token_data),array());
}

if (!empty($token_data['pk'])) {
    $pk_fields = cw_query("SHOW KEYS FROM {$tables[$token_data['table']]} WHERE Key_name = 'PRIMARY'");

    if (count($pk_fields)>1) {
        $error = error('Token error. Primary key is specified, but table has multicolumn primary key. Use "where" instead');
    }
    if (count($pk_fields)==0) {
        $error = error('Token error. Primary key is specified, but table has no primary key. Use "where" instead');
    }    
}


if (is_error()) {
    cw_add_top_message(get_error_message(),'E');
    return $error;
}

$where = array();

if (!empty($token_data['pk'])) {
    $where[] = $pk_fields[0]['Column_name'].' = '.$token_data['pk'];
}
if (!empty($token_data['where'])) {
    $where[] = $token_data['where'];
}

$cw_tokens[$request_prepared['token']]['old_value'] = cw_query_first_cell($q="SELECT {$token_data['field']} FROM {$tables[$token_data['table']]} WHERE ".join(' AND ',$where));

cw_array2update($token_data['table'],array($token_data['field']=>$request_prepared['value']),join(' AND ',$where));
cw_add_top_message("Data has been saved successfully");

if ($token_data['handler']) {
    cw_call($token_data['handler'],array($token_data,$request_prepared['value']));
}
