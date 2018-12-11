<?php
$memberships = cw_query("select * from $tables[memberships] where area='C' order by orderby");
$smarty->assign('memberships', $memberships);

$memberships = cw_query("select * from $tables[memberships] where area='R' order by orderby");
$smarty->assign('resellers_memberships', $memberships);

$smarty->assign('main', 'reg_links');
