<?php

if ($REQUEST_METHOD == 'POST') {
    if ($action == 'save_match_config') {
        db_query("DELETE FROM $tables[datahub_buffer_match_config]");
        foreach ($buffer_match as $mfield => $bm_cfg) {

            if (empty($bm_cfg['bfield']) && empty($bm_cfg['custom_sql'])) {
//                db_query("DELETE FROM $tables[datahub_buffer_match_config] WHERE mfield='$mfield'");
                continue;
            }

            if (!empty($bm_cfg['custom_sql'])) 
                $bm_cfg['custom_sql'] = base64_encode(stripslashes(str_ireplace(array('ALTER', 'CREATE TABLE', 'DROP', 'DELETE', 'INSERT', 'REPLACE INTO', 'UPDATE'),'',$bm_cfg['custom_sql'])));

            $bm_cfg['mfield'] = $mfield;

            cw_array2insert('datahub_buffer_match_config', $bm_cfg, true);
 
        }
        cw_header_location("index.php?target=datahub_configuration");
    } elseif ($action == 'save_merge_config') {
        db_query("DELETE FROM $tables[datahub_buffer_merge_config]");
        foreach ($buffer_merge as $bfield => $bmerge_cfg) {

            if (empty($bmerge_cfg['mfield'])) {
                continue;
            }

            $bmerge_cfg['bfield'] = $bfield;

            cw_array2insert('datahub_buffer_merge_config', $bmerge_cfg, true);
 
        }
        cw_header_location("index.php?target=datahub_configuration#buff_merge");
    } elseif ($action == 'save_price_config') {
        db_query("delete from $tables[datahub_price_settings] where store_id=1");
        cw_array2insert('datahub_price_settings', $price_settings);

        cw_header_location("index.php?target=datahub_configuration#price_config");
    } elseif ($action == 'save_pos_config') {

        db_query("DELETE FROM $tables[datahub_pos_update_config]");
        foreach ($pos_update as $mfield => $pu_cfg) {

            if (empty($pu_cfg['pfield']) && empty($pu_cfg['custom_sql'])) {
                continue;
            }

            if (!empty($pu_cfg['custom_sql']))
                $pu_cfg['custom_sql'] = base64_encode(stripslashes(str_ireplace(array('ALTER', 'CREATE TABLE', 'DROP', 'DELETE', 'INSERT', 'REPLACE INTO', 'UPDATE'),'',$pu_cfg['custom_sql'])));

            $pu_cfg['mfield'] = $mfield;

            cw_array2insert('datahub_pos_update_config', $pu_cfg, true);

        }
        cw_header_location("index.php?target=datahub_configuration#pos_update");

    }

}

$bm_config = cw_query_hash("select * from $tables[datahub_buffer_match_config]", 'mfield', false);

foreach ($bm_config as $mfield => $bm_cfg) {
    if (!empty($bm_cfg['custom_sql'])) 
        $bm_config[$mfield]['custom_sql'] = base64_decode($bm_cfg['custom_sql']);
}

$smarty->assign('bm_config', $bm_config);

$bmerge_config = cw_query_hash("select * from $tables[datahub_buffer_merge_config]", 'bfield', false); 

$smarty->assign('bmerge_config', $bmerge_config);

$pos_config = cw_query_hash("select * from $tables[datahub_pos_update_config]", 'mfield', false);

foreach ($pos_config as $mfield => $pu_cfg) {
    if (!empty($pu_cfg['custom_sql']))
        $pos_config[$mfield]['custom_sql'] = base64_decode($pu_cfg['custom_sql']);
}

$smarty->assign('pos_config', $pos_config);


$smarty->assign('update_cond_options', array('A'=>'Always', 'E'=>'Non-empty src to empty dest'));

$smarty->assign('merge_cond_options', array('A'=>'Always', 'E'=>'Non-empty src to empty dest', 'M'=>'Manual merge only'));

$buffer_tbl_fields = cw_check_field_names(array(), $tables['datahub_import_buffer']);
$smarty->assign('buffer_tbl_fields', $buffer_tbl_fields);

$main_tbl_fields = cw_check_field_names(array(), $tables['datahub_main_data']);
$smarty->assign('main_tbl_fields', $main_tbl_fields);

$pos_tbl_fields = cw_check_field_names(array(), $tables['datahub_pos']);
$smarty->assign('pos_tbl_fields', $pos_tbl_fields);

$price_settings_vals = cw_query_first("select * from $tables[datahub_price_settings] where store_id=1");
$price_settings = array();
foreach ($price_settings_vals as $fname => $fval) {
    $_item = $price_settings_fields[$fname];    
    $_item['value'] = $fval;
    $price_settings[$fname] = $_item; 
}
$smarty->assign('price_settings', $price_settings);

$smarty->assign('main', 'datahub_configuration');
