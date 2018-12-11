<?php
cw_load('map');

if ($mode == 'cities' && $country) {
    cw_include('include/map/cities.php');
    $smarty->assign('main', 'cities');
    $location[] = array(cw_get_langvar_by_name('lbl_cities'), '');
}
elseif($mode == 'regions' && $country) {
    cw_include('include/map/regions.php');
    $smarty->assign('main', 'regions');
    $location[] = array(cw_get_langvar_by_name('lbl_regions'), '');
}
elseif($mode == 'states' && $country) {
    cw_include('include/map/states.php');
    $smarty->assign('main', 'states');
    $location[] = array(cw_get_langvar_by_name('lbl_states'), '');
}
elseif($mode == 'counties' && $country) {
    cw_include('include/map/counties.php');
    $smarty->assign('main', 'counties');
    $location[] = array(cw_get_langvar_by_name('lbl_counties'), '');
}
else {
    cw_include('include/map/countries.php');
    $smarty->assign('main', 'countries');
    $location[] = array(cw_get_langvar_by_name('lbl_countries_management'), '');
}
