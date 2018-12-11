<?php
function ppd_permissions($perms = 0) {

    $perms_array = array('vis' => 0, 'acc' => 0);

    switch ($perms) {
        case 1:
            $perms_array['vis'] = 0;
            $perms_array['acc'] = 1;
            break;
        case 4:
            $perms_array['vis'] = 4;
            $perms_array['acc'] = 0;
            break;
        case 5:
            $perms_array['vis'] = 4;
            $perms_array['acc'] = 1;
            break;
        default:
            $perms_array['vis'] = 0;
            $perms_array['acc'] = 0;
            break;
    }

    return $perms_array;
}



function ppd_get_url_fileicon($fileicon = null) {
    global $var_dirs, $config, $current_location, $app_dir;
    static $icons_dir, $dir_url_param;

    if (empty($fileicon)) {
        return null;
    }

    $path_to_icon = null;

    if (!isset($icons_dir)) {
        $icons_dir = ppd_correct_dirname($config['ppd']['ppd_icons_dir']);
    }

    $_is_fileicon = realpath($var_dirs['files'] . $icons_dir . $fileicon);

    if (!empty($_is_fileicon)) {
        if (!isset($dir_url_param)) {
            $dir_url_param = $current_location . str_replace(array($app_dir, '\\', '\\\\'), array('', '/', '/'), $var_dirs['files'] . $icons_dir);
        }
        $path_to_icon = $dir_url_param . $fileicon;
    }

    return $path_to_icon;
}


function ppd_get_url_tofile($file = null) {
    global $var_dirs, $config, $current_location, $app_dir;
    static $files_dir, $dir_url_param;

    if (empty($file)) {
        return null;
    }

    $url_to_file = null;

    if (!isset($files_dir)) {
        $files_dir = ppd_correct_dirname($config['ppd']['ppd_product_files_dir']);
    }

    $_is_file = realpath($var_dirs['files'] . $files_dir . $file);

    if (!empty($_is_file)) {
        if (!isset($dir_url_param)) {
            $dir_url_param = $current_location . str_replace(array($app_dir, '\\', '\\\\'), array('', '/', '/'), $var_dirs['files'] . $files_dir);
        }
        $url_to_file = $dir_url_param . $file;
    }

    return $url_to_file;
}


function ppd_get_pathto_file($file = null) {
    global $var_dirs, $config;
    static $files_dir;

    if (empty($file)) {
        return null;
    }

    if (substr($file, 0, 7) == 'aws3://') {
        return $file;
    }

    if (!isset($files_dir)) {
        $files_dir = ppd_correct_dirname($config['ppd']['ppd_product_files_dir']);
    }

    $path = realpath($var_dirs['files'] . $files_dir . $file);

    if (empty($path)) {
        $path = cw_get_langvar_by_name('txt_ppd_file_not_found');
    }

    return $path;
}



function ppd_convertfrom_bytes($bytes = 0) {

    $bytes = (float) $bytes;

    if (empty($bytes)) {
        return $bytes . 'Kb';
    }

    if ($bytes >= 1073741824) {
        $bytes = round($bytes / 1073741824, 1) . 'Gb';
    } elseif ($bytes >= 1048576) {
        $bytes = round($bytes / 1048576, 1) . 'Mb';
    } elseif ($bytes >= 1024) {
        $bytes = round($bytes / 1024, 1) . 'Kb';
    } else {
        $bytes .= 'b';
    }

    return $bytes;
}



function ppd_check_path($path = null) {
    global $var_dirs, $config;
    static $files_dir;

    if (empty($path)) {
        return null;
    }

    if (!isset($files_dir)) {
        $files_dir = ppd_correct_dirname($config['ppd']['ppd_product_files_dir']);
    }

    $path = realpath($var_dirs['files'] . $files_dir . $path);

    return $path;
}



function ppd_get_filesize($file = null) {

    if (empty($file)) {
        return 0;
    }

    if (substr($file, 0, 7) == 'aws3://') {
        global $app_main_dir, $config;  
        include_once $app_main_dir.'/include/lib/AmazonS3/S3.php';
        $s3 = new S3($config['ppd']['ppd_awsAccessKey'], $config['ppd']['ppd_awsSecretKey']); 
        $bucketName = $config['ppd']['ppd_aws3_bucketName'];
        $info = $s3->getObjectInfo($bucketName, substr($file, 7));
        return $info['size'];
    }
    return filesize($file);
}



