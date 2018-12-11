<?php

if (!function_exists('exif_imagetype')) {
    function exif_imagetype($filename) {
        if ((list($width, $height, $type, $attr) = getimagesize($filename)) !== false) {
            return $type;
        }
        return false;
    }
}

// build dir fo files
function cw_fbr_build_var_dirs(&$vars) {
	global $app_dir;

	$vars['feedback'] = array(
		'path' => $app_dir . '/files/' . feedback_files_folder_name,
		'mode' => 0777,
		'files' => array (
			'.htaccess' => array(
				'mode' => 0666,
				'content' => "Order Deny,Allow\nDeny from all"
			)
		),
		'criticality' => 1
	);
}

// checks if image is an actual image
function cw_fbr_check_image_is_correct_type($image) {
    global $var_dirs;

    $result = TRUE;

    if (is_writable($var_dirs['tmp'])) {
        $avail_types = array(
            'png' => IMAGETYPE_PNG,
            'jpeg' => IMAGETYPE_JPEG
        );

        $path = $var_dirs['tmp'] . '/image_to_check.' . feedback_image_type;
        file_put_contents($path, $image);

        if (exif_imagetype($path) != $avail_types[feedback_image_type]) {
            $result = FALSE;
        }

        unlink($path);
 	}

    return $result;
}

// create new feedback folder
function cw_fbr_create_new_folder() {
	global $app_dir;
	
	cw_load('files');

	$time = time();
	$counter = 0;
	$path = $app_dir . '/files/' . feedback_files_folder_name;

	if (is_writable($path)) {
		$path = $app_dir . '/files/' . feedback_files_folder_name . '/' . $time . '_' . $counter;

		while (is_dir($path)) {
			$counter++;
			$path = $app_dir . '/files/' . feedback_files_folder_name . '/' . $time . '_' . $counter;
		}

		$result = cw_mkdir($path);

		if ($result) {
			return $path;
		}
	}

	return FALSE;
}

/**
 * create files in a folder $path from $files array
 * 
 * @param string $path - path to folder
 * @param array $files - array with filename/content elements
 */
function cw_fbr_put_files_to_folder($path, $files) {
	if (is_writable($path) && !empty($files)) {

		foreach ($files as $filename => $content) {

			if (!empty($filename) && !empty($content)) {
				file_put_contents($path . '/' . $filename, $content);
			}
		}
	}
}

/**
 * add files and sub-directories in a folder to zip file.
 * 
 * @param string $folder
 * @param ZipArchive $zip_file
 * @param int $exclusive_length Number of text to be exclusived from the file path.
 */
function cw_fbr_folder_to_zip($folder, &$zip_file, $exclusive_length) {
	$handle = opendir($folder);

	while ($f = readdir($handle)) {

		if ($f != '.' && $f != '..') {
			$file_path = "$folder/$f";
			// Remove prefix from file path before add to zip.
			$local_path = substr($file_path, $exclusive_length);

			if (is_file($file_path)) {
				$zip_file->addFile($file_path, $local_path);
			}
			elseif (is_dir($file_path)) {
				// Add sub-directory.
				$zip_file->addEmptyDir($local_path);
				cw_fbr_folder_to_zip($file_path, $zip_file, $exclusive_length);
			}
		}
	}
	closedir($handle);
}

/**
 * zip a folder (include itself).
 *
 * @param string $source_path Path of directory to be zip.
 * @param string $out_zip_path Path of output zip file.
 */
function cw_fbr_zip_dir($source_path, $out_zip_path) {
	$path_info = pathInfo($source_path);
	$parent_path = $path_info['dirname'];
	$dir_name = $path_info['basename'];

	$z = new ZipArchive();
	$z->open($out_zip_path, ZIPARCHIVE::CREATE);
	$z->addEmptyDir($dir_name);
	cw_fbr_folder_to_zip($source_path, $z, strlen("$parent_path/"));
	$z->close();	
}

/**
 * get list of folder names in feedback folder
 * 
 * @param timestamp $time - start time (name)
 * @return array $folders - list folder name, not path
 */
