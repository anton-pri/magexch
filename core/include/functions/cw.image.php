<?php
#
# Construct path to directory of images of type $type
#
function cw_image_dir($type) {
	global $var_dirs;

	$dir = $var_dirs['images'].'/'.$type;

	if (!file_exists($dir))
		cw_mkdir($dir);

	return $dir;
}

#
# Get image file extension using mime type of image
#
function cw_get_image_ext($mime_type) {
	static $corrected = array (
		"application/x-shockwave-flash" => "swf"
	);

	if (!empty($corrected[$mime_type]))
		return $corrected[$mime_type];

	if (!zerolen($mime_type)) {
		list($type, $subtype) = explode('/', $mime_type, 2);
		if (!strcmp($type, "image") && !zerolen($subtype))
			return $subtype;
	}

	return "img"; # unknown generic file extension
}

#
# Check uniqueness of image filename
#
function cw_image_filename_is_unique($file, $type, $image_id=false) {
	global $available_images, $tables, $app_dir;

	if (empty($available_images[$type])) 
		return false;

	$_table = $tables[$type];
	$_where = "filename='".addslashes($file)."'";
	if (!empty($image_id))
		$_where .= " AND image_id<>'".addslashes($image_id)."'";

	if (cw_query_first_cell("select count(*) from ".$_table." where ".$_where) > 0)
		return false;

	return !@file_exists(cw_image_dir($type)."/".$file);
}

function cw_image_gen_unique_filename($file_name, $type, $mime_type="image/jpg", $id=false, $image_id=false) {
	static $max_added_idx = 99999;
	static $last_max_idx = array();

	if (zerolen($file_name)) {
		$file_name = strtolower($type);
		if (!zerolen((string)$id))
			$file_name .= "-".$id."-".$image_id;

		$file_ext = cw_get_image_ext($mime_type);
	} 
    elseif (preg_match("/^(.+)\.([^\.]+)$/S", $file_name, $match)) {
		$file_name = $match[1];
		$file_ext = $match[2];
	}

	$is_unique = cw_image_filename_is_unique($file_name.".".$file_ext, $type, $image_id);

	if ($is_unique)
		return $file_name.".".$file_ext;

	# Generate unique name
	$idx = isset($last_max_idx[$type][$file_name]) ? $last_max_idx[$type][$file_name] : cw_get_next_unique_id($file_name, $type);
	$name_tmp = $file_name;
	$dest_dir = cw_image_dir($type);
	do {
		$file_name = sprintf("%s-%02d", $name_tmp, $idx++);
		$is_unique = cw_image_filename_is_unique($file_name.'.'.$file_ext, $type, $image_id);
	} while (!$is_unique && $idx < $max_added_idx);

	if (!$is_unique)
		return false;

	if ($idx > 2) {
		if (!isset($last_max_idx[$type]))
			$last_max_idx[$type] = array();
		$last_max_idx[$type][$name_tmp] = $idx-1;
	}

	return $file_name.".".$file_ext;
}

#
# Get last unique id for image file name
#
function cw_get_next_unique_id($file, $type) {
	global $available_images, $tables, $app_dir;

	$max = 1;
	if (empty($available_images[$type]))
		return $max;

	$res = db_query("SELECT filename FROM ".$tables[$type]." WHERE SUBSTRING(filename, 1, ".(strlen($file)+1).") = '".addslashes($file)."-'");
	if ($res) {

		while ($f = db_fetch_array($res)) {
			$f = substr(array_pop($f), strlen($file)+1);
			if (preg_match("/^(\d+)/S", $f, $match) && $max < intval($match[1]))
				$max = intval($match[1]);

		}
		db_free_result($res);
		if ($max > 1)
			$max++;

		return $max;
	}

    return $max;
}

#
# Check image permissions
#
function cw_check_image_storage_perms($file_upload_data, $type = 'T', $get_message = true) {
	global $config, $app_dir;

	if (!cw_image_check_posted($file_upload_data[$type]))
		return true;

	return cw_check_image_perms($type, $get_message);
}

