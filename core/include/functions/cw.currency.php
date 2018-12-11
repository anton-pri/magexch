<?php
function cw_currency_get_list() {
    global $tables;
    return cw_query("select * from $tables[currencies]");
}
