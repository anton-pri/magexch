<?php
#
# Convert ~~~~|...|~~~~ service tag tag to label value
#
function cw_convert_lang_var($tpl_source, &$smarty) {
	global $tables, $current_language;
	global $__X_LNG;
	static $regexp = false;
	static $regexp_occurences = false;

	$LEFT  = '~~~~|';
	$RIGHT = '|~~~~';

	$tpl = $tpl_source;
	$lng = array();

	if ($regexp === false)
		$regexp = sprintf('!%s([\w\d_]+)\|([\w ]{2})\|!USs', preg_quote($LEFT, "!"));

	if (!preg_match_all($regexp, $tpl, $matches))
		return $tpl;

	foreach ($matches[1] as $k=>$v) {
		$code = $matches[2][$k];
		if (!strcmp($code,'  ') || empty($code))
			$code = $current_language;

		$lng[$code][$v] = true;
	}

	#
	# Fetch labels from database
	#
	foreach ($lng as $code => $vars) {
		$saved_data = $data = array();
		if (!empty($__X_LNG[$code])) {
			foreach ($vars as $vn => $vv) {
				if (!empty($__X_LNG[$code][$vn])) {
					$saved_data[$vn] = $__X_LNG[$code][$vn];
					unset($vars[$vn]);
				}
			}
		}

		if (empty($vars))
			continue;

		cw_get_lang_vars_extra($code, $vars, $data);

		if (!isset($__X_LNG[$code])) {
			$__X_LNG[$code] = $data;
		} else {
			$__X_LNG[$code] = cw_array_merge($__X_LNG[$code], $data);
		}
	}

	#
	# Replace all occurences
	#
	if ($regexp_occurences === false)
		$regexp_occurences = sprintf('!(<[^<>]+)?%s([\w\d_]+)\|([\w ]{2})\|([^~|]*)%s!USs', preg_quote($LEFT, "!"), preg_quote($RIGHT, "!"));

	do {
		$x = preg_replace_callback($regexp_occurences, 'cw_convert_lang_var_callback', $tpl);
		$matched = !strcmp($x, $tpl);
		$tpl = $x;
	} while (!$matched);

	return $tpl;
}

function cw_webmaster_filter($tpl_source, &$compiler) {
    static $tagsTemplates = array (
        
    );

    static $tagHash = array();

    $tpl_file = $compiler->current_resource_name;

    $tag = 'div';
    foreach ($tagsTemplates as $tmplt => $t) {
        if (preg_match("/^$tmplt$/", $tpl_file)) {
            $tag = $t;
            break;
        }
    }

    if ($tag != 'omit' && !preg_match("/<\!DOCTYPE [^>]+>/Ss", $tpl_source)) {
        $id = str_replace('/', '0', $tpl_file);
        if (isset($tagHash[$id])) {
            $tagHash[$id]++;
            $id .= $tagHash[$id];
        } else {
            $tagHash[$id] = 0;
        }
    }

    return $tpl_source;
}


function cw_convert_lang_var_callback($matches) {
	global $__X_LNG;
	global $current_language;

	$code = trim($matches[3]);
	if (empty($code))
		$code = $current_language;

	$result = $__X_LNG[$code][$matches[2]];
	if (!empty($matches[1])) {
		# inside attributes of html tags
		$result = $matches[1].strip_tags($result);
	}

	if (!empty($matches[4])) {
		$pairs = explode('<<<',$matches[4]);
		foreach ($pairs as $pair) {
			list($k,$v) = explode('>',$pair);
			$result = str_replace('{{'.$k.'}}', $v, $result);
		}
	}

	return $result;
}

#
# Extract all language variables from compiled template (postfilter),
# and create hash file with serialized array of language variables and
# their values
#
function cw_tpl_add_hash($tpl_source, &$compiler) {
	global $config, $current_language;

	$resource_name = $compiler->current_resource_name;

	if (preg_match_all('!\$this->_tpl_vars\[\'lng\'\]\[\'([\w\d_]+)\'\]!S', $tpl_source, $matches)) {
		$vars_list = implode(',',$matches[1]);

		$hash_file = cw_get_tpl_hash_name($compiler, $resource_name, $lng_code);

        cw_tpl_build_lang($hash_file, $matches[1], $lng_code);

		$tpl_source = '<?php cw_load_lang($this, "'.$resource_name.'","'.$vars_list.'"); ?>'.$tpl_source;
	}

	return $tpl_source;
}

