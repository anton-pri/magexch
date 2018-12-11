<?php
cw_load('files');

function fo_local_log_add($operation, $op_status, $op_message=false) {
	global $customer_id;
	global $REMOTE_ADDR;

	if ($op_message !== false)
		$op_message = trim($op_message);

	$message = sprintf("customer_id: %s\nIP: %s\nOperation: %s\nOperation status: %s%s",
		$customer_id,
		$REMOTE_ADDR,
		$operation,
		($op_status?'success':'failure'),
		(!empty($op_message)?"\n".$op_message:"")
	);

	cw_log_flag('log_file_operations', 'FILES', $message);
}

$opener_str = "&opener=".$opener;

if (isset($filename)) $filename = trim($filename);

if (isset($new_directory)) $new_directory = trim($new_directory);

if (isset($new_file)) $new_file = trim($new_file);

if (!empty($file)) {
	# Edit file
	$path = cw_allowed_path($root_dir, $root_dir.$file);

	if ($path === false || empty($file)) {
		# Path is not allowed or empty new dir name
		$top_message['content'] = cw_get_langvar_by_name("msg_err_file_wrong");
		$top_message['type'] = "E";

		fo_local_log_add('Open file', false, "Filename: ".$file);

		cw_header_location($action_script."&dir=$dir".$opener_str);
	}
    elseif (!is_readable($path)) {
		# Permission denied
		$top_message['content'] = cw_get_langvar_by_name("msg_err_file_read_permission_denied");
		$top_message['type'] = "E";

		fo_local_log_add('Open file', false, "Filename: ".$file);

		cw_header_location($action_script."&dir=$dir".$opener_str);
	}
    else {
		$op_status = true;
		if (@getimagesize($path)) {
			$smarty->assign('file_type', 'image');
		}
		else {
			$smarty->assign('filebody', file($path));
		}
	}

	$smarty->assign('filename', $file);
	$smarty->assign('main', "edit_file");
}
else {

    if ($storage_location_code == 'FS') {
        $maindir = cw_allowed_path($root_dir, $root_dir.$dir);
	    if ($maindir === false) $maindir = $root_dir;

    	if ($dh = @opendir($maindir)) {
	    	while (($file = readdir($dh))!==false) {
		    	if ($file=="." || preg_match("/^\.[^.]/S",$file))
			    	continue;

    			$dir_entries[] = array (
	    			"file" => $file,
		    		//"href" => ($file==".." ? ereg_replace("\/[^\/]*$","",$dir):"$dir/$file"),
                    "href" => ($file==".." ? preg_replace("/\/[^\/]*$/","",$dir):"$dir/$file"),
				    "filetype" => @filetype($maindir.DIRECTORY_SEPARATOR.$file)
    			);
	   	    }

    		function myfilesortfunc($a,$b) {
	    		return strcasecmp($a['filetype'], $b['filetype']) * 1000 + strcasecmp($a['file'], $b['file']);
		    }

    		usort ($dir_entries, "myfilesortfunc");
	    	closedir($dh);
    	}
    } elseif ($storage_location_code == 'AS3') {
        include_once $app_main_dir.'/include/lib/AmazonS3/S3.php';
        $s3 = new S3($config['ppd']['ppd_awsAccessKey'], $config['ppd']['ppd_awsSecretKey']);
        $contents = $s3->getBucket($config['ppd']['ppd_aws3_bucketName']);

        $current_init_dir = '';
        $storage_locations = cw_call('cw_get_file_storage_locations');
        foreach ($storage_locations as $_storage_location) {
            if ($_storage_location['code'] == $storage_location_code)    
                $current_init_dir = $_storage_location['init_dir']; 
        }  

        if ($dir != $current_init_dir) {

            $dir_parts = explode('/', $dir);
            if (!empty($dir_parts)) {
                $dir_parts = array_reverse($dir_parts);
                array_shift($dir_parts);
                $parent_dir_parts = array_reverse($dir_parts);
                $parent_path = implode('/',$parent_dir_parts);
            } else {
                $parent_path = '';
            }

            $dir_entries[] = array(
                "file" => "..",
                "href" => $parent_path,
                "filetype" => 'dir'
            );
        }

        foreach ($contents as $bucket_item) {

            if ($bucket_item['name'] == $dir."/")  
                continue;

            if (!empty($dir) && substr($bucket_item['name'], 0, strlen($dir)) != $dir) 
                continue;

            if (!empty($dir)) 
                $sub_elem_path = str_replace($dir, '', $bucket_item['name']);
            else 
                $sub_elem_path = $bucket_item['name'];          

            $sub_elem_path = trim($sub_elem_path,'/');

            if (strpos($sub_elem_path, "/") === false) {
                $dir_entries[] = array(
                    "file" => $sub_elem_path,
                    "href" => (($bucket_item['size'] == 0)?'':'aws3://').trim($bucket_item['name'], '/'),
                    "filetype" => ($bucket_item['size'] == 0)?'dir':'file'
                ); 
            }  
        }
    }

	$smarty->assign('root_dir', $root_dir);
	$smarty->assign('dir_entries', $dir_entries);
	$smarty->assign('dir_entries_half', (int) (sizeof($dir_entries)/2));
	$smarty->assign('main', "edit_dir");
	$smarty->assign('is_writeable', @is_writable($root_dir));
}

$smarty->assign('upload_max_filesize', ini_get("upload_max_filesize"));
