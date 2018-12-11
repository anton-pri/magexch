<?php

set_time_limit(86400);

$root_dir = $var_dirs['files'].DIRECTORY_SEPARATOR;

if($product_warehouse) $root_dir.="$product_warehouse/";

$storage_locations = cw_call('cw_get_file_storage_locations');
$smarty->assign('storage_locations', $storage_locations);
if (empty($storage_location_code)) {
    $current_storage_location = reset($storage_locations);
    $storage_location_code = $current_storage_location['code'];

    if (isset($current_storage_location['init_dir'])) 
        $dir = $current_storage_location['init_dir'];

    cw_call('cw_check_init_dir', array($current_storage_location)); 

    cw_header_location("index.php?target=popup_files&field_filename=$field_filename&field_path=$field_path&dir=$dir&storage_location_code=$storage_location_code"); 
}
$smarty->assign('storage_location_code', $storage_location_code);

include $app_main_dir.'/include/image/file.php';

if ($REQUEST_METHOD == "POST") {

    if ($action == "delete_file") {

        include_once $app_main_dir.'/include/lib/AmazonS3/S3.php';
        $s3 = new S3($config['ppd']['ppd_awsAccessKey'], $config['ppd']['ppd_awsSecretKey']);
        $bucketName = $config['ppd']['ppd_aws3_bucketName'];

        if (substr($path, 0, 7) == "aws3://")
            $path = substr($path, 7);

        $s3->deleteObject($bucketName, $path);
        cw_header_location("index.php?target=popup_files&field_filename=$field_filename&field_path=$field_path&dir=$dir&storage_location_code=$storage_location_code");
    }

    $data = array();

   if ($storage_location_code == 'FS') {

       $curr_destination = $var_dirs['files'].$dir;
       $userfiles = $_FILES['userfiles'];
       if (is_array($userfiles))
       foreach($userfiles['tmp_name'] as $index=>$userfile) {
           if (zerolen($userfile)) break;
           $tmp = array();
           $tmp['is_copied'] = true;
           $tmp['filename'] = strtolower($userfiles['name'][$index]);
           $tmp['file_path'] = cw_move_uploaded_file('userfiles', $curr_destination, $index);
           $tmp['source'] = 'S';
           $data[] = $tmp;
       }
   } elseif ($storage_location_code == 'AS3') {
       $curr_destination = $var_dirs['tmp'];
       $tmp_subdir = md5(time());
       $curr_destination .= "/".$tmp_subdir;

       if (!file_exists($curr_destination))
           @mkdir($curr_destination);

       $userfiles = $_FILES['userfiles'];
       if (is_array($userfiles))
       foreach($userfiles['tmp_name'] as $index=>$userfile) {
           if (zerolen($userfile)) break;
           $tmp = array();
           $tmp['is_copied'] = true;
           $tmp['filename'] = strtolower($userfiles['name'][$index]);
           $tmp['file_path'] = cw_move_uploaded_file('userfiles', $curr_destination, $index);
           $tmp['source'] = 'S';
           $data[] = $tmp;
       }

//cw_log_add('upload_files', array($tmp, $data));

       if (!empty($data)) {
           include_once $app_main_dir.'/include/lib/AmazonS3/S3.php';
           $s3 = new S3($config['ppd']['ppd_awsAccessKey'], $config['ppd']['ppd_awsSecretKey']);
           foreach ($data as $copied_file) {
               $uploadFile = $copied_file['file_path'];  
               $bucketName = $config['ppd']['ppd_aws3_bucketName']; 
               $s3destinationName = (!empty($dir)?($dir."/"):"").$copied_file['filename']; 
               $s3->putObjectFile($uploadFile, $bucketName, $s3destinationName, S3::ACL_AUTHENTICATED_READ);
           }
           @unlink($curr_destination);
       }
   } 
   cw_header_location("index.php?target=popup_files&field_filename=$field_filename&field_path=$field_path&dir=$dir&storage_location_code=$storage_location_code");
}

if (!empty($product_warehouse))
	$smarty->assign('product_warehouse', $product_warehouse);


$smarty->assign('home_style', 'popup');
$smarty->assign('current_main_dir', 'main');
$smarty->assign('current_section_dir', 'files');

$smarty->assign('field_filename', (string)$_GET['field_filename']);
$smarty->assign('field_path', (string)$_GET['field_path']);

$location[] = array(cw_get_langvar_by_name('lbl_select_file'), '');

if ($tp == 'images')
    $smarty->assign('main', 'images');
else
    $smarty->assign('main', 'files');
