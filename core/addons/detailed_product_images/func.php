<?php

if (!defined('APP_START')) die('Access denied');


function cw_dpi_check_viewers($_addon = null) {
	global $smarty, $tables, $config;

	if (empty($_addon)) {
		return false;
	}

	$_addon = (string)$_addon;

	$result = true;

	global $addon_skin_dir;
	$addon_skin_dir = end($smarty->template_dir) . DIRECTORY_SEPARATOR . 'addons' . DIRECTORY_SEPARATOR . $_addon . DIRECTORY_SEPARATOR . 'viewers' . DIRECTORY_SEPARATOR;
	$addon_skin_dir = str_replace(array('//', '\\\\'), array('/', '\\'), $addon_skin_dir);

	$available_viewers = glob($addon_skin_dir . '*', GLOB_ONLYDIR|GLOB_NOSORT);

	do {

		if (empty($available_viewers) || !is_array($available_viewers)) {
			$result = false;
			break;
		}

		$_available_viewers = str_replace($addon_skin_dir, '', $available_viewers);
		$_available_viewers = array_flip($_available_viewers);
		foreach ($_available_viewers as $key => $value) {
			$_available_viewers[$key] = $available_viewers[$value];
		}
		$available_viewers = $_available_viewers;
		unset($_available_viewers);


		$available_viewers_str = array_map('cw_dpi_process_items', array_keys($available_viewers));
		$available_viewers_str = implode("\n", cw_addslashes($available_viewers_str));

		$_current_viewer = $config[$_addon]['dpi_images_viewer'];

		if (!isset($available_viewers[$_current_viewer]) || empty($available_viewers[$_current_viewer])) {
			$_current_viewer = array_shift(array_keys($available_viewers));
			db_query("UPDATE $tables[config] SET variants = '' WHERE name = 'dpi_theme'");
			$config[$_addon]['dpi_theme'] = null;
		}

		db_query("UPDATE $tables[config] SET value = '" . addslashes($_current_viewer) . "', variants = '$available_viewers_str' WHERE name = 'dpi_images_viewer'");
		$config[$_addon]['dpi_images_viewer'] = $_current_viewer;

		if (!isset($available_viewers[$_current_viewer]) || empty($available_viewers[$_current_viewer])) {
			$result = false;
			break;
		}


		$viewer_dir = $available_viewers[$_current_viewer] . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR;

		$available_themes = glob($viewer_dir . '*', GLOB_ONLYDIR|GLOB_NOSORT);
		$available_themes_str = null;

		$_current_theme = null;

		if (!empty($available_themes) && is_array($available_themes)) {
			$available_themes = str_replace($viewer_dir, '', $available_themes);
			$available_themes_str = array_map('cw_dpi_process_items', $available_themes);
			$available_themes_str = implode("\n", cw_addslashes($available_themes_str));

			$_current_theme = $config[$_addon]['dpi_theme'];

			if (empty($_current_theme) || !in_array($_current_theme, $available_themes)) {
				$_current_theme = array_shift($available_themes);
			}
		}

		db_query("UPDATE $tables[config] SET value = '" . addslashes($_current_theme) . "', variants = '$available_themes_str' WHERE name = 'dpi_theme'");
		$config[$_addon]['dpi_theme'] = $_current_theme;


	} while (0);

	return $result;
}



function cw_dpi_process_items($viewer) {
	return "$viewer:lbl_dpi_$viewer";
}


function cw_dpi_refresh($product_id) {
	global $app_catalogs, $target, $ge_id;

	$productid_url_param = null;

    if (!empty($product_id)) {
        $product_id = (int)$product_id;
        $productid_url_param = '&product_id=' . $product_id;
    }

    $ge_id_url_param = null;

    if (!empty($ge_id)) {
        $ge_id = (int)$ge_id;
        $ge_id_url_param = '&ge_id=' . $ge_id;
    }

    if (!empty($product_id)) {
		cw_header_location("$app_catalogs[admin]/index.php?target=$target&mode=details&js_tab=dpi$productid_url_param$ge_id_url_param");
    } else {
		cw_header_location("$app_catalogs[admin]/index.php?target=$target&mode=add");
    }
}
?>
