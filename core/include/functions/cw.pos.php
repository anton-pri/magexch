<?
function cw_pos_user_info($pos_customer_id) {
    global $tables;
    return cw_query_first("select cci.order_entering_format, c.customer_id, firstname, lastname from $tables[customers_addresses] as ca, $tables[customers] as c, $tables[customers_customer_info] as cci where ca.customer_id = c.customer_id and cci.customer_id = c.customer_id and c.customer_id='$pos_customer_id' and ca.main=1");
}

function cw_pos_get_doc_info($doc_info_id) {
    global $tables;

    $ret = cw_query_first("select * from $tables[docs_pos_info] where doc_info_id='$doc_info_id'");
    $ret['pos_user_info'] = cw_pos_user_info($ret['pos_customer_id']);
    return $ret;
}

function cw_pos_get_list_smarty() {
    $pos_users = cw_user_get_short_list('G');
    return $pos_users;
}
?>