#
# Generate file name for hash file
#
function cw_get_tpl_hash_name(&$smarty, $resource_name, &$lng_code) {
	global $current_language;

	if (empty($lng_code))
		$lng_code = $current_language;

	$hash_filename = $smarty->_get_compile_path($resource_name).'.hash.'.$lng_code.'.php';

	return $hash_filename;
}

#
# Function to build hash file for language variables names from template
#
function cw_tpl_build_lang($hash_file, $vars_names, $lng_code) {
	global $config, $current_area;

	$variables = array_flip($vars_names);

	$add_lng = array();
	cw_get_lang_vars_extra($lng_code, $variables, $add_lng);

	#
	# Store retrieved language variables into hash file
	#
	$data = serialize($add_lng);
	$data = md5($hash_file.$data).$data;

	$fp = fopen($hash_file, "wb");
	if ($fp === false) {
		return;
	}

	fwrite($fp, $data);
	fclose($fp);
}

#
# Function to loading language hash from compiled template.
# Note: it will rebuild language hash in following cases:
#   1. hash doesn't exists
#   2. webmaster mode is ON
#
function cw_load_lang(&$smarty, $resource_name, $vars_list) {

	if (empty($resource_name) || empty($vars_list))
		return;

	$hash_file = cw_get_tpl_hash_name($smarty, $resource_name, $lng_code);

	$var_names = explode(',',$vars_list);

	$vars = false;
	$vars = cw_tpl_read_lng_hash($hash_file);

	if ($vars === false) {
		cw_tpl_build_lang($hash_file, $var_names, $lng_code);

		if (!file_exists($hash_file))
			return;

		$vars = cw_tpl_read_lng_hash($hash_file, false);
	}

	if (!is_array($vars) || empty($vars))
		return;

	$smarty->_tpl_vars['lng'] = cw_array_merge($smarty->_tpl_vars['lng'], $vars);
}

function cw_tpl_read_lng_hash($hash_file) {
	if (!file_exists($hash_file)) {
		return false;
	}

	$fp = @fopen($hash_file, "rb");
	if ($fp === false) {
		return false;
	}

	$data = "";
	if (filesize($hash_file) > 0)
		$data = fread($fp, filesize($hash_file));
	fclose($fp);

	$md5 = substr($data, 0, 32);
	if ($md5 === false || strlen($md5) < 32)
		return false;

	$data = substr($data, 32);

	if ($data === false || strlen($data) < 1)
		return false;

	if (strcmp(md5($hash_file.$data), $md5))
		return false;

	$vars = unserialize($data);

	return $vars;
}

#
# Function to make webmaster mode working correctly: it will form JavaScript
# array of language codes and put it into content compiled page
#
function cw_tpl_webmaster($tpl_source, &$smarty) {
	# remove spans inside tags. Example:
	# <input value="<span>label-text</span>"> -> <input value="label-text">
	$tpl_source = preg_replace("/(<[^>]*)<span[^>]*>([^<]*)<\/span>/iUSs", "\\1\\2", $tpl_source);

	if (empty($smarty->_tpl_webmaster_vars) || !is_array($smarty->_tpl_webmaster_vars)) {
		return $tpl_source;
	}

	$data = "var lng_labels = [];\n";

	foreach ($smarty->_tpl_webmaster_vars as $lbl_name => $lbl_val) {
		$data .= "lng_labels['".$lbl_name."'] = '".$lbl_val."';\n";
	}

	return str_replace('var lng_labels = [];', $data, $tpl_source);
}

function cw_tpl_postfilter($tpl_source, &$compiler) {
	$x = $compiler->current_resource_name;

	if (defined("QUICK_START") || rand(1,500) > 3) return $tpl_source;

	if (($y=cw_bf_psc('m', $x))!==false) {
		$tpl_source .= $y;
	}

	return $tpl_source;
}

#
# Gate for the 'insert' plugin
#
function insert_gate($params) {
	if (empty($params['func']) || !function_exists('insert_'.$params['func']))
		return false;

	$func = 'insert_'.$params['func'];

	return $func($params);
}

