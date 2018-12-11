<?php
function cw_xml_parse($data, &$error, $options = array(), $is_simple = 0) {
	static $default_options = array (
		'XML_OPTION_CASE_FOLDING' => 0,
		'XML_OPTION_SKIP_WHITE' => 1
	);

	$data = trim($data);
	$vals = $index = $array = array();
	$parser = xml_parser_create();
	$options = cw_array_merge($default_options, $options);

	foreach ($options as $opt=>$val) {
		if (!defined($opt)) continue;

		xml_parser_set_option($parser, constant($opt), $val);
	}

	if (!xml_parse_into_struct($parser, $data, $vals, $index)) {
        $error = array (
			'code' => xml_get_error_code($parser),
			'string' => xml_error_string(xml_get_error_code($parser)),
			'line' => xml_get_current_line_number($parser)
		);
		xml_parser_free($parser);
		return false;
	}

	xml_parser_free($parser);

	$i = 0; 

    if ($is_simple) {
        $tagname = $vals[$i]['tag'];
        $array[$tagname] = _func_xml_make_tree($vals, $i, $is_simple);
    }
    else {
    	$tagname = $vals[$i]['tag'];
	    if (isset($vals[$i]['attributes']))
		    $array[$tagname]['@'] = $vals[$i]['attributes'];
    	else
	    	$array[$tagname]['@'] = array();
	    $array[$tagname]['#'] = _func_xml_make_tree($vals, $i, $is_simple);
    }

	return $array;
}

function _func_xml_make_tree($vals, &$i, $is_simple) { 
	$children = array();

	if (isset($vals[$i]['value'])) {
		array_push($children, $vals[$i]['value']);
	}

	while (++$i < count($vals)) {
		switch ($vals[$i]['type']) {
		case 'open':
			if (isset($vals[$i]['tag'])) {
				$tagname = $vals[$i]['tag'];
			} else {
				$tagname = '';
			}

			if (isset($children[$tagname])) {
				$size = sizeof($children[$tagname]);
			} else {
				$size = 0;
			}

			if (isset($vals[$i]['attributes']) && !$is_simple) {
				$children[$tagname][$size]['@'] = $vals[$i]['attributes'];
			}

            if ($is_simple == 2)
                $children[$tagname] = _func_xml_make_tree($vals, $i, $is_simple);
            elseif($is_simple == 1)
                $children[$tagname][$size] = _func_xml_make_tree($vals, $i, $is_simple);
            else
    			$children[$tagname][$size]['#'] = _func_xml_make_tree($vals, $i, $is_simple);
			break;

		case 'cdata':
			array_push($children, $vals[$i]['value']);
			break;

		case 'complete':
			$tagname = $vals[$i]['tag'];

			if (isset($children[$tagname])) {
				$size = sizeof($children[$tagname]);
			} else {
				$size = 0;
			}

            if ($is_simple == 2)
                $set = &$children[$tagname];
            elseif($is_simple == 1)
                $set = &$children[$tagname][$size];
            else
                $set = &$children[$tagname][$size]['#'];
            $set = '';
            if (isset($vals[$i]['value'])) 
                $set = $vals[$i]['value'];

			if (isset($vals[$i]['attributes']) && !$is_simple) {
				$children[$tagname][$size]['@'] = $vals[$i]['attributes'];
			}

			break;

		case 'close':
			return $children;
			break;
		}
	}

	return $children;
}

#
# This function returns element of array by path to it
# Returns false when $tag_path cannot be resolved
#
function & cw_array_path(&$array, $tag_path, $strict=false) {
	if (!is_array($array) || empty($array)) return false;

	if (empty($tag_path)) return $array;
	
	$path = explode('/',$tag_path);

	$elem =& $array; 

	foreach ($path as $key) {
		if (isset($elem[$key])) {
			$tmp_elem =& $elem[$key];
		}
		else {
			if (!$strict && isset($elem['#'][$key])) {
				$tmp_elem =& $elem['#'][$key];
			}
			else if (!$strict && isset($elem[0]['#'][$key])) {
				$tmp_elem =& $elem[0]['#'][$key];
			}
			else {
				# path is not found
				return false;
			}
		}

		unset($elem);
		$elem = $tmp_elem;
		unset($tmp_elem);
	}

	return $elem;
}