function cw_fbr_get_feedback_folder_list($time) {
	global $app_dir;

	$dir = $app_dir . '/files/' . feedback_files_folder_name;

	if (is_dir($dir) && $time) {
		$folders = array();

		if ($dirstream = @opendir($dir)) {

			while (false !== ($filename = readdir($dirstream))) {
				$path = with_slash($dir) . $filename;

				if (($filename != '.') && ($filename != '..') && is_dir($path)) {
					list ($check_name, $counter) = explode('_', $filename);
					$check_name = intval($check_name);
					// if folder is in time period
					if ($check_name >= $time + 1) {
						$folders[] = $filename;
					}
				}
			}
		}
		closedir($dirstream);

		return $folders;
	}

	return array();
}

// prepare body content
function cw_fbr_prepare_body_content($body_data) {
	$body = '';

	foreach ($body_data as $key => $_body) {
		$num = $key + 1;
		$body .= '#' . $num . ' -------------------------------------------------------------------<br />';
		$body .= '<b>Feedback time:</b> ' . $_body['date'] . '<br />';
		$body .= '<b>Feedback message:</b> ' . $_body['message'] . '<br />';

		if (isset($_body['link_to_screen'])) {
			$body .= '<b>Link to screen:</b> <a href="' . $_body['link_to_screen'] . '" target="_blank">Link to screen</a><br />';
		}		

		$body .= '<b>Path to session dump:</b> ' . $_body['path_to_session_dump'] . '<br />';

		if (isset($_body['path_to_navigation_history'])) {
			$body .= '<b>Path to navigation history:</b> ' . $_body['path_to_navigation_history'] . '<br />';
		}

		if (isset($_body['path_to_php_log'])) {
			$body .= '<b>Path to PHP log:</b> ' . $_body['path_to_php_log'] . '<br />';
		}

		if (isset($_body['path_to_sql_log'])) {
			$body .= '<b>Path to SQL log:</b> ' . $_body['path_to_sql_log'] . '<br />';
		}

		$body .= '<br />';
	}

	return $body;
}

// send saved feedbacks to appropriate emails
// function run by cron
function cw_fbr_prepare_and_send_feedbacks($time) {
	global $app_dir, $config, $current_location, $var_dirs;

	// if empty both emails
	if (
		empty($config[feedback_addon_name]['fbr_email_to_send'])
		&& feedback_our_email_to_send == ""
	) {
		return '';
	}

	$list = cw_fbr_get_feedback_folder_list($time-SECONDS_PER_HOUR);

	if (!empty($list)) {
		cw_load('mail', 'files');

		$path = $var_dirs['tmp'];

		if (is_writable($path)) {
			$path .= '/' . feedback_files_folder_name;
			// delete folder if exist
			if (is_dir($path)) {
				cw_rm_dir($path);
			}

			$result = cw_mkdir($path);
			// if folder created
			if ($result) {
				$body_data = array();

				// copy each folder without image and add PHP and SQL logs
				foreach ($list as $folder) {
					$body_array = array();
					$srcdir = $app_dir . '/files/' . feedback_files_folder_name . '/' . $folder;
					$dstdir = $path . '/' . $folder;

					$result = cw_mkdir($dstdir);
					// if folder created
					if (!$result) {
						continue;
					}

					// prepare content array
					$files = array(
						'session_dump.txt' => file_get_contents($srcdir . '/session_dump.txt'),
						'navigation_history.txt' => file_get_contents($srcdir . '/navigation_history.txt')
					);

					list ($create_date, $counter) = explode('_', $folder);

					// save data for body message
					$body_array = array(
						'date' =>  date('Y-m-d H:i:s', $create_date),
						'message' => file_get_contents($srcdir . '/message.txt'),
						'path_to_session_dump' => $folder . '/session_dump.txt'
					);

					if (file_exists($srcdir . '/navigation_history.txt')) {
						$body_array['path_to_navigation_history'] = $folder . '/navigation_history.txt';
					}

                    if (file_exists($srcdir . '/image.' . feedback_image_type)) {
						$body_array['link_to_screen'] = $current_location . '/index.php?target=feedback&file=' . $folder . '&fkey=' . $config[feedback_addon_name]['fbr_secret_hash'];
					}

					// copy PHP and SQL logs
					$now_date = date('ymd', $create_date);
					$php_log_name = 'php-' . $now_date . '.php';
					$sql_log_name = 'sql-' . $now_date . '.php';

					// copy PHP log
					if (file_exists($var_dirs['log'] . '/' . $php_log_name)) {
						$files[$php_log_name] = file_get_contents($var_dirs['log'] . '/' . $php_log_name);
						$body_array['path_to_php_log'] = $folder . '/' . $php_log_name;
					}

					// copy SQL log
					if (file_exists($var_dirs['log'] . '/' . $sql_log_name)) {
						$files[$sql_log_name] = file_get_contents($var_dirs['log'] . '/' . $sql_log_name);
						$body_array['path_to_sql_log'] = $folder . '/' . $sql_log_name;
					}

					$body_data[] = $body_array;

					cw_fbr_put_files_to_folder($dstdir, $files);
				}

				// zip folder
				$zip_file = $var_dirs['tmp'] . '/feedback_' . $time . '.zip';
				cw_fbr_zip_dir($path, $zip_file);

				// send email
				$from = $config['Company']['site_administrator'];
				$subject = 'Feedbacks';
				// prepare body content
				$body = cw_fbr_prepare_body_content($body_data);

				if (!empty($config[feedback_addon_name]['fbr_email_to_send'])) {
					$to = $config[feedback_addon_name]['fbr_email_to_send'];
					cw_send_simple_mail($from, $to, $subject, $body, array(), array($zip_file));
				}

				if (feedback_our_email_to_send != "" && feedback_our_email_to_send != $config[feedback_addon_name]['fbr_email_to_send']) {
					$to = feedback_our_email_to_send;
					cw_send_simple_mail($from, $to, $subject, $body, array(), array($zip_file));
				}
			}
		}
	}

	return intval(count($list)).' feedbacks sent'; 
}

