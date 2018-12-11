<?php
/* moved to function called then via tunnel
$params = array(
    'mode' => 'dashboard',
    'sections' => cw_query_hash("SELECT * FROM $tables[dashboard]", 'name', false, false),
);

$dashboard = cw_func_call('dashboard_build_sections',$params);

// Re-check if some addon ignored active flag
foreach ($dashboard as $name=>$dash) {

     $dashboard[$name] = array_merge(array('frame'=>1, 'header'=>1),$dashboard[$name]);

     if (isset($params['sections'][$name])) {
         $dashboard[$name] = array_merge($dashboard[$name],$params['sections'][$name]);
     }

     if ($dashboard[$name]['active']==0) unset($dashboard[$name]);
}

uasort($dashboard, 'cw_uasort_by_order');

$smarty->assign('dashboard', $dashboard);
*/
