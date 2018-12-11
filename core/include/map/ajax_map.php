<?php
    cw_load('map');
    $name = $_GET['name']; $country = $_GET['country']; $state = $_GET['state'];
    include_once  $app_main_dir.'/include/templater/plugins/modifier.id.php';
    $name = str_ends_with($name, '[country]') ? substr($name, 0, strlen($name) - 9) : $name;
    if (empty($country)) $country = $config['General']['default_country'];
    $countries = cw_call('cw_map_get_countries', array($name));
    $smarty->assign('countries',$countries);
    $smarty->assign('name',$name.'[country]');
    $smarty->assign('default',$country);
    cw_add_ajax_block(array(
        'id'=> smarty_modifier_id($name).'country',
        'action'=> 'replace',
        'content'=> cw_display('main/map/_countries.tpl',$smarty,false)
    ));

    $states = cw_map_get_states($country);
    $smarty->assign('states',$states);
    $smarty->assign('name',$name.'[state]');
    $smarty->assign('default',$state);
    cw_add_ajax_block(array(
        'id'=> smarty_modifier_id($name).'state',
        'action'=> 'replace',
        'content'=> cw_display('main/map/_states.tpl',$smarty,false)
    ));

