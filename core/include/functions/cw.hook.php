<?php
function cw_func_call() {
    global $ars_hooks, $app_main_dir;
    global $tables, $smarty, $request_prepared;
    global $location, $current_area, $addons;

    static $recently_reworked_func = array(
        'cw_core_get_meta',
        'cw_core_get_html_page_url',
        'cw_attributes_save');

    $params = func_get_args();

    if (in_array($params[0], $recently_reworked_func)) {
        $MSG = 'API Core error: function '.$params[0].' has been recently reworked for call via cw_call() instead of depricated cw_func_call(). Please review your code';
        error_log($MSG);
        die($MSG);
    }
        
    if (function_exists($params[0])) {
    
        $pre = $post = array();
        $replace = $params[0];

        if (is_array($ars_hooks['func'][$replace])) {
            ksort($ars_hooks['func'][$replace]);
            foreach($ars_hooks['func'][$replace] as $type=>$funcs)
                foreach($funcs as $func) {
                    switch($type) {
                    case 'replace':
                        $replace = $func;
                        break;
                    case 'pre':
                        $pre[] = $func;
                        break;
                    case 'post':
                        $post[] = $func;
                        break;
                    }
                }
        }

# kornev, the null is default value for the return of the prev call
        if (empty($pre) && empty($post) && ($replace == $params[0])) {
# kornev, fake return is possible
            $return = $params[2]?$params[2]:null;
            return call_user_func($replace, $params[1], $return);
        }
        else {
            $call_funcs = array_merge($pre, array($replace), $post);
            $return = $params[2]?$params[2]:null;
            foreach($call_funcs as $func) {
                    if ($func!=$replace) {
                        $return = cw_func_call($func, $params[1], $return);
                    } else {
                        $return = call_user_func($func, $params[1], $return);
                    }
                    
                    if (instance_of($return,'EventReturn')) {
                        if (!is_null($return->params))
                            $params[1] = $return->params;
                        $return = $return->getReturn();
                    }
                    
                }
            return $return;
        }
    }
    elseif (is_array($params)) {
        if (strpos($params[0],'.php')!==false) {
            echo "<pre>BACKTRACE\n";
                debug_print_backtrace();
            die('API Core error: Usage cw_func_call() for including PHP ['.$params[0].'] is depricated. Use cw_include(\''.$params[0].'\') instead.');

        }
        extract($request_prepared);

        foreach($params as $prm) {
            $pre = $post = array();
            $orig_file = $replace = $params[0];

            if (is_array($ars_hooks['incl'][$replace]))
            foreach($ars_hooks['incl'][$replace] as $type=>$__files)
                foreach($__files as $__file) {
                    switch($type) {
                    case 'replace':
                        $replace = $__file;
                        break;
                    case 'pre':
                        $pre[] = $__file;
                        break;
                    case 'post':
                        $post[] = $__file;
                        break;
                    }
                }

                $include_files = array_merge($pre, array($replace), $post);
                foreach($include_files as $__file)
                    if ($__file == $orig_file) {
                        extract($GLOBALS);
# kornev, include_once is not a good style here - it's possible that we will need to include the file a few times
                        include $app_main_dir.'/'.$replace;
                    }
                    else
                         cw_func_call($__file);
        }
    }
}

function cw_addons_set_controllers() {
    global $ars_hooks;

    $params = func_get_args();
    if (is_array($params))
    foreach($params as $v) {
        $ars_hooks['incl'][$v[1]][$v[0]][] = $v[2];

    // Duplicate hook for new notation so it can be also called thru cw_include()
    $type = ($v[0]=='replace'?EVENT_REPLACE:($v[0]=='pre'?EVENT_PRE:EVENT_POST));
    cw_set_controller($v[1],$v[2],$type);
    }
}

function cw_addons_set_hooks() {
    global $ars_hooks, $_current_hook_order;

    $params = func_get_args();
    if (is_array($params))
    foreach($params as $v) {
        $index = $_current_hook_order;
        while(isset($ars_hooks['func'][$v[1]][$v[0]][$index])) $index++;
        $ars_hooks['func'][$v[1]][$v[0]][$index] = $v[2];


    // Duplicate hook for new notation so it can be also called thru cw_call()
    $type = ($v[0]=='replace'?EVENT_REPLACE:($v[0]=='pre'?EVENT_PRE:EVENT_POST));
    cw_set_hook($v[1],$v[2],$type);
    }

}