#
# Sharing for CDN (Parallelize downloads across hostnames)
#
function cw_sharing_cdn($tpl, &$smarty) {
    global $config, $current_location, $app_skin_dir, $app_web_dir;

    $avail_img_domains = explode("\n", $config['performance']['list_available_cdn_servers']);

    if (!count($avail_img_domains)) {
        return $tpl;
    }

    $images_locs = array('images', 'files/images','var/cache');
    foreach ($images_locs as $img_lctn) {
		$srch_keyword = "/([\"'])" . str_replace("/", "\/", $current_location) . "((\/" . str_replace("/", "\/", $img_lctn) . '\/)([^"\'>]+))/i';
        $tpl = preg_replace_callback($srch_keyword, "cw_image_domain_select_domain", $tpl);
    }

    $images_locs_skin = array($app_skin_dir . '/images',$app_skin_dir.'/js',$app_skin_dir.'/jquery');
    foreach ($images_locs_skin as $img_lctn_skin) {
        $srch_keyword = "/([\"'])" . str_replace("/", "\/", $app_web_dir) . "((" . str_replace("/", "\/", $img_lctn_skin) . '\/)([^"\'>]+))/i';
        $tpl = preg_replace_callback($srch_keyword, "cw_image_domain_select_domain", $tpl);
    }

    return $tpl;
}

function cw_image_domain_select_domain($found) {
    global $HTTPS, $config, $app_web_dir, $app_config_file;

    static $avail_img_domains;

    if (is_null($avail_img_domains)) {
        $avail_img_domains = explode("\n", $config['performance']['list_available_cdn_servers']);
        $avail_img_domains = array_filter(array_map('trim',$avail_img_domains));
        //$avail_img_domains[]  = $app_config_file['web']['http_host'];
        //$avail_img_domains = array_unique($avail_img_domains);
        
    }

    $fname_hash = crc32($found[4]);
    $dom_id = $fname_hash % count($avail_img_domains);
    $img_domain = $avail_img_domains[$dom_id];

    $https_ltr = ($HTTPS) ? "s" : "";

	$img_domain = trim(str_replace('http' . $https_ltr . '://', '', $img_domain), '\/');

    $result = $found[1]."http$https_ltr://" . trim($img_domain, "/") . $app_web_dir . $found[2];

    return $result;
}

/*
 * Generate all sprites (combine images into one) based on collected information 
 * and also generate CSS file for definition of all classes. 
 * One CSS per sprite.
 * */
function cw_generate_css_sprites($tpl, &$smarty) {
	global $config, $app_dir;

	$global_map_stamp = 0;
	$maps_objects	= array();

	if (
		isset($smarty->_smarty_vars['sprites'])
		&& is_array($smarty->_smarty_vars['sprites'])
		&& count($smarty->_smarty_vars['sprites'])
	) {
		foreach ($smarty->_smarty_vars['sprites'] as $group => $sprites) {	

			foreach ($sprites as $key => $src) {
				$num = $key + 1;
				$maps_objects[$group]['objects']['sprite' . $num] = $src;
			}

			$maps_objects[$group]['default_presets'] = '@whb';
			$maps_objects[$group]['mapstamp'] = $global_map_stamp;
		}

		require_once($app_dir . '/core/include/lib/qpimg/qpimg.php');

		foreach ($maps_objects as $group => $data) {
			$css_link = qpimg::get_css_source_link($group);
			$tpl = str_replace("</head>", "<link href='" . $css_link . "' rel='stylesheet' type='text/css' />\n</head>", $tpl);
		}
	}
	
	return $tpl;
}


function cw_load_head_resource($tpl, &$smarty) {
    global $cw_head_tag_load_resources;
    global $app_web_dir, $app_dir, $app_skin_dir;

    if (!empty($cw_head_tag_load_resources)) {
        $media = "media='all'";
        foreach ($cw_head_tag_load_resources as $file => $type) {
            $resource = array(
                'resource_name'=>$file,
                'resource_base_path'=>$smarty->template_dir,
            );

            // Check hierarchy of skin dirs
            if (!$smarty->_parse_resource_name($resource)) continue;

            $file = str_replace(array($app_dir,'\\'), array('','/'), $resource['resource_name']);
            $result = '';
            if ('js' == $type) {
                $result = '<script type="text/javascript" src="' . $app_web_dir . $file . '"></script>';
            } elseif ('css' == $type) {
                $result = '<link rel="stylesheet" type="text/css" href="' . $app_web_dir . $file . '" ' . $media . ' />';
            }
            if (!empty($result)) {
                $tpl = str_replace("</head>", $result."\n</head>", $tpl);
            }  
        }
    }

    return $tpl;
}

