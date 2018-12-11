<?php
$smarty->assign('current_main_dir', 'addons');
$smarty->assign('current_section_dir', 'dashboard/admin');
$smarty->assign('main', 'dashboard'); //  this calls addons/dashboard/admin/dashboard.tpl

cw_addons_add_css('addons/dashboard/admin/dashboard.css');

$addon_actions = array(
    'setting' => 'dashboard_action_setting',
    'update' => 'dashboard_action_update',
);

if (empty($action) || !isset($addon_actions[$action]) || !function_exists($addon_actions[$action])) {
    $action = 'setting';
}

$smarty->assign('action', $action);
cw_call($addon_actions[$action]);

return;


function dashboard_action_setting() {
    
    global $smarty, $tables;

    $params = array(
        'mode' => 'setting',
        'sections' => cw_call('dashboard_get_sections_list') 
    );

    $dashboard = cw_func_call('dashboard_build_sections',$params);

    foreach ($dashboard as $name=>$dash) {
        $dashboard[$name] = array_merge($dashboard[$name],cw_query_first('SELECT * FROM '.$tables['dashboard'].' WHERE name="'.db_escape_string($name).'"'));
    }

    uasort($dashboard, 'cw_uasort_by_order');

    $smarty->assign('dashboard', $dashboard);

}

function dashboard_action_update() {

    if ($_SERVER['REQUEST_METHOD'] != 'POST') dashboard_redirect();

    $dashboard = $_POST['dashboard'];

    if (empty($dashboard)) dashboard_redirect();

    foreach($dashboard as $name=>$dash) {
        $data = array(
            'name' => $name,
            'pos' => intval($dash['pos']),
            'active'=>intval($dash['active']),
        );
        cw_array2insert('dashboard', $data, true);
    }

    $top_message = array('content' => cw_get_langvar_by_name('msg_ppd_filetypes_updated_succes'), 'type' => 'I');


    dashboard_redirect();
    
}

function dashboard_redirect() {
    global $app_catalogs;
    cw_header_location("$app_catalogs[admin]/index.php?target=dashboard");
}
