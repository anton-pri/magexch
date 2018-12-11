<?php
@set_time_limit(2700);

if (!$addons['egoods'])
	cw_header_location('index.php');

cw_load('files');

if ($_GET['action'] != "get") {
	#
	# Prepare the appearing of download page
	#
	if ($id) {
		$product_id = cw_query_first_cell("SELECT product_id FROM $tables[download_keys] WHERE download_key = '$id'");

		if ($product_id) {
			$product_data = cw_query_first("SELECT * FROM $tables[products] WHERE product_id='$product_id'");

			$distribution = $product_data['distribution'];
			$warehouse = $product_data['warehouse'];

			if (!is_url($distribution)) {
				if (!empty($warehouse)) {
					$warehouse_flag = cw_query_first_cell("SELECT $tables[memberships].flag FROM $tables[customers], $tables[memberships] WHERE $tables[customers].customer_id='$warehouse' AND $tables[customers].membership_id = $tables[memberships].membership_id");
				}

                $distribution = $var_dirs['files'].$distribution;

				$size = @filesize($distribution);
			}
			else {
				$fp = @fopen($distribution, "rb");
				for ($size=0, $string = @fread($fp, 8192); strlen($string) != 0; $string = @fread($fp, 8192)) {
					$size += strlen($string);
				}
				@fclose($fp);
			}

			$product_data['length'] = number_format($size, 0, '', ' ');

			$smarty->assign('product', $product_data);
			$smarty->assign('url', $app_catalogs['customer']."/download.php?".$QUERY_STRING."&action=get");
		}

	}

	$location[] = array(cw_get_langvar_by_name("lbl_download"), "");

	$smarty->assign('main', 'download');

	# Assign the current location line
	

	cw_display('customer/index.tpl', $smarty);
	exit;
}

if (empty($id)) exit();

$chunk_size = 100*1024;  # 100 Kb

$query = "SELECT * FROM $tables[download_keys] WHERE download_key = '$id'";
$res = cw_query_first($query);

# If there is corresponding key in database and not expired
if ((count($res) > 0) AND ($res['expires'] > time())) {
	# check if there is valid distribution for this product
	$product_id = $res['product_id'];

	$result = cw_query_first("SELECT distribution, product, warehouse FROM $tables[products] WHERE product_id = '$product_id'");

	$distribution = $result['distribution'];
	$warehouse = $result['warehouse'];

	if (!is_url($distribution)) {

		if (!empty($warehouse)) {
			$warehouse_flag = cw_query_first_cell("SELECT $tables[memberships].flag FROM $tables[customers], $tables[memberships] WHERE $tables[customers].customer_id='$warehouse' AND $tables[customers].membership_id = $tables[memberships].membership_id");
		}

		$distribution = $var_dirs['files'].$distribution;

		$remote_file = false;
		$fd = cw_fopen($distribution, "rb");
	}
	else {
		$remote_file = true;
		$fd = fopen($distribution, "rb");
	}

	if ($fd) {

		$fname = basename($distribution);

		header("Content-type: application/force-download");
		header("Content-Disposition: attachment; filename=\"$fname\"");

		if (!$remote_file) {
			$size = filesize($distribution);
			header("Content-length: $size");
		}

		fpassthru($fd);

		fclose ($fd);
	}
	else {
		# If no such distributive
		$smarty->assign('product', $result['product']);

		# Assign the current location line
		

		cw_display("addons/egoods/no_distributive.tpl",$smarty);
		exit();
	}
}
else {
	db_query("DELETE FROM $tables[download_keys] WHERE expires <= '".time()."'");

	# Assign the current location line
	

	cw_display("addons/egoods/wrong_key.tpl",$smarty);
	exit;
}
?>