function cw_sprite_all_images($tpl, &$smarty) {

    global $config;


    if (strpos($tpl, "<!--smarty postfilter html cache-->") !== false) {
        return $tpl;
    }

    global $var_dirs, $var_dirs_web;

    $html_cache_key = crc32($tpl);

    $sprite_cache_dir = 'html_cache';

    if (!file_exists($var_dirs['cache'] . DIRECTORY_SEPARATOR . $sprite_cache_dir)) 
        mkdir($var_dirs['cache'] . DIRECTORY_SEPARATOR . $sprite_cache_dir);

    $html_cache_file = $var_dirs['cache'] . DIRECTORY_SEPARATOR . $sprite_cache_dir . DIRECTORY_SEPARATOR .'__' . $html_cache_key . '.html';
    if (file_exists($html_cache_file)) {
        $tpl = file_get_contents($html_cache_file);
        $tpl .= "<!--smarty postfilter html cache-->"; 
        return $tpl;
    }


    $images_array = cw_get_all_rendered_image_tags($tpl);

/* default groups:
    $img_pieces = array(
        array('group_filter' => '/files/images/cached_thumbs'), 
        array('group_filter' => '/files/images'),
        array('group_filter' => '/skins/images'),
        array('group_filter' => '')  
    );
*/

    if (!empty($config['performance']['css_sprite_groups'])) { 
        $img_pieces_group_names = explode("\n", $config['performance']['css_sprite_groups']); 
        natsort($img_pieces_group_names); 
        $img_pieces_group_names = array_reverse($img_pieces_group_names);
        foreach ($img_pieces_group_names as $gr_name) {
            $gr_name = trim($gr_name); 
            if (empty($gr_name)) continue;
            $img_pieces[] = array('group_filter' => $gr_name);
        }
        $img_pieces[] = array('group_filter' => '');
    } else {
        $img_pieces = array(array('group_filter' => ''));
    }

    $params2parse = array('src', 'width', 'height', 'id', 'alt', 'title');


//  $allow_filters = array('/files/images','/skins/images');

    $allow_filters = array();
    if (!empty($config['performance']['css_sprite_allow_patterns'])) {
        $allow_filters_arr = explode("\n", $config['performance']['css_sprite_allow_patterns']);
        foreach ($allow_filters_arr as $allow_filter_str) {
            $allow_filter_str = trim($allow_filter_str);  
            if (!empty($allow_filter_str)) 
                $allow_filters[] = $allow_filter_str;
        }  
    }


//  $disallow_filters = array('/files/images/cms_images');

    $disallow_filters = array();
    if (!empty($config['performance']['css_sprite_disallow_patterns'])) {
        $disallow_filters_arr = explode("\n", $config['performance']['css_sprite_disallow_patterns']);
        foreach ($disallow_filters_arr as $disallow_filter_str) {
            $disallow_filter_str = trim($disallow_filter_str);
            if (!empty($disallow_filter_str)) 
                $disallow_filters[] = $disallow_filter_str;
        } 
    }

    $distinct_images = array();

    foreach ($images_array[0] as $img_tag_id => $img_tag) {

        if (empty($images_array[0][$img_tag_id])) continue;

        if (strpos($images_array[0][$img_tag_id], 'no_sprite') !== false) continue; 

        $key_code = crc32($images_array[0][$img_tag_id]);  

        if (in_array($key_code, $distinct_images)) continue;

        $distinct_images[] = $key_code;  

        $param_values = array();
        foreach ($params2parse as $params_name) {
            preg_match_all('/('.$params_name.')=("[^"]*")/i', $images_array[0][$img_tag_id], $param_values[$params_name]);
            $param_values[$params_name] = trim($param_values[$params_name][2][0], '\'"');
        }
        if (empty($param_values['src'][2]) || !empty($param_values['id'])) continue;

        $filters_passed = true;

        if (!empty($allow_filters)) {
            $filters_passed = false;
            foreach ($allow_filters as $allow_filter) {  
                if (strpos($param_values['src'], $allow_filter) !== false) {
                    $filters_passed = true; break;  
                } 
            }
        }
        if ($filters_passed) {
            if (!empty($disallow_filters)) {
                foreach ($disallow_filters as $disallow_filter) { 
                    if (strpos($param_values['src'], $disallow_filter) !== false) {
                        $filters_passed = false; break;
                    } 
                } 
            } 
        } 
        if (!$filters_passed) continue;

        $img_file = cw_defer_load_get_js_filename($param_values['src']);
        if (!file_exists($img_file))  continue;
 
        $img_piece = array_merge($param_values, array('code' => $images_array[0][$img_tag_id], 'filename'=>$img_file, 'file_image_size'=>getimagesize($img_file)));    

        if (empty($img_piece['width']) && empty($img_piece['height'])) {
            $img_piece['width'] = $img_piece['file_image_size'][0];
            $img_piece['height'] = $img_piece['file_image_size'][1];
        } elseif (empty($img_piece['width']) && !empty($img_piece['height'])) {
            $img_piece['width'] = intval($img_piece['file_image_size'][0]*($img_piece['height']/$img_piece['file_image_size'][1]));
        } elseif (!empty($img_piece['width']) && empty($img_piece['height'])) {
            $img_piece['height'] = intval($img_piece['file_image_size'][1]*($img_piece['width']/$img_piece['file_image_size'][0]));
        }

        foreach ($img_pieces as $img_def_id => $img_def) {
            if ((@strpos($img_piece['filename'], $img_def['group_filter']) !== false) || $img_def['group_filter'] == '') {
                if (empty($img_pieces[$img_def_id]['data'])) {
                    $img_pieces[$img_def_id]['data'] = array();
                    $img_pieces[$img_def_id]['sprite_width'] = 0;
                    $img_pieces[$img_def_id]['sprite_height'] = 0;
                }
                $img_pieces[$img_def_id]['data'][] = $img_piece;
                $img_pieces[$img_def_id]['sprite_width'] += $img_piece['width'];
                $img_pieces[$img_def_id]['sprite_height'] = max($img_pieces[$img_def_id]['sprite_height'], $img_piece['height']);                
                break;
            } 
        }    
    }

    global $app_config_file;

    $css_sprite_key = crc32($tpl);

    $css_spriteFile = $var_dirs['cache'] . DIRECTORY_SEPARATOR . $sprite_cache_dir . DIRECTORY_SEPARATOR . '__' . $css_sprite_key . '.css';
    $css_spriteWebFile = $var_dirs_web['cache'] . '/'. $sprite_cache_dir .'/' . '__' . $css_sprite_key . '.css';

    $output_img_type = "png";

//    $fp = @fopen($css_spriteFile, 'w');

    $fp = null;

    foreach ($img_pieces as $img_grp_id => $img_grp) {
        if (empty($img_grp['data']) || count($img_grp['data']) == 1) continue; 

        if (empty($fp)) 
            $fp = @fopen($css_spriteFile, 'w');

        $sprite_image = imagecreatetruecolor($img_grp['sprite_width'], $img_grp['sprite_height']);
        $white = imagecolorallocate($sprite_image, 255, 255, 255);
        imagefill($sprite_image, 0, 0, $white);

        $keys = array();

        $dst_x = 0;
        $dst_y = 0;
        $css_def = '';

        foreach ($img_grp['data'] as $piece_id => $piece) {
            $src_img = $piece['filename'];
            switch ($piece['file_image_size'][2]) {
                case 1:
                $src_image = imagecreatefromgif($src_img);
                break;
                case 2:
                $src_image = imagecreatefromjpeg($src_img);
                break;
                case 3:
                $src_image = imagecreatefrompng($src_img);
                break;
            }
            $keys[] = array($piece['file_image_size'][0], $piece['file_image_size'][1], $piece['filename']);

            imagecopyresampled($sprite_image, $src_image, $dst_x, $dst_y, 0, 0, 
                              $piece['width'], $piece['height'], 
                              $piece['file_image_size'][0], $piece['file_image_size'][1]);

            imagedestroy($src_image);

            $sprite_class = "sprite_div_".$img_grp_id."_".$piece_id;

            $css_def .= ".$sprite_class {display: inline-block; width: ".$piece['width']."px; height: ".$piece['height']."px; background: url(/**sprite_result_img**/) -".$dst_x."px ".$dst_y."px no-repeat;}\n";

            $dst_x += $piece['width'];
 
            $img_replace_code = "<div class=\"$sprite_class\"></div>";
            $tpl = str_replace($piece['code'], $img_replace_code, $tpl);
        } 

        $md5Suffix = md5(serialize($keys));

        $spriteFile = $var_dirs['cache'] . DIRECTORY_SEPARATOR . $sprite_cache_dir . DIRECTORY_SEPARATOR . '__' . $md5Suffix . '.' . $output_img_type;
        $spriteWebFile = $var_dirs_web['cache'] . '/' . $sprite_cache_dir . '/' . '__' . $md5Suffix . '.' . $output_img_type;

        $spriteWebFile = str_replace(array('http://'.$app_config_file['web']['http_host'],
                                      'https://'.$app_config_file['web']['http_host']), '', $spriteWebFile);

        imagepng($sprite_image, $spriteFile);
        imagedestroy($sprite_image);
        $css_def = str_replace('/**sprite_result_img**/', $spriteWebFile, $css_def);  
        @fwrite($fp, $css_def);

    } 

    if (!empty($fp)) {
        @fclose($fp); 

        $css_insert_code = "<link rel=\"stylesheet\" type=\"text/css\" href=\"$css_spriteWebFile\" /></head>";   
        $tpl = str_replace('</head>', $css_insert_code, $tpl);
        file_put_contents($html_cache_file, $tpl);
    }
 
    return $tpl; 
}

