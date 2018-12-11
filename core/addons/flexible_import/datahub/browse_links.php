<?php

function cw_dh_browse_tables_by_field_of_table($field_name, $table_name) {
    $result = array();

    global $dh_browse_linked_keys;
    $found_group = '';
    $found_group_table = '';

    foreach ($dh_browse_linked_keys as $k_group=>$_keys) {  
        foreach ($_keys as $tbl_name=>$f_name) {
            if ($f_name == $field_name && $tbl_name == $table_name) {
                $found_group = $k_group;
                $found_group_table = $tbl_name; 
                break;
            }   
        } 
        if (!empty($found_group)) {  
            $result = $dh_browse_linked_keys[$found_group];  
            unset($result[$found_group_table]);
            break;  
        }
    }

    return $result;
}

function cw_dh_browse_collect_data($table_name, $field_name, $value, $lvl=1) {
  
    if ($lvl >= 10) return; 
 
    global $dh_browse_collection;
    if (!isset($dh_browse_collection)) $dh_browse_collection = array();
//print("<pre>");print_r($dh_browse_collection);print("</pre>");
    $src_data = cw_query($q = "select * from `$table_name` where `$field_name`='$value'");

    $lines2process = array();
    foreach ($src_data as $src_line) {
        $md5key = md5(serialize($src_line));
        if (!isset($dh_browse_collection[$md5key])) {
            $dh_browse_collection[$md5key] = array('query'=>$q, 'data'=>$src_line, 'table'=>$table_name);
            $lines2process[] = $src_line;
        }
    } 

    if (empty($lines2process)) return;

    $tbl_fields = array_keys(reset($lines2process)); //array_keys(cw_query_hash("desc `$table_name`", 'Field'));

    foreach($lines2process as $l2p) {  
        foreach ($tbl_fields as $tbl_field) {
            $tables_linked2field = cw_dh_browse_tables_by_field_of_table($tbl_field, $table_name);
            if (empty($tables_linked2field)) continue;
//print("<pre>");print_r($tables_linked2field);print("</pre>");
            foreach ($tables_linked2field as $table_linked=>$field_linked) {
                if ($l2p[$tbl_field] == 0 || trim($l2p[$tbl_field])=='') continue;
//print("<b>$lvl</b> $table_linked,$field_linked, ".$l2p[$tbl_field]."<br>");
                cw_dh_browse_collect_data($table_linked,$field_linked, $l2p[$tbl_field], $lvl+1);                                      
            }  
        }
    } 

}

global $dh_browse_collection, $dh_table, $dh_column, $dh_value;

cw_dh_browse_collect_data($dh_table, $dh_column, $dh_value);

foreach ($dh_browse_collection as $col_entry) {

    if (!isset($dh_filtered_columns[$col_entry['table']])) 
        $dh_filtered_columns[$col_entry['table']] = unserialize(cw_query_first_cell("select value from $tables[config] where name='datatable_columns_dh_browser_".$col_entry['table']."'"));

   $col_filter = $dh_filtered_columns[$col_entry['table']];

   $to_display = array();
   foreach ($col_entry['data'] as $col_name=>$col_value) {  
 //      if (isset($col_filter[$col_name])) 
 //          if (!$col_filter[$col_name])
         if ($col_filter[$col_name] === 'false') 
               continue;

       $to_display[$col_name] = $col_value;  
   } 

   $cellstyle = "style='border-top: 1px solid gray; border-right: 1px solid gray'";

   print("<h3>$col_entry[table]</h3><table style='border: 1px solid black' cellpadding=4>");
   print("<tr><td><b>");print(implode("</b></td><td><b>", array_keys($to_display)));print("</b></td></tr>");
   print("<tr><td $cellstyle>");print(implode("</td><td $cellstyle>", $to_display));print("</td></tr>");
   print("</table>");

}


die;
