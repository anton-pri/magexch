<?php

global $datahub_browse_tables;

$dh_curr_browse_table = &cw_session_register('dh_curr_browse_table',0);

global $_browse_table;
if (empty($_browse_table)) {

    if (!$dh_curr_browse_table)
        $dh_curr_browse_table = reset($datahub_browse_tables);

    cw_header_location("index.php?target=datahub_browser&_browse_table=$dh_curr_browse_table");
}

cw_include('include/datatable/init.php');


$smarty->assign('_browse_table', $_browse_table);
$smarty->assign('datahub_browse_tables', $datahub_browse_tables);

$smarty->assign('main', 'datahub_browser');
