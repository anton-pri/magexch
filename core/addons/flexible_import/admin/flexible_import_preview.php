<?php
cw_load('dev');
global $cw_fi_preview_mode;

$cw_fi_preview_mode = true;

$extra_databases = array();
if (!empty($config['flexible_import']['fi_extra_tables_databases'])) {
   $extra_databases = explode(";", $config['flexible_import']['fi_extra_tables_databases']);
}

if (!in_array($app_config_file['sql']['db'], $extra_databases)) 
    $extra_databases[] = $app_config_file['sql']['db'];

foreach ($extra_databases as $_et) {
    $old_prev_mode_tables = cw_query_column("show tables from $_et like 'prev_mode_%'");
    foreach ($old_prev_mode_tables as $prev_mode_tbl) {
        cw_csvxc_logged_query("DROP TABLE $_et.`$prev_mode_tbl`");
    }
}

$profile = cw_call('cw_flexible_import_get_profile', array('params'=>array('id'=> intval($profile_id))));

if (empty($profile)) 
    cw_header_location("index.php?target=import&mode=flexible_import");

//cw_var_dump($profile);

cw_flexible_import_run_profile($profile_id, array($profile['test_import_file_name']));

$diff_rep = array();

$prev_mode_tables = array();
foreach ($extra_databases as $extra_db) {
    $_prev_mode_tables = cw_query_column("show tables from $extra_db like 'prev_mode_%'");
    foreach ($_prev_mode_tables as $_et) {
        if ($extra_db != $app_config_file['sql']['db']) {
            $prev_mode_tables[] = "$extra_db.`$_et`";
        } else {
            $prev_mode_tables[] = $_et;
        }
    }
}

foreach ($prev_mode_tables as $prev_mode_tbl) {
    $orig_table = str_replace('prev_mode_','',$prev_mode_tbl);

    $diff_rep[$orig_table] = array('add_del' => array(), 'changed' => array());

    if (!cw_csvxc_is_table_exists($orig_table)) continue;

    $diff_tbl_name = cw_flexible_import_add_prefix('diff_', $orig_table);
    cw_csvxc_logged_query("DROP TABLE IF EXISTS $diff_tbl_name");
    $table_schema = cw_query("DESC $orig_table");
    $all_fields = array();
    $key_fields = array();
    $nonkey_fields = array();
    foreach ($table_schema as $tbl_field) {
        $all_fields[] = $tbl_field['Field'];
        if ($tbl_field['Key'] == 'PRI' || $tbl_field['Key'] == 'MUL') 
            $key_fields[] = $tbl_field['Field'];  
        else
            $nonkey_fields[] = $tbl_field['Field'];  
    }
 
    cw_csvxc_logged_query("CREATE TABLE $diff_tbl_name AS (SELECT * FROM (SELECT $orig_table.*, 'del' as diff FROM $orig_table UNION ALL SELECT $prev_mode_tbl.*, 'add' as diff FROM $prev_mode_tbl) v GROUP BY `".implode("`,`", $all_fields)."` HAVING COUNT(*) = 1 ORDER BY `".implode("`,`", $key_fields)."`)");

    $diff_rep[$orig_table]['add_del'] = cw_query("select * from $diff_tbl_name group by `".implode("`,`", $key_fields)."` having count(*) = 1");
 
    $key_field = '';
    if (count($key_fields) > 1) { 
        cw_csvxc_logged_query("ALTER TABLE $diff_tbl_name ADD COLUMN univ_key varchar(255) NOT NULL DEFAULT ''");
        cw_csvxc_logged_query("UPDATE $diff_tbl_name SET univ_key=CONCAT(`".implode("`,`", $key_fields)."`)");
        $key_field = 'univ_key';  
    } elseif (count($key_fields) == 1)  {
        $key_field = $key_fields[0];
    }

    if (!empty($key_field)) {
        $changed_rows_ids = cw_query_column("select `$key_field` from $diff_tbl_name group by `$key_field` having count(*) > 1");
        foreach ($changed_rows_ids as $row_id) {
            $diff_rep[$orig_table]['changed'][$row_id] = cw_query("select * from $diff_tbl_name where `$key_field`='$row_id' ORDER BY DIFF DESC");
        }
//        $diff_rep[$orig_table]['changed'] = cw_query("select * from $diff_tbl_name where `$key_field` in (select `$key_field` from $diff_tbl_name group by `$key_field` having count(*) > 1)");
    }
    cw_csvxc_logged_query("DROP TABLE IF EXISTS $diff_tbl_name");
    cw_csvxc_logged_query("DROP TABLE IF EXISTS $prev_mode_tbl");
}

//cw_var_dump($diff_rep);

//die;
$smarty->assign('home_style', 'iframe');
$smarty->assign('flexible_diff_rep',$diff_rep);
$smarty->assign('profile', $profile);
$smarty->assign('main', 'flexible_import_preview');
