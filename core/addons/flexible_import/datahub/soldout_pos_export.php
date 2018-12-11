<?php

//$dbgprint = 1;

$pos_prev_table = 'pos_test';

$pos_prev_table = 'pos_prev';


db_query("
CREATE TABLE IF NOT EXISTS cw_datahub_pos_soldout (
`Old SKU` int(11) not null default 0,
`New SKU` int(11) not null default 0,
`Product name` varchar(255) not null default '',
UPC varchar(255) not null default '',
PRIMARY KEY (`Old SKU`,`New SKU`))
");

db_query("DELETE FROM cw_datahub_pos_soldout");

$sold_out_items = cw_query($s = "select p.`Item Description`, p.`Alternate Lookup`, p.`Item Number`, p.Attribute as vintage, pv.`Qty 1` as old_qty, p.`Qty 1` as new_qty, pv.UPC from pos p inner join $pos_prev_table pv on pv.`Item Number`=p.`Item Number` and pv.`Qty 1` > 0 and p.`Qty 1` <= 0 and p.UPC!='' and p.Attribute!='' and p.Attribute != 'NV'");

$listed_sku = array(0);

foreach ($sold_out_items as $soi) {

    if ($dbgprint) print("<hr><h3>Item: ".implode(", ", $soi)."</h3> <br>");

    $link_queries_arr = array("dhmd0.store_sku='".$soi['Item Number']."'");
    if (!empty($soi['Alternate Lookup']))  
        $link_queries_arr[] = "dhmd0.ID='".$soi['Alternate Lookup']."'";

    $orig_dhmd_item = cw_query_first("SELECT dhmd0.* FROM cw_datahub_main_data dhmd0 WHERE (".implode(' OR ', $link_queries_arr).")");

    if ($dbgprint) print("<h4>Original wine: $orig_dhmd_item[Vintage] $orig_dhmd_item[name] $orig_dhmd_item[Size] $orig_dhmd_item[varietal]</h4>");

    if ($dbgtest) $test_ext1 = '_test';

    $sql = "SELECT dhmd.* FROM cw_datahub_main_data$test_ext1 dhmd WHERE dhmd.ID<>'$orig_dhmd_item[ID]' AND dhmd.Producer='".addslashes($orig_dhmd_item['Producer'])."' AND dhmd.name='".addslashes($orig_dhmd_item['name'])."' AND dhmd.varietal='$orig_dhmd_item[varietal]' AND dhmd.Size='$orig_dhmd_item[Size]' AND CAST(dhmd.Vintage as SIGNED)>'$orig_dhmd_item[Vintage]' ORDER BY CAST(dhmd.Vintage as SIGNED) ASC";

    if ($dbgprint) print("<h5>$sql</h5>");

    $similar_vintages = cw_query($sql);
 
    if ($dbgtest) $test_ext2 = '_test2'; 
 
    if (!empty($similar_vintages)) {
        foreach ($similar_vintages as $sw) {   
            $new_pos_item = cw_query_first("SELECT * FROM pos$test_ext2 WHERE `Alternate Lookup`='$sw[ID]' AND `Qty 1`>0");
            if ($dbgprint) print("<h5>new pos item: ".$new_pos_item['Item Number']." </h5>");
            if (!empty($new_pos_item)) { 
                $listed_sku[] = $soi['Item Number'];
                $listed_sku[] = $new_pos_item['Item Number'];
                db_query("INSERT INTO cw_datahub_pos_soldout (`Old SKU`, `New SKU`, `Product name`, UPC) VALUES ('".$soi['Item Number']."', '".$new_pos_item['Item Number']."', '".addslashes($soi['Item Description'])."', '".$soi['UPC']."')");

                if ($dbgprint) print("<h5>$sw[Vintage] $sw[name] $sw[Size] $sw[varietal]</h5>");
                break; 
            } else {
                if ($dbgprint) {
                    $new_pos_item = cw_query_first("SELECT * FROM pos$test_ext2 WHERE `Alternate Lookup`='$sw[ID]'");
                    if (!empty($new_pos_item)) {
                        print("<h6>Similar vintage, not enough inventory. SKU:".$new_pos_item['Item Number'].", ALU:".$new_pos_item['Alternate Lookup'].", Qty:".$new_pos_item['Qty 1']." </h6>");
                    } else {
                        print("<h6> pos table contains no item with ALU=".$new_pos_item['Alternate Lookup']."</h6>");
                    } 
                }  
            } 
        } 
    } else {
        if ($dbgprint) { 
            print("<h5>No similar vintages with year > $orig_dhmd_item[Vintage] </h5>");
            print("<h5>similar vintages with other years:</h5>");  
            $other_sim_vintages = cw_query("select dhmd.ID, dhmd.Vintage FROM cw_datahub_main_data$test_ext1 dhmd WHERE dhmd.ID<>'$orig_dhmd_item[ID]' AND dhmd.Producer='".addslashes($orig_dhmd_item['Producer'])."' AND dhmd.name='".addslashes($orig_dhmd_item['name'])."' AND dhmd.varietal='$orig_dhmd_item[varietal]' AND dhmd.Size='$orig_dhmd_item[Size]' ORDER BY CAST(dhmd.Vintage as SIGNED) ASC");
            print_r($other_sim_vintages); 
        }
    } 
}

//if ($dbg)
//die('Sorry, the script is being checked');

$extra_susp_items = cw_query($esi_qry = "SELECT * FROM pos WHERE `Item Number` NOT IN (".implode(',', $listed_sku).") AND `Qty 1`>12 AND COALESCE(UPC,'')='' ORDER BY `Item Number`");
 if ($dbgprint) print("<h3>$esi_qry</h3>");
foreach ($extra_susp_items as $esi) {
    db_query("INSERT INTO cw_datahub_pos_soldout (`Old SKU`, `New SKU`, `Product name`, UPC) VALUES ('', '".$esi['Item Number']."', '".addslashes($esi['Item Description'])."', '')");
}

$sold_pos_count = cw_query_first_cell("select count(*) from cw_datahub_pos_soldout");
if ($sold_pos_count) {
    if ($dbgprint) {
        print("sold_pos_count: ".$sold_pos_count);
    } else {
        $ts =  date('Y-m-d__H-i-s');
        cw_datahub_exportMysqlToXls('cw_datahub_pos_soldout', "soldout_$ts.xls", "select * from cw_datahub_pos_soldout");
    } 
} else {
    print("<h4>no records to export...</h4><a href='index.php?target=datahub_main_edit'>Return to main edit page</a>");
}

die;
