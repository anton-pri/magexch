<?php
$mtf = cw_datahub_get_main_table_fields();
$dt = 0;
foreach($mtf as $m_k => $m_v) {
    if ($m_v['main_display']) 
      $mtf[$m_k]['dt'] = $dt++;
}

$smarty->assign('main_tbl_fields', $mtf);

$pre_hide_columns = cw_datahub_load_hide_columns('main_edit', array());

/*
if ($REMOTE_ADDR == '79.132.7.227')
print_r($pre_hide_columns);
*/

$smarty->assign('pre_hide_columns', $pre_hide_columns);

$smarty->assign('show_toggle_links', 1||($REMOTE_ADDR == '79.132.7.227'));

$smarty->assign('main', 'datahub_main_edit');