function cw_addons_unset_hooks() {
    global $ars_hooks;

    $params = func_get_args();
    if (is_array($params))
    foreach($params as $v) {
		$index = array_search($v[2], $ars_hooks['func'][$v[1]][$v[0]]);
		if ($index !== false)
			unset($ars_hooks['func'][$v[1]][$v[0]][$index]);


    // Duplicate hook for new notation so it can be also called thru cw_call()
    $type = ($v[0]=='replace'?EVENT_REPLACE:($v[0]=='pre'?EVENT_PRE:EVENT_POST));
    cw_delete_hook($v[1],$v[2],$type);
    }

}

function cw_addons_set_template() {
    global $ars_hooks;

    $params = func_get_args();
    if (is_array($params))
    foreach($params as $v)
        $ars_hooks['tpl'][$v[1]][$v[0]][md5($v[2])] = array($v[2], $v[3]);
}

function cw_addons_unset_template() {
    global $ars_hooks;

    $params = func_get_args();
    if (is_array($params))
    foreach($params as $v)
        unset($ars_hooks['tpl'][$v[1]][$v[0]][md5($v[2])]);
}


# kornev, AREA_TYPE might be present in the areas
function cw_addons_add_css($file, $areas = 'all') {
    cw_addons_add_resource('css',$file, $areas);
}

function cw_addons_add_js($file, $areas = 'all') {
    cw_addons_add_resource('js',$file, $areas);
}

function cw_addons_add_resource($type, $file, $areas = 'all') {
    global $ars_hooks;

    if (!is_array($areas)) $areas = array($areas);
    foreach($areas as $area)
        $ars_hooks[$type][$area][md5($file)] = $file;
}

function cw_addons_delete_resource($type, $file, $area='all') {
    global $ars_hooks;

    if (!is_array($areas)) $areas = array($areas);
    foreach($areas as $area)
        unset($ars_hooks[$type][$area][md5($file)]);
}

/**
 * function splits hooks registered to one template entry into multiple pseudohooks.
 * @before
 *  $ars_hooks['tpl']['index.tpl@id']['pre'] = <pre_hooks_array>
 *  $ars_hooks['tpl']['index.tpl@id']['post'] = <post_hooks_array>
 * @after
 *  $ars_hooks['tpl']['index.tpl@id#pre']['pre'] = <pre_hooks_array>
 *  $ars_hooks['tpl']['index.tpl@id#post']['post'] = <post_hooks_array>
 */
function cw_addons_split_hook($hook_name) {
    global $ars_hooks;
    
    if (empty($ars_hooks['tpl'][$hook_name])) return null;
    
    foreach ($ars_hooks['tpl'][$hook_name] as $type=>$hooks) {
        $ars_hooks['tpl'][$hook_name.'#'.$type][$type] = &$ars_hooks['tpl'][$hook_name][$type];
    }

}


/**
 * Scan tmplates dir and declare hooks for all files named as hook pattern, eg:
 * file.tpl@label
 * file.tpl#pre
 * file.tpl@lable#post
 */