#
# Check image type permissions
#
function cw_check_image_perms($type, $get_message = true) {
	global $config, $app_dir, $available_images;

	if (!isset($available_images[$type]))
		return true;

	$path = cw_image_dir($type);
	$arr = explode("/", substr($path, strlen($app_dir)+1));
	$suffix = $app_dir;

	foreach ($arr as $p) {
		$suffix .= DIRECTORY_SEPARATOR.$p;

		$return = array();
		if (!is_writable($suffix))
			$return[] = 'w';

		if (!is_readable($suffix))
			$return[] = 'r';

		if (count($return) > 0) {
			$return['path'] = $suffix;
			if ($get_message) {
				if (in_array("r", $return) && in_array("w", $return)) {
					$return['label'] = "msg_err_image_cannot_saved_both_perms";

				} elseif (in_array("r", $return)) {
					$return['label'] = "msg_err_image_cannot_saved_read_perms";

				} else {
					$return['label'] = "msg_err_image_cannot_saved_write_perms";
				}
				$return['content'] = cw_get_langvar_by_name($return['label'], array("path" =>  $return['path']));
			}

			return $return;
		}
	}
	
	return true;
}

function cw_image_check_posted($image_posted) {
	$return = false;

	if (!cw_allow_file($image_posted['file_path'], true))
		return false;

	if ($image_posted['source'] == "U") {
		if ($fd = cw_fopen($image_posted['file_path'], "rb", true)) {
			fclose($fd);
			$return = true;
		}
	} 
    else 
		$return = file_exists($image_posted['file_path']);

	return $return;
}

#
# Prepare posted image for saving
#
function cw_image_prepare($image_posted) {
	global $app_dir, $tables;
	if (empty($image_posted['file_path']) || !in_array($image_posted['source'], array('U', 'S', 'L')))
		return false;

	$image_data = $image_posted;
    $image_data['filename'] = $image_data['filename']?$image_data['filename']:basename($image_data['file_path']);

    $type = $image_posted['type'];

	$file_path = $image_data['file_path'];
	if (!is_url($file_path))
		$file_path = cw_realpath($file_path);

    list(
        $image_data['file_size'],
        $image_data['image_x'],
        $image_data['image_y'],
        $image_data['image_type'],
        $image_data['md5']) = cw_get_image_size($file_path);

	$prepared = array(
        'id' => $image_posted['id'],
		'image_size' => $image_data['file_size'],
		'md5' => $image_data['md5'],
		'filename' => $image_data['filename'],
		'image_type' => $image_data['image_type'],
		'image_x' => $image_data['image_x'],
		'image_y' => $image_data['image_y'],
	);

    $prepared['image_path'] = '';

    $prepared['image_path'] = cw_image_store_fs($image_data, $type);

    if (zerolen($prepared['image_path']))
        return false;

    $prepared['filename'] = basename($prepared['image_path']);

    $path = cw_relative_path($prepared['image_path'], $app_dir);

	if ($path !== false)
	    $prepared['image_path'] = $path;

	return $prepared;
}

#
# Save uploaded/changed image
#
function cw_image_save(&$image_posted, $added_data = array(), $_image_id = NULL) {
	global $tables, $available_images, $skip_image, $config;

    $type = $image_posted['type'];
	$image_data = cw_image_prepare($image_posted);

    if ((
            $available_images[$type]['max_width']>0 && 
            $image_data['image_x']>$available_images[$type]['max_width']
        )
        ||
        (
            $available_images[$type]['min_width']>0 && 
                ($image_data['image_x']<$available_images[$type]['min_width'] 
                || $image_data['image_y']<$available_images[$type]['min_width'])
        )
       ) {
        cw_image_resize($image_data, $available_images[$type]['max_width'],$available_images[$type]['min_width']);
    }
    elseif ($config['Appearance']['size_user_avatar'] && $type == 'customers_images') {
        cw_image_resize($image_data, $config['Appearance']['size_user_avatar']);
    }

    if (!empty($added_data))
        $image_data = cw_array_merge($image_data, $added_data);

	if (!$image_data || !$image_data['id'])
		return false;

	if ($skip_image[$type] == 'Y') {
		if (!empty($image_posted['is_copied']))
			@unlink($image_posted['file_path']);
		unset($image_posted);
		return false;
	}

	$image_data['date'] = cw_core_get_time();

	$image_data = cw_addslashes($image_data);
	unset($image_posted);

	$_table = $tables[$type];

	if ($available_images[$type]['type'] == 'U')
        cw_image_delete($image_data['id'], $type);

	return cw_array2insert($type, $image_data);
}

