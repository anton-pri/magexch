<?php
$testimonials = cw_review_get_testimonials();

$location[] = array(cw_get_langvar_by_name('lbl_help_zone'), 'index.php?target=help');
$location[] = array(cw_get_langvar_by_name('lbl_testimonials'), '');

$smarty->assign('testimonials', $testimonials);
$smarty->assign('current_section_dir', 'help');
$smarty->assign('main', 'estore_testimonials');