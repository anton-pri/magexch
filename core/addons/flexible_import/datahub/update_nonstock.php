<?php

set_time_limit(86400);

function cw_dh_uns_create_sql($a) {
//    if (strpos($a, 'image') === false) 
        return $a." text not null default ''";
//    else
//        return $a;     
}
function cw_dh_uns_sel_sql($a) {
    global $current_location;

    if (strpos($a, '`Src_') !== false)
        $a = str_replace('`Src_', 'b.`',$a);
    elseif(strpos($a, '`Dest_') !== false) {
        if ($a == '`Dest_cimageurl`') {
            $a = "IF(COALESCE(mi.web_path, '')!='', CONCAT('$current_location/', TRIM(LEADING '/' FROM mi.web_path)), '') as Dest_cimageurl";
        } else {  
            $a = str_replace('`Dest_', 'm.`',$a);
        } 
    }

    return $a;
}

function cw_dh_uns_transfer_images($src_field, $dest_field, $update_mode) {
    //print("cw_dh_uns_transfer_images $src_field, $dest_field <br>");

    global $tables, $var_dirs;

    $update_cond = ($update_mode == 'E')?" AND `Dest_$dest_field`=''":'';

    $update_items_left = cw_query_first_cell("SELECT count(*) FROM $tables[datahub_update_nonstock_preview] INNER JOIN $tables[datahub_main_data] dhmd ON dhmd.catalog_id=$tables[datahub_update_nonstock_preview].catalog_id WHERE `Src_$src_field`!='' $update_cond ");

    if (!$update_items_left) return 0; 

    print("<h3>Transferring images from import buffer to hub, $update_items_left entries left</h3>");


    $update_items = cw_query($s="SELECT table_id, xref, dhmd.catalog_id, dhmd.id, `Src_$src_field`, `Dest_$dest_field`, dhmd.cimageurl as old_image_id FROM $tables[datahub_update_nonstock_preview] INNER JOIN $tables[datahub_main_data] dhmd ON dhmd.catalog_id=$tables[datahub_update_nonstock_preview].catalog_id WHERE `Src_$src_field`!='' $update_cond ORDER BY table_id ASC LIMIT 8");

//print("<br>");
//print_r($update_items);
    $images_processed = 0;

    foreach($update_items as $item) { 

        $file_ext = pathinfo($item["Src_$src_field"], PATHINFO_EXTENSION);
        $filename2save = $item['catalog_id'].'-'.$item['xref'].'.'.$file_ext;
        $filepath2save = $var_dirs['flex_import_images'].'/'.$filename2save;

//print($filepath2save."<br>");  
//print($item["Src_$src_field"]."<br>");
print("<br>Processing item <b>#$item[catalog_id]</b><br>");

        if (file_exists($filepath2save))
            unlink($filepath2save);

print("Saving ".$item["Src_$src_field"]." to images/$filename2save <br>");

        shell_exec("wget ".$item["Src_$src_field"]." -O $filepath2save");

        $bytes_written = filesize($filepath2save);

cw_flush("Bytes written $bytes_written <br>");

        if ($dest_field == 'cimageurl') { 
            if ($item['old_image_id'] > 0) { 
               db_query("DELETE FROM $tables[datahub_main_data_images] WHERE id='$item[old_image_id]'");  
            }
            db_query("DELETE FROM $tables[datahub_main_data_images] WHERE item_id='$item[id]'"); 

            $img2insert = array(
                'filename' => 'images/'.$filename2save,
                'web_path' => 'images/'.$filename2save,
                'system_path' => 'images/'.$filename2save,
                'filesize'  => $bytes_written,
                'item_id' => $item['id']
            );

            $image_id = cw_array2insert('datahub_main_data_images', $img2insert);
            cw_array2update('datahub_main_data', array('cimageurl'=>$image_id), "ID='$item[id]'");


        }
//        db_query("UPDATE $tables[datahub_update_nonstock_preview] SET `Dest_$dest_field`=`Src_$src_field` WHERE table_id='$item[table_id]'"); 
        db_query("UPDATE $tables[datahub_update_nonstock_preview] SET `Src_$src_field`='' WHERE table_id='$item[table_id]'");

        $images_processed++; 
    } 

    return $images_processed;
}

