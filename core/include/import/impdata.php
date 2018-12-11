<?php

//error_reporting(E_ALL ^ E_NOTICE);
set_time_limit(86400);
ini_set('memory_limit', '256M');

$max_upl_size=($pms=ini_get('post_max_size')>$umfs=ini_get('upload_max_filesize'))?$umfs:$pms;
$smarty->assign('max_upl_size', $max_upl_size);

cw_load('import_export_csv','product', 'category');
$import = &cw_session_register('import');
$err="";

if ($action == 'upload' && isset($_FILES) && !empty($_FILES) && isset($_FILES['csvfile'])) {
	$tmp = $_FILES['csvfile']['tmp_name'];
	$fn = $_FILES['csvfile']['name'];
	$mtype = strtolower($_FILES['csvfile']['type']);
	if (isset($_FILES['csvfile']['error'])) {
		$error=$_FILES['csvfile']['error'];
	}

	if (isset($error) && $error!=0) {
		cw_add_top_message(cw_get_langvar_by_name('lbl_upld_err')." ".$error,'E');
	}
	elseif ($mtype!='text/csv') {
		cw_add_top_message(cw_get_langvar_by_name('lbl_err_file_is_not_csv'),'E'); 
	}
	else {
		$path = csv_path;
		if (!is_dir($path)) mkdir($path);
		$path .= '/uploaded'; 
		if (!is_dir($path)) mkdir($path);
		copy($tmp,"$path/$fn");
	}
	cw_header_location("index.php?target=$target&mode=$mode");
}

if ($action == 'delete' && isset($filenames) && is_array($filenames)) {
	foreach ($filenames as $v) 
		if (file_exists(csv_path.'/uploaded/'.$v)) 
			unlink(csv_path.'/uploaded/'.$v);
}

if ($action == 'delete2' && isset($filenames) && is_array($filenames)) {
	foreach ($filenames as $v) 
		if (file_exists(csv_path.'/'.$v)) 
			unlink(csv_path.'/'.$v);
}


if ($action == 'import' && isset($filenames) && is_array($filenames)) 
foreach ($filenames as $v) if (file_exists(csv_path.'/uploaded/'.$v)) {
$res=cw_csv2table(csv_path.'/uploaded/'.$v);
if ($res!==true) $err.="$v -- $res<br />"; else $err.="$v -- Import -- OK<br />";
}


if ($action == 'import2' && isset($filenames) && is_array($filenames)) 
foreach ($filenames as $v) if (file_exists(csv_path.'/'.$v)) {
$res=cw_csv2table(csv_path.'/'.$v);
if ($res!==true) $err.="$v -- $res<br />"; else $err.="$v -- Import -- OK<br />";
}


$files=cw_list_csv_upl_dir();
$smarty->assign('files', $files);
$files2=cw_list_csv_dir();
$smarty->assign('files2', $files2);

$smarty->assign('err_msg', $err);

$smarty->assign('current_section_dir','import_export');
$smarty->assign('main', 'import_data');


?>
