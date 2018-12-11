<?php
if (!$addons['Salesman'])
    cw_header_location('index.php');

cw_load('salesman');

$location[] = array(cw_get_langvar_by_name('lbl_targets_premiums'), '');

if (!$user) {
    $salesmen = cw_query("select customer_id from $tables[customers] where usertype='B'");
    $smarty->assign('salesmen', $salesmen);

    $smarty->assign('main', 'targets_list');
    cw_display('admin/index.tpl', $smarty);
    exit(0);
}

cw_cleanup_target($user);
$current_level = cw_salesman_current_level($user);
$target = cw_salesman_get_target($user);
$reached = cw_salesman_is_reached($user);
$smarty->assign('salesman_reached', $reached);
$premiums_selected = cw_salesman_is_selected($user);
if (!$reached) {
    if ($action == 'update_target') {
        $date_fields = array ('' =>array('start_date' => 0, 'end_date' => 1));
        cw_core_process_date_fields($posted_data, $date_fields);

        $posted_data['salesman_customer_id'] = $user;
        cw_array2insert('salesman_target', $posted_data, true, array('target', 'start_date', 'end_date', 'salesman_customer_id'));
        cw_header_location('index.php?target=targets&user='.$user);
    }
    if ($action == 'add' && $data_new['title']) {
        $data_new['salesman_customer_id'] = $user;
        cw_array2insert('salesman_premiums', $data_new, true);
        cw_header_location('index.php?target=targets&user='.$user);
    }
    if ($action == 'update_premiums' && is_array($data)) {
        foreach($data as $id=>$val) {
            if ($val['del'] == 'Y') {
                cw_delete_salesman_premium($id);
                continue;
            }
            $val['salesman_customer_id'] = $user;
    
            $upd_lng['code'] = $edited_language;
            $upd_lng['id'] = $id;
            $upd_lng['title'] = $val['title'];
            cw_array2insert('salesman_premiums_lng', $upd_lng, true);

            unset($val['title']);
            cw_array2update('salesman_premiums', $val, "id='$id'");
        }
        cw_header_location('index.php?target=targets&user='.$user);
    }
}
elseif ($premiums_selected && !$target['approved']) {
    if($action == 'approve_premiums' && is_array($data)) {
        db_query("update $tables[salesman_target] set approved=1 where customer_id='$user'");
        db_query("update $tables[salesman_premiums] set selected=0 where customer_id='$user'");
        foreach($data as $key=>$val)
            db_query("update $tables[salesman_premiums] set selected=1 where id='$key'");
        cw_header_location('index.php?target=targets&user='.$user);
    }
}
else {
    $top_message['content'] = cw_get_langvar_by_name("lbl_target_is_reached");
}
$top_message['type'] = 'W';
$smarty->assign('top_message', $top_message);
$top_message = array();

$smarty->assign('navigation_script', 'index.php?target=targets&user=$user');

$smarty->assign('premiums_selected', $premiums_selected);
$smarty->assign('premiums', cw_salesman_get_premiums($user, $edited_language));
$smarty->assign('current_level', $current_level);
$smarty->assign('salesman_target', $target);
$smarty->assign('user', $user);

$smarty->assign('main', 'targets');
?>