if ($test=='Y') {
cw_dh_uns_transfer_images('image', 'cimageurl', 'E'); die;
}

function cw_dh_uns_prepare_preview() {
    global $tables;

    $update_nonstock_config = cw_query_hash("select * from $tables[datahub_update_nonstock_config]", 'bfield', false);

    $edit_src_fields = array();

    $edit_src_fields_cond = array();

    $transfer = array('sql'=>array(), 'func'=>array());

    if (empty($update_nonstock_config)) {
        cw_csvxc_logged_query("DROP TABLE IF EXISTS $tables[datahub_update_nonstock_preview]");
        return;  
    } 

    foreach ($update_nonstock_config as $bfield => $uns_data) {
        $edit_src_fields[] = "`Src_$bfield`";
        $edit_src_fields[] = "`Dest_$uns_data[mfield]`";  

        $update_cond = '';
        if ($uns_data['update_nonstock_cond'] == 'E') {
            $update_cond = "(COALESCE(b.`$bfield`,'')!='' AND COALESCE(m.`$uns_data[mfield]`,'')='')";  
            $edit_src_fields_cond[] = $update_cond;     
        }

        if (strpos($bfield, 'image') === false && strpos($uns_data['mfield'], 'image') === false)  
            $transfer['sql'][] = base64_encode(str_replace("b.`$bfield`", "b.`Src_$bfield`","UPDATE $tables[datahub_main_data] m, $tables[datahub_update_nonstock_preview] b SET m.`$uns_data[mfield]`=b.`$bfield` WHERE m.catalog_id=b.catalog_id".(!empty($update_cond)?(' AND '.$update_cond):'')));
        else
            $transfer['func'][] = "$bfield###$uns_data[mfield]###$uns_data[update_nonstock_cond]";  

        if ($uns_data['mfield'] == 'cimageurl') {
            $edit_src_fields_left_join = " LEFT JOIN $tables[datahub_main_data_images] mi ON mi.item_id=m.ID AND mi.id=m.cimageurl";
        } 

    }

    if (!empty($transfer['sql']) || !empty($transfer['func'])) { 
        db_query("REPLACE INTO cw_config (name, value) VALUES ('datahub_update_nonstock_queries', '".serialize($transfer)."')");
    }

    if (!empty($edit_src_fields)) {
        $edit_src_fields_create_sql = implode(', ', array_map('cw_dh_uns_create_sql',$edit_src_fields)).', ';
        $edit_src_fields_ins_sql = ', '.implode(', ', $edit_src_fields);
        $edit_src_fields_sel_sql = ', '.implode(', ', array_map('cw_dh_uns_sel_sql', $edit_src_fields)); 
    }
    if (!empty($edit_src_fields_cond)) {
        $edit_src_fields_cond_sql = " AND ".implode(" OR ", $edit_src_fields_cond);
    }

    cw_csvxc_logged_query("DROP TABLE IF EXISTS $tables[datahub_update_nonstock_preview]");

    $create_tabl_sql = "CREATE TABLE IF NOT EXISTS $tables[datahub_update_nonstock_preview] (table_id int(11) not null default 0, xref varchar(50) not null default '', catalog_id int(11) not null default 0, name varchar(255) not null default '', $edit_src_fields_create_sql PRIMARY KEY (table_id))";

    cw_csvxc_logged_query($create_tabl_sql);

    global $is_interim;
    if ($is_interim)
        $interim_ext = "interim_";


    $fill_data_sql = "REPLACE INTO $tables[datahub_update_nonstock_preview] (table_id, xref, catalog_id, name $edit_src_fields_ins_sql) SELECT b.table_id, b.item_xref, m.catalog_id, m.name $edit_src_fields_sel_sql FROM ".$tables['datahub_'.$interim_ext.'import_buffer']." AS b INNER JOIN cw_datahub_match_links ix ON ix.item_xref=b.item_xref INNER JOIN $tables[datahub_main_data] AS m ON m.catalog_id=ix.catalog_id $edit_src_fields_left_join WHERE 1 $edit_src_fields_cond_sql";

    cw_csvxc_logged_query($fill_data_sql);

    cw_csvxc_logged_query("alter table $tables[datahub_update_nonstock_preview] add index dhuns_catalog_id (catalog_id)");

}

function cw_dh_uns_price_update($del_on_update = false, $is_interim=false) {
    if ($is_interim)
        $interim_ext = "interim_";

    cw_csvxc_logged_query("replace into cw_items_scraped_prices select dml.catalog_id, iib.competitor_price, iib.Source, iib.competitor_site, unix_timestamp() from cw_datahub_".$interim_ext."import_buffer iib inner join cw_datahub_match_links dml on dml.item_xref=iib.item_xref");
    $updated_rows = db_affected_rows();

    if ($del_on_update) {
        cw_csvxc_logged_query("delete iib.* from cw_datahub_".$interim_ext."import_buffer iib inner join cw_datahub_match_links dml on dml.item_xref=iib.item_xref"); 
    } 

    return $updated_rows;
}

if ($action == "prepare_preview") {
    cw_dh_uns_prepare_preview();
    if (cw_query_first_cell("select count(*) from $tables[datahub_update_nonstock_preview]")) 
        cw_header_location("index.php?target=datahub_update_nonstock".($is_interim?'&is_interim=1':''));   
    else {
        if ($prices_updated_count = cw_dh_uns_price_update(1,$is_interim)) 
            cw_add_top_message("Performed update of the scraped prices, rows affected: $prices_updated_count",'I');   
        else
            cw_add_top_message('There are no fields for update, please check settings','E'); 
        cw_header_location("index.php?target=datahub_".($is_interim?'interim_':'')."buffer_match");        
    }
}

global $is_interim;
if ($is_interim)
    $interim_ext = "interim_";

if (strpos($action,"update") !== false) {

    $transfer_step = intval($transfer_step);

    $transfer_data_code = cw_query_first_cell("SELECT value FROM $tables[config] WHERE name='datahub_update_nonstock_queries'");
    if (!empty($transfer_data_code)) {
        $transfer_code = unserialize($transfer_data_code);

        if (!$transfer_step) {

            if (!empty($transfer_code['sql'])) {
                $transfer_sql_queries = array_map('base64_decode', $transfer_code['sql']);
                array_map('cw_csvxc_logged_query', $transfer_sql_queries);
            } 
            $transfer_step = 1;
        }

        if ($transfer_step == 1) {
            $images_processed = 0;
            if (!empty($transfer_code['func'])) {
                foreach($transfer_code['func'] as $code2run) {
                    $func_params = explode('###', $code2run); 
                    $images_processed+=cw_dh_uns_transfer_images($func_params[0], $func_params[1], $func_params[2]);                  
                }
            }
            if ($images_processed > 0) 
                cw_header_location("index.php?target=datahub_update_nonstock&action=$action&transfer_step=1".($is_interim?'&is_interim=1':''));
            else 
                $transfer_step = 2; 
        } 


        if ($transfer_step == 2) {

            if ($action == 'update_and_clean') {
                cw_csvxc_logged_query("DELETE ib.* FROM ".$tables['datahub_'.$interim_ext.'import_buffer']." ib INNER JOIN $tables[datahub_update_nonstock_preview] unp ON unp.table_id=ib.table_id");   
            }

            cw_add_top_message("Hub Main data table is updated.",'I');

            cw_csvxc_logged_query("DELETE FROM $tables[datahub_update_nonstock_preview]");
        } 
    } else {
        cw_add_top_message('There are no fields for update, please check settings','E');
    }
    cw_header_location("index.php?target=datahub_".($is_interim?'interim_':'')."buffer_match");
} 

//$added_data = cw_query("SELECT * FROM $tables[datahub_update_nonstock_preview]");
//print_r($added_data);

global $dh_uns_table_fields;
$dh_uns_table_fields = array('table_id'=>array(), 
                             'xref'=>array('type'=>'readonly'), 
                             'catalog_id'=>array('type'=>'readonly'), 
                             'name'=>array('type'=>'readonly'));

$smarty->assign('uns_tbl_fields', cw_datahub_get_update_nonstock_preview_fields());

$default_hidden_buffer_columns = array();
$smarty->assign('pre_hide_columns', cw_datahub_load_hide_columns('update_nonstock', $default_hidden_buffer_columns));

$smarty->assign('main', 'datahub_update_nonstock');