#
# Covert XML string to hash array
#
function cw_xml2hash($str) {
	global $app_main_dir;

	$err = false;
	$options = array(
		'XML_OPTION_CASE_FOLDING' => 0,
		'XML_OPTION_TARGET_ENCODING' => 'ISO-8859-1'
	);

    $parsed = cw_xml_parse($str, $err, $options);

	if (!empty($parsed)) {
		foreach ($parsed as $k => $v) {
			if (is_array($v['#'])) {
				$is_str = $is_arr = 0;
				foreach ($v['#'] as $subv) {
					if (is_array($subv)) {
						$is_array++;
					} else {
						$is_str++;
					}
				}

				if ($is_array > 0 && $is_str > 0) {
					foreach ($v['#'] as $subk => $subv) {
						if (!is_array($subv))
							unset($v['#'][$subk]);
					}
				}

				if ($is_array > 0) {
					$parsed[$k] = cw_xml2hash_postprocess($v['#']);
				} else {
					$parsed[$k] = array_pop($v['#']);
				}

			} else  {
				$parsed[$k] = $v['#'];
			}
		}
	}
	else {
		return array();
	}

	return $parsed;
	
}

#
# Covert XML string to hash array: postprocessing subfunction
#
function cw_xml2hash_postprocess($arr) {
	foreach ($arr as $tname => $t) {
		$arr[$tname] = array_pop($t);
		$arr[$tname] = $arr[$tname]['#'];
		if (is_array($arr[$tname]))
			$arr[$tname] = cw_xml2hash_postprocess($arr[$tname]);

	}

	return $arr;
}

#
# Convert hash array to XML string
#
function cw_hash2xml($hash, $level = 0) {
	if (!is_array($hash)) {
		return cw_xml_escape($hash);

	} elseif (empty($hash)) {
		return "";
	}

	$xmk = "";
	foreach($hash as $k => $v) {
		$xml .= str_repeat("\t", $level)."<$k>".cw_hash2xml($v, $level+1)."</$k>\n";
	}

	if ($level > 0) {
		$xml = "\n".$xml."\n".str_repeat("\t", $level);
	}

	return $xml;
}

#
# Format XML string
#
function cw_xml_format($xml) {
	$xml = preg_replace("/>[ \t\n\r]+</", "><", trim($xml));

	$level = -1;
	$i = 0;
	$prev = 0;
	$path = array();
	while(preg_match("/<([\w\d_\?]+)(?: [^>]+)?>/S", substr($xml, $i), $match)) {
		$tn = $match[1];
		$len = strlen($match[0]);
		$i = strpos($xml, $match[0], $i);
		$level++;

		# Detect close-tags
		if ($i - $prev > 0) {
			$ends = substr_count(substr($xml, $prev, $i-$prev), "</");
			if ($ends > 0)
				$level -= $ends;
		}

		# Add indents
		if ($level > 0) {
			$xml = substr($xml, 0, $i).str_repeat("\t", $level).substr($xml, $i);
			$i += $level;
		}

		# Add EOL symbol
		if (
			(
				($end = strpos(substr($xml, $i+$len), "</$tn>")) !== false &&
				preg_match("/<[\w\d_\?]+(?: [^>]+)?>/S", substr($xml, $i+$len, $end))
			) ||
			substr($tn, 0, 1) == '?'
		) {
			$xml = substr($xml, 0, $i+$len)."\n".substr($xml, $i+$len);
			$i++;

			# Add indent for close-tag
			if ($level > 0) {
				$end += $i+$len;
				$xml = substr($xml, 0, $end).str_repeat("\t", $level).substr($xml, $end);
			}
		}

		$i += $len;
		$prev = $i;
	}

	return preg_replace("/(<\/[\w\d_]+>)/", "\\1\n", $xml);
}

function cw_xml_escape($str) {
    return str_replace(
        array("&", "<", ">", '"', "'"),
        array("&#x26;", "&#x3c;", "&#x3e;", "&quot;", "&#39;"),
        $str
    );
}