function cw_image_store_fs($image_data, $type) {
	$dest_dir = cw_image_dir($type);

	if (isset($image_data['file_path'])) {
		$image_data['id'] = false;
		$image_data['image_id'] = false;
        $image_data['image'] = cw_file_get($image_data['file_path'], true);
	}

	$file_name = cw_image_gen_unique_filename($image_data['filename'], $type, $image_data['image_type'], $image_data['id'], $image_data['image_id']);

	if ($file_name === false)
		return false;

	$file = $dest_dir.'/'.$file_name;

	$fd = cw_fopen($file, "wb", true);
	if ($fd === false)
		return false;

    $image =
	fwrite($fd, $image_data['image']);
	fclose($fd);
	@chmod($file, 0666);

	if (!empty($image_data['is_copied']))
		unlink(cw_realpath($image_data['file_path']));

	return $file;
}

function cw_image_properties($type, $id) {
	global $available_images, $tables;

	if (empty($available_images[$type]))
		return false;

	return cw_query_first("SELECT image_x, image_y, image_type, image_size FROM ".$tables[$type]." WHERE id = '$id'");
}

function cw_get_default_image($type, $web_path = true) {
    global $available_images, $var_dirs_web, $var_dirs;
   
    if (!isset($available_images[$type]))
        return false;
   
    $default_image = $var_dirs['images'].'/'.$available_images[$type]['default_image'];

    if (is_file($default_image)) {
        if ($web_path)  
            $default_image = $var_dirs_web['images'].'/'.$available_images[$type]['default_image'];
        return $default_image;
    }

    return '';
}

function cw_image_get_url($id, $type, $image_path) {
    global $available_images, $app_dir, $var_dirs, $current_location, $config;

    if (is_url($image_path))
        return $image_path;

    if ($image_path == false) return cw_get_default_image($type);

    $image_path = cw_realpath($image_path);
    if (!strncmp($var_dirs['images'], $image_path, strlen($var_dirs['images'])) && @file_exists($image_path))
	//if (!strncmp(str_replace('/','\\',$var_dirs['images']), str_replace('/','\\',$image_path), strlen($var_dirs['images'])) && @file_exists($image_path))
        return $current_location.str_replace(DIRECTORY_SEPARATOR, "/", substr($image_path, strlen(preg_replace("/".preg_quote(DIRECTORY_SEPARATOR, "/")."$/S", "", $app_dir))));
    return cw_get_default_image($type);
}

function cw_image_info($type, $image) {
    global $available_images, $var_dirs_web;

    $image['in_type'] = $type;
    $image['type'] = cw_get_image_type($image['image_type']);
    if (!empty($image['image_path'])) {
        $id = $available_images[$type] == "U" ? "id" : "image_id" ;
        $image['tmbn_url'] = cw_image_get_url($image[$id], $type, $image['image_path']);
    }
    else {
        $image['tmbn_url'] = cw_get_default_image($type);
        $image['is_default'] = 1;
        if (!empty($image['tmbn_url'])) $image['image_path'] = $var_dirs_web['images'].'/'.$available_images[$type]['default_image'];
    }

   return $image;
}

function cw_image_get_list_count($type, $id, $avail = 0) {
    global $tables;

    return cw_query_first_cell("select count(*) from ".$tables[$type]." where id='$id'".($avail?" and avail=1":"")." order by orderby, image_id");
}

function cw_image_get_list($type, $id, $avail = 0) {
    global $tables;

    $images = cw_query("select * from ".$tables[$type]." where id='$id'".($avail?" and avail=1":"")." order by orderby, image_id");
    if ($images)
        foreach($images as $k=>$val)
            $images[$k] = cw_image_info($type, $val);

    return $images;
}

function cw_image_get($type, $image_id) {
    global $tables, $available_images;

    $where = ($available_images[$type]['type'] != 'U' ? 'image_id' : 'id');

    $image = cw_query_first($sql="select * from ".$tables[$type]." where $where='$image_id'");
    $image[$where] = $image_id;

    return cw_image_info($type, $image);
}

function cw_image_delete($id, $type = '') {
    global $available_images;

    $where = $available_images[$type]['type'] != 'U'? 'image_id' : 'id';
    if (is_array($id))
        $where .= " IN ('".implode("','", $id)."')";
    else
        $where .= " = '$id'";

    return cw_image_delete_all($type, $where);
}

