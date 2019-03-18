<?php
cw_load('error');

/**
 * Load functions on demand from include/func/func.*.php or /include/func/base/*.php
 */
function cw_load() {
    static $included;
    global $app_main_dir;

    if (!$included) $included = array();

	$names = func_get_args();
	foreach ($names as $n) {

        if (isset($included[$n])) {
            continue;
        }

        $n = str_replace(array('/','..'), '', $n);

        $f = $app_main_dir . '/include/functions/cw.' . $n . '.php';
        if (file_exists($f)) {
            require_once $f;
        }

        $f = $app_main_dir . '/include/functions/base/' . $n . '.php';
        if (file_exists($f)) {
            require_once $f;
        }

        $included[$n] = 1;
    }
}

function cw_core_get_config() {
    global $tables;

    $config = array();
    if($config_tmp = db_query("select name, value, category, type from $tables[config] as c, $tables[config_categories] as cc where cc.config_category_id = c.config_category_id and c.type != 'separator'")) {
        while ($arr = db_fetch_array($config_tmp)) {
            $arr['category'] = preg_replace('/[^a-zA-Z0-9_]/', '_', $arr['category']);
            if ($arr['category'] == 'main')
                $config[$arr['name']] = $arr['value'];
            elseif($arr['type'] == 'multiselector')
                $config[$arr['category']][$arr['name']] = explode(';', $arr['value']);
            else
                $config[$arr['category']][$arr['name']] = $arr['value'];
                
            if (strtolower($arr['category'])!=$arr['category']) {
				$config[strtolower($arr['category'])] = &$config[$arr['category']];
			}
        }
        db_free_result($config_tmp);
    }
    return $config;
}

