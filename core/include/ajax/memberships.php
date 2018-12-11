<?php
cw_load('user');

$smarty->assign('el_membership', $el_membership);
$smarty->assign('memberships', cw_user_get_memberships($usertype));

cw_display('main/ajax/memberships.tpl', $smarty);
exit(0);
?>
