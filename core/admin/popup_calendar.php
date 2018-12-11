<?php
$redirect_script = "index.php?target=popup_calendar&user=$user";
$sales[] = $user;

$appointments = cw_query("select * from $tables[appointments] where salesman='$user' order by time");
$smarty->assign('appointments', $appointments);

$smarty->assign('user', $user);

# kornev, TOFIX
cw_display("admin/main/popup_calendar.tpl", $smarty);
