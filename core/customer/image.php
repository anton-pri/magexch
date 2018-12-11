<?php
cw_load('files');

$login_type = &cw_session_register("login_type");
$is_substitute = (($login_type == 'A' || $login_type == 'P') ? false : true);

if (empty($id)) $id = false;
if (empty($type)) $type = 'products_images_thumb';

$image_type = '';
$image_path = '';
$image_size = 0;

if (isset($_GET['tmp'])) {
    $file_upload_data = &cw_session_register('file_upload_data', array());

    if ($available_images[$type]['multiple'] && is_array($file_upload_data)) {

        if ($file_upload_data[$type][$imgid])
            $image_posted = $file_upload_data[$type][intval($imgid)];

    } else {
    	$image_posted = $file_upload_data[$type];
    } 

	if (!empty($image_posted)) {
		if ($image_posted['date'] == 0 || (time()-$image_posted['date']) > USE_SESSION_LENGTH) {
			cw_unset($file_upload_data, $type);
			unset($image_posted);
		}
		elseif (!empty($image_posted['file_path']) && $image_posted['id']==$id && $image_posted['type']==$type) {
            $image_type = $image_posted['image_type'];
			$image_path = $image_posted['file_path'];
			$image_type = $image_posted['image_type'];
			$image_size = $image_posted['file_size'];
		}
	}
}

$orig_type = $type;
if (zerolen($image_path) && isset($available_images[$type]) && !empty($tables[$type]) && !empty($id)) {
	$hash_types = array();
	$i = 0;

	$max_attempts = 1;
	while ($i++ < $max_attempts) {
		# counting attempts to prevent infinite loop
		$_table = $tables[$type];
    $_field = (($available_images[$type]['type'] == "U") ? "id" : "image_id");

		$result = db_query("SELECT image_path, image_type, md5, image_size, filename FROM $_table WHERE $_field='$id' LIMIT 1");
		if ($result && db_num_rows($result) > 0) {
			list($image_path,$image_type,$md5,$image_size,$_filename) = db_fetch_row($result);

			if (zerolen($image_path) && !zerolen($_filename)) {
				cw_load("image");
				$image_path = cw_image_dir($type)."/".$_filename;
			}

			db_free_result($result);
			break;
		}

		if ($is_substitute) {
			if (!empty($config['substitute_images'][$type])
			&& isset($config['available_images'][$config['substitute_images'][$type]])
			&& !isset($hash_types[$config['substitute_images'][$type]])) {
				$type = $config['substitute_images'][$type];
				$hash_types[$type] = true;
				continue;
			}

# kornev, TOFIX
			if ($type == "W") {
				$tmp_id = cw_query_first_cell("SELECT product_id FROM $tables[product_variants] WHERE variant_id = '$id'");
				if ($tmp_id) {
					$id = $tmp_id;
					$type = "P";
					$hash_types[$type] = true;
					continue;
				}
			}
		}

		db_free_result($result);
		break;
	}

	if (!zerolen($image_path) && !is_url($image_path)) {
		if (!file_exists($image_path) || !is_readable($image_path)) {
			$image_path = "";
		}
		elseif ($config['setup_images'][$type]['md5_check'] == 'Y') {
			$image_md5 = md5_file($image_path);
		}
	}

	if (!zerolen($image_path) && $config['setup_images'][$type]['md5_check'] == 'Y' && $image_md5 !== $md5)
		$image_path = "";
}

if (zerolen($image_path)) {
	# when image is not available, use the "default image"
	$type = $orig_type;
    $image_path = cw_get_default_image($type);

	$tmp = cw_get_image_size($image_path);
	$image_size = $tmp[0];
	$image_type = empty($tmp[3]) ? "image/gif" : $tmp[3];
}

header("Content-Type: ".$image_type);
if ($image_size > 0)
	header("Content-Length: ".$image_size);

cw_readfile($image_path, true);
exit();
