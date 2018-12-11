<?php
#
# Get image size abstract function
#
function cw_get_image_size($filename, $is_image = false) {
	static $img_types = array (
		'1' => 'image/gif',
		'2' => 'image/jpeg',
		'3' => 'image/png',
		'4' => 'application/x-shockwave-flash',
		'5' => 'image/psd',
		'6' => 'image/bmp',
		'13' => 'application/x-shockwave-flash',
	);

	if (empty($filename))
		return false;

	if ($is_image) {
		global $var_dirs;
		$size = strlen($filename);
		$filename = cw_temp_store($filename);
		if (!$filename)
			return false;
	}

	list($width, $height, $type) = @getimagesize($filename);

	if (!empty($img_types[$type])) {
		$type = $img_types[$type];
	}
	else {
		if ($is_image)
			@unlink($filename);

		return false;
	}

	if ($is_image) {
		@unlink($filename);
	} else {
		$size = cw_filesize($filename);
        $md5 = md5_file($filename);
	}

	return array(intval($size),$width,$height,$type, $md5);
}

#
# Determine that $userfile is image file with non zero size
#
function cw_is_image_userfile($userfile, $userfile_size, $userfile_type) {
	return ($userfile != "none")
		&& ($userfile != "")
		&& ($userfile_size > 0)
		&& (substr($userfile_type, 0, 6) == 'image/' || $userfile_type == 'application/x-shockwave-flash');
}

  /**
   * Return the folder list in provided directory
   * folders are returned with absolute path
   *
   * @param string $dir
   * @param int $mode - binary flag [1-files; 2-folders; 3-both]
   * @param boolean $recursive
   * @return array
   */
  function cw_files_get_dir($dir, $mode = 1, $recursive = false) {
    if(!is_dir($dir)) {
      return false;
    } // if

    $folders = array();

    if($dirstream = @opendir($dir)) {
        while(false !== ($filename = readdir($dirstream))) {
                $path = with_slash($dir) . $filename;
            if(($filename != '.') && ($filename != '..')) {
                if (($mode & 1)  && is_file($path)) $folders[] = $path;
                if (($mode & 2)  && is_dir($path)) $folders[] = $path;

              if ($recursive && is_dir($path)) {
                $sub_folders = cw_files_get_dir($path, $mode, $recursive);
                if (is_array($sub_folders)) {
                  $folders = array_merge($folders, $sub_folders);
                } // if
              } // if
            } // if
        } // while
    } // if

    closedir($dirstream);
    return $folders;
  }

function cw_rm_dir_files ($path) {
    $dir = @opendir ($path);
    if (!$dir)
        return false;

    while ($file = readdir ($dir)) {
        if (($file == ".") || ($file == ".."))
            continue;

        if (filetype ("$path/$file") == "dir") {
            cw_rm_dir("$path/$file");

        } else {
            @unlink ("$path/$file");
        }
    }

    closedir($dir);
}

function cw_rm_dir ($path) {
    cw_rm_dir_files ($path);
    @rmdir ($path);
}

function cw_core_create_dirs($dirs, &$result) {
    $status = true;
    umask(0);
    foreach ($dirs as $_k=>$val) {
        if (!file_exists($val)) {
            $res = @mkdir($val, 0777);
            $status &= $res;
            if (!$res) $result[] = $val;
        }
    }
    return $status;
}

function cw_core_copy_dir($srcdir, $dstdir, &$result) {
    $status = true;

    if (!$handle = opendir($srcdir))
        return false;

    while ($status && ($file = readdir($handle)) !== false) {
        if ($file == '.' || $file == '..') continue;

        if (!file_exists($dstdir))
            $status = $status && cw_core_create_dirs(array($dstdir), $result);

        if (!$status) break;

        if (is_file($srcdir.DIRECTORY_SEPARATOR.$file)) {
            if (!@copy($srcdir.DIRECTORY_SEPARATOR.$file, $dstdir.DIRECTORY_SEPARATOR.$file)) {
                $result[] = $dstdir.DIRECTORY_SEPARATOR.$file;
                $status = false;
            }
            else {
                @chmod($dstdir.DIRECTORY_SEPARATOR.$file, 0666);
            }
        }
        elseif (is_dir($srcdir.DIRECTORY_SEPARATOR.$file) && $file != "." && $file != "..") {
            if (!file_exists($dstdir.DIRECTORY_SEPARATOR.$file)) {
                if (!file_exists($dstdir))
                    $status = $status && cw_core_create_dirs(array($dstdir), $result);

                $status = $status && cw_core_create_dirs(array($dstdir.DIRECTORY_SEPARATOR.$file), $result);
            }

            $status = $status && cw_core_copy_dir($srcdir.DIRECTORY_SEPARATOR.$file, $dstdir.DIRECTORY_SEPARATOR.$file, $result);
        }
    }

    closedir($handle);

    return $status;
}

