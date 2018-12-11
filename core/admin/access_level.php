<?php
if ($action == 'update') {
    db_query($sql="replace $tables[access_levels] (level, membership_id, area) values('".serialize($up_access_level)."', '$membership_id', '".strtoupper($mem_area)."')");
    cw_header_location("index.php?target=access_level&mem_area=$mem_area&membership_id=".$membership_id);
}

cw_load('auth');

if ($membership_id != 0)
    $membership_info = cw_query_first("select * from $tables[memberships] where membership_id='$membership_id'");
else
    $membership_info['area'] = $mem_area;

if (!$membership_info['area'])
    cw_header_location('index.php?target=memberships');

$ret = cw_query_first("select level from $tables[access_levels] where membership_id='$membership_id' and area='".$membership_info['area']."'");
$smarty->assign('access_level', unserialize($ret['level']));

$location[] = array(cw_get_langvar_by_name('lbl_access_level'), '');

global $arr_auth;
$smarty->assign('def', $arr_auth[$membership_info['area']]);
$smarty->assign('membership_id', $membership_id);
$smarty->assign('mem_area', $mem_area);
$smarty->assign('current_section_dir','memberships');
$smarty->assign('main', 'access_level');