function cw_image_delete_all($type = '', $where = '') {
    global $available_images, $tables, $app_dir;

    if (!isset($available_images[$type]))
        return false;

    if (!empty($where))
        $where = " where ".$where;

    $_table = $tables[$type];

    if (cw_query_first_cell("select count(*) from ".$_table.$where) == 0)
        return false;

    $res = db_query("SELECT image_id, image_path, filename FROM ".$_table.$where);

    if ($res) {
        cw_load('image');
        $img_dir = cw_image_dir($type)."/";
        while ($v = db_fetch_array($res)) {
            if (!zerolen($v['image_path']) && is_url($v['image_path']))
                continue;

            $image_path = $v['image_path'];
            if (zerolen($image_path))
                $image_path = cw_relative_path($img_dir.$v['filename']);
                
            $is_found = false;
            # check other types
            foreach ($available_images as $k => $i) {
                $is_found = cw_query_first_cell("select count(*) from ".$tables[$k]." where image_path='".addslashes($image_path)."'".($k == $type ? " AND image_id != '$v[image_id]'" : "")) > 0;
                if ($is_found) break;
            }

            if (!$is_found && file_exists($image_path)) {
                @unlink($image_path);
                if ($type == 'products_images_thumb')
                    cw_rm_dir($img_dir.'/'.$v['image_id']);
            }
        }

        db_free_result($res);
    }

    db_query("delete from ".$_table.$where);

    return true;
}

function cw_image_clear($types) {
    global $file_upload_data, $available_images;
    $file_upload_data = &cw_session_register('file_upload_data');

    if (!is_array($types)) $types = array($types);

    if (is_array($file_upload_data)) {
    foreach ($file_upload_data as $k => $v) {
        if (!in_array($k, $types)) continue;
        if (!isset($available_images[$k]))
            unset($file_upload_data[$k]);
        elseif (isset($v['file_path'])) {
            if ($v['is_redirect'])
                unset($file_upload_data[$k]);
            else
                $file_upload_data[$k]['is_redirect'] = true;
        } 
        elseif(!empty($v) && is_array($v)) {
            foreach ($v as $k2 => $v2) {
                if (!isset($v2['file_path']))
                        continue;
                if ($v2['is_redirect'])
                        unset($file_upload_data[$k][$k2]);
                else
                        $file_upload_data[$k][$k2]['is_redirect'] = true;
                }
            }
        }
    }
}

function cw_image_convert($from, $to) {
    $image = imagecreatefromstring(file_get_contents($from));
    image2wbmp($image, $to, 24);
}

/*
* resizes transparent pngs
*/
function cw_resizePng($im, $dst_width, $dst_height,$canvas_width=null, $canvas_height=null) {

    if (!$canvas_width) $canvas_width=$dst_width;
    if (!$canvas_height) $canvas_height=$dst_height;

    $canvas_x = ($canvas_width-$dst_width)/2;
    $canvas_y = ($canvas_height-$dst_height)/2;
        
    $width = imagesx($im);
    $height = imagesy($im);

    $newImg = imagecreatetruecolor($canvas_width, $canvas_height);

    imagealphablending($newImg, false);
    imagesavealpha($newImg, true);
    $transparent = imagecolorallocatealpha($newImg, 255, 255, 255, 127);
    imagefilledrectangle($newImg, 0, 0, $canvas_width, $canvas_height, $transparent);
    imagecopyresampled($newImg, $im, $canvas_x, $canvas_y, 0, 0, $dst_width, $dst_height, $width, $height);

    return $newImg;
}