// custom error handler to catch a PHP Fatal Error
function cw_fbr_error_handler() {

	if (error_reporting() == 0) {
		return;
	}

	$errfile = "unknown file";
	$errstr  = "shutdown";
	$errno   = E_CORE_ERROR;
	$errline = 0;

	$error = error_get_last();

	if ($error !== NULL) {
		$errno   = $error["type"];
		$errfile = $error["file"];
		$errline = $error["line"];
		$errstr  = $error["message"];
	}
	
	$errorType = array (
		E_ERROR            	=> 'FATAL ERROR',
		//E_WARNING        	=> 'WARNING',
		//E_PARSE          	=> 'PARSING ERROR',
		//E_NOTICE         	=> 'NOTICE',
		E_CORE_ERROR     	=> 'CORE ERROR',
		//E_CORE_WARNING   	=> 'CORE WARNING',
		E_COMPILE_ERROR  	=> 'COMPILE ERROR',
		//E_COMPILE_WARNING 	=> 'COMPILE WARNING',
		E_USER_ERROR     	=> 'USER ERROR',
		//E_USER_WARNING   	=> 'USER WARNING',
		//E_USER_NOTICE    	=> 'USER NOTICE',
		//E_STRICT         	=> 'STRICT NOTICE',
		//E_RECOVERABLE_ERROR => 'RECOVERABLE ERROR'
	);

	$errMsg = '';
	if (array_key_exists($errno, $errorType)) {
		$errMsg = "$errorType[$errno]: $errstr in $errfile on line $errline";
	}

    cw_fbr_log_add('php', $errMsg);

    return FALSE;
}

// run if was an error and create feedback if need
function cw_fbr_log_add($label, $data) {
	global $config, $APP_SESSION_VARS, $tables;

	list ($time, $counter) = explode('|', $config['feedback_error_log']);
	$_label = strtolower($label);

    $time = intval($time);
    $counter = intval($counter);

	// if limit feedback error on today is expired or wrong error type
	if (
		(date("ymd", $time) == date("ymd")
		&& $counter >= $config[feedback_addon_name]['fbr_errors_per_day'])
		|| !in_array($_label, array('sql', 'php'))
		|| empty($data)
	) {
		return;
	}

	$path = cw_fbr_create_new_folder();

	$data = str_replace("\n", "<br />", $data);
	$files = array(
		'message.txt' => 'Feedback on ' . strtoupper($_label) . ' error record. <br /><b>Text:</b> <br />' . $data,
		'session_dump.txt' => print_r($APP_SESSION_VARS, TRUE),
		'navigation_history.txt' => print_r($APP_SESSION_VARS['navigation_history_list'], TRUE)
	);	
	cw_fbr_put_files_to_folder($path, $files);
	
	// if create feedback on exist date
	if (date("ymd", $time) == date("ymd")) {
		$counter++;
	}
	else {
		$counter = 1;
	}
	$time = time();
	db_query("REPLACE $tables[config] (name, config_category_id, value) values ('feedback_error_log', 1, '$time|$counter')");
	$config['feedback_error_log'] = $time . '|' . $counter;
}
