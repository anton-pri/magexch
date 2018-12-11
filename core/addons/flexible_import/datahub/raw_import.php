<?php
$search_prefilled = array();

$search_prefilled['sort_field']     = ($sort && $sort !=""? $sort : "id");
$search_prefilled['sort_direction'] = ($sort_direction && $sort_direction!=0 ? 0 : 1);
$search_prefilled['items_per_page'] = ($items_per_page ? $items_per_page : 20);
$search_prefilled['page']           = ($page ? $page : 1);
$search_prefilled['unserialize_fields'] = true;
 
$all_fi_profiles = cw_call('cw_flexible_import_get_profiles', array('params'=>$search_prefilled));

$datahub_fi_profiles = array();
foreach ($all_fi_profiles as $_p) {
    if ($_p['import_src_type']  == 'T') continue;

    $profile_is_interim = (strpos($_p['name'], 'interim')!==false);

    $profile_is_beva = (strpos(strtolower($_p['name']), 'beva')!==false); 
    if ($profile_is_beva)
        $_p['active_reccuring'] = ($config['flexible_import']['fi_datahub_autoload_bevadaily']=='Y' || $config['flexible_import']['fi_datahub_autoload_bevadaily_interim']=='Y');

    $profile_is_vias = (strpos(strtolower($_p['name']), 'vias')!==false);

    if ($profile_is_vias)
        $_p['active_reccuring'] = ($config['flexible_import']['fi_datahub_autoload_vias_interim']=='Y' || $config['flexible_import']['fi_datahub_autoload_vias']=='Y');

    if (!$profile_is_beva && !$profile_is_vias)
    if (!$_p['active_reccuring']) continue;

    if (in_array($_p['name'],array('POS update','Domaine'))) continue;

    $datahub_fi_profiles[] = $_p;
}

//$datahub_fi_profiles = cw_call('cw_datahub_filter_profiles', array($all_fi_profiles));




$smarty->assign('datahub_fi_profiles', $datahub_fi_profiles);
//print_r($datahub_fi_profiles);

$datahub_log_entries = cw_call('cw_datahub_search_log_entries', array(array('limit'=>100)));
//print_r($datahub_log_entries);
$smarty->assign('datahub_log_entries', $datahub_log_entries);

$buffer_lines_count = cw_query_first_cell("SELECT COUNT(*) FROM $tables[datahub_import_buffer]");
$smarty->assign('buffer_lines_count', $buffer_lines_count);

$interim_buffer_lines_count = cw_query_first_cell("SELECT COUNT(*) FROM $tables[datahub_interim_import_buffer]");
$smarty->assign('interim_buffer_lines_count', $interim_buffer_lines_count);

$smarty->assign('main', 'datahub_raw_import');
