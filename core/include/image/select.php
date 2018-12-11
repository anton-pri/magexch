<?php
cw_load( 'files', 'image', 'user');

$file_upload_data = &cw_session_register('file_upload_data', array());

if (empty($file_upload_data))
    $file_upload_data = array();

$service_fields = array("file_path", "source", "image_x", "image_y", "image_size", "image_type", "dir_upload", "id", "type", "date", "filename");

if (!isset($available_images[$type]) || empty($type))
	cw_close_window();

$userfiles_dir = cw_user_get_files_location().DIRECTORY_SEPARATOR;

if ($REQUEST_METHOD == "POST") {
	$data = array();

    $userfiles = $_FILES['userfiles'];
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
    	    	$val['image_type']) = cw_get_image_size($val['file_path']);

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
    cw_session_save();

    $smarty->assign('type', $type);
    $smarty->assign('imgid', $imgid);
    $smarty->assign('id', $id);
    $smarty->assign('multiple', $available_images[$type]['multiple']);

    $smarty->assign('file_upload_data', $file_upload_data[$type]);
    $smarty->assign('current_main_dir', 'main');
    $smarty->assign('current_section_dir', 'image_selection');
    $smarty->assign('main', 'image_selection_close');
    $smarty->assign('home_style', 'iframe');
    cw_display($app_skins_dirs[$current_area].'/index.tpl', $smarty);
	exit;
}

$_table = $tables[$type];
$_field = $available_images[$type] == 'U' ? "id" : "imageid";

$smarty->assign('multiple', $available_images[$type]['multiple']);
$smarty->assign('tabs', empty($tabs)?'':explode(',',$tabs));
$smarty->assign('type', $type);
$smarty->assign('imgid', $imgid);
$smarty->assign('id', $id);
$smarty->assign('multiple_id', $id);
$smarty->assign('parent_window', $parent_window);

$smarty->assign('upload_max_filesize', ini_get('upload_max_filesize'));

$smarty->assign('current_main_dir', 'main');
$smarty->assign('current_section_dir', 'image_selection');
$smarty->assign('main', 'image_selection');
$smarty->assign('home_style', 'iframe');

define('PREVENT_XML_OUT', true); // need simple HTML out if controller called as ajax via $.load()

?>
