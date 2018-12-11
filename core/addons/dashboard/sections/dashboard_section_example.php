<?php

/*
 * EXAMPLE skeleton for dashboard section
 * use function for registration in dashboard as follwing:
 * cw_addons_set_hooks(array('post','dashboard_build_sections','dashboard_section_example'));
 *
 * Input:
 * $parms['mode'] is 'setting' or 'dashboard'. You should not prepare full content in 'setting' mode.
 * $parms['sections'] is list of current dashboard settings
 */
function dashboard_section_example($params, $return=null) {

    // Set the dashboard code name here
    $name = 'example';

    // If the section is disabled then skip it on dashboard
    if ($params['mode'] == 'dashboard' && $params['sections'][$name]['active']==='0') return $return;

    // Define basic data for configuration
    $return[$name] =  array(
        'title'         => 'Example section',
        'description'   => 'This is example of dashboard section explains how to build your own widget',
        'active'        => 1,       // Default status: 0 or 1; optional
        'pos'           => -999,       // Default position; optional
        'size'          => 'big',   // Section size: 'small' (25%), 'medium' (50%) or 'big' (100%); optional
        'frame'         => 1,       // Show frame: 0 or 1; 1 by default; optional
        'header'        => 1,       // Show header: 0 or 1; 1 by default; optional; igmored if frame is 0
    );

    if ($params['mode']=='setting') return $return;

    // Add content for dashboard in 'dashboard' mode
    // Define either content or template name or both
    $return[$name]['content']  = '<h2>This example dashboard section explains how to build your own widget</h2>';
    $return[$name]['template'] = 'addons/dashboard/admin/sections/example.tpl';

    return $return;
}