<?php
// This page is memporary ejected from main menu
// It controls content of 3 special langvars which are used for page meta: Title, Description and Keywords

if ($action == 'update') {
    
    cw_array2insert('languages', array('code' => $edited_language,'name'=>'lbl_site_meta_title','topic'=>'Labels','value'=>$meta['title']), true);
    cw_array2insert('languages', array('code' => $edited_language,'name'=>'lbl_site_meta_descr','topic'=>'Labels','value'=>$meta['descr']), true);
    cw_array2insert('languages', array('code' => $edited_language,'name'=>'lbl_site_meta_keywords','topic'=>'Labels','value'=>$meta['meta_keywords']), true);

    cw_cache_clean('lang');

    cw_header_location('index.php?target='.$target);
}

$meta_title = cw_get_langvar_by_name('lbl_site_meta_title');
$meta_descr = cw_get_langvar_by_name('lbl_site_meta_descr');
$meta_keywords = cw_get_langvar_by_name('lbl_site_meta_keywords');

$smarty->assign('meta_title', $meta_title);
$smarty->assign('meta_descr', $meta_descr);
$smarty->assign('meta_keywords', $meta_keywords);

$smarty->assign('main', 'meta_tags');
