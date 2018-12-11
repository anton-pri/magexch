<?php

function cw_datahub_product_backup_create() {
    global $app_config_file, $var_dirs, $customer_id;
    global $tables;

    $mysql_acc_str = 'mysqldump --skip-comments -u '.$app_config_file['sql']['user'].' -p\''.$app_config_file['sql']['password'].'\'';
    $dump_path = $var_dirs['products_backups'].'/';
    $date_str = date('Y-m-d__H-i-s');

    $cw_tables_list = "cw_products_system_info cw_products_prices cw_products_warehouses_amount cw_products_detailed_images cw_products_images_det cw_products_images_thumb cw_products cw_attributes_values cw_attributes_default cw_attributes_classes_assignement cw_attributes cw_categories_images_thumb cw_categories_parents cw_categories cw_manufacturers cw_memberships cw_product_options_values cw_product_options cw_product_variant_items cw_product_variants cw_linked_products cw_products_memberships cw_products_lng cw_attributes_lng cw_product_options_lng cw_product_options_values_lng cw_manufacturers_lng cw_categories_lng";

    $mysql_db_name = $app_config_file['sql']['db'];
    $dump_filename = "dump0_cw_item_$date_str.sql";
    $full_dump_path = $dump_path.$dump_filename;

    $res = system($s = "$mysql_acc_str $mysql_db_name $cw_tables_list > ".$full_dump_path);

    cw_log_add(__FUNCTION__,array($mysql_acc_str, $dump_filename, $full_dump_path, $s));
 
    $is_new = false;
    if (file_exists($full_dump_path)) {
        $hash = md5_file($full_dump_path);
        $backup_id = cw_query_first_cell("SELECT * FROM $tables[datahub_products_backups] WHERE hash='$hash'");  
        if ($backup_id) {
            @unlink($full_dump_path);
        } else {
            $is_new = true;
            $backup_id =  
            cw_array2insert('datahub_products_backups', array(
                'date'=>time(),
                'filename' => $dump_filename,
                'hash' => $hash, 
                'extra_data' => serialize(array('filesize'=>filesize($full_dump_path), 'customer_id'=>$customer_id, 'active_products_count'=>cw_datahub_products_backups_active_products_count()))
            ));
        } 
    } 
    $result = array('res'=>$res, 'is_new'=>$is_new, 'backup_id'=>$backup_id);

    if (!$is_new) 
        $result['backup'] = cw_query_first("SELECT * FROM $tables[datahub_products_backups] WHERE backup_id='$backup_id'");

    return $result;
}

function cw_datahub_products_backups_active_products_count() {
    global $tables;

    return cw_query_first_cell("SELECT COUNT(*) FROM $tables[products] WHERE status=1"); 
}

function cw_datahub_products_backups_FileSizeConvert($bytes) {
    $bytes = floatval($bytes);
        $arBytes = array(
            0 => array(
                "UNIT" => "TB",
                "VALUE" => pow(1024, 4)
            ),
            1 => array(
                "UNIT" => "GB",
                "VALUE" => pow(1024, 3)
            ),
            2 => array(
                "UNIT" => "MB",
                "VALUE" => pow(1024, 2)
            ),
            3 => array(
                "UNIT" => "KB",
                "VALUE" => 1024
            ),
            4 => array(
                "UNIT" => "B",
                "VALUE" => 1
            ),
        );

    foreach($arBytes as $arItem)
    {
        if($bytes >= $arItem["VALUE"])
        {
            $result = $bytes / $arItem["VALUE"];
            $result = str_replace(".", "," , strval(round($result, 2)))." ".$arItem["UNIT"];
            break;
        }
    }
    return $result;
}

function cw_datahub_products_backups_list() {
    global $tables, $var_dirs;
    $result = array();
    $backups = cw_query("SELECT * FROM $tables[datahub_products_backups] ORDER BY date DESC"); 

    cw_load('user');

    foreach ($backups as $b) {
        if (!file_exists($var_dirs['products_backups'].'/'.$b['filename'])) {  
            db_query("DELETE FROM $tables[datahub_products_backups] WHERE backup_id = '$b[backup_id]'"); 
            continue;
        } else { 
            $extra_data = unserialize($b['extra_data']);
            unset($b['extra_data']);
            $result[$b['backup_id']] = array_merge($b, $extra_data); 
            $result[$b['backup_id']]['created_by'] = cw_user_get_label($extra_data['customer_id'], '{{firstname}} {{lastname}}'); 
            $result[$b['backup_id']]['filesize'] = cw_datahub_products_backups_FileSizeConvert($extra_data['filesize']);
        }
    } 

    return $result;
}

function cw_datahub_product_backup_save_current_and_restore($restore_backup_id) {
    global $app_config_file, $var_dirs;
    global $tables;

    cw_datahub_product_backup_create();

    $non_product_attrs = "'DI','S','D','B','AB','G','DM','Q','PS','PA'";

    db_query("drop table if exists cw_attributes_non_products;");
    db_query("drop table if exists cw_attributes_values_non_products");
    db_query("drop table if exists cw_attributes_default_non_products");
 
    db_query("create table cw_attributes_non_products like cw_attributes");
    db_query("create table cw_attributes_values_non_products like cw_attributes_values");
    db_query("create table cw_attributes_default_non_products like cw_attributes_default");

    db_query("insert into cw_attributes_non_products select * from cw_attributes where item_type in ($non_product_attrs)");

    db_query("insert into cw_attributes_values_non_products select * from cw_attributes_values where attribute_id in (select attribute_id from cw_attributes where item_type in ($non_product_attrs))");

    db_query("insert into cw_attributes_default_non_products select * from cw_attributes_default where attribute_id in (select attribute_id from cw_attributes where item_type in ($non_product_attrs))");
  

    $mysql_acc_str = 'mysql -u '.$app_config_file['sql']['user'].' -p\''.$app_config_file['sql']['password'].'\'';
    $mysql_db_name = $app_config_file['sql']['db'];
    $dump_path = $var_dirs['products_backups'].'/';

    $dump_filename = cw_query_first_cell("SELECT filename FROM $tables[datahub_products_backups] WHERE backup_id='$restore_backup_id'");

    $full_dump_path = $dump_path.$dump_filename;

    $s = "$mysql_acc_str $mysql_db_name < ".$full_dump_path;

    if (file_exists($full_dump_path))
        $res = system($s);

    cw_log_add(__FUNCTION__,array($mysql_acc_str, $dump_filename, $full_dump_path, $s));

    db_query("delete cw_attributes_values_non_products.* from cw_attributes_values_non_products, cw_attributes_values where cw_attributes_values_non_products.item_id=cw_attributes_values.item_id and cw_attributes_values_non_products.attribute_id=cw_attributes_values.attribute_id and cw_attributes_values_non_products.code=cw_attributes_values.code and cw_attributes_values_non_products.item_type=cw_attributes_values.item_type");

    db_query("replace into cw_attributes select * from cw_attributes_non_products");
    db_query("replace into cw_attributes_values select * from cw_attributes_values_non_products");
    db_query("replace into cw_attributes_default select * from cw_attributes_default_non_products"); 

    db_query("drop table if exists cw_attributes_non_products");
    db_query("drop table if exists cw_attributes_values_non_products");
    db_query("drop table if exists cw_attributes_default_non_products");

}
