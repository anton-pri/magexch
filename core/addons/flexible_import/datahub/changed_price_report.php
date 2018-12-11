<?php

$pos_prev_table = 'pos_prev';

db_query("
CREATE TABLE IF NOT EXISTS cw_datahub_pos_changed_price (
`Item Number` int(11) not null default 0,
`Alternate Lookup` varchar(255) not null default '',
`Product name` varchar(255) not null default '',
`Old Custom Price 3` decimal(19,4) not null default 0,
`New Custom Price 3` decimal(19,4) not null default 0,
`Old Custom Price 4` decimal(19,4) not null default 0,
`New Custom Price 4` decimal(19,4) not null default 0,
PRIMARY KEY (`Item Number`))
");

db_query("DELETE FROM cw_datahub_pos_changed_price");


db_query("INSERT INTO cw_datahub_pos_changed_price (`Item Number`, `Alternate Lookup`, `Product name`, `Old Custom Price 3`, `New Custom Price 3`, `Old Custom Price 4`, `New Custom Price 4`) SELECT p.`Item Number`, p.`Alternate Lookup`, p.`Item Description`, prev.`Custom Price 3`, p.`Custom Price 3`, prev.`Custom Price 4`, p.`Custom Price 4` FROM pos p INNER JOIN $pos_prev_table prev ON p.`Item Number`=prev.`Item Number` AND ((prev.`Custom Price 3`!=p.`Custom Price 3`) OR (prev.`Custom Price 4`!=p.`Custom Price 4`))");

$changed_pos_count = cw_query_first_cell("select count(*) from cw_datahub_pos_changed_price");
if ($changed_pos_count) {
    if ($dbgprint) {
        print("changed_pos_count: ".$changed_pos_count);
    } else {
        $ts =  date('Y-m-d__H-i-s');
        cw_datahub_exportMysqlToXls('cw_datahub_pos_changed_price', "changed_price_$ts.xls", "select * from cw_datahub_pos_changed_price");
    }
} else {
    print("<h4>no records to export...</h4><a href='index.php?target=datahub_main_edit'>Return to main edit page</a>");
}

die;
