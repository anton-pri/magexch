<?php
set_time_limit(86400);

if (empty($mode)) $mode = 'view';

cw_include('include/export/export.php');

$smarty->assign('mode', $mode);
