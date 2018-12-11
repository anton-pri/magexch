<?php
set_time_limit(86400);

if (empty($mode)) $mode = 'impdata';

if ($mode == 'stock')
    cw_include('include/import/stock.php');
elseif($mode == 'database')
    cw_include('include/import/database.php');
elseif($mode == 'xcart')
    cw_include('include/import/xcart.php');
elseif($mode == 'impdata')
    cw_include('include/import/impdata.php');
elseif($mode == 'expdata')
    cw_include('include/import/expdata.php');
elseif($mode == 'export_set')
    cw_include('include/import/export_set.php');

$smarty->assign('mode', $mode);