function cw_image_resize(&$image_data, $new_width, $min_width=0) {
    list(
        $image_data['image_size'],
        $image_data['image_x'],
        $image_data['image_y'],
        $image_data['image_type'],
        $image_data['md5']) = cw_get_image_size($image_data['image_path']);

    if (!in_array($image_data['image_type'], array('image/jpeg', 'image/gif', 'image/png')))
        return false;

    $image = imagecreatefromstring(file_get_contents($image_data['image_path']));
   
    if ($new_width == 0 || $new_width>$image_data['image_x']) $new_width = $image_data['image_x'];

    $new_height = ($new_width/$image_data['image_x'])*$image_data['image_y'];

    $canvas_width = max($min_width,$new_width);
    $canvas_height = max($min_width,$new_height);
    
    if ($canvas_width == $image_data['image_x'] &&  $canvas_height == $image_data['image_y']) return true;
    
    $canvas_x = ($canvas_width-$new_width)/2;
    $canvas_y = ($canvas_height-$new_height)/2;
    if ($image_data['image_type'] == 'image/png') { 
        $ciH = cw_resizePng($image, $new_width, $new_height, $canvas_width, $canvas_height); 
    } else {
        $ciH = imagecreatetruecolor($canvas_width, $canvas_height);
        $transparent = imagecolorallocate($ciH, 255, 255, 255); // background color
        imagefilledrectangle($ciH, 0, 0, $canvas_width, $canvas_height, $transparent);        
        imagecopyresized($ciH, $image, $canvas_x, $canvas_y, 0, 0, $new_width, $new_height, $image_data['image_x'], $image_data['image_y']);
    }

    switch ($image_data['image_type']) {
        case 'image/jpeg':
            $result = imagejpeg($ciH, $image_data['image_path'], 100);
            break;
        case 'image/gif':
            $result = imagegif($ciH, $image_data['image_path'], 100);
            break;
        case 'image/png':
            $result = imagepng($ciH, $image_data['image_path']);
            break;
    }    

    list(
        $image_data['image_size'],
        $image_data['image_x'],
        $image_data['image_y'],
        $image_data['image_type'],
        $image_data['md5']) = cw_get_image_size($image_data['image_path']);

    return $result;
}



function cw_image_copy(&$data, $from_type, $to_type) {
    $data[$to_type] = $data[$from_type];
    $data[$to_type]['type'] = $to_type;
    if (!is_url($data[$to_type]['file_path'])) {
        $data[$to_type]['file_path'] .= '_'.$to_type;
        @copy($data[$from_type]['file_path'], $data[$to_type]['file_path']);
    }
}

function cw_process_image_save_tmp($type, $userfiles, $filenames, $fileurls) {
    global $available_images;

    cw_load( 'files', 'user');

    $data = array(); 

    $file_upload_data = array();

    if (is_array($userfiles))
        foreach($userfiles['tmp_name'] as $index=>$userfile) {
            if (zerolen($userfile)) break;
            if (cw_is_image_userfile($userfile, $userfiles['size'][$index], $userfiles['type'][$index])) {
                $tmp = array();
                $tmp['is_copied'] = true;
                $tmp['filename'] = strtolower($userfiles['name'][$index]);
                $tmp['file_path'] = cw_move_uploaded_file('userfiles', '', $index);
                $tmp['source'] = 'S';
                $data[] = $tmp;
            }
        }

    if (is_array($filenames))
        foreach($filenames as $ind=>$filename) {
            $filename = trim($filename);
            if (!zerolen($filename)) {
                $tmp = array();
                $tmp['file_path'] = $userfiles_dir.$file_paths[$ind];
                $tmp['is_copied'] = false;
                $tmp['source'] = 'L';
                $data[] = $tmp;
            }
        }

    if (is_array($fileurls))
        foreach($fileurls as $fileurl) {
            $tmp = array();
            $fileurl = trim($fileurl);
            if (!zerolen($fileurl)) {
                if (strpos($fileurl, "/") === 0)
                    $fileurl = $http_location.$fileurl;
                elseif (!is_url($fileurl))
                    $fileurl = "http://".$fileurl;
                $tmp['file_path'] = $fileurl;
                $tmp['is_copied'] = false;
                $tmp['source'] = 'U';
                $data[] = $tmp;
            }
        }

    if (is_array($data)) {
        foreach($data as $k=>$val) {

            if (isset($val['file_path']) && !cw_is_allowed_file($val['file_path']) || !isset($val['file_path']) || zerolen($val['file_path'])) {
                if ($val['is_copied']) @unlink($val['file_path']);
                unset($data[$k]);
                continue;
            }

            list(
                $val['file_size'],
                $val['image_x'],
                $val['image_y'],
                $val['image_type'],
                $val['md5']) = cw_get_image_size($val['file_path']);

            if ($val['file_size'] == 0) {
                if ($data['is_copied']) unlink($val['file_path']);
                unset($data[$k]);
                continue;
            }

            if (!isset($val['filename']))
                $val['filename'] = basename($val['file_path']);

            $val['id'] = $id;
            $val['type'] = $type;
            $val['date'] = cw_core_get_time();

            if ($available_images[$type]['multiple'] == 2)
                $file_upload_data[$type][] = $val;
            elseif($available_images[$type]['multiple'] == 1)
                $file_upload_data[$type][$id] = $val;
            else
                $file_upload_data[$type] = $val;
        }
    }

    return $file_upload_data;
}

