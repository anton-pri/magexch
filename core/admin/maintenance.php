<?php
$location[] = array(cw_get_langvar_by_name('lbl_maintenance'), 'index.php?target='.$target);

$smarty->assign('current_main_dir', 'admin');
$smarty->assign('current_section_dir', 'maintenance');
$smarty->assign('main','maintenance');