function cw_core_get_addons($all = null) {
    global $tables;
// TODO: use cache
    $addons = cw_query('SELECT a.addon, a.orderby 
    FROM ' . $tables['addons'] .' a'. ($all ? '' : ' LEFT JOIN '.$tables['addons'].' p ON a.parent = p.addon WHERE a.active=1 AND (p.active=1 OR p.active IS NULL)') . ' ORDER BY a.orderby');
    return $addons;
}

function cw_header_location($location, $keep_https = true, $possible_frame = false, $http_code = 302) {
	global $APP_SESS_ID;
	global $is_location;
	global $config, $HTTPS, $REQUEST_METHOD, $REQUEST_URI;

    global $target, $action;
    cw_event('on_after_'.$target);
    cw_event('on_after_'.$target.'_'.$action);
    
    if (defined('IS_AJAX') && constant('IS_AJAX')) return false;  // Do not redirect in AJAX requests

	$is_location = 'Y';
	cw_session_save();

	$added = array();

	if ($keep_https && $REQUEST_METHOD == "POST" && $HTTPS && strpos($location,'keep_https=yes') === false && $config['Security']['dont_leave_https'] == "Y") {
		$added[] = "keep_https=yes";
		# this block is necessary (in addition to https.php) to prevent appearance of secure alert in IE
	}

	if (!empty($added)) {
		$hash = "";
		if (preg_match("/^(.+)#(.+)$/", $location, $match)) {
			$location = $match[1];
			$hash = $match[2];
		}

		$location .= (strpos($location, "?") === false ? "?" : "&").implode("&", $added);
		if (!empty($hash))
			$location .= "#".$hash;
	}

	$supported_http_codes = array(
		'301' => "301 Moved Permanently",
		'302' => "302 Found",
		'303' => "303 See Other",
		'304' => "304 Not Modified",
		'305' => "305 Use Proxy",
		'307' => "307 Temporary Redirect"
	);

	# Opera 8.51 (8.x ?) notes:
	# 1. Opera requires both headers - "Location" & "Refresh". Without "Location" it displays
	#    HTML code for META redirect
	# 2. 'Refresh' header is required when ansvering on POST request
    if ($possible_frame) {
        echo "<br /><br />".cw_get_langvar_by_name('txt_header_note', array("time" => 2, "location" => $location), false, true, true);
        echo "<script language=\"javascript\">window.parent.location.href=\"$location\"</script>";
    }
    else {
    	if (
	    	!empty($http_code)
	    	&& in_array($http_code, array_keys($supported_http_codes))
    	) {
    		@header("HTTP/1.1 " . $supported_http_codes[$http_code]);
    	}

    	if (!@preg_match("/Microsoft|WebSTAR|Xitami/", getenv("SERVER_SOFTWARE")))
    		@header("Location: ".$location);
	    if (strpos($_SERVER['HTTP_USER_AGENT'],'Opera')!==false || @preg_match("/Microsoft|WebSTAR|Xitami/", getenv("SERVER_SOFTWARE")))
    		@header("Refresh: 0; URL=".$location);

    	echo "<br /><br />".cw_get_langvar_by_name('txt_header_note', array("time" => 2, "location" => $location), false, true, true);
	    echo "<meta http-equiv=\"Refresh\" content=\"0;URL=$location\" />";
    }

	if ($location == 'index.php?target=help&section=login_customer') {
		$remember_data = &cw_session_register("remember_data");
		$remember_data['URL'] = $REQUEST_URI;
	}

	if ($REQUEST_URI != $location) {
		cw_track_navigation_history($REQUEST_URI, $REQUEST_METHOD, FALSE);
	}

    cw_track_navigation_history($location, $REQUEST_METHOD, FALSE);

	cw_flush();
	exit();
}

#
# Calculates weight from user units to grams
#
function cw_weight_in_grams($weight) {
	global $config;
	return $weight*$config['General']['weight_symbol_grams'];
}

function cw_dim_in_centimeters($value) {
    global $config;
    return $value * $config['General']['dimensions_symbol_cm'];
}

function cw_units_convert($value, $from_unit = 'lbs', $to_unit = 'kg', $precision = null)
{
    $from_unit     = strtolower($from_unit);
    $to_unit     = strtolower($to_unit);

    if (strcmp($from_unit, $to_unit) != 0) {

        $units = array(
            "lbs-oz" => 16,
            "lbs-g"  => 453.59237,
            "kg-lbs" => 2.20462262,
            "kg-oz"  => 35.2739619,
            "kg-g"   => 1000,
            "oz-g"   => 28.3495231,
            "in-cm"  => 2.54,
            "in-dm"  => 25.4,
            "in-m"   => 254,
            "cm-dm"  => 10,
            "cm-m"   => 100,
            "dm-m"   => 10,

        );

        $rate = 1.0;

        if (array_key_exists("$from_unit-$to_unit", $units)) {

            $rate = $units["$from_unit-$to_unit"];

        } elseif (array_key_exists("$to_unit-$from_unit", $units)) {

            $rate = $units["$to_unit-$from_unit"];
            $rate = (($rate <= 0) ? 1.0 : (1.0 / $rate));

        }

        $value = $value * $rate;
    }

    return is_null($precision)
        ? ceil($value)
        : round($value, intval($precision));
}

#
# Get county by code
#
function cw_get_county ($county_id) {
	global $tables;

	$county_name = cw_query_first_cell("SELECT county FROM $tables[map_counties] WHERE county_id='$county_id'");

	return ($county_name ? $county_name : $county_id);
}

function cw_get_region($retion_id) {
    global $tables;
	// @TODO what is $region_id?
    return cw_query_first_cell("select region from $tables[map_regions] where region_id='$region_id'");
}

function cw_get_state ($state_code, $country_code) {
	global $tables;

    if (!$state_code || !$country_code) return '';

	$state_name = cw_query_first_cell("SELECT state FROM $tables[map_states] WHERE country_code='$country_code' AND code='".addslashes($state_code)."'");
	return ($state_name ? $state_name : $state_code);
}

#
# Get country by code
#
function cw_get_country ($country_code, $force_code = '', $force_output = false) {
	global $tables, $current_language;

    if (!$country_code) return '';

    $code = $force_code?$force_code:$current_language;
    return cw_get_langvar_by_name('country_'.$country_code, array(), $code, $force_output);
}

#
# This function returns true if $cart is empty
#
function cw_is_cart_empty($cart) {
	return empty($cart) || empty($cart['products']) && empty($cart['giftcerts']);
}

#
# Get value of language variable by its name and usertype
#
function cw_get_langvar_by_name($lang_name, $replace_to=NULL, $force_code = false, $force_output = false, $cancel_wm=false, $escape_tooltip=true) {
	global $tables, $current_area, $config, $current_language, $app_config_file;
	global $smarty;
	global $predefined_lng_variables;

    global $cw__langvars;

	$language_code = $current_language;
    
	if ($force_code !== false)
		$language_code = $force_code;

    if (empty($language_code)) $language_code = ($current_area == "C" ? $config['default_customer_language'] : $config['default_admin_language']);
    if (empty($language_code)) $language_code = 'EN';

	if ($force_output === false) {
		$predefined_lng_variables[$lang_name] = $lang_name;
		if ($force_code === false)
			$language_code = "  ";

		$tmp = "";
		if (is_array($replace_to) && !empty($replace_to)) {
			foreach($replace_to as $k => $v) {
				$tmp .= "$k>$v<<<";
			}

			$tmp = substr($tmp, 0, -3);
		}

		return "~~~~|".$lang_name."|".$language_code."|".$tmp."|~~~~";
	}
    
    if (is_null($cw__langvars[$language_code])) {
        $cw__langvars[$language_code] = cw_cache_get($language_code.'_'.$current_area,'lang');
    }
    if (isset($cw__langvars[$language_code][$lang_name]) && empty($replace_to)) {
        return $cw__langvars[$language_code][$lang_name];
    }

	$result = cw_query_first("SELECT value, tooltip FROM $tables[languages] WHERE code='$language_code' AND name='$lang_name'");
	if (empty($result)) {
		$_language_code = ($current_area == "C" ? $config['default_customer_language'] : $config['default_admin_language']);
		if ($_language_code != $language_code) {
			$result = cw_query_first("SELECT value, tooltip FROM $tables[languages] WHERE code='$_language_code' AND name='$lang_name'");
		}
		elseif ($language_code != 'EN') {
			$result = cw_query_first("SELECT value, tooltip FROM $tables[languages] WHERE code='EN' AND name='$lang_name'");
		}
	}

    if ($app_config_file['debug']['show_langvars']) $result['tooltip'] = $lang_name.$result['tooltip'];
    if (!empty($result['tooltip']) && !$escape_tooltip) {
        $result['value'] = '<span class="lng_tooltip" title="' . str_replace('"', '&quot;', $result['tooltip']) . '">' . $result['value'] . '</span>';
    }

	if (is_array($replace_to)) {
		foreach ($replace_to as $k=>$v)
			$result['value'] = str_replace("{{".$k."}}", $v, $result['value']);
	}

    if (empty($replace_to)) {
        $cw__langvars[$language_code][$lang_name] = $result['value'];
        $cw__langvars['updated'] = true;
    }

    if (!$escape_tooltip 
        && $config['webmaster']['webmaster_langvar'] == 'Y'
        && defined('WEBMASTER_STATUS') && constant('WEBMASTER_STATUS')==true
        ) {
        $result['value'] .= "<webmaster type='langvar' key='$result[name]'></webmaster>";
    }
    
	return $result['value'];
}

#
# Flush output
#
function cw_flush($s = NULL) {
	if (!is_null($s))
		echo $s;

	if (preg_match("/Apache(.*)Win/S", getenv("SERVER_SOFTWARE")))
		echo str_repeat(" ", 2500);
	elseif (preg_match("/(.*)MSIE(.*)\)$/S", getenv("HTTP_USER_AGENT")))
		echo str_repeat(" ", 256);

	ob_flush();

	flush();
}

#
# This function added the ability to redirect a user to another page using HTML meta tags
# (without using header() function or Javascript)
#
function cw_html_location($url, $time=3) {
	global $REQUEST_URI, $REQUEST_METHOD;

	cw_session_save();

	echo "<br /><br />".cw_get_langvar_by_name("txt_header_note", array("time" => $time, "location" => $url),false,true);
	echo "<meta http-equiv=\"Refresh\" content=\"$time;URL=$url\" />";

	cw_track_navigation_history($REQUEST_URI, $REQUEST_METHOD, FALSE);

	cw_flush();
	exit;
}

#
# This function returns the language variable value by name and language code
#
function cw_get_languages_alt($name, $lng_code = false, $force_get = false) {
	global $tables, $current_language, $config, $current_area;

	if ($lng_code === false)
		$lng_code = $current_language;

	if ($force_get) {
		# Force get language variable(s) content
		$is_array = is_array($name);
		if (!$is_array)
			$name = array($name);

		if ($current_area == 'C' || $current_area == 'B') {
			$lngs = array($lng_code, $config['default_customer_language'], $config['default_admin_language'], false);
		} else {
			$lngs = array($lng_code, $config['default_admin_language'], $config['default_customer_language'], false);
		}
		$lngs = array_unique($lngs);

		$hash = array();
		foreach ($lngs as $lng_code) {
			$where = '';
			if ($lng_code !== false)
				$where = " AND code = '$lng_code'";

			$res = cw_query_hash("SELECT name, value FROM $tables[languages_alt] WHERE name IN ('".implode("','", $name)."')".$where, "name", false, true);

			if (empty($res))
				continue;

			foreach($res as $n => $l) {
				if (!isset($hash[$n])) {
					$hash[$n] = $l;
					$idx = array_search($n ,$name);
					// @TODO what is $ids?
					if ($ids !== false)
						unset($name[$idx]);
				}
			}

			if (empty($name))
				break;
		}

		return !$is_array ? array_shift($hash) : $hash;
	}

	if (is_array($name)) {
		return cw_query_hash("SELECT name, value FROM $tables[languages_alt] WHERE code='$lng_code' AND name IN ('".implode("','", $name)."')", "name", false, true);
	}

	return cw_query_first_cell("SELECT value FROM $tables[languages_alt] WHERE code='$lng_code' AND name='$name'");
}

#
# This function quotes arguments for shell command according
# to the host operation system
#
function cw_shellquote() {
	static $win_s = '!([\t \&\<\>\?]+)!S';
	static $win_r = '"\\1"';
	$result = "";
	$args = func_get_args();
	foreach ($args as $idx=>$arg)
		$args[$idx] = CW_IS_OS_WINDOWS ? (preg_replace($win_s,$win_r,$arg)) : (escapeshellarg($arg));

	return implode(' ', $args);
}

function cw_code_get_template_dir() {
    global $app_dir, $app_config_file;
    return $app_dir.$app_config_file['web']['skin'];
}

function cw_display($tpl, &$templater, $to_display = true, $lng_code = '') {
	global $config, $location, $HTTPS;
	global $predefined_lng_variables, $current_language, $__smarty_time, $__smarty_size;
	global $app_main_dir;
	global $__X_LNG, $REQUEST_URI, $REQUEST_METHOD;
    global $ars_hooks;

    global $target, $action;
	if ($to_display) {
		cw_event('on_after_'.$target);
		cw_event('on_after_'.$target.'_'.$action);
	}

    $include = array('css' => array(), 'js' => array());
    if (is_array($ars_hooks['css']['all'])) $include['css'] = array_merge($include['css'], $ars_hooks['css']['all']);
    if (is_array($ars_hooks['css'][AREA_TYPE])) $include['css'] = array_merge($include['css'], $ars_hooks['css'][AREA_TYPE]);
    if (is_array($ars_hooks['js']['all'])) $include['js'] = array_merge($include['js'], $ars_hooks['js']['all']);
    if (is_array($ars_hooks['js'][AREA_TYPE])) $include['js'] = array_merge($include['js'], $ars_hooks['js'][AREA_TYPE]);
    $templater->assign('include', $include);

    $templater->assign('config', $config);
    $templater->assign('location', $location);
    $templater->assign('lng', array());

	cw_load('templater');

	if (!empty($config['Security']['compiled_tpl_check_md5']) && $config['Security']['compiled_tpl_check_md5'] == 'Y')
		$templater->compile_check_md5 = true;
	else
		$templater->compile_check_md5 = false;

	if (!empty($predefined_lng_variables)) {
		if (empty($lng_code))
			$lng_code = $current_language;

		if (!empty($predefined_lng_variables)) {
			$predefined_lng_variables = array_flip($predefined_lng_variables);
			$predefined_vars = array();
			cw_get_lang_vars_extra($lng_code, $predefined_lng_variables, $predefined_vars);

			$templater->_tpl_vars['lng'] = cw_array_merge($templater->_tpl_vars['lng'], $predefined_vars);

			if (!isset($__X_LNG[$current_language]))
				$__X_LNG[$current_language] = $predefined_vars;
			else
				$__X_LNG[$current_language] = cw_array_merge($__X_LNG[$current_language], $predefined_vars);

			unset($predefined_vars);
		}
		unset($predefined_lng_variables);
	}

	$templater->register_postfilter("cw_tpl_add_hash");
	$templater->register_postfilter('cw_tpl_postfilter');
/*
        global $REMOTE_ADDR;
        if ($REMOTE_ADDR == '217.174.62.172') {
            print('current language '.$current_language); 
            global $test_email; 
            if ($test_email) {
                print('test email<br>');
            }
        } else
*/
	$templater->register_outputfilter('cw_convert_lang_var');

	$templater->register_outputfilter('cw_generate_css_sprites');
    $templater->load_filter('post', 'cw_hooks');

    $templater->register_outputfilter('cw_load_head_resource'); 

    global $current_area; 
    if ($config['performance']['defer_load_js_code'] == 'Y' && $current_area == 'C' && !(defined('IS_AJAX') && constant('IS_AJAX'))) { 
        $templater->register_outputfilter('cw_defer_load_js_code');
    }

    if ($config['performance']['sprite_all_images'] == 'Y' && $current_area == 'C' && !(defined('IS_AJAX') && constant('IS_AJAX'))) { 
        $templater->register_outputfilter('cw_sprite_all_images');
    }
    if ($config['performance']['list_available_cdn_servers'] && !$HTTPS) {
        $templater->register_outputfilter('cw_sharing_cdn');
    }

	cw_track_navigation_history($REQUEST_URI, $REQUEST_METHOD, TRUE);

	if($to_display == true) {
		$templater->display($tpl);
		$ret = "";

		# Display page content
		cw_flush();
	}
    else
        $ret = $templater->fetch($tpl);

	return $ret;
}

#
# Function for fetching language variables values for one code
#
function cw_get_lang_vars($code, &$variables, &$lng) {
	global $tables, $app_config_file, $config;
    
    global $cw__langvars, $current_area;

    if (is_null($cw__langvars[$code])) {
        $cw__langvars[$code] = cw_cache_get($code.'_'.$current_area,'lang');
    }
    
    if (!empty($variables) && is_array($variables)) {
        foreach ($variables as $lang_name => $_v) {
            if (isset($cw__langvars[$code][$lang_name])) {
                $lng[$lang_name] = $cw__langvars[$code][$lang_name];
                unset($variables[$lang_name]);
                continue;
            }
        }
    }

    if (empty($variables)) return true;
    
    $variables = array_fill_keys(array_keys($variables),'');
    $cw__langvars[$code] = cw_array_merge($cw__langvars[$code], $variables);
    
	$labels = db_query("
        SELECT name, value, tooltip
        FROM $tables[languages]
        WHERE code = '$code' AND name IN ('".implode("','", array_keys($variables))."')
	");
	if ($labels) {
		while ($v = db_fetch_array($labels)) {
            if ($app_config_file['debug']['show_langvars']) $v['tooltip'] = $v['name'].$v['tooltip'];
            if (!empty($v['tooltip'])) {
                $lng[$v['name']] = '<span class="lng_tooltip" title="' . str_replace('"', '&quot;', $v['tooltip']) . '">' . $v['value'] . '</span>';
            }
            else {
			    $lng[$v['name']] = $v['value'];
            }
            $cw__langvars[$code][$v['name']] = $lng[$v['name']];
			unset($variables[$v['name']]);

            if (!$escape_tooltip 
                && $config['webmaster']['webmaster_langvar'] == 'Y'
                && defined('WEBMASTER_STATUS') && constant('WEBMASTER_STATUS')==true
                ) {
                $lng[$v['name']] .= "<webmaster type='langvar' key='$v[name]'></webmaster>";
            }

		}
        $cw__langvars['updated'] = true;
		db_free_result($labels);
	}
}

#
# Extra version of cw_get_lang_vars(): try to fetch values of language variables
# using all possible language codes
#
function cw_get_lang_vars_extra($prefered_lng_code, &$variables, &$lng) {
	global $current_area, $config;

	if (empty($variables))
		return;

	cw_get_lang_vars($prefered_lng_code, $variables, $lng);
	if (empty($variables))
		return;

/*
	$default_language = ($current_area == 'C' ? $config['default_customer_language'] : $config['default_admin_language']);
	if ($default_language != $prefered_lng_code) {
		cw_get_lang_vars($default_language, $variables, $lng);
		if (empty($variables))
			return;
	}

	if ($default_language != 'US')
		cw_get_lang_vars('US', $variables, $lng);
*/
}

/**
 * Save loaded langvars to cache file per lang/area
 * function is registered to call on shutdown
 * 
 * @see init/lng.php
 * 
 */
function cw_langvars_cache() {
    // Langvars cache
    global $cw__langvars, $current_area;
    if (isset($cw__langvars['updated']))
    if ($cw__langvars['updated'] == true) {
        unset($cw__langvars['updated']);
        foreach ($cw__langvars as $language_code=>$cached_vars) {
            cw_cache_save($cached_vars, $language_code.'_'.$current_area, 'lang');
        }
        cw_call_delayed('cw_track_langvar_usage');

    }    
}
#
# Parse string to hash array like:
# x=1|y=2|z=3
# where:
#	str 	= x=1|y=2|z=3
#	delim 	= |
# convert to:
# array('x' => 1, 'y' => 2, 'z' => 3)
#
function cw_parse_str($str, $delim = '&', $pair_delim = '=', $value_filter=false) {
	if (empty($str))
		return array();

	$arr = explode($delim, $str);
	$return = array();
	for ($x = 0; $x < count($arr); $x++) {
		$pos = strpos($arr[$x], $pair_delim);
		if ($pos === false) {
			$return[$arr[$x]] = false;
		}
		elseif ($pos >= 0) {
			$v = substr($arr[$x], $pos+1);
			if (!empty($value_filter))
				$v = $value_filter($v);	// @TODO $value_filter is function name?

			$return[substr($arr[$x], 0, $pos)] = $v;
		}
	}

	return $return;
}

#
# Remove parameters from QUERY_STRING by name
#
function cw_qs_remove($qs) {
    if (func_num_args() <= 1)
        return $qs;

    $args = func_get_args();
	array_shift($args);

	if (count($args) == 0 || (strpos($qs, "=") === false && strpos($qs, "?") === false))
		return $qs;

	# Get scheme://domain/path part
	if (strpos($qs, '?') !== false)
		list($main, $qs) = explode("?", $qs, 2);

	# Get #hash part
	if (strrpos($qs, "#") !== false) {
		$hash = substr($qs, strrpos($qs, "#")+1);
		$qs = substr($qs, 0, strrpos($qs, "#"));
	}

	# Parse query string
	$arr = cw_parse_str($qs);

	# Filter query string
	foreach ($args as $param_name) {
		if (empty($param_name) || !is_string($param_name))
			continue;

        $reg = "/^".preg_quote($param_name, "/")."(\[[^\]]*\])*(\Z|=)/S";
		foreach ($arr as $ak => $av) {
			if (preg_match($reg, $ak) || empty($ak)) {
				unset($arr[$ak]);
				break;
			}
		}
	}

	# Assembly return string
	foreach ($arr as $ak => $av)
		$arr[$ak] = $ak."=".$av;

	$qs = implode("&", $arr);

	if (isset($main))
		$qs = $main.(empty($qs) ? "" : ("?".$qs));

	if (isset($hash))
		$qs .= "#".$hash;

	return $qs;
}

function cw_get_default_field($name) {
	return cw_get_langvar_by_name('lbl_'.$name, false, false, true);
}

#
# Get memberships list
#
function cw_get_memberships($area = 'C', $as_hash = false) {
	global $tables, $current_language;

	$query_string = "SELECT $tables[memberships].membership_id, IFNULL($tables[memberships_lng].membership, $tables[memberships].membership) as membership FROM $tables[memberships] LEFT JOIN $tables[memberships_lng] ON $tables[memberships].membership_id = $tables[memberships_lng].membership_id AND $tables[memberships_lng].code = '$current_language' WHERE $tables[memberships].active = 'Y' AND $tables[memberships].area = '$area' and $tables[memberships].membership_id!=0 ORDER BY $tables[memberships].orderby";

	if ($as_hash) {
		return cw_query_hash($query_string, "membership_id", false);
	} else {
		return cw_query($query_string);
	}
}

function cw_detect_membership($membership = "", $type = false) {
	global $tables;

	if (empty($membership))
		return 0;

	$where = "";
	if ($type != false)
		$where = " AND area = '$type'";

	$membership = addslashes($membership);
	$id = cw_query_first_cell("SELECT membership_id FROM $tables[memberships] WHERE membership = '$membership'".$where);

	return $id ? $id : 0;
}

#
# The function is merging arrays by keys
# Ex.:
# array(5 => "y") = cw_array_merge_assoc(array(5 => "x"), array(5 => "y"));
#
function cw_array_merge_assoc() {
	if (!func_num_args())
		return array();

	$args = func_get_args();

	$result = array();
	foreach ($args as $val) {
		if (!is_array($val) || empty($val))
			continue;

		foreach ($val as $k => $v)
			$result[$k] = $v;
	}

	return $result;
}

function cw_membership_update($type, $id, $membership_ids, $field = false) {
	global $tables;

	$tbl = $tables[$type."_memberships"];
	if (empty($tbl) || empty($id))
		return false;

	if ($field === false)
		$field = $type.'_id';

	db_query("delete from $tbl where $field = '$id'");

	if (is_array($membership_ids))
    foreach ($membership_ids as $v)
        db_query("replace into $tbl values ('$id','$v')");

	return true;
}

#
# Detect price
#
function cw_is_price($price, $cur_symbol = '$', $cur_symbol_left = true) {
	if (is_numeric($price))
		return true;

	$price = trim($price);
	$cur_symbol = preg_quote($cur_symbol, "/");
	if ($cur_symbol_left) {
		$price = preg_replace("/^".$cur_symbol."/S", "", $price);
	} else {
		$price = preg_replace("/".$cur_symbol."$/S", "", $price);
	}

	return cw_is_numeric($price);
}

#
# Convert price
#
function cw_detect_price($price, $cur_symbol = '$', $cur_symbol_left = true) {

	if (!is_numeric($price)) {
		$price = trim($price);
		$cur_symbol = preg_quote($cur_symbol, "/");
		if ($cur_symbol_left) {
			$price = preg_replace("/^".$cur_symbol."/S","", $price);
		} else {
			$price = preg_replace("/".$cur_symbol."$/S","", $price);
		}
		$price = cw_convert_number($price);
	}

	return doubleval($price);
}

#
# Detect number
#
function cw_is_numeric($var, $from = NULL) {
	global $config;

	if (is_numeric($var))
		return true;

	if (strlen(@$var) == 0)
		return false;

	if (empty($from))
		$from = $config['Appearance']['number_format'];

	if (empty($from))
		$from = "2.,";

	$var = str_replace(" ", "", str_replace(substr($from, 1, 1), ".", str_replace(substr($from, 2, 1), "", $var)));

	return is_numeric($var);
}

function price_format($price, $round_top = false, $is_total = false) {
    global $config;
    $sign = 2;
    if ($config['product']['more_signs'] == 'Y') $sign = 3;
    if ($is_total && $config['product']['round_total'] == 'Y') $sign = 2;

    $del = $sign ==3?1000:100;

    if ($round_top && $config['product']['round_top'] == 'Y') return  sprintf('%.'.$sign.'f', ceil((double)$price * $del)/$del);
    return sprintf('%.'.$sign.'f', round((double)$price+0.00000000001, $sign));
}

function cw_convert_number($var, $from = NULL) {
	global $config;

	if (strlen(@$var) == 0)
		return $var;

	if (empty($from))
		$from = $config['Appearance']['number_format'];

	if (empty($from))
		$from = "2.,";

    if ($config['product']['more_signs'] == 'Y') $from = strtr($from, array('2' => '3'));

	return round(cw_convert_numeric($var, $from), intval(substr($from, 0, 1)));
}

#
# Convert local number format (without precision) to inner number format
#
function cw_convert_numeric($var, $from = NULL) {
	global $config;

	if (strlen(@$var) == 0)
		return $var;

	$var = trim($var);
	if (preg_match("/^\d+$/S", $var))
		return doubleval($var);

	if (empty($from))
		$from = $config['Appearance']['number_format'];

	if (empty($from))
		$from = "2.,";

	return doubleval(str_replace(" ", "", str_replace(substr($from, 1, 1), ".", str_replace(substr($from, 2, 1), "", $var))));
}

#
# Format price according to 'Input and display format for floating comma numbers' option
#
function cw_format_number($price, $thousand_delim = NULL, $decimal_delim = NULL, $precision = NULL) {
	global $config;

	if (empty($price)) return 0;

	$format = $config['Appearance']['number_format'];

	if (empty($format)) $format = "2.,";
    if ($config['product']['more_signs'] == 'Y') $format= strtr($format, array('2' => '3'));

	if (is_null($thousand_delim) || $thousand_delim === false)
		$thousand_delim = substr($format,2,1);

	if (is_null($decimal_delim) || $decimal_delim === false)
		$decimal_delim = substr($format,1,1);

	if (is_null($precision) || $precision === false)
		$precision = intval(substr($format,0,1));

	return number_format(round((double)$price+0.00000000001, $precision), $precision, $decimal_delim, $thousand_delim);
}

#
# Store temporary data in database for some reason
#
function cw_db_tmpwrite($data, $ttl=600) {
	$id = md5(microtime());

	$hash = array (
		'id' => addslashes($id),
		'data' => addslashes(serialize($data)),
		'expire' => time() + $ttl
	);

	cw_array2insert('temporary_data', $hash, true);
	return $id;
}

#
# Read previously stored temporary data
#
function cw_db_tmpread($id, $destroy=false) {
	global $tables;

	$tmp = cw_query_first_cell("SELECT data FROM $tables[temporary_data] WHERE id='".addslashes($id)."' LIMIT 1");
	if ($tmp === false)
		return false;

	if ($destroy) {
		db_query("DELETE FROM $tables[temporary_data] WHERE id='".addslashes($id)."'");
	}

	return unserialize($tmp);
}

#
# Display service page header
#
function cw_display_service_header($title = "", $as_text = false) {
	global $smarty;

	if (!defined("SERVICE_HEADER")) {
		define("SERVICE_HEADER", true);
		set_time_limit(86400);

		cw_display("main/service_header.tpl", $smarty);
		cw_flush();

		if (!defined("NO_RSFUNCTION"))
			register_shutdown_function("cw_display_service_footer");
	}

	if (!empty($title)) {
		if (!$as_text) {
			$title = cw_get_langvar_by_name($title, null, false, true);
			if (empty($title))
				return;
		}
		cw_flush($title.": ");
	}
}

#
# Display service page footer
#
function cw_display_service_footer() {
	global $smarty;

	if (defined("SERVICE_HEADER")) {
		cw_display("main/service_footer.tpl", $smarty);
		cw_flush();
	}
}

#
# Close current window through JS-code
#
function cw_close_window() {
?>
<script type="text/javascript">
<!--
window.close();
-->
</script>
<?php
	exit;
}

#
# Get value from array with presence check and default value
#
function get_value($array, $index, $default=false) {
	if (isset($array[$index]))
		return $array[$index];

	return $default;
}

#
# Convert EOL symbols to BR tags
# if content hasn't any tags
#
function cw_eol2br($content) {
	return ($content == strip_tags($content)) ? str_replace("\n", "<br />", $content) : $content;
}

#
# Insert the trademark to string (used for shipping methods name)
#
function cw_insert_trademark($string, $empty=false, $use_alt=false) {
	$reg = $sm = $tm = "";

	if (!empty($empty)) {
		$reg = "&#174;";
		if (empty($use_alt)) {
			$sm = "<sup>SM</sup>";
			$tm = "<sup>TM</sup>";
		}
		else {
			$sm = " (SM)";
			$tm = " (TM)";
		}
	}

	$result = preg_replace("/##R##/", $reg, $string);
	$result = preg_replace("/##SM##/", $sm, $result);
	$result = preg_replace("/##TM##/", $tm, $result);

	return $result;
}

# kornev, use some account for the admin zones/taxes
function cw_get_default_account_for_zones() {
    return 0; //'admin';
}

function cw_get_months($year = '') {

    list($year,$period) = explode(":", $year);

    if ($period == "0") {
        $start_month = 1;
        $end_month = 6;
    }
    elseif($period == "1") {
        $start_month = 7;
        $end_month = 12;
    }
    else {
        $start_month = 1;
        $end_month = 12;
    }

    $months = array();
    for ($i=$start_month; $i<=$end_month; $i++)
        $months[$i] = cw_get_langvar_by_name('lbl_month_'.$i);//date("F", mktime(0, 0, 0, $i, 1, $current_year));
    return $months;
}

/* cw_get_whois_country is NOT USED in init/lng.php anymore. Too slow for customer init. */
function cw_get_whois_country() {
    global $who_is_country, $who_is_defined, $config;
    $who_is_country = &cw_session_register('who_is_country', '');
    $who_is_defined = &cw_session_register('who_is_defined', 0);

    if ($who_is_defined) return $who_is_country;

    if (!$_SERVER['REMOTE_ADDR']) return $config['Company']['country'];

    $var = exec("whois ".$_SERVER['REMOTE_ADDR']." | grep -i country");
    $var = explode(":", $var);

    $who_is_defined = true;
    $who_is_country = trim($var[1]);

    if (!$who_is_country) $who_is_country = $config['Company']['country'];

    return $who_is_country;
}

function cw_get_year_periods() {
    $ret = array();

    $year = date('Y');
    for($i = $year-2; $i <= $year+1; $i++) {
        $ret[$i.":0"] = cw_get_langvar_by_name('lbl_month_1')."-".cw_get_langvar_by_name('lbl_month_6')." ".$i;
        $ret[$i.":1"] = cw_get_langvar_by_name('lbl_month_7')."-".cw_get_langvar_by_name('lbl_month_12')." ".$i;
    }

    return $ret;
}

function cw_get_current_period() {
    $year = date('Y');
    $month = date('n');
    return $year.":".($month > 6?"1":"0");
}

function cw_core_file_replace($path) {
	$content = file_get_contents($path);
        $content = preg_replace('/APP_START/', 'APP_START', $content);
        $content = preg_replace('/app_main_dir/', 'app_main_dir', $content);
	file_put_contents($path, $content);
}

function cw_core_get_time() {
//$config['Appearance']['timezone_offset']
    return time();
}

function cw_core_get_month_end($time = 0) {
    if (!$time) $time = cw_core_get_time();
    $date = getdate($time);
    return mktime(23, 59, 59, $date['month'], date('t', $time), $date['year']);
}

function cw_core_strtotime($str) {
    global $config;

    if ($str == strval(intval($str))) return $str;

    $str = trim($str);

    $format = $config['Appearance']['date_format'];
    $formats = array(
        '%d-%m-%Y' => array('-', array(2, 1, 3)),
        '%d/%m/%Y' => array('/', array(2, 1, 3)),
        '%d.%m.%Y' => array('.', array(2, 1, 3)),
        '%m-%d-%Y' => array('-', array(1, 2, 3)),
        '%Y-%m-%d' => array('-', array(3, 1, 2)),
    );
    if ($formats[$format]) {
        list($date, $time) = explode(' ', $str, 2);
        $parse = explode($formats[$format][0], $date);
        if (count($parse) != 3) return 0;
        array_multisort($formats[$format][1], $parse);
        $str = implode('/', $parse).' '.$time;
    }
    return strtotime($str);
}

function cw_core_get_time_frame($str1, $str2 = '') {
    $time = cw_core_get_time();
    return array(strtotime($str1, $time), ($str2?strtotime($str2, $time):$time));
}

function cw_core_parse_discount($str) {
    $arr = explode('%', $str);
    return array(cw_detect_price($arr[0]), isset($arr[1]));
}

function cw_core_process_date_fields(&$posted_data, $date_fields = array(), $multiple_fields = array()) {
    if (is_array($date_fields))
    foreach($date_fields as $section=>$fields)
        foreach($fields as $field=>$type) {
            if ($section) $process = &$posted_data[$section][$field];
            else $process = &$posted_data[$field];

            if ($process)
                $process = cw_core_strtotime($process) + $type*86399; # day - 1 sec
        }

    if (is_array($multiple_fields))
    foreach($multiple_fields as $section=>$fields) {
        if (is_array($fields))
        foreach($fields as $field) {
            if ($section) {
                $process = &$posted_data[$section][$field];
                $posted_data[$section][$field.'_orig'] = $posted_data[$section][$field];
                $process_orig = &$posted_data[$section][$field.'_orig'];
            }
            else {
                $process = &$posted_data[$field];
                $posted_data[$field.'_orig'] = $posted_data[$field];
                $process_orig = &$posted_data[$field.'_orig'];
            }

            if (is_array($process)) {
                $ret = array();
                foreach($process as $k=>$v) {
                    if (!empty($v)) $ret[$v] = true;
# kornev, 0 should be fine also
                    elseif ($v == '0') $ret[$v] = true;
                    else unset($process_orig[$k]);
                }
                $process = $ret;
            }
        }
    }
}

function cw_core_microtime() {
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

function cw_core_get_navigation($target, $total_items, $page, $default_items_per_page = 20) {
    global $items_per_page_targets, $config;
    
    $objects_per_page = $items_per_page_targets[$target]?$items_per_page_targets[$target]:$default_items_per_page;
    if (!$objects_per_page) $objects_per_page = 20;

    $total_nav_pages = ceil(intval($total_items)/$objects_per_page)+1;

    if (empty($page)) $page = 1;

    $preload_objects = 0;
    if ($_GET['preload_items']) {
        $preload_items_arr = explode("_", $_GET['preload_items']);
        $preload_items_arr[0] = intval($preload_items_arr[0]); 
        if (!empty($preload_items_arr[0])) {
            $page = $preload_items_arr[0]/$objects_per_page;
            $preload_objects = $preload_items_arr[0]-$objects_per_page;
        }
    }

    if ($page >= $total_nav_pages)
        $page = $total_nav_pages-1;

    $first_page = $objects_per_page*($page-1);

    $start_page = 0;

    $total_pages = $total_nav_pages;

    if ($total_pages > $total_nav_pages)
        $total_pages = $total_nav_pages;

    if ($page > 1 and $page >= $total_pages) {
        $page = $total_pages - 1;
        $first_page = $objects_per_page*($page-1);
    }

    if ($first_page < 0)
        $first_page = 0;

    return array(
        'total_items' => $total_items,
        'objects_per_page' => $objects_per_page,
        'preload_objects' => $preload_objects,
        'page' => $page,
        'total_pages' => $total_pages,
        'total_pages_minus' => $total_pages-1,
        'start_page' => $start_page+1,
        'first_page' => $first_page,
        'first_item' => $first_page+1,
        'last_item' => min($first_page+$objects_per_page, $total_items),
        'max_item' => ceil(max($objects_per_page, min($total_items, 100))/10)*10+1,
        'script' => 'index.php?target='.$target,
    );
}

function cw_core_restore_navigation($customer_id) {
    global $tables;

    if (!$customer_id) return;

    $ret = cw_query_first_cell("select navigation from $tables[customers_settings] where customer_id='$customer_id'");
    return unserialize($ret);
}

function cw_core_save_navigation($customer_id, $navigation) {
    global $tables;

    if (!$customer_id) return;

    if (!cw_query_first_cell("select count(*) from $tables[customers_settings] where customer_id='$customer_id'"))
        cw_array2insert('customers_settings', array('customer_id' => $customer_id), true);

    cw_array2update('customers_settings', array('navigation' => addslashes(serialize($navigation))), "customer_id='$customer_id'");
}

function cw_core_generate_string($count, $use_chars = true) {
    $ret = '';
    if ($count) {
        static $arr;
        if (!$arr) {
            for($i=48; $i <= 57; $i++) $arr[] = chr($i);
            for($i=65; $i <= 90; $i++) $arr[] = chr($i);
        }
        $max = ($use_chars?35:9);
        for($i = 0; $i < $count; $i++)
            $ret .= $arr[rand(0, $max)];
    }
    return $ret;
}

function cw_core_get_required_status($current_area) {
    global $config, $target;
    if ($config['General']['disabled_products_access_by_direct_link'] == 'Y' && AREA_TYPE == 'C' && $current_area == 'C' && $target == 'product') {
        return array(0,1,2);
    }

    if (in_array($current_area, array('A', 'P', 'S')) || (AREA_TYPE == 'A' && $current_area == 'C'))
        return array(0, 1, 2);
    elseif ($current_area == 'G')
        return array(1);

    return array(1);
}

function cw_core_copy_tables($table, $key_field, $product_id, $new_product_id) {
    global $tables;
    global $app_main_dir;
    global $language_var_names;

    $table_name = $tables[$table];
    if (empty($table) || empty($table_name))
        return false;

    $error_string = "";

    $res = db_query("SHOW COLUMNS FROM $table_name");
    while ($row = db_fetch_array($res)) {
        $name  = $row['Field'];
        $flags = $row['Extra'];
        $fields[$name] = $flags;
    }

    db_free_result($res);

    $result = cw_query("SELECT * FROM $table_name WHERE $key_field='$product_id'");

    if (!$result)
        return false;

    foreach ($result as $key=>$row) {
        if (!$row) continue;

        $str = "INSERT INTO $table_name (";
        foreach ($row as $k=>$v) {
            if (is_numeric($k)) continue;

            if ($k==$key_field || strpos($fields[$k], "auto_increment")===false)
                $str .= "$k,";
        }

        $str = preg_replace("/,$/", ") VALUES (", $str);
        foreach ($row as $k=>$v) {
            if (is_numeric($k)) continue;

            if ($k==$key_field || strpos($fields[$k], "auto_increment")===false) {
                if ($k == $key_field) {
                    if (is_numeric($new_product_id))
                        $str .= "$new_product_id,";
                    else
                        $str .= "'".addslashes($new_product_id)."',";
                }
                else {
                    $str .= "'".addslashes($v)."',";
                }
            }
        }

        $str = preg_replace("/,$/Ss", ")", $str);
        db_query($str);

        if (db_affected_rows() < 0) {
            $error_string .= "$str<br />";
        }
    }

    return $error_string;
}

function cw_lock($lockname, $ttl = 15, $cycle_limit = 0) {
    global $var_dirs, $_lock_hash;

    if (empty($lockname))
        return false;

    if (!empty($_lock_hash[$lockname]))
        return $_lock_hash[$lockname];

    $fname = $var_dirs['tmp'].DIRECTORY_SEPARATOR.$lockname;

    # Generate current id
    $id = md5(uniqid(rand(0, substr(floor(cw_core_microtime()*1000), 3)), true));
    $_lock_hash[$lockname] = $id;

    $file_id = false;
    $limit = $cycle_limit;
    while (($limit-- > 0 || $cycle_limit <= 0)) {
        if (!file_exists($fname)) {

            # Write locking data
            $fp = fopen($fname, "w");
            if ($fp) {
                fwrite($fp, $id.time());
                fclose($fp);
            }
        }

        $fp = fopen($fname, "r");
        if (!$fp)
            return false;

        $tmp = fread($fp, 43);
        fclose($fp);

        $file_id = substr($tmp, 0, 32);
        $file_time = substr($tmp, 32);

        if ($file_id == $id)
            break;

        if ($ttl > 0 && time() > $file_time+$ttl) {
            @unlink($fname);
            continue;
        }

        sleep(1);
    }

    return $file_id == $id ? $id : false;
}

function cw_unlock($lockname) {
    global $var_dirs, $_lock_hash;

    if (empty($lockname))
        return false;

    if (empty($_lock_hash[$lockname]))
        return false;

    $fname = $var_dirs['tmp'].DIRECTORY_SEPARATOR.$lockname;
    if (!file_exists($fname))
        return false;

    $fp = fopen($fname, "r");
    if (!$fp)
        return false;

    $tmp = fread($fp, 43);
    fclose($fp);

    $file_id = substr($tmp, 0, 32);
    $file_time = substr($tmp, 32);

    if ($file_id == $_lock_hash[$lockname])
        @unlink($fname);

    cw_unset($_lock_hash, $lockname);

    return true;
}

function cw_core_get_available_languages() {
    global $tables, $current_language;
    return cw_query_hash("select ls.*, lng.value as language from $tables[languages_settings] as ls left join $tables[languages] as lng ON lng.code = '$current_language' and lng.name = CONCAT('language_', ls.code) where ls.enable=1", 'code', false);
}

// replacing of the default php functions
/* kornev, don't need it anymore
if (!function_exists('file_put_contents')) {
function file_put_contents($filename, $data) {
    if (($h = fopen($filename, 'w')) === false)
        return false;

    if (($bytes = @fwrite($h, $data)) === false)
        return false;

    fclose($h);
    return $bytes;
}
}
*/
/**
 * Function adds additonal GET params to the URL, passed params can replace existing in url
 * 
 * @param string $url - base url
 * @param array $params - array (or multilevel array) for conversion into GET string
 * @param array $exclude - plain array of keys to be excluded from $params
 * @param string $delimeter - params delimiter, default is &amp;
 * @param bool $exclude_default - flag to exclude default params
 * 
 * @return string $url
 */
function cw_core_assign_addition_params($url, $params, $exclude = array(), $delimiter = null, $exclude_default = true) {

    static $default_exclude = array('include_question', 'var', 'language', 'assign', 'action', 'delimiter');

    if (is_null($delimiter)) $delimiter= '&amp;';

    $base_url = $url;
    $get_params = $params;
    $q = parse_url($base_url);

    parse_str($q['query'], $base_url_get_params);

    $get_params = array_replace_recursive($base_url_get_params, $get_params);

    if ($exclude_default) {
        $exclude = array_merge($exclude, $default_exclude);
    }

    if (is_array($exclude) && !empty($exclude)) {
        foreach ($get_params as $k=>$v) {
            if (in_array($k, $exclude) || is_null($v)) unset($get_params[$k]);
        }
    }

    $query_url = http_build_query($get_params,'',$delimiter);
    
    // cleanup att[X][Y]= into att[X][]=
    $query_url = preg_replace('|att\[(\d+)\]\[\d+\]=|','att[\1][]=',$query_url);

    $question_sign = (empty($query_url)?'':'?');

    $url = $q['host'].$q['path'].$question_sign.$query_url;

    return $url;

}

function cw_core_get_html_page_url($params) {
    global $app_web_dir, $tables, $current_language, $app_catalogs, $area;

    if ($ret = cw_get_return()) return $ret;

# kornev, disable language for now;
//    $language = strtolower($params['language']?$params['language']:$current_language);
    return cw_core_assign_addition_params($app_web_dir.'/index.php?target='.$params['var'], $params, array(), $params['delimiter'], isset($params['is_exclude'])?$params['is_exclude']:true);
}

function cw_core_get_meta($tag) {
    global $smarty, $location, $config;

    if ($ret = cw_get_return()) return $ret;

    if ($tag == 'title') {
        $tmp = array();
        if ($location) {
            $first_title_part = cw_call('cw_core_get_first_title_part', array($location));
            if (!empty($first_title_part)) $tmp[] = $first_title_part;  

            $last_title_part = cw_call('cw_core_get_last_title_part', array($location));
            if (!empty($last_title_part)) $tmp[] = $last_title_part;
        }
        if ($config['General']['page_title_format'] == 'D') $tmp = array_reverse($tmp);
        $ret = strip_tags(implode(' | ', $tmp));
    }
    elseif ($tag == 'keywords') {
        $ret = cw_get_langvar_by_name('lbl_site_meta_keywords');
    }
    elseif ($tag == 'description') {
        if ($smarty->_tpl_vars['product']) $ret = $smarty->_tpl_vars['product']['descr'];
        elseif ($smarty->_tpl_vars['manufacturer']) $ret = $smarty->_tpl_vars['manufacturer']['descr'];
        elseif ($smarty->_tpl_vars['page_data']) $ret = $smarty->_tpl_vars['page_data']['content'];
        elseif ($smarty->_tpl_vars['current_category']) $ret = $smarty->_tpl_vars['current_category']['description'];
        $ret = substr(strip_tags($ret.cw_get_langvar_by_name('lbl_site_meta_descr')), 0, 255);
    }
    return $ret;
}

function cw_core_get_first_title_part($location) {
    return $location[0][0];
}

function cw_core_get_last_title_part($location) {

    $last_title_part = "";
    if (($cnt=count($location))>1) 
        $last_title_part = $location[$cnt-1][0];   

    return $last_title_part;
}


    /**
   * Return path with trailing slash
   *
   * @param string $path Input path
   * @return string Path with trailing slash
   */
  function with_slash($path) {
    return str_ends_with($path, '/') ? $path : $path . '/';
  } // end func with_slash

  /**
   * Remove trailing slash from the end of the path (if exists)
   *
   * @param string $path File path that need to be handled
   * @return string
   */
  function without_slash($path) {
    //str_ends_with($path, '/') ? substr($path, 0, strlen($path) - 1) : $path;
    return rtrim($path,'/');
  } // without_slash

  /**
   * Add leading slash (if exists)
   */
  function with_leading_slash($path) {
    return str_starts_with($path, '/') ? $path : '/' . $path;
  } // end with_leading_slash

  /**
   * Remove leading slash (if exists)
   */
  function without_leading_slash($path) {
    return ltrim($path,'/'); //str_starts_with($path, '/') ? substr($path, 1) : $path;
  } // end without_leading_slash

  /**
   * Format path with leading slash and without trailing to make concatenation easier
   */
  function with_leading_slash_only($path, $root_slash_for_empty=false) {
    if (empty($path) && !$root_slash_for_empty) return '';
    return with_leading_slash(without_slash($path));
  }

  /**
   * This function will return true only if input string ends with
   * needle
   *
   * @param string $string Input string
   * @param string $needle Needle string
   * @return boolean
   */
  function str_ends_with($string, $needle) {
    return substr($string, strlen($string) - strlen($needle), strlen($needle)) == $needle;
  } // end func str_ends_with

  function str_starts_with($string, $needle) {
    return $string[0] == $needle;
  }

/**
 * Check if it is AJAX request
 */
function cw_is_ajax_request()
{
    return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' ) || (!empty($_GET['is_ajax']) || !empty($_POST['is_ajax']));
}

/**
 * Check if $var is object of $class_name
 *
 * @param mixed $var
 * @param string $class_name
 * @return boolean
 */
function instance_of($var, $class_name) {
	return is_object($var) && is_a($var, $class_name);
} // instance_of


/**
 * array_column available from php 5.5 only
 */
if (!function_exists('array_column')) {
/**
* Returns the values from a single column of the input array, identified by
* the $columnKey.
*
* Optionally, you may provide an $indexKey to index the values in the returned
* array by the values from the $indexKey column in the input array.
*
* @param array $input A multi-dimensional array (record set) from which to pull
* a column of values.
* @param mixed $columnKey The column of values to return. This value may be the
* integer key of the column you wish to retrieve, or it
* may be the string key name for an associative array.
* @param mixed $indexKey (Optional.) The column to use as the index/keys for
* the returned array. This value may be the integer key
* of the column, or it may be the string key name.
* @return array
*/
    function array_column($input = null, $columnKey = null, $indexKey = null)
    {
        // Using func_get_args() in order to check for proper number of
        // parameters and trigger errors exactly as the built-in array_column()
        // does in PHP 5.5.
        $argc = func_num_args();
        $params = func_get_args();

        if ($argc < 2) {
            trigger_error("array_column() expects at least 2 parameters, {$argc} given", E_USER_WARNING);
            return null;
        }

        if (!is_array($params[0])) {
            trigger_error('array_column() expects parameter 1 to be array, ' . gettype($params[0]) . ' given', E_USER_WARNING);
            return null;
        }

        if (!is_int($params[1])
            && !is_float($params[1])
            && !is_string($params[1])
            && $params[1] !== null
            && !(is_object($params[1]) && method_exists($params[1], '__toString'))
        ) {
            trigger_error('array_column(): The column key should be either a string or an integer', E_USER_WARNING);
            return false;
        }

        if (isset($params[2])
            && !is_int($params[2])
            && !is_float($params[2])
            && !is_string($params[2])
            && !(is_object($params[2]) && method_exists($params[2], '__toString'))
        ) {
            trigger_error('array_column(): The index key should be either a string or an integer', E_USER_WARNING);
            return false;
        }

        $paramsInput = $params[0];
        $paramsColumnKey = ($params[1] !== null) ? (string) $params[1] : null;

        $paramsIndexKey = null;
        if (isset($params[2])) {
            if (is_float($params[2]) || is_int($params[2])) {
                $paramsIndexKey = (int) $params[2];
            } else {
                $paramsIndexKey = (string) $params[2];
            }
        }

        $resultArray = array();

        foreach ($paramsInput as $row) {

            $key = $value = null;
            $keySet = $valueSet = false;

            if ($paramsIndexKey !== null && array_key_exists($paramsIndexKey, $row)) {
                $keySet = true;
                $key = (string) $row[$paramsIndexKey];
            }

            if ($paramsColumnKey === null) {
                $valueSet = true;
                $value = $row;
            } elseif (is_array($row) && array_key_exists($paramsColumnKey, $row)) {
                $valueSet = true;
                $value = $row[$paramsColumnKey];
            }

            if ($valueSet) {
                if ($keySet) {
                    $resultArray[$key] = $value;
                } else {
                    $resultArray[] = $value;
                }
            }

        }

        return $resultArray;
    }
}

/**
 * Track navigation history
 * 
 * @param url - visited URL
 * @param method - request method [GET|POST]
 * @param show - true if page was shown to client, false if redirect occurred for example after POST request processing
 */
function cw_track_navigation_history($url, $method, $show) {
	global $config;

	if (empty($config['General']['last_urls_tracked_in_session'])) return;

	$navigation_history_list = &cw_session_register('navigation_history_list', array());

	$item = array(
		"url" => $url,
		"method" => $method,
		"show" => $show
	);
	$count = array_unshift($navigation_history_list, $item);
	
	if ($count > $config['General']['last_urls_tracked_in_session']) {
		array_splice($navigation_history_list, $config['General']['last_urls_tracked_in_session']);
	}
}

/**
 * Track langvar usage
 *
 * function used in cw_get_langvar_by_name
 * statistic saved in table cw_langvars_statistics
 * set track_langvar_usage to true in config.ini to enable the collection of statistics
 */
function cw_track_langvar_usage() {
    global $app_config_file, $tables, $current_area, $config;

    if ($app_config_file['statistic']['track_langvar_usage'] != TRUE) return false;
    
    global $cw__langvars;

    $insert_str = array();
    
    foreach ($cw__langvars as $language_code=>$cached_vars) {
        foreach ($cached_vars as $lang_name=>$lang_value) {
            $is_exists = (empty($lang_value) ? 0 : 1);
            $insert_str[] = "('$lang_name', '$language_code', 1, '$is_exists')";
        }
    }
    
    if (!empty($insert_str)) {
        db_query("
            INSERT DELAYED INTO $tables[langvars_statistics] (name, code, counter, is_exists)
            VALUES ".join(', ',$insert_str)."
            ON DUPLICATE KEY UPDATE counter = counter + 1, is_exists=VALUES(is_exists)
        ");
    }

}

#
# This function updates/inserts the language variable into 'languages_alt'
#
function cw_languages_alt_insert($name, $value, $code="") {
	global $tables, $all_languages;

	if (!is_array($all_languages))
		return false;

	if (empty($code)) {
		#
		# For empty code update/insert variables for all languages
		#
		foreach($all_languages as $k=>$v) {
			db_query("REPLACE INTO $tables[languages_alt] (code, name, value) VALUES ('$v[code]', '$name', '$value')");
		}
	}
	else {
		#
		# For not empty $code...
		#
		$result = false;

		#
		# Check if $code is valid
		#
		foreach($all_languages as $k=>$v) {
			if ($code == $k) {
				$result = true;
				break;
			}
		}

		if (!$result)
			return false;
		#
		# Update/insert variable for $code
		#
		db_query("REPLACE INTO $tables[languages_alt] (code, name, value) VALUES ('$code', '$name', '$value')");
	}

	return true;
}



#
# Display time period
#
function cw_display_time_period($t) {
	if (empty($t))
		return "0:0:0";

	$ms = $t - floor($t);
	$ms = $ms > 0 ? round($ms*1000, 0) : 0;

	$t = floor($t);
	$s = $t % 60;

	$t = floor($t / 60);
	$m = $t > 0 ? $t % 60 : 0;

	if ($t > 0)
		$t = floor($t / 60);

	$h = $t > 0 ? $t % 24 : 0;

	return $h.":".$m.":".$s;

}

#
# Detect max data size for inserting to DB
#
function cw_get_max_upload_size() {

    $sql_max_allowed_packet = cw_db_get_max_allowed_packet();

	$upload_max_filesize = trim(ini_get("upload_max_filesize"));
	if (preg_match("/^\d+(G|M|K)$/", $upload_max_filesize, $match)) {
		$upload_max_filesize = doubleval(substr($upload_max_filesize, 0, -1));
		switch ($match[1]) {
			case "G":
				$upload_max_filesize = $upload_max_filesize*1024;
			case "M":
				$upload_max_filesize = $upload_max_filesize*1024;
			case "K":
				$upload_max_filesize = $upload_max_filesize*1024;
		}

	} else {
		$upload_max_filesize = intval($upload_max_filesize);
	}

	if ($sql_max_allowed_packet && $sql_max_allowed_packet < $upload_max_filesize)
		$upload_max_filesize = $sql_max_allowed_packet-1024;

	if ($upload_max_filesize > 1073741824)
		$upload_max_filesize = round($upload_max_filesize/1073741824, 1)."G";
	elseif ($upload_max_filesize > 1048576)
		$upload_max_filesize = round($upload_max_filesize/1048576, 1)."M";
	elseif ($upload_max_filesize > 1024)
		$upload_max_filesize = round($upload_max_filesize/1024, 1)."K";

	return $upload_max_filesize;
}

function cw_add_top_message($msg, $type='I') {
	global $top_message;

	static $priority = array('I'=>0,'W'=>1,'E'=>2);
	
	if (!in_array($type,array('I','W','E'))) $type = 'I';
	
	if (is_array($msg)) $msg = implode("\n",$msg);
	
	if (!empty($top_message['content'])) $top_message['content'] .= "\n";
    $top_message['content'] .= $msg;
	if ($priority[$top_message['type']]<$priority[$type]) $top_message['type'] = $type;
	
	if (defined('IS_AJAX') and constant('IS_AJAX')) {
		cw_load('ajax');
		cw_ajax_add_block(array(
			'id' => 'top_message_content',
			'content' => nl2br($top_message['content'])
		), 'top_message');
        cw_ajax_add_block(array(
            'id' => 'top_message_type',
            'action' => 'ignore',
            'content' => $top_message['type']
        ), 'top_message_type');
		cw_ajax_add_block(array(
			'id' => 'script',
			'content' => 'show_top_message("'.$top_message['type'].'")'
		), 'top_message_script');
	}
	
	return $top_message;
}


if (!function_exists('mime_content_type')) {

    function mime_content_type($filename) {

        $mime_types = array(
            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',

            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',

            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',

            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',

            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',

            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',

            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        );

        $ext = strtolower(array_pop(explode('.',$filename)));
        if (array_key_exists($ext, $mime_types)) {
            return $mime_types[$ext];
        }
        elseif (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME);
            $mimetype = finfo_file($finfo, $filename);
            finfo_close($finfo);
            return $mimetype;
        }
        else {
            return 'application/octet-stream';
        }
    }
}


function cw_bench_open_tag($name, $type, $note='') {
	global $__bench, $__bench_counter,  $__bench_depth, $__bench_max_memory;
	
	if (!constant('BENCH')) return null;
	if (constant('BENCH_GET_PARAM') && !isset($_GET[constant('BENCH_GET_PARAM')])) return null;
	
	$id = intval($__bench_counter++);
	
	$__bench[$id] = array(
		'id' => $id,
		'name' => $name,
		'type' => $type,
		'depth' => $__bench_depth++,
		'note' => $note,
		
		'start_time' => cw_core_microtime(),
		'start_mem' => memory_get_usage(true),
		);

	return $id;
}

function cw_bench_close_tag($id) {
		global $__bench,$__bench_depth,$__bench_max_memory;
		
		if (!constant('BENCH') || !isset($__bench[$id])) return false;
	
		$__bench[$id]['end_time'] = cw_core_microtime();
		$__bench[$id]['end_mem'] = memory_get_usage(true);
		
		$__bench_depth--;
}

function cw_allow_no_products_found_redirect() {
    return true;
}

/**
 * Common calback for uasort function. Supposed that sorted array has one of the following fields for ordering
 * - orderby
 * - order_by
 * - pos
 * 
 * @param array $a, array $b - elements (also arrays) of sorted array
 * @return bool as supposed by uasort() callback
 * 
 * @use uasort($array, 'cw_uasort_by_order')
 */
function cw_uasort_by_order($a,$b) {
    static $sort_by = array('orderby','order_by','pos');
    
    foreach ($sort_by as $v) {
        if (isset($a[$v])) {
            if ($a[$v] == $b[$v]) return 0;
            return ($a[$v] < $b[$v])?-1:1;
        }
    }
    return 0;
}

function cw_admin_forms_display_get($target, $mode) {
    global $tables, $customer_id;

    $hidden_elements = cw_query_column("select element_name from $tables[admin_forms_hidedisplay] where customer_id='$customer_id' and target='$target' and mode='$mode'");

    return $hidden_elements;
}

/**
 * convert string into identifier
 */
function cw_core_identifier($value) {
    $value = str_replace(' ','_',$value);
    return strtolower(preg_replace('/[^\w\d_]/', '', $value));
}

/**
* Tunnel called function which turns on/off breadcrumbs template in customer area
*
* @param $main - templates variable that specifies current page
* @return bool - flag that enables location.tpl in customer/index.tpl
*/
function cw_check_if_breadcrumbs_enabled($main) {

    $result = true;
    if ($main == 'welcome' || $main=='index') {
        $result = false;
    }
    return $result;
}

/* Register function call together with params after output.
 * 
 * Registered functions are callled in the same run and context, but postponed after main output and connection close (for fastCGI)
 * @use same as cw_call()
 */ 
function cw_call_delayed($func,$params = array()) {
    global $cw__call_delayed;
    
    $cw__call_delayed[] = array(
        'func'=>$func,
        'params'=>$params
    );
}

/**
 * Shutdown function to call all delayed functions which were registered via cw_call_delayed()
 */
function cw_exec_delayed() {
    global $cw__call_delayed;
    
    if (!empty($cw__call_delayed))
    foreach ($cw__call_delayed as $v) {
        $result = cw_call($v['func'],$v['params']);
        if (is_error($result)) {
            trigger_error("Delayed call of {$v['func']}() returned error: ".$result->getMessage(), E_USER_ERROR);
        }
    }
}