function cw_addons_scan_skin($skin_dir) {
    global $app_dir, $addons;

    $skin_name = basename($skin_dir);

    if (!($hooks = cw_cache_get($skin_name,'autoload_templates')) || defined('DEV_MODE')) {
        $hooks = array();
        $files = cw_files_get_dir($skin_dir,1, true);
        if (!empty($files))
        foreach ($files as $file) {
            // Parse only tpl files
            if (strpos($file,'.tpl') === false) continue;
            
            // Remove skin base dir
            $file = str_replace($app_dir.'/'.$skin_name.'/','',$file);
            list($hooked_file,$hook_type_code) = cw_hook_parse_autoload($file);
            $hook_area = '';
                            
            if (preg_match("!addons/(\w+)/_autoload/!",$hooked_file, $match)) {
                if (empty($addons[$match[1]])) continue; 
                $hooked_file = str_replace("addons/{$match[1]}/","",$hooked_file);
            }
            
            if (strpos($hooked_file,'_autoload/') !== false) {
                
                if (preg_match("!_autoload/_(admin|customer|seller)!",$hooked_file, $match)) {
                    $hook_area = $match[1];
                    $hooked_file = str_replace("_$hook_area/",'',$hooked_file);
                }
                $hooked_file = str_replace('_autoload/','',$hooked_file);
                $hooks[] = array($hooked_file, $file,$hook_type_code, $hook_area);
                
            } elseif ($skin_name != 'skins' && (strpos($file,'@')!==false || strpos($file,'#')!==false)) {
                
                list($hooked_file,$hook_type_code) = cw_hook_parse_autoload($file);
                $hooks[] = array($hooked_file, $file,$hook_type_code, $hook_area);
                                
            }

        }
        cw_cache_save($hooks, $skin_name, 'autoload_templates');
    }

    static $ht = array(EVENT_PRE => 'pre',EVENT_POST=>'post',EVENT_REPLACE=>'replace');

    if (!empty($hooks)) {
        foreach ($hooks as $hook) {
            if (!empty($hook[3]) && APP_AREA != $hook[3]) continue;
            cw_addons_set_template(array($ht[$hook[2]],$hook[0],$hook[1]));
    }
    }

    //cw_var_dump($hooks);
    return $hooks;
}


/**
 * Load controller hooks from _autoload dir in addon
 */
function cw_hook_controllers_autoload() {
    global $app_dir, $app_main_dir, $addons;

    if (!($hooks = cw_cache_get($addons,'autoload_controllers')) || defined('DEV_MODE')) {
            $hooks = array();
            foreach ($addons as $addon=>$v) {
            $files = cw_files_get_dir($app_main_dir.'/addons/'.$addon.'/_autoload',1, true);
            if (!empty($files)) {
                foreach ($files as $file) {
                    if (str_ends_with($file,'.php')) {
                        $file = str_replace($app_main_dir.'/','',$file);
                        list($hooked_file,$hook_type_code) = cw_hook_parse_autoload($file);
                        if (empty($hook_type_code) || $hook_type_code == EVENT_REPLACE) $hook_type_code = EVENT_POST;
                        $hook_area = '';
                        if (preg_match("!$addon/_autoload/_(admin|customer|seller)!",$hooked_file, $match)) {
                            $hook_area = $match[1];
                            $hooked_file = str_replace("_$hook_area/",'',$hooked_file);
                        }
                        $hooked_file = str_replace('addons/'.$addon.'/_autoload/','',$hooked_file);
                        $hooks[] = array($hooked_file, $file,$hook_type_code, $hook_area);
                    }
                }
            }
        }
        cw_cache_save($hooks, $addons, 'autoload_controllers');
    }
    
    if (!empty($hooks)) {
        foreach ($hooks as $hook) {
            if (!empty($hook[3]) && APP_AREA != $hook[3]) continue;
            cw_set_controller($hook[0],$hook[1],$hook[2]);
            //cw_var_dump($hook);
        }
    }
    //cw_var_dump($hooks);
    return $hooks;
}

/**
 * Parse filenames with path
 * dir/fname.ext@id#pre
 * dir/fname@id#pre.ext
 * dir/fname#pre.ext
 * into
 * array('pre','dir/fname.ext@id',<hook_file>)
 */
function cw_hook_parse_autoload($fname) {
    static $ht = array('pre'=>EVENT_PRE,'post'=>EVENT_POST,'replace'=>EVENT_REPLACE);
    $label = null;
    if (preg_match('/@\w+\b/',$fname,$match)) {
        $label = $match[0];
    }
    
    $hook_type_code = EVENT_REPLACE;
    if (preg_match('/#(post|pre|replace)\b/',$fname,$match)) {
        $hook_type = $match[1];
        $hook_type_code = $ht[$hook_type];
    }

    $hooked_file = str_replace(array($label,'#'.$hook_type),'',$fname).($label?:'');

    return array($hooked_file,$hook_type_code);
}
