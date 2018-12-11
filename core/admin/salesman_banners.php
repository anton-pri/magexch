<?php
if (!$addons['Salesman'])
    cw_header_location('index.php');

cw_load('files');

if ($userfile_type != "application/x-shockwave-flash" && substr($userfile_name, -4) == '.swf')
	$userfile_type = "application/x-shockwave-flash";

if ($action == 'close') {
	$banner_type = '';
	$mode = 'close';
	$banner_id = '';
}
elseif ($action == "upload" && cw_is_image_userfile($userfile, $userfile_size, $userfile_type)) {

	$userfile = cw_move_uploaded_file("userfile");
	list($img_size, $img_width, $img_height) = cw_get_image_size($userfile);

	$image = addslashes(cw_file_get($userfile, true));

	if (!is_numeric($image_width) || (!is_numeric($image_height)) || ($image_width < 1) || ($image_height < 1)) {
		if ($img_width && $img_height) {
			$image_width = $img_width;
			$image_height = $img_height;
		}
		elseif($width && $height) {
			$image_width = $width;
			$image_height = $height;
		}
	}

	db_query("INSERT INTO $tables[salesman_banners_elements] (data, data_type, data_x, data_y) VALUES ('$image', '$userfile_type', '$image_width', '$image_height')");
	@unlink($userfile);
	$banner_type = "M";
}
elseif ($action == 'add' && $add && $add['banner']) {
	if ($add['banner_type'] == 'G') {
		$userfile = cw_move_uploaded_file("userfile");
		$fp = cw_fopen($userfile, "rb", true);
		if ($fp !== false) {
			$add['body'] = addslashes(fread($fp, filesize($userfile)));
			$add['image_type'] = $userfile_type;
			fclose($fp);
		}
	}

    $fields = array('banner', 'banner_type', 'avail', 'is_image', 'is_name', 'is_descr', 'is_add', 'banner_type', 'open_blank', 'legend', 'alt', 'direction', 'banner_x', 'banner_y');
    if ($banner_id) {
        $add['banner_id'] = $banner_id;
        $fields[] = 'banner_id';
    }
    if ($add['body']) {
        $fields[] = 'body';
        $fields[] = 'image_type';
	}

    cw_array2insert('salesman_banners', $add, 1, $fields);
	cw_header_location("index.php?target=salesman_banners&banner_type=$banner_type&banner_id=$banner_id");
}
elseif ($action == "delete") {
	if ($banner_id) {
		db_query ("DELETE FROM $tables[salesman_banners] WHERE banner_id = '$banner_id'");
	}
	elseif ($elementid) {
		db_query("DELETE FROM $tables[salesman_banners_elements] WHERE elementid = '$elementid'");
		cw_header_location("index.php?target=salesman_element_list");
	}
}

if (!empty($action)) {
	cw_header_location("index.php?target=salesman_banners".($banner_type?"&banner_type=".$banner_type:""));
}

if ($banner_id) {
	$banner = cw_query_first("SELECT * FROM $tables[salesman_banners] WHERE banner_id = '$banner_id'");
	$smarty->assign ("banner", $banner);
	$banner_type = $banner['banner_type'];
}

$banners = cw_query("select * from $tables[salesman_banners]");
$smarty->assign("banners", $banners);

$elements = cw_query("select elementid, data_type from $tables[salesman_banners_elements] ORDER BY elementid");
$smarty->assign ("elements", $elements);

$smarty->assign('main', 'banners');
