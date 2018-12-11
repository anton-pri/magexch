<?php

/**
 * Javascript defer plugins. Registers files to use in defer loading
 *
 * @param array  $params should have 'file' and 'type' elements
 * @param Smarty $smarty Smarty object
 *
 * @return string always empty string
 */
function smarty_function_load_defer($params, &$smarty) {
    global $app_web_dir, $app_dir, $app_skin_dir, $deferRegistry, $directInfoRegistry, $config, $app_config_file, $already_included_files;

    if ($config['performance']['defer_load_js_code'] == "Y") 
        $config['performance']['use_speed_up_js'] = "N";   

    if (is_null($already_included_files)) {
        $already_included_files = array();
    }

    if (
        !isset($params['file'])
        || empty($params['file'])
    ) {
        return '';
    }

    if (
        !isset($params['type'])
        || empty($params['type'])
        || !in_array($params['type'], array('js', 'css'))
    ) {
        return '';
    }

    $media = '';
	if (
		isset($params['media'])
		&& !empty($params['media'])
		&& in_array($params['media'], array('all', 'braille', 'handheld', 'print', 'screen', 'speech', 'projection', 'tty', 'tv'))
	) {
		$media = 'media="' . $params['media'] . '"';
	}

    $resource = array(
        'resource_name'=>$params['file'],
        'resource_base_path'=>$smarty->template_dir,
    );

    // Check hierarchy of skin dirs
    if (!$smarty->_parse_resource_name($resource) && empty($params['direct_info'])) return false;

    // $file = $app_skin_dir . '/' . $params['file'];
    $file = str_replace(array($app_dir,'\\'), array('','/'), $resource['resource_name']);

    $type = $params['type'];
    $queue = isset($params['queue']) ? intval($params['queue']) : 0;

    $result = '';

    if (
        isset($config['performance']['use_speed_up_' . $type])
        && 'Y' == $config['performance']['use_speed_up_' . $type]
        && defined('AREA_TYPE')
        && ('C' == constant('AREA_TYPE') || 'A' == constant('AREA_TYPE'))
    ) {
        if (
            !isset($params['direct_info'])
            && !in_array($file, $already_included_files)
            && is_readable($app_dir . $file)
        ) {
            $deferRegistry[$type][$queue][$file] = $app_dir . $file;
            $already_included_files[] = $file;
        }
        elseif (isset($params['direct_info'])) {
            $directInfoRegistry[$type][$queue][$file] = 'css' == $type
                ? cw_get_direct_css($params['direct_info'])
                : $params['direct_info'];
        }
    }
    else {

        if (!isset($params['direct_info'])) {

            if (!in_array($file, $already_included_files) && is_readable($app_dir . $file)) {
                $result = '';

                if ('js' == $type) {
                    $result = '<script type="text/javascript" src="' . $app_web_dir . $file . '"></script>';
                }
                elseif (
                    !empty($params['css_inc_mode'])
                    && 'css' == $type
                ) {
                      // include css via @import directive: hack to get around IE limitation
                      // applied to the number of css files
                      $result = '@import url("' . $app_web_dir . $file . '"); ' . "\n";
                }
                else {
                    $result = '<link rel="stylesheet" type="text/css" href="' . $app_web_dir . $file . '" ' . $media . ' />';
                }
                $already_included_files[] = $file;

                if ($config['performance']['list_available_cdn_servers']) {
                    $srch_pattern = "/([\"'])" . str_replace("/", "\/", $app_web_dir) . "((" . str_replace("/", "\/", $app_skin_dir) . '\/)([^"\'>]+))/i';
                    $result = preg_replace_callback($srch_pattern, "cw_image_domain_select_domain", $result);
                }
                
            }
        }
        else {
            $result = ('css' == $type)
                ? '<style type="text/css">' . "\n"
                  . '<!--' . "\n"
                  . cw_get_direct_css($params['direct_info']) . "\n"
                  . '-->' . "\n"
                  . '</style>'
                : '<script type="text/javascript">' . "\n"
                  . '//<![CDATA[' . "\n"
                  . $params['direct_info'] . "\n"
                  . '//]]>' . "\n"
                  .'</script>';
        }
    }

    static $first_call = true;
    if (
        $app_config_file['debug']['development_mode']
        && $first_call
    ) {
        $first_call = false;
        register_shutdown_function('cw_check_load_defer_plugin_integrity');
    }

    return $result;
}

/**
 * return custom css styles
 *
 * @param array $directInfo custom CSS styles array
 */
function cw_get_direct_css($directInfo) {
    $styles = array();

    if (!is_array($directInfo))
        return '';

    foreach ($directInfo as $id => $css) {
        $styles[$id] = $id . ' {';

        foreach ($css as $name => $value) {
            $styles[$id] .= $name . ': ' . $value . ';' . "\n";
        }
        $styles[$id] .= '}';
    }

    return implode("\n", $styles);
}

function cw_check_load_defer_plugin_integrity() {
    global $deferRegistry, $directInfoRegistry;

    assert('empty($deferRegistry) && empty($directInfoRegistry) /*Func_check_load_defer_plugin_integrity <b>It seems load_defer_code call should be added at the end of the page</b>*/');
}