# kornev, very important
function cw_is_allowed_file($file) {
    $info = pathinfo($file);
    return !preg_match('!,\s*'.preg_quote($info['extension'],'!').'\s*,!Ui',
        ','.DISALLOWED_FILE_EXTS.',');
}
function cw_is_executable($file) {
    $count = 0;
    while (strlen($file) > 0 && @file_exists($file) && @is_link($file) && $count < 2) {
        $file = @readlink($file);
        $count++;
    }

    if (function_exists("is_executable"))
        return @is_executable($file);

    return @is_readable($file);
}

#
# Executable lookup
# Check prefered file first, then do search in PATH environment variable.
# Will return false if no executable is found.
#
function cw_find_executable($filename, $prefered_file = false) {
    global $app_dir;

    if (ini_get("open_basedir") != "" && !empty($prefered_file))
        return $prefered_file;

    $path_sep = CW_IS_OS_WINDOWS ? ';' : ':';

    if ($prefered_file) {
        if (!CW_IS_OS_WINDOWS && cw_is_executable($prefered_file))
            return $prefered_file;

        if (CW_IS_OS_WINDOWS) {
            $info = pathinfo($prefered_file);
            if (empty($info['extension'])) $prefered_file .= ".exe";
            if (cw_is_executable($prefered_file)) return $prefered_file;
        }
    }

    $directories = explode($path_sep, getenv("PATH"));
    array_unshift($directories, $app_dir.DIRECTORY_SEPARATOR."payment");

    foreach ($directories as $dir){
        $file = $dir.DIRECTORY_SEPARATOR.$filename;
        if (!CW_IS_OS_WINDOWS && cw_is_executable($file) ) return $file;
        if (CW_IS_OS_WINDOWS && cw_is_executable($file.".exe") ) return $file.".exe";
    }

    return false;
}
function cw_temp_store($data) {
    global $var_dirs;
    $tmpfile = @tempnam($var_dirs['tmp'], "cwtmp");
    if (empty($tmpfile)) return false;

    $fp = @fopen($tmpfile,"w");
    if (!$fp) {
        @unlink($tmpfile);
        return false;
    }

    fwrite($fp,$data);
    fclose($fp);

    return $tmpfile;
}

#
# Get tmpfile content
#
function cw_temp_read($tmpfile, $delete = false) {
    if (empty($tmpfile))
        return false;

    $fp = @fopen($tmpfile,"rb");
    if(!$fp)
        return false;

    while (strlen($str = fread($fp, 4096)) > 0 )
        $data .= $str;
    fclose($fp);

    if ($delete) {
        @unlink($tmpfile);
    }

    return $data;
}
function cw_realpath($path) {
    global $app_dir;

    if (CW_IS_OS_WINDOWS && preg_match('!^((?:\\\\\\\\[^\\\\]+)|(?:\w:))(.*)!S', $path, $matched)) {
        # windows paths: \\server\path and DRIVE:\path
        $path = $matched[1].cw_normalize_path($matched[2]);
    }
    else {
        # other paths
        if ($path[0] != '/' && $path[0] != '\\')
            $path = $app_dir.DIRECTORY_SEPARATOR.$path;

        $path = cw_normalize_path($path);

        $cache = array ();
        do {
            $cache[$path] = true; # prevent the loop
            $path = cw_resolve_fs_symlinks($path);
            if ($path === false) {
                # cannot resolve, broken path
                return false;
            }
        } while (empty($cache[$path]));
    }

    return $path;
}
function cw_resolve_fs_symlinks($path) {
    if (CW_IS_OS_WINDOWS || strlen($path) < 2 || strlen(ini_get('open_basedir')) > 0)
        return $path;

    $parts = explode('/', substr($path,1));
    $resolved = "";

    $normalize = false;
    while (!empty($parts)) {
        $elem = array_shift($parts);
        if (strlen($elem) == 0)
            continue;

        $resolved .= '/' . $elem;
        if (!file_exists($resolved))
            continue;

        if (is_link($resolved)) {
            $normalize = true;
            $link = readlink($resolved);
            if ($link === false || strlen($link) == 0 || !strcmp($link, $elem)) {
                # cannot resolve, broken path
                return false;
            }

            $link = preg_replace('!/+$!S','',$link);

            if (strlen($link) == 0) {
                $resolved = '/';
            }
            elseif ($link[0] == '/') {
                $resolved = $link;
            }
            else {
                $resolved .= '/../' . $link;
            }
        }
    }

    $path = $resolved;

    if ($normalize)
        $path = cw_normalize_path($path);

    return strlen($path) == 0 ? false : $path;
}
function cw_allowed_path($allowed_path, $path) {
    if (empty($path)) return false;

    if (CW_IS_OS_WINDOWS) {
        $allowed_path = strtolower($allowed_path);
        $path = strtolower($path);
    }

    $allowed_path = cw_realpath($allowed_path);
    if (empty($allowed_path)) return false;

    # absolute path
    if ((CW_IS_OS_WINDOWS && preg_match("/^(\\\\)|(\w:)/S",$path)) || !CW_IS_OS_WINDOWS && $path[0] == '/') {
        $path = cw_realpath($path);
    }
    else {
        $path = cw_realpath($allowed_path.DIRECTORY_SEPARATOR.$path);
    }

    if (!strcmp($allowed_path, $path))
        return $allowed_path;

    if ($allowed_path[strlen($allowed_path)-1] != DIRECTORY_SEPARATOR)
        $allowed_path .= DIRECTORY_SEPARATOR;

    if (!strncmp($path, $allowed_path, strlen($allowed_path)))
        return $path;

    return false;
}

function cw_allow_file($file, $is_root = false) {
    global $app_dir, $customer_id, $current_area, $var_dirs;

    if (empty($file) || !cw_is_allowed_file($file))
        return false;

    if (!is_url($file)) {
        $dir = $app_dir;
        if (!$is_root) {
            if ($current_area=="A")
                $dir = $var_dirs['files'];
            elseif ($current_area=="P" || $current_area == 'A')
                $dir = $var_dirs['files'].DIRECTORY_SEPARATOR.$customer_id;
            else
                $dir = $var_dirs['files'];
        }

        $file = cw_allowed_path($dir, $file);
    }

    return $file;
}

#
# fopen() wrapper
#
function cw_fopen($file, $perm = 'r', $is_root = false) {
    $file = cw_allow_file($file, $is_root);
    if ($file === false)
        return false;

    return @fopen($file, $perm);
}
function cw_file_get($file, $is_root = false) {
    $fp = cw_fopen($file, 'rb', $is_root);

    if ($fp === false) return false;

    while (strlen($str = fread($fp, 8192)) > 0 )
        $data .= $str;

    fclose($fp);
    return $data;
}

#
# readfile() wrapper
#
function cw_readfile($file, $is_root = false) {
    $file = cw_allow_file($file, $is_root);
    if ($file === false) return false;

    return readfile($file);
}

function cw_move_uploaded_file($file, $destination = '', $index = null) {
    global $_FILES, $var_dirs;

    if (!is_array($_FILES[$file])) return false;

    if (isset($index))
        foreach($_FILES[$file] as $name=>$val)
            $file_information[$name] = $val[$index];
    else $file_information = $_FILES[$file];

    $destination = $destination?$destination.'/'.$file_information['name']:tempnam($var_dirs['tmp'], preg_replace('/^.*[\/\\\]/S', '', $file_information['name']));

    $path = cw_allow_file($destination, true);
    if ($path === false)
        return false;

    if (move_uploaded_file($file_information['tmp_name'], $path))
        return $path;

    @chmod($path, 0644);

    return false;
}

