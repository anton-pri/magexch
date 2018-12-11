<?php
set_time_limit(86400);
ini_set('memory_limit', '512M');

cw_load('import.xcart','product', 'category');
$import = &cw_session_register('import');

if ($action == 'import') {
	if (function_exists('apache_setenv')) apache_setenv('no-gzip', 1);
	ini_set('zlib.output_compression', 0);
	ini_set('implicit_flush', 1);

	extract($request_prepared);
	$warn="You have to agree explicitly that all previous data will be erased.<br>If you do agree with that, please check
	\"I agree, that all previous data will be erased...\" box at the bottom of this page to continue";
	if (!isset($agree) || !$agree) { $conn=false; $err=$warn; }
		else $conn=cw_xcart_get_conn ($path,$err,$xcart_conf);
	if ($conn === false) {
	      $smarty->assign('main', 'import_xcart');
	      $smarty->assign('err_msg', $err);
	      $smarty->assign('path', $path);
	} else {
	      $t="bcse_quotes"; $f='email';
	      //$smarty->assign('err_msg', "<textarea>".print_r(cw_common_tables_names($conn),true)."</textarea>");
	      //cw_common_tables_diff ($conn);
	      //  print_r(cw_vers_diff_attr($conn)); exit;
	      cw_import_users($xcart_conf);
	      import_products ($xcart_conf);
	      //exit;
	      //header ('Location: /admin/index.php?target=categories');
//	      echo "<script>\nwindow.location.href='/admin/index.php?target=categories'\n</script>";
            cw_add_top_message('Ok');
            cw_header_location('index.php?target=import&mode=xcart');
	}
}

$smarty->assign('main', 'import_xcart');
