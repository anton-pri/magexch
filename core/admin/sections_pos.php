<?php
if ($action == 'update' && is_array($positions)) {
    foreach($positions as $key=>$val)
        cw_array2update('sections_pos', $val, "section='$key'");
    cw_header_location("index.php?target=sections_pos");
}

$result = array();
$sections_pos = cw_query("select * from $tables[sections_pos] order by location, orderby");
foreach($sections_pos as $k=>$tmp) {
    $val = $tmp['section'];
    if ($tmp['addon'] && !$addons[$tmp['addon']]) continue;
    $result[$val] = $tmp;
    $result[$val]['title'] = cw_get_langvar_by_name('lbl_'.$val);
}
$smarty->assign('sections', $result);

$location[] = array(cw_get_langvar_by_name('lbl_sections_pos'), '');
$smarty->assign('main', 'sections_pos');