#
# file() wrapper
#
function cw_file($file, $is_root = false) {
    $file = cw_allow_file($file, $is_root);
    if ($file === false) return array();

    $result = @file($file);

    return (is_array($result) ? $result : array());
}
function cw_normalize_path($path, $separator=DIRECTORY_SEPARATOR) {
    $qs = preg_quote($separator,'!');
    $path = preg_replace("/[\\\\\/]+/S",$separator,$path);
    $path = preg_replace("!".$qs."\.".$qs."!S", $separator, $path);

    $regexp = "!".$qs."[^".$qs."]+".$qs."\.\.".$qs."!S";
    for ($old="", $prev="1"; $old != $path; $path = preg_replace($regexp, $separator, $path)) {
        $old = $path;
    }

    return $path;
}
function cw_relative_path($dir, $home_dir = false) {
    global $app_dir;

    if (empty($dir))
        return false;

    if ($home_dir === false)
        $home_dir = $app_dir;

    $home_dir = preg_replace("/".preg_quote(DIRECTORY_SEPARATOR, "/")."$/", '', $home_dir);

    $dir = cw_realpath($dir);
    $is_dir = is_dir($dir);

    # Get paths as arrays
    $d = explode(DIRECTORY_SEPARATOR, $is_dir ? $dir : dirname($dir));
    $h = explode(DIRECTORY_SEPARATOR, $home_dir);
    $dir_disc = strtoupper(array_shift($d));
    $home_disc = strtoupper(array_shift($h));

    if (CW_IS_OS_WINDOWS) {

        # Check disk letters
        if (($dir_disc !== $home_disc)) {
            return false;

        # Check net devies names
        } elseif ((empty($dir_disc) && empty($home_disc) && empty($d[0]) && empty($h[0]))) {
            array_shift($d);
            array_shift($h);
            $dir_disc = array_shift($d);
            $home_disc = array_shift($h);
            if ($dir_disc != $home_disc)
                return false;

        }
    }

    $max = count($h);
    if (count($d) < $max)
        $max = count($d);

    # Define equal root for both paths
    $root = 0;
    for ($x = 0; $x < $max; $x++) {
        if ($d[$x] !== $h[$x])
            break;
        $root++;
    }

    # Build prefix (return from home dir to destination dir) for result path
    $prefix_str = str_repeat("..".DIRECTORY_SEPARATOR, count($h)-$root);
    if (empty($prefix_str)) {
        $prefix_str = ".".DIRECTORY_SEPARATOR;
    }

    # Remove root from destination dir
    if ($root > 0) {
        array_splice($d, 0, $root);
    }

    if (!empty($d)) {
        $prefix_str .= implode(DIRECTORY_SEPARATOR, $d).DIRECTORY_SEPARATOR;
    }
    if (!$is_dir) {
        $prefix_str .= basename($dir);
    }

    return $prefix_str;
}

function is_url($url) {
    if (empty($url))
        return false;

    return preg_match("/^(http|https|ftp):\/\//iS", $url);
}

function cw_get_image_type($image_type) {
    static $imgtypes = array (
        "/gif/i" => "GIF",
        "/jpg|jpeg/i" => "JPEG",
        "/png/i" => "PNG",
        "/bmp/i" => "BMP");

    foreach ($imgtypes as $k=>$v) {
        if (preg_match($k, $image_type))
            return $v;
    }

    return "JPEG";
}
function cw_filesize($file) {
    clearstatcache(); # without can return zero for just uploaded, non-zero size and exists files (affected: PHP 4.4.0 CGI).
    if (!is_url($file))
        return @filesize($file);

    $host = parse_url($file);
    if ($host['scheme'] != 'http')
        return false;

    if (empty($host['port']))
        $host['port'] = 80;

    $fp = fsockopen($host['host'], $host['port'], $errno, $errstr, 30);
    if (!$fp)
        return false;

    fputs ($fp, "HEAD $host[path]?$host[query] HTTP/1.0\r\n");
    fputs ($fp, "Host: $host[host]:$host[port]\r\n");
    fputs ($fp, "User-Agent: Mozilla/4.5 [en]\r\n");
    fputs ($fp,"\r\n");

    $err = chop(fgets($fp, 4096));
    if (strpos($err, " 200 ") === false)
        return false;

    $header_passed = false;
    $len = false;
    while (!feof($fp)) {
        $line = fgets($fp, 4096);
        if ($line == "\n" || $line == "\r\n") {
            break;
        }

        $header_line = explode(": ", $line, 2);
        if (strtoupper($header_line[0]) == 'CONTENT-LENGTH') {
            $len = (int)trim($header_line[1]);
            break;
        }
    }

    fclose($fp);

    if ($len === false) {
        if($fp = cw_fopen($file, 'rb')) {
            while (strlen($str = fread($fp, 8192)) > 0) {
                $len += strlen($str);
            }

            fclose($fp);
        }
    }

    return $len;
}

