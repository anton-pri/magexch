<?php
error_reporting(E_ALL);
cw_include('addons/flexible_import/include/func.datahub_dbg.php');
cw_datahub_dbg_save_snapshot(798459, 'item_id', 1);
cw_datahub_dbg_save_snapshot(798463, 'item_id', 1);

die;
function cw_dh_err_line($str='') {
    if (is_array($str)) {
        foreach($str as $s) 
            cw_dh_err_line($s);
    } else {
        print("<td style='border-top: 1px solid black; border-right: 1px solid black'>$str</td>");
    }
}

$gl = " | ";

$active_only_sql = " inner join xfer_products_SWE xps on xps.catalogid=ix.item_id and xps.hide=0";

db_query("drop table if exists cw_datahub_multiple_xref_error_tmp");
db_query("create table cw_datahub_multiple_xref_error_tmp as select ix.item_id, count(*) as c from item_xref ix inner join cw_datahub_main_data md on md.catalog_id=ix.item_id $active_only_sql group by ix.item_id having c>=2 order by ix.item_id");

$col_names = array('ALU', 'Duplicate Xref count', 'Alt XREFS', 'Main table xref', 'Xfer table xref', 'POS xref', 'is online', 'Link');

$errors_list = cw_query("select * from cw_datahub_multiple_xref_error_tmp");

if (!empty($errors_list)) {
print("<h2>Active items count: ".count($errors_list)."<h2>");
print("<table width='100%' style='border: 2px solid black'>");
print("<tr><td>".implode("</td><td>", $col_names)."</td></tr>");

foreach ($errors_list as $err) {
$style = '';
$err_line = array();
$err_line['item_id'] = $err['item_id'];
$err_line['count'] = $err['c'];

$alt_xrefs = cw_query("select xref,cost_per_bottle, cost_per_case,split_case_charge from item_xref where item_id = $err[item_id]");
$alt_xrefs_arr = array();
foreach($alt_xrefs as $ax) {
$ax['cost_per_bottle'] += $ax['split_case_charge'];
unset($ax['split_case_charge']);
$alt_xrefs_arr[] = implode($gl, $ax);
}

$err_line['axrefs'] = implode('<br>', $alt_xrefs_arr);

$dhmd = cw_query_first("select initial_xref, cost, cost_per_case, split_case_charge from cw_datahub_main_data where catalog_id='$err[item_id]'");
$dhmd['cost'] += $dhmd['split_case_charge'];
unset($dhmd['split_case_charge']);

$err_line['dhmd'] = implode($gl,$dhmd);

$xfer = cw_query_first("select ccode, Cost, ctwelvebottlecost,cprice from xfer_products_SWE where catalogid='$err[item_id]'");
$err_line['xfer'] = implode($gl, $xfer); 

$pos = cw_query_first("select `Custom Field 5`,`Average Unit Cost`, MSRP, `Custom Price 1` from pos where `Alternate Lookup`='$err[item_id]'");
$err_line['pos'] = implode($gl, $pos); 

$err_line['active'] = cw_query_first_cell("select IF(hide=0,'Y','N') from xfer_products_SWE where catalogid='$err[item_id]'");

if (empty($err_line['active']))
    $err_line['active'] = 'N';

$err_line['link'] = '';

if ($dhmd['initial_xref'] != $xfer['ccode']) {
    $style="background-color: yellow";
} 

if ($pos['Custom Field 5'] != $xfer['ccode']) {
    $style="background-color: red";
}

print("<tr style='$style'>");
cw_dh_err_line($err_line);
print("</tr>");
}


print("</table>");
}


die;
