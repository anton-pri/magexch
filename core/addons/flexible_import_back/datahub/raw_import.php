<?php
$search_prefilled = array();

$search_prefilled['sort_field']     = ($sort && $sort !=""? $sort : "id");
$search_prefilled['sort_direction'] = ($sort_direction && $sort_direction!=0 ? 0 : 1);
$search_prefilled['items_per_page'] = ($items_per_page ? $items_per_page : 20);
$search_prefilled['page']           = ($page ? $page : 1);
$search_prefilled['unserialize_fields'] = true;
 
$all_fi_profiles = cw_call('cw_flexible_import_get_profiles', array('params'=>$search_prefilled));
//print_r($all_fi_profiles);
$datahub_fi_profiles = cw_call('cw_datahub_filter_profiles', array($all_fi_profiles));
$smarty->assign('datahub_fi_profiles', $datahub_fi_profiles);
//print_r($datahub_fi_profiles);

$datahub_log_entries = cw_call('cw_datahub_search_log_entries', array(array('limit'=>100)));
//print_r($datahub_log_entries);
$smarty->assign('datahub_log_entries', $datahub_log_entries);

$buffer_lines_count = cw_query_first_cell("SELECT COUNT(*) FROM $tables[datahub_import_buffer]");
$smarty->assign('buffer_lines_count', $buffer_lines_count);

$smarty->assign('main', 'datahub_raw_import');