function cw_is_full_path($path) {
    return (is_url($path) || (CW_IS_OS_WINDOWS && preg_match("/^(?:\w:\\\)|^(?:\\\\\\\\\\w+\\\)/S", $path)) || (!CW_IS_OS_WINDOWS && preg_match("/^\//S", $path)));
}
function cw_mkdir($dir, $mode = 0777) {
    $dir = cw_realpath($dir);

    $dirstack = array();

    while (!@is_dir($dir) && $dir != '/') {
        if ($dir != ".") {
            array_unshift($dirstack, $dir);
        }

        $dir = dirname($dir);
    }

    while ($newdir = array_shift($dirstack)) {
        if (substr($newdir, -2) == "..") continue;

        umask(0000);
        if (!@mkdir($newdir, $mode)) {
            return false;
        }
    }

    return true;
}
function cw_pathcmp($path1, $path2, $use_len=NULL) {
    static $cw_defs = array (
        0 => array ('strcmp', 'strncmp'),
        1 => array ('strcasecmp', 'strncasecmp')
        );

    $index = (int)(CW_IS_OS_WINDOWS);
    $func = $cw_defs[$index];

    $path1 = cw_normalize_path($path1);
    $path2 = cw_normalize_path($path2);

    if (is_null($use_len))
        return !$func[0]($path1, $path2);

    $len = ($use_len == 1) ? strlen($path1) : strlen($path2);

    return !$func[1]($path1, $path2, $len);
}

function cw_is_empty_dir($path) {

    if (!is_dir($path)) return null;

    // Scans the path for directories and if there are more than 2
    // directories i.e. "." and ".." then the directory is not empty
    if ( ($files = @scandir($path)) && (count($files) > 2) )
    {
        return false;
    }

    return true;
}


function cw_cleanup_cache($type="") {
    global $smarty, $app_dir;

    $result = FALSE;
    if (!in_array($type, array("", "tpl", "cache"))) {
        $type = "";
    }

    cw_event('on_cleanup', array($type));

    if ($type == "tpl") {
        $result = $smarty->clear_compiled_tpl();
        cw_rm_dir($app_dir . '/var/templates');
    }
    elseif ($type == "cache") {
        $result = $smarty->clear_all_cache();
        cw_rm_dir($app_dir . '/var/cache');
    }
    elseif ($type == "") {
        $result = $smarty->clear_compiled_tpl();
        $result &= $smarty->clear_all_cache();
        cw_rm_dir($app_dir . '/var/templates');
        cw_rm_dir($app_dir . '/var/cache');
    }

    return $result;
}

function cw_get_file_storage_locations() {
    $result = array(array('code'=>'AS3', 'title' => 'Amazon S3', 'init_dir' => ''), array('code'=>'FS', 'title' => 'Server File System'));
    return $result;
}

function cw_check_init_dir($storage_location) {
    global $config, $app_main_dir;

    if ($storage_location['code'] == 'AS3' && $storage_location['init_dir'] != '') {   
        include_once $app_main_dir.'/include/lib/AmazonS3/S3.php';
        $s3 = new S3($config['ppd']['ppd_awsAccessKey'], $config['ppd']['ppd_awsSecretKey']);
        $info = $s3->getObjectInfo($config['ppd']['ppd_aws3_bucketName'], $storage_location['init_dir'].'/');
        if (empty($info)) {
            $s3->putObjectString('', $config['ppd']['ppd_aws3_bucketName'], $storage_location['init_dir'].'/', S3::ACL_PUBLIC_READ);
        }
    }
}