function ppd_get_mime_type($file = null, $close_finfo = false) {
    static $finfo;
    global $product_id;

    if (substr($file, 0, 7) == 'aws3://') {
        global $app_main_dir, $config;
        include_once $app_main_dir.'/include/lib/AmazonS3/S3.php';
        $s3 = new S3($config['ppd']['ppd_awsAccessKey'], $config['ppd']['ppd_awsSecretKey']);
        $bucketName = $config['ppd']['ppd_aws3_bucketName'];
        $info = $s3->getObjectInfo($bucketName, substr($file, 7));
        return $info['type'];
    }

    if ($close_finfo) {
        if (isset($finfo)) {
            finfo_close($finfo);
        }
        return null;
    }

    if (empty($file)) {
        return null;
    }

    $mime_type = null;
    $mime_data = null;

    if (function_exists("mime_content_type")) {
        $mime_data = mime_content_type($file);
    } elseif (function_exists("finfo_file")) {
        if (!isset($finfo)) {
            $finfo = finfo_open(FILEINFO_MIME);
        }
        $mime_data = finfo_file($finfo, $file);
    } else {
        $os_name = ppd_get_os_name();
        if ($os_name == 'win') {
            $mime_data = 'application/octet-stream';
        }
        if ($os_name == 'mac') {
            $mime_data = trim(@exec('file -b --mime ' . escapeshellarg($file)));
        } else {
            $mime_data = trim(@exec('file -bi ' . escapeshellarg($file)));
        }
    }

    if (!empty($mime_data)) {
        $mime_data = explode(';', $mime_data);
        $mime_type = trim($mime_data[0]);
    }

    return $mime_type;
}



function ppd_get_file_extension($file) {

    if (empty($file)) {
        return null;
    }

    $extension = null;
    preg_match("/\.([^.]*?)$/", $file, $matches);

    if (!empty($matches) && is_array($matches) && isset($matches[1])) {
        $extension = strtolower($matches[1]);
    }

    return $extension;
}



function ppd_get_filetypes() {
    global $tables;
    static $types;

    $required_fields = array('type_id', 'type', 'fileicon');

    if (!isset($types)) {
        $types = cw_query('SELECT `' . implode('`, `', $required_fields) . '` FROM ' . $tables['ppd_types']);
        foreach ($types as $key => $type) {
            $types[$key]['fileicon'] = ppd_get_url_fileicon($type['fileicon']);
        }
    }

    return $types;
}



function ppd_correct_dirname($dir = null) {
    if (empty($dir)) {
        return null;
    }

    $os_name = ppd_get_os_name();

    if ($os_name != 'win') {
        $dir = str_replace('\\', '/', $dir);
    } else {
        $dir = str_replace('/', '\\', $dir);
    }
    $dir = str_replace(array('//', '\\\\'), array('/', '\\'), $dir);

    if (strpos($dir, DIRECTORY_SEPARATOR) !== 0) {
        $dir = DIRECTORY_SEPARATOR . $dir;
    }

    if (strpos($dir, DIRECTORY_SEPARATOR, strlen($dir) - 1) + 1 != strlen($dir)) {
        $dir = $dir . DIRECTORY_SEPARATOR;
    }

    return $dir;
}



function ppd_get_os_name() {
    static $os_name;

    if (!isset($os_name)) {
        if (strtolower(substr(PHP_OS, 0, 3)) == 'win') {
            $os_name = 'win';
        } elseif (strstr(strtolower(PHP_OS), 'darwin') !== false) {
            $os_name = 'mac';
        } else {
            $os_name = 'nix';
        }
    }

    return $os_name;
}



function ppd_get_stats($file_id, $product_id, $start = 0, $through = 0) {
	global $tables;
	
	$file_id = (int)$file_id;
	$product_id = (int)$product_id;
	
	if (empty($file_id) || empty($product_id)) {
		return 0;
	}
	
	if ($start > $through) {
		$start = 0;
	}
	
	if (empty($start) || empty($through)) {
		return cw_query_first_cell('SELECT SUM(counter) as total FROM ' . $tables['ppd_stats'] . ' WHERE file_id = \'' . $file_id . '\' AND product_id = \'' . $product_id . '\'');
	}
	
	return cw_query_first_cell('SELECT SUM(counter) as total FROM ' . $tables['ppd_stats'] . ' WHERE file_id = \'' . $file_id . '\' AND product_id = \'' . $product_id . '\' AND period >= \'' . $start . '\' AND period <= \'' . $through . '\'');
}

?>
