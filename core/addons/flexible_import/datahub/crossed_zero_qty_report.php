<?php

$pos_prev_table = 'pos_prev';

db_query("
CREATE TABLE IF NOT EXISTS cw_datahub_pos_crossed_zero_qty (
`Item Number` int(11) not null default 0,
`Alternate Lookup` varchar(255) not null default '',
`Product name` varchar(255) not null default '',
`Custom Price 3` decimal(19,4) not null default 0,
`Custom Price 4` decimal(19,4) not null default 0,
`Old Qty 1` int(11) not null default 0,
`New Qty 1` int(11) not null default 0,
PRIMARY KEY (`Item Number`))
");

db_query("DELETE FROM cw_datahub_pos_crossed_zero_qty");


db_query("INSERT INTO cw_datahub_pos_crossed_zero_qty (`Item Number`, `Alternate Lookup`, `Product name`, `Custom Price 3`, `Custom Price 4`, `Old Qty 1`, `New Qty 1`) SELECT p.`Item Number`, p.`Alternate Lookup`, p.`Item Description`, p.`Custom Price 3`, p.`Custom Price 4`, prev.`Qty 1`,p.`Qty 1` FROM pos p INNER JOIN $pos_prev_table prev ON p.`Item Number`=prev.`Item Number` AND (prev.`Qty 1`!=p.`Qty 1` and p.`Qty 1`<=0 and prev.`Qty 1`>0) AND (p.`Custom Price 3`>0 OR p.`Custom Price 4`>0)");

//db_query("INSERT INTO cw_datahub_pos_crossed_zero_qty (`Item Number`, `Alternate Lookup`, `Product name`, `Custom Price 3`, `Custom Price 4`, `Old Qty 1`, `New Qty 1`) SELECT p.`Item Number`, p.`Alternate Lookup`, p.`Item Description`, p.`Custom Price 3`, p.`Custom Price 4`, prev.`Qty 1`,p.`Qty 1` FROM pos p INNER JOIN $pos_prev_table prev ON p.`Item Number`=prev.`Item Number` AND (prev.`Qty 1`!=p.`Qty 1` and p.`Qty 1`<=0) AND (p.`Custom Price 3`>0 OR p.`Custom Price 4`>0)");

$changed_pos_count = cw_query_first_cell("select count(*) from cw_datahub_pos_crossed_zero_qty");
if ($changed_pos_count) {
    if ($dbgprint) {
        print("crossed_zero_count: ".$changed_pos_count);
    } else {
        $ts =  date('Y-m-d__H-i-s');
        cw_datahub_exportMysqlToXls('cw_datahub_pos_crossed_zero_qty', "crossed_zero_qty_$ts.xls", "select * from cw_datahub_pos_crossed_zero_qty");
    }
} else {
    print("<h4>no records to export...</h4><a href='index.php?target=datahub_main_edit'>Return to main edit page</a>");
}

die;