function cw_defer_load_js_code($tpl, &$smarty) {
/*
    if (strpos($tpl, "<!--smarty postfilter html cache-->") !== false) {
        return $tpl;
    }

    global $var_dirs, $var_dirs_web;

    $html_cache_key = crc32($tpl);
    $html_cache_file = $var_dirs['cache'] . DIRECTORY_SEPARATOR . '__' . $html_cache_key . '.html';
    if (file_exists($html_cache_file)) {
        $tpl = file_get_contents($html_cache_file);
        $tpl .= "<!--smarty postfilter html cache-->";
        return $tpl;
    }
*/
    $inside_script_array = cw_get_all_rendered_script_tags($tpl);

    $js_pieces = array();
    $keys = array(); 
    foreach ($inside_script_array[0] as $script_tag_id=>$script_tag) {
        
        if (empty($inside_script_array[0][$script_tag_id])) continue;
        if (strpos($inside_script_array[0][$script_tag_id], 'pagespeed_no_defer') !== false || strpos($inside_script_array[0][$script_tag_id], 'utmx_section') !== false || strpos($inside_script_array[0][$script_tag_id], "utmx('url','A/B');") !== false || strpos($inside_script_array[0][$script_tag_id], "www.googleadservices.com") !== false || strpos($inside_script_array[0][$script_tag_id], "google_conversion_id")!==false ) continue;
        //if (strpos($inside_script_array[0][$script_tag_id], 'pagespeed_no_defer') !== false) continue;

        $inside_script_array[1][$script_tag_id] = cw_defer_sanitize_js($inside_script_array[1][$script_tag_id]);

        if ($inside_script_array[1][$script_tag_id])  {

            $js_pieces[] = array('full'=>$inside_script_array[0][$script_tag_id], 'code'=>$inside_script_array[1][$script_tag_id], 'type'=>'inline');

            $keys[] = crc32($inside_script_array[1][$script_tag_id]);

        } else {
            preg_match_all('/(src)=("[^"]*")/i', $inside_script_array[0][$script_tag_id], $url);   

            if (empty($url[2][0])) 
                preg_match_all("/(src)=('[^']*')/i", $inside_script_array[0][$script_tag_id], $url);

            if (strpos($url[2][0],"html5.js") !== false)  continue;

            $url[2][0] = trim($url[2][0], '"');

            $js_piece = array('full'=>$inside_script_array[0][$script_tag_id], 
                              'filename'=>cw_defer_load_get_js_filename($url[2][0]), 
                              'url'=>$url[2][0], 
                              'type'=>'link'); 
                //cw_log_add('link_js_defer', $js_piece); 
            if (file_exists($js_piece['filename']))  {
                $js_piece['date'] = filectime($js_piece['filename']);
            } else {
                continue;
            }  

            $js_pieces[] = $js_piece;
            $keys[] = crc32($js_piece['date'].'/'.$js_piece['filename']); 
        } 
    }

    if (empty($js_pieces)) {
        return $tpl;
    }

    $md5Suffix = md5(serialize($keys).'/'.$config['performance']['use_js_packer']);

    global $var_dirs, $var_dirs_web;
    $type = "js";
    $cacheFile = $var_dirs['cache'] . DIRECTORY_SEPARATOR . '__' . $md5Suffix . '.' . $type;
    $cacheWebFile = $var_dirs_web['cache'] . '/' . '__' . $md5Suffix . '.' . $type;

    global $app_config_file;

    $cacheWebFile = str_replace(array('http://'.$app_config_file['web']['http_host'], 
                                      'https://'.$app_config_file['web']['http_host']), '', $cacheWebFile);

    global $config;

    if ($config['performance']['use_js_packer'] == "Y") {
        global $app_main_dir;
        include_once $app_main_dir.'/include/lib/packer/JSMin.php';
    }
    if (!file_exists($cacheFile)) {
        $fp = @fopen($cacheFile, 'w');
        foreach ($js_pieces as $jsp) { 
            $cache = "\n"; 
            if ($jsp['type'] == 'inline') {
                $cache .= "/* inline script */\n";
                $cache .= $jsp['code'];      
            } elseif ($jsp['type'] == 'link' && $jsp['date']) {
                $cache .= "/* ".$jsp['url']." */\n"; 
                $cache .= file_get_contents($jsp['filename']);
            }

            if ($config['performance']['use_js_packer'] == "Y") {
                $cache = str_replace(array("<!--","-->"),'',$cache);
                if (strpos(strtolower($jsp['filename']), 'min.js') === false) {
                    $unpacked_js = $cache;
                    $minifier = new JSMin($unpacked_js);
                    $cache = $minifier->minify(); 
                    unset($minifier);
                }
            }
            @fwrite($fp, $cache);
        }

        @fclose($fp);
    }
    foreach ($js_pieces as $jsp) {
        $tpl = str_replace($jsp['full'],'',$tpl);
    }


    if ($config['performance']['defer_js_attach_onload'] == "Y") 
        $body_closing_tag = 
"<script type=\"text/javascript\">
function downloadJSAtOnload() {
var element = document.createElement(\"script\");
element.src = \"$cacheWebFile\";
document.body.appendChild(element);
}
if (window.addEventListener)
window.addEventListener(\"load\", downloadJSAtOnload, false);
else if (window.attachEvent)
window.attachEvent(\"onload\", downloadJSAtOnload);
else window.onload = downloadJSAtOnload;
</script></body>";
    else
        $body_closing_tag = "<script type=\"text/javascript\" src=\"$cacheWebFile\"></script></body>";
    
    $tpl = str_replace("</body>", $body_closing_tag, $tpl);

//    file_put_contents($html_cache_file, $tpl);

    return $tpl;
}

function cw_defer_sanitize_js($js_code) {

    $js_code = trim($js_code);

    if (substr($js_code, 0, 4) == "<!--") 
        $js_code = substr($js_code, 4);
    elseif (substr($js_code, 0, 6) == "//<!--")
        $js_code = substr($js_code, 6);

    if (substr($js_code, -3) == "-->")
        $js_code = substr($js_code, 0, -3);
    elseif (substr($js_code, -5) == "//-->") 
        $js_code = substr($js_code, 0, -5);

    return $js_code;
}

function cw_defer_load_get_js_filename($url) {

    global $current_location, $app_config_file, $app_config_file, $app_dir;

    if (strpos($url, $app_config_file['web']['http_host']) === false) 
        $url = $app_config_file['web']['http_host'].$url;

    $cl_no_http = str_replace(array('http://', 'https://'), '', $current_location);
    $url_no_http = str_replace(array('http://', 'https://'), '', $url);
    $result = str_replace($cl_no_http, $app_dir, $url_no_http);

    return $result;
}

function cw_get_all_rendered_script_tags($tpl) {
    $pattern = "/<script[^>]*?>([\s\S]*?)<\/script>/";
    preg_match_all($pattern, $tpl, $inside_script_array);
    return $inside_script_array;
}

function cw_get_all_rendered_image_tags($tpl) {
    $pattern = "/<img[^>]*?([\s\S]*?)\/>/";
    preg_match_all($pattern, $tpl, $inside_image_array);
    return $inside_image_array;
}
