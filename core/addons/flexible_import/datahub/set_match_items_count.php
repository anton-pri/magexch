<?php
/*$dh_columns_config = array();

$cfg_name = 'datahub_columns_'.$_GET['cfg_area'];
$cols_cfg = cw_query_first_cell("select value from $tables[config] where name='$cfg_name'");

if (!empty($cols_cfg))
    $dh_columns_config = unserialize($cols_cfg);

$dh_columns_config[$_GET['column']] = $_GET['visible'];

cw_array2insert('config', array('name'=>'datahub_columns_'.$_GET['cfg_area'], 'value'=>serialize($dh_columns_config), 'type'=>'text'), true);
*/
$max_display_matches_current = &cw_session_register('max_display_matches_current',0);

$max_display_matches_current = $_GET['mi_visible'];


exit;
