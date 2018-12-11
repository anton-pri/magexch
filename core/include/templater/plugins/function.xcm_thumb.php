<?php

/*!!! check the "allow_url_fopen" option in php.ini file, the setting should equal to '1' */

function xcm_get_vars () {

	$res = array();

	$res["xcm_thumb_self_install"] = true;


	$res["xcm_thumb_appname"] = "cartworks";


	if ($res["xcm_thumb_appname"] == 'xcart') {

		$res["xcm_thumb_cache_tbl"] = "xcart_xcm_cached_images";
		$_smarty_category = 'General';
		$_detailed_images_category = 'General';

	} elseif ($res["xcm_thumb_appname"] == 'cartworks') {

		$res["xcm_thumb_cache_tbl"] = 'cw_xcm_cached_images';
		$_smarty_category = xcm_get_smarty_category_name();
		$_detailed_images_category = xcm_get_detimages_category_name();
	}


	$res["xcm_thumb_def_config"] = array(
        "keep_orig_name" => array(
                "category" => $_smarty_category,
                "comment" => "Include original image name into thumbnail image file (when you change this setting please delete manually all cached thumbnails, this will allow script to regenerate new files which are named according to this setting)",
                "value" => "N",
                "orderby" => "1060",
                "type" => "checkbox"
        ),
        "xcm_jpeg_quality" => array(
                "category" => $_smarty_category,
                "comment" => "JPEG Thumbnails quality rate (0-100)",
                "value" => "80",
                "orderby" => "1070",
                "type" => "text"
        ),
        "imagemagick_bin_path" => array(
                "category" => $_smarty_category,
                "comment" => "Location of ImageMagick command line tools",
                "value" => "/usr/local/bin",
                "orderby" => "1100",
                "type" => "text"
        ),
        "force_fs_path" => array(
                "category" => $_smarty_category,
                "comment" => "Convert images URLs to server filesystem names before processing them with xcm_thumb script",
                "value" => "Y",
                "orderby" => "1065",
                "type" => "checkbox"
        ),
        "trust_remote_urls" => array(
                "category" => $_smarty_category,
                "comment" => "Trust remote image URLs and do not check access additionally (speed up option)",
                "value" => "Y",
                "orderby" => "1070",
                "type" => "checkbox"
        ),        
        "xcm_watermark_on" => array(
                "category" => $_detailed_images_category,
                "comment" => "Place watermark at images processed with xcm_thumb plugin",
                "value" => "N",
                "orderby" => "1150",
                "type" => "checkbox"
        ),
        "xcm_watermark_mode" => array(
                "category" => $_detailed_images_category,
                "comment" => "Watermark type",
                "value" => "text",
                "orderby" => "1160",
                "type" => "selector",
                "variants"=> "text:lbl_text\npng24:lbl_24_bit_png"
        ),
        "xcm_watermark_text" => array(
                "category" => $_detailed_images_category,
                "comment" => "Watermark text (applicable for text type watermark)",
                "value" => "Company Name",
                "orderby" => "1165",
                "type" => "text"
        ),
        "xcm_watermark_font" => array(
                "category" => $_detailed_images_category,
                "comment" => "Watermark font (applicable for text type watermark)",
                "value" => "skins/addons/detailed_product_images/fonts/arialbd.ttf",
                "orderby" => "1170",
                "type" => "text"
        ),
        "xcm_watermark_hexcolor" => array(
                "category" => $_detailed_images_category,
                "comment" => "Watermark color (applicable for text type watermark)",
                "value" => "#FFFFFF",
                "orderby" => "1175",
                "type" => "text"
        ),
        "xcm_watermark_image" => array(
                "category" => $_detailed_images_category,
                "comment" => "Watermark image file path (can be specified file of any graphic format supported with ImageMagick <a target=\'_window\' style=\"color:blue\" href=\'http://www.imagemagick.org/script/formats.php\'>\"convert\"</a> command)",
                "value" => "skins/addons/detailed_product_images/images/copyright.png",
                "orderby" => "1185",
                "type" => "text"
        ),
        "xcm_watermark_orientation" => array(
                "category" => $_detailed_images_category,
                "comment" => "Watermark text orientation",
                "value" => "horizontal",
                "orderby" => "1190",
                "type" => "selector",
                "variants" => "horizontal:lbl_dpi_horizontal\ndiagonal:lbl_dpi_diagonal\nvertical1:lbl_dpi_vertical1\nvertical2:lbl_dpi_vertical2"
        ),
        "xcm_watermark_h_align" => array(
                "category" => $_detailed_images_category,
                "comment" => "Watermark horizontal alignment",
                "value" => "right",
                "orderby" => "1191",
                "type" => "selector",
                "variants" => "right:lbl_right\ncenter:lbl_center\nleft:lbl_left"
        ),
        "xcm_watermark_v_align" => array(
                "category" => $_detailed_images_category,
                "comment" => "Watermark vertical alignment",
                "value" => "bottom",
                "orderby" => "1192",
                "type" => "selector",
                "variants" => "bottom:lbl_bottom\ncenter:lbl_center\ntop:lbl_top"
        ),
        "xcm_watermark_alpha" => array(
                "category" => $_detailed_images_category,
                "comment" => "Watermark transparency (0-100)",
                "value" => "50",
                "orderby" => "1200",
                "type" => "text"
        ),
        "xcm_watermark_scale" => array(
                "category" => $_detailed_images_category,
                "comment" => "Watermark image scale, % of watermarked image size",
                "value" => "35",
                "orderby" => "1210",
                "type" => "text"
        ),
        "cached_thumbs_dir_limit" => array(
                "category" => $_smarty_category,
                "comment" => "Max size of thumbnails cache directory, Megs",
                "value" => "100",
                "orderby" => "1052",
                "type" => "text"
        ),
        "cached_thumbs_dir" => array(
                "category" => $_smarty_category,
                "comment" => "Store cached resized thumbs in directory",
                "value" => "files/images/cached_thumbs",
                "orderby" => "1051",
                "type" => "text"
        )
);

	$res["xcm_thumb_di_only_config"] = array("xcm_watermark_on", "xcm_watermark_mode", "xcm_watermark_text", "xcm_watermark_font", "xcm_watermark_hexcolor", "xcm_watermark_orientation", "xcm_watermark_image", "xcm_watermark_h_align", "xcm_watermark_v_align", "xcm_watermark_alpha", "xcm_watermark_scale");

	return $res;
}

function xcm_create_cache_dir ($real_cache_path) {

        $result = false;

        if (mkdir($real_cache_path)) {
            $fp = fopen("$real_cache_path/.htaccess", 'w');
            fwrite($fp, 'Allow from all');
            fclose($fp);
            $result = true;
        }
        return $result;
}

function xcm_slash_sum ($a, $b) {
        return rtrim($a,"/")."/".ltrim($b, "/");
}

function xcm_get_curr_location () {
        //global $xcm_thumb_appname;
        $xcm_vars = xcm_get_vars();
        extract($xcm_vars);

        $result = "";
        if ($xcm_thumb_appname == "xcart") {
                global $http_location, $https_location, $_SERVER;
                $result = (!empty($_SERVER['HTTPS']))?$https_location:$http_location;
        } elseif ($xcm_thumb_appname == "cartworks") {
                global $current_location;
                $result = $current_location;
        }
        return $result;
}

function xcm_get_dir () {
        //global $xcm_thumb_appname;
        $xcm_vars = xcm_get_vars();
        extract($xcm_vars);

        $result = "";
        if ($xcm_thumb_appname == "xcart") {
                global $xcart_dir;
                $result = $xcart_dir;
        } elseif ($xcm_thumb_appname == "cartworks") {
                global $app_main_dir, $app_dir;
                $result = $app_dir;
        }
        return $result;
}

function xcm_get_host () {
        //global $xcm_thumb_appname;
        $xcm_vars = xcm_get_vars();
        extract($xcm_vars);

        $result = "";
        if ($xcm_thumb_appname == "xcart") {
                global $xcart_http_host, $xcart_https_host, $_SERVER;
                $result = (!empty($_SERVER['HTTPS']))?$xcart_https_host:$xcart_http_host;
        } elseif ($xcm_thumb_appname == "cartworks") {
                global $app_config_file, $HTTPS;
                $result = $HTTPS ? $app_config_file['web']['https_host'] : $app_config_file['web']['http_host'];

        }
        return $result;
}

function xcm_url_to_fs ($url) {

        $app_location = xcm_get_curr_location();
        $app_fs_dir = xcm_get_dir();

        return urldecode(str_replace($app_location, $app_fs_dir, $url));
}

function xcm_get_config($var_name) {
        //global $xcm_thumb_def_config;
        //global $xcm_thumb_appname;
        $xcm_vars = xcm_get_vars();
        extract($xcm_vars);

        if ($xcm_thumb_appname == "xcart") {
                global $config;
				$category = $xcm_thumb_def_config[$var_name]["category"];
                $result = $config[$category][$var_name];
        } elseif ($xcm_thumb_appname == "cartworks") {
				global $config;
				$category = $xcm_thumb_def_config[$var_name]["category"];
                $result = $config[$category][$var_name];
        } else {
                $result = $xcm_thumb_def_config[$var_name]["value"];
        }

        if ($var_name == "xcm_jpeg_quality") {
                if (intval($result) < 0) $result = 1;
                if (intval($result) > 100) $result = 100;
        }

        return $result;
}

function xcm_install_config($var_name) {
        //global $xcm_thumb_def_config;
        //global $xcm_thumb_appname;

        $xcm_vars = xcm_get_vars();
        extract($xcm_vars);

        if ($xcm_thumb_appname == "xcart") {
                global $config;
                $category = $xcm_thumb_def_config[$var_name]["category"];
                $comment = $xcm_thumb_def_config[$var_name]["comment"];
                $value = $xcm_thumb_def_config[$var_name]["value"];
                $orderby = $xcm_thumb_def_config[$var_name]["orderby"];
                $type = $xcm_thumb_def_config[$var_name]["type"];
                $variants = $xcm_thumb_def_config[$var_name]["variants"];

                if (in_array($var_name, $xcm_thumb_di_only_config) && !isset($config["Dynamic_Images_3"]["det_image_thumbnails"]))
                        return;

                if (!isset($config[$category][$var_name])) {
                        db_query("INSERT INTO xcart_config SET name='$var_name', comment='$comment', value='$value', category='$category', orderby='$orderby', type='$type', variants='$variants'");
                        $config[$category][$var_name] = $value;
                }
        }elseif ($xcm_thumb_appname == "cartworks") {
                global $config, $tables;
                $category = $xcm_thumb_def_config[$var_name]["category"];
                $comment = $xcm_thumb_def_config[$var_name]["comment"];
                $value = $xcm_thumb_def_config[$var_name]["value"];
                $orderby = $xcm_thumb_def_config[$var_name]["orderby"];
                $type = $xcm_thumb_def_config[$var_name]["type"];
                $variants = $xcm_thumb_def_config[$var_name]["variants"];

               	if (in_array($var_name, $xcm_thumb_di_only_config) && !isset($addons[xcm_get_detimages_category_name()])) {
                        return;
                }

                if (!isset($config[$category][$var_name])) {
                        db_query("INSERT INTO $tables[config] SET name='$var_name', comment='$comment', value='$value', config_category_id='" . xcm_set_config_category_id($category) . "', orderby='$orderby', type='$type', variants='$variants'");
                        $config[$category][$var_name] = $value;
                }
        }
}

function xcm_install_all_config() {
		static $_installed;
        //global $xcm_thumb_def_config;
        //global $xcm_thumb_appname;

		if (isset($_installed)) {
			return true;
		}

        $xcm_vars = xcm_get_vars();
        extract($xcm_vars);


        if ($xcm_thumb_appname == "cartworks") {
        	global $config, $sql_db, $tables, $app_config_file, $app_main_dir, $addons;

			if (isset($tables[$xcm_thumb_cache_tbl])) {
				$_installed = true;
				return true;
			}
        }


        foreach ($xcm_thumb_def_config as $var_name=>$var_data) {
//                xcm_install_config($var_name);

		        if ($xcm_thumb_appname == "xcart") {
		                global $config;
		                $category = $xcm_thumb_def_config[$var_name]["category"];
		                $comment = $xcm_thumb_def_config[$var_name]["comment"];
		                $value = $xcm_thumb_def_config[$var_name]["value"];
		                $orderby = $xcm_thumb_def_config[$var_name]["orderby"];
		                $type = $xcm_thumb_def_config[$var_name]["type"];
		                $variants = $xcm_thumb_def_config[$var_name]["variants"];

		                if (in_array($var_name, $xcm_thumb_di_only_config) && !isset($config["Dynamic_Images_3"]["det_image_thumbnails"]))
		                        continue;

		                if (!isset($config[$category][$var_name])) {
		                        db_query("INSERT INTO xcart_config SET name='$var_name', comment='$comment', value='$value', category='$category', orderby='$orderby', type='$type', variants='$variants'");
		                        $config[$category][$var_name] = $value;
		                }
		        }elseif ($xcm_thumb_appname == "cartworks") {

		                if (isset($config[$category][$var_name])) {
		                	continue;
		                }

		                $category = $xcm_thumb_def_config[$var_name]["category"];
		                $comment = $xcm_thumb_def_config[$var_name]["comment"];
		                $value = $xcm_thumb_def_config[$var_name]["value"];
		                $orderby = $xcm_thumb_def_config[$var_name]["orderby"];
		                $type = $xcm_thumb_def_config[$var_name]["type"];
		                $variants = $xcm_thumb_def_config[$var_name]["variants"];

		                if (in_array($var_name, $xcm_thumb_di_only_config) && !isset($addons[xcm_get_detimages_category_name()])) {
		                        continue;
		                }

		                if (!isset($config[$category][$var_name])) {
		                        db_query("INSERT INTO $tables[config] SET name='$var_name', comment='$comment', value='$value', config_category_id='" . xcm_set_config_category_id($category) . "', orderby='$orderby', type='$type', variants='$variants'");
		                        $config[$category][$var_name] = $value;
		                }
		        }
        }



        if ($xcm_thumb_appname == "xcart") {
                global $sql_db;
                if (!cw_query_first_cell("show tables where Tables_in_".$sql_db." = '$xcm_thumb_cache_tbl'")) {
                        db_query("CREATE TABLE $xcm_thumb_cache_tbl (imageid int(11) NOT NULL auto_increment,image_path varchar(255) NOT NULL default '',image_type varchar(64) NOT NULL default 'image/jpeg',image_x int(11) NOT NULL default '0',image_y int(11) NOT NULL default '0',image_size int(11) NOT NULL default '0',filename varchar(255) NOT NULL default '',date int(11) NOT NULL default '0',md5key varchar(32) NOT NULL default '',PRIMARY KEY (imageid),KEY image_path (image_path), KEY md5key (md5key)) TYPE=MyISAM;");
                }

                //Setting up the new separator line at the General settings page
                if (!cw_query_first_cell("SELECT COUNT(*) FROM xcart_config WHERE name='xcm_thumb_sep'")) {
                        db_query("INSERT INTO xcart_config SET name='xcm_thumb_sep', comment='CartWorks.com xcm_thumb Smarty plugin options', value='', category='General', orderby='1050', type='separator', defvalue='', variants=''");
                }

                //adding the clear cache link to the comment of the cached_thumbs_dir config variable
                global $xcart_dir;
                $comment_no_clear = "Store cached resized thumbs in directory";
                $comment_clear_cache = "Store cached resized thumbs in directory <br /><a style='color: #0000ff; text-decoration: underline;' href='xcm_thumb_clear.php'>Clear cache dir</a>";

                $cached_thumbs_dir_comment = cw_query_first_cell("SELECT comment FROM xcart_config WHERE name='cached_thumbs_dir'");
                if (file_exists($xcart_dir."/admin/xcm_thumb_clear.php")) {
                        if ($cached_thumbs_dir_comment != $comment_clear_cache)
                                db_query("UPDATE xcart_config SET comment='".addslashes($comment_clear_cache)."' WHERE name='cached_thumbs_dir'");
                } else {
                        if ($cached_thumbs_dir_comment != $comment_no_clear)
                                db_query("UPDATE xcart_config SET comment='$comment_no_clear' WHERE name='cached_thumbs_dir'");

                }
        }elseif ($xcm_thumb_appname == "cartworks") {

                if (!cw_query_first_cell("show tables where Tables_in_".$app_config_file[sql][db]." = '$xcm_thumb_cache_tbl'")) {
                        db_query("CREATE TABLE $xcm_thumb_cache_tbl (imageid int(11) NOT NULL auto_increment,image_path varchar(255) NOT NULL default '',image_type varchar(64) NOT NULL default 'image/jpeg',image_x int(11) NOT NULL default '0',image_y int(11) NOT NULL default '0',image_size int(11) NOT NULL default '0',filename varchar(255) NOT NULL default '',date int(11) NOT NULL default '0',md5key varchar(32) NOT NULL default '',PRIMARY KEY (imageid),KEY image_path (image_path), KEY md5key (md5key)) TYPE=MyISAM;");
                }


                $current_language = $config['default_customer_language'];
    			$all_languages = cw_query_hash("select ls.*, lng.value as language from $tables[languages_settings] as ls left join $tables[languages] as lng ON lng.code = '$current_language' and lng.name = CONCAT('language_', ls.code) where ls.enable=1", 'code', false);

    			if (is_array($all_languages)) {
    				$lang_cat_name = "option_title_" . xcm_get_smarty_category_name();
    				$lang_opt_name = 'opt_xcm_thumb_sep';
    				foreach ($all_languages as $lang => $lang_data) {
    					if (!cw_query_first_cell("SELECT name FROM $tables[languages] WHERE code = '$lang' and name = '$lang_cat_name'")) {
    						db_query("INSERT INTO $tables[languages] SET code = '$lang', name = '$lang_cat_name', value = 'Smarty plugins options', topic = 'Options'");
    					}
    					if (!cw_query_first_cell("SELECT name FROM $tables[languages] WHERE code = '$lang' and name = '$lang_opt_name'")) {
    						db_query("INSERT INTO $tables[languages] SET code = '$lang', name = '$lang_opt_name', value = 'Image thumbnails options', topic = 'Options'");
    					}
    				}
    			}

                //Setting up the new separator line at the General settings page
                if (!cw_query_first_cell("SELECT COUNT(*) FROM $tables[config] WHERE name='xcm_thumb_sep'")) {
                        db_query("INSERT INTO $tables[config] SET name='xcm_thumb_sep', comment='Image thumbnails options', value='', config_category_id='" . xcm_set_config_category_id() . "', orderby='1050', type='separator', defvalue='', variants=''");
                }

                //adding the clear cache link to the comment of the cached_thumbs_dir config variable
                $comment_no_clear = "Store cached resized thumbs in directory";
                $comment_clear_cache = "Store cached resized thumbs in directory <br /><a style='color: #0000ff; text-decoration: underline;' href='index.php?target=thumbs_clear'>Clear cache dir</a>";

                $cached_thumbs_dir_comment = cw_query_first_cell("SELECT comment FROM $tables[config] WHERE name='cached_thumbs_dir'");
                if (file_exists($app_main_dir."/admin/thumbs_clear.php")) {
                        if ($cached_thumbs_dir_comment != $comment_clear_cache)
                                db_query("UPDATE $tables[config] SET comment='".addslashes($comment_clear_cache)."' WHERE name='cached_thumbs_dir'");
                } else {
                        if ($cached_thumbs_dir_comment != $comment_no_clear)
                                db_query("UPDATE $tables[config] SET comment='$comment_no_clear' WHERE name='cached_thumbs_dir'");

                }
        }

        $_installed = true;
}

function xcm_extra_img_code($params) {

        $extra_img_code = "";

        if (!empty($params['extra'])) {
                $extra_img_code = $params['extra'];
        }
        $extra_html_params = array("title", "class", "style", "id", "alt");

        foreach ($extra_html_params as $p_name) {
                if (!empty($params[$p_name]))
                        $extra_img_code .= " $p_name=\"".$params[$p_name]."\"";
        }

        return $extra_img_code;
}

function xcm_cache_insert ($ins_arr) {

        //global $xcm_thumb_appname, $xcm_thumb_cache_tbl;
        $xcm_vars = xcm_get_vars();
        extract($xcm_vars);

        if ($xcm_thumb_appname == "xcart") {
                global $tables;
                $tables['cached_images'] = $xcm_thumb_cache_tbl;
                cw_array2insert('cached_images',$ins_arr);
        }elseif ($xcm_thumb_appname == "cartworks") {
                global $tables;
                $tables['cached_images'] = $xcm_thumb_cache_tbl;
                cw_array2insert('cached_images',$ins_arr);
        }
}

function xcm_cache_delete ($where_cond) {

        //global $xcm_thumb_appname, $xcm_thumb_cache_tbl;
        $xcm_vars = xcm_get_vars();
        extract($xcm_vars);

        if ($xcm_thumb_appname == "xcart") {
                db_query("DELETE FROM $xcm_thumb_cache_tbl WHERE $where_cond");
        }elseif ($xcm_thumb_appname == "cartworks") {
                db_query("DELETE FROM $xcm_thumb_cache_tbl WHERE $where_cond");
        }
}

function xcm_db_cell ($sql) {
        //global $xcm_thumb_appname;

        $xcm_vars = xcm_get_vars();
        extract($xcm_vars);

        $result = "";
        if ($xcm_thumb_appname == "xcart") {
                $result = cw_query_first_cell($sql);
        }elseif ($xcm_thumb_appname == "cartworks") {
                $result = cw_query_first_cell($sql);
        }
        return $result;
}



function xcm_thumb_assign_var ($assign_to, $value, &$smarty) {

        if (strpos($assign_to, ".") !== false) {
            $_names = explode('.',$assign_to);
            if (count($_names) == 2) {
                $main_obj_name = $_names[0];
                $sub_obj_name = $_names[1];
                $main_obj = $smarty->get_template_vars($main_obj_name);
                $main_obj[$sub_obj_name] = $value;
                $smarty->assign($main_obj_name, $main_obj);
            } elseif (count($_names) == 3) {
                $main_obj_name = $_names[0];
                $sub_obj_index = $_names[1];
                $sub_obj_name = $_names[2];
                $main_obj = $smarty->get_template_vars($main_obj_name);
                $main_obj[$sub_obj_index][$sub_obj_name] = $value;
                $smarty->assign($main_obj_name, $main_obj);
            }
        } else {
            $smarty->assign($assign_to, $value);
        }
}


function xcm_thumb_prepare_output($img_src, $thumb_width, $params, &$smarty, $err_str = "") {

    $app_fs_dir = xcm_get_dir();
    $app_location = xcm_get_curr_location();

    $assign_return = false;

    if (!empty($params['assign_url'])) {
	xcm_thumb_assign_var($params['assign_url'], $img_src, $smarty);
	$assign_return = true;
    }

    if (!empty($params['assign_x']) || !empty($params['assign_y'])) {
        $fs_img_src = urldecode(str_replace($app_location, $app_fs_dir, $img_src));
        list($img_x, $img_y) = @getimagesize($fs_img_src);

        if (!empty($params['assign_x'])) {
            xcm_thumb_assign_var($params['assign_x'], $img_x, $smarty);
        }

        if (!empty($params['assign_y'])) {
            xcm_thumb_assign_var($params['assign_y'], $img_y, $smarty);
        }

	$assign_return = true;

    }

    if ($assign_return) return "";

    $result = "";

    if ($params['just_url'] == "Y") {
        if ($params['addslashes'] == "Y") {
            $result = addslashes($img_src);
        } else {
            $result = $img_src;
        }
    } else {
        if (!empty($err_str)) {
            $result = "<!--ERROR: $err_str -->";
        }
	$extra_img_code = xcm_extra_img_code($params);
        $result .= "<img src='$img_src' width='$thumb_width' $extra_img_code />";
    }
    return $result;
}

function xcm_thumb_create_watermark_text( $main_img_obj, $text, $font, $r = 128, $g = 128, $b = 128, $alpha_level = 100 )
{
    $width = imagesx($main_img_obj);
    $height = imagesy($main_img_obj);

    $orig_width = $width;
    $orig_height = $height;


    $align_h = xcm_get_config("xcm_watermark_h_align");
    $align_v = xcm_get_config("xcm_watermark_v_align");

	if (xcm_get_config("xcm_watermark_scale") > 0) {
		$width = $orig_width*intval(xcm_get_config("xcm_watermark_scale"))/100;
		$height = $orig_height*intval(xcm_get_config("xcm_watermark_scale"))/100;
	}

    if (xcm_get_config("xcm_watermark_orientation") == "diagonal") {
        $angle =  -rad2deg(atan2((-$height),($width)));
    } elseif (xcm_get_config("xcm_watermark_orientation") == "vertical1") {
        $angle = 90;
    } elseif (xcm_get_config("xcm_watermark_orientation") == "vertical2") {
        $angle = -90;
    } else {
        $angle = 0;
    }

//    $text = " ".$text." ";

    $c = imagecolorallocatealpha($main_img_obj, $r, $g, $b, $alpha_level);

    if (xcm_get_config("xcm_watermark_orientation") == "diagonal") {
        $size = (($width+$height)/2)*2/strlen($text);
        $align_h = 'center';
        $align_v = 'center';
    } elseif (xcm_get_config("xcm_watermark_orientation") == "vertical1") {
        $size = 1.5*($height)/strlen($text);
    } elseif (xcm_get_config("xcm_watermark_orientation") == "vertical2") {
        $size = 1.5*($height)/strlen($text);
    } else {
        $size = 1.27*($width)/strlen($text);
    }

    $box  = imagettfbbox ( $size, $angle, $font, $text );

    $x_offs = $orig_width*0.03;
    $y_offs = $x_offs;//$orig_height*0.05;

    if ($align_h == 'right') {

        if (xcm_get_config("xcm_watermark_orientation") == "vertical1") {
            $x = $orig_width - $x_offs;
        } elseif (xcm_get_config("xcm_watermark_orientation") == "vertical2") {
            $x = $orig_width - abs($box[4] - $box[0]) - $x_offs;
        } else {
            $x = $orig_width - abs($box[4] - $box[0]) - $x_offs;
        }
    } elseif ($align_h == 'center') {
        $x = $orig_width/2 - abs($box[4] - $box[0])/2;
        if (xcm_get_config("xcm_watermark_orientation") == "vertical1") {
            $x = $orig_width/2 + abs($box[4] - $box[0])/2;
        }
    } else {
        if (xcm_get_config("xcm_watermark_orientation") == "vertical1") {
            $x = $x_offs + abs($box[4] - $box[0]);
        } elseif (xcm_get_config("xcm_watermark_orientation") == "vertical2") {
            $x = $x_offs;
        } else {
            $x = $x_offs;
        }
    }

    if ($align_v == 'bottom') {

        if (xcm_get_config("xcm_watermark_orientation") == "vertical1") {
            $y = $orig_height - $y_offs;
        } elseif (xcm_get_config("xcm_watermark_orientation") == "vertical2") {
            $y = $orig_height - abs($box[5] - $box[1]) - $y_offs;
        } else {
            $y = $orig_height - $y_offs;
        }
    } elseif ($align_v == 'center') {
        $y = $orig_height/2 + abs($box[5] - $box[1])/2;
    } else {
        if (xcm_get_config("xcm_watermark_orientation") == "vertical1") {
            $y = $y_offs + abs($box[5] - $box[1]);
        } elseif (xcm_get_config("xcm_watermark_orientation") == "vertical2") {
            $y = $y_offs;
        } else {
            $y = $y_offs + abs($box[5] - $box[1]);
        }
    }

    imagettftext($main_img_obj,$size ,$angle, $x, $y, $c, $font, $text);

    return $main_img_obj;
}


function xcm_thumb_create_watermark_png8( $main_img_obj, $watermark_img_obj, $alpha_level = 100)
{

    $align_h = xcm_get_config("xcm_watermark_h_align");
    $align_v = xcm_get_config("xcm_watermark_v_align");

    $watermark_width = imagesx($watermark_img_obj);
    $watermark_height = imagesy($watermark_img_obj);

    if ($align_h == 'right')
        $dest_x = imagesx($main_img_obj) - $watermark_width - 5;
    elseif ($align_h == 'center')
        $dest_x = floor( ( imagesx($main_img_obj) / 2 ) - ( $watermark_width / 2 ) );
    else
        $dest_x = 5;

    if ($align_v == 'bottom')
        $dest_y = imagesy($main_img_obj) - $watermark_height - 5;
    elseif ($align_v == 'center')
        $dest_y = floor( ( imagesy($main_img_obj) / 2 ) - ( $watermark_height / 2 ) );
    else
        $dest_y = 5;

    imagecopymerge($main_img_obj, $watermark_img_obj, $dest_x, $dest_y, 0, 0, $watermark_width, $watermark_height, $alpha_level);

    return $main_img_obj;
}

# given two images, return a blended watermarked image
function xcm_thumb_create_watermark_png24( $main_img_obj, $watermark_img_obj, $alpha_level = 100)
{

         $align_h = xcm_get_config("xcm_watermark_h_align");
         $align_v = xcm_get_config("xcm_watermark_v_align");
         $alpha_level /= 100; # convert 0-100 (%) alpha to decimal

         # calculate our images dimensions
         $main_img_obj_w = imagesx( $main_img_obj );
         $main_img_obj_h = imagesy( $main_img_obj );
         $watermark_img_obj_w    = imagesx( $watermark_img_obj );
         $watermark_img_obj_h    = imagesy( $watermark_img_obj );

         # determine center position coordinates
        if ($align_h == 'right') {
            $main_img_obj_min_x = $main_img_obj_w - 5 - $watermark_img_obj_w;
            $main_img_obj_max_x = $main_img_obj_w - 5;
        } elseif ($align_h == 'center') {
            $main_img_obj_min_x = floor( ( $main_img_obj_w / 2 ) - ( $watermark_img_obj_w / 2 ) );
            $main_img_obj_max_x = ceil( ( $main_img_obj_w / 2 ) + ( $watermark_img_obj_w / 2 ) );
        } else {
            $main_img_obj_min_x = 5;
            $main_img_obj_max_x = 5 + $watermark_img_obj_w;
        }

        if ($align_v == 'bottom') {
            $main_img_obj_min_y = $main_img_obj_h - 5 - $watermark_img_obj_h;
            $main_img_obj_max_y = $main_img_obj_h - 5;
        } elseif ($align_v == 'center') {
            $main_img_obj_min_y = floor( ( $main_img_obj_h / 2 ) - ( $watermark_img_obj_h / 2 ) );
            $main_img_obj_max_y = ceil( ( $main_img_obj_h / 2 ) + ( $watermark_img_obj_h / 2 ) );
        } else {
            $main_img_obj_min_y = 5;
            $main_img_obj_max_y = 5 + $watermark_img_obj_h;
        }

         # create new image to hold merged changes
         $return_img = imagecreatetruecolor( $main_img_obj_w, $main_img_obj_h );

         # walk through main image
         for( $y = 0; $y < $main_img_obj_h; $y++ ) {
             for( $x = 0; $x < $main_img_obj_w; $x++ ) {
                 $return_color   = NULL;

                 # determine the correct pixel location within our watermark
                 $watermark_x    = $x - $main_img_obj_min_x;
                 $watermark_y    = $y - $main_img_obj_min_y;

                 # fetch color information for both of our images
                 $main_rgb = imagecolorsforindex( $main_img_obj, imagecolorat( $main_img_obj, $x, $y ) );

                 # if our watermark has a non-transparent value at this pixel intersection
                 # and we're still within the bounds of the watermark image
                 if ($watermark_x >= 0 && $watermark_x < $watermark_img_obj_w &&
                     $watermark_y >= 0 && $watermark_y < $watermark_img_obj_h ) {
                     $watermark_rbg = imagecolorsforindex( $watermark_img_obj, imagecolorat( $watermark_img_obj, $watermark_x, $watermark_y ) );

                     # using image alpha, and user specified alpha, calculate average
                     $watermark_alpha    = round( ( ( 127 - $watermark_rbg['alpha'] ) / 127 ), 2 );
                     $watermark_alpha    = $watermark_alpha * $alpha_level;

                     # calculate the color 'average' between the two - taking into account the specified alpha level
                     $avg_red        = xcm_thumb_get_ave_color( $main_rgb['red'],       $watermark_rbg['red'],      $watermark_alpha );
                     $avg_green  = xcm_thumb_get_ave_color( $main_rgb['green'], $watermark_rbg['green'],    $watermark_alpha );
                     $avg_blue       = xcm_thumb_get_ave_color( $main_rgb['blue'],  $watermark_rbg['blue'],     $watermark_alpha );

                     # calculate a color index value using the average RGB values we've determined
                     $return_color   = xcm_thumb_get_image_color( $return_img, $avg_red, $avg_green, $avg_blue );

                 # if we're not dealing with an average color here, then let's just copy over the main color
                 } else {
                     $return_color   = imagecolorat( $main_img_obj, $x, $y );

                 } # END if watermark

                 # draw the appropriate color onto the return image
                 imagesetpixel( $return_img, $x, $y, $return_color );

             } # END for each X pixel
         } # END for each Y pixel

         # return the resulting, watermarked image for display
         return $return_img;

} # END create_watermark()

# average two colors given an alpha
function xcm_thumb_get_ave_color( $color_a, $color_b, $alpha_level ) {
    return round( ( ( $color_a * ( 1 - $alpha_level ) ) + ( $color_b * $alpha_level ) ) );
} # END _get_ave_color()

# return closest pallette-color match for RGB values
function xcm_thumb_get_image_color($im, $r, $g, $b) {
    $c=imagecolorexact($im, $r, $g, $b);
    if ($c!=-1) return $c;
    $c=imagecolorallocate($im, $r, $g, $b);
    if ($c!=-1) return $c;
    return imagecolorclosest($im, $r, $g, $b);
} # EBD _get_image_color()

function xcm_thumb_HexToRGB($hex) {
    $hex = str_replace("#", "", $hex);
    $color = array();

    if(strlen($hex) == 3) {
        $color['r'] = hexdec(substr($hex, 0, 1) . $r);
        $color['g'] = hexdec(substr($hex, 1, 1) . $g);
        $color['b'] = hexdec(substr($hex, 2, 1) . $b);
    }
    else if(strlen($hex) == 6) {
        $color['r'] = hexdec(substr($hex, 0, 2));
        $color['g'] = hexdec(substr($hex, 2, 2));
        $color['b'] = hexdec(substr($hex, 4, 2));
    }
    return $color;
}

function xcm_thumb_file_accessible($fname) {
	
	static $fcache;
	
	if (!isset($fcache[$fname])) {
	$use_fopen_check = true;
	$result = false;

	if (strpos($fname,'http://')!==false && xcm_get_config('trust_remote_urls')=='Y') {
		$result = true;
	} elseif ($use_fopen_check) {
		if ($handle = @fopen($fname, 'r')) {
			$result = true;
			fclose($handle);
		}
	} else {
		$result = @getimagesize($fname);
	}
	$fcache[$fname] = $result;
	}

	return $fcache[$fname];
}

function xcm_getimagesize($filename) {

    $im_formats = array(
	0=>"undefined",
	1=>"GIF",
	2=>"JPEG",
	3=>"PNG"
    );
    $result = array();
    $img_info = "";

    $is_http_path = (strtolower(substr($filename,0,4)) == "http");

    $imagemagick_bin_path = xcm_get_config("imagemagick_bin_path");

    if (!empty($imagemagick_bin_path) && !$is_http_path) {
	$img_info = shell_exec(xcm_get_config("imagemagick_bin_path")."/identify $filename");
	if (!empty($img_info)) {
		$lines = explode(' ', $img_info);
		$info = array();
		foreach ($lines as $k=>$l) {
			if ($k == 0) {
				list($orig_path, $tmp_path) = explode('=>',$l);
				if (!empty($tmp_path)) {
					$result["access_path"] = $tmp_path;
				} else {
					$result["access_path"] = $filename;
				}
			} elseif ($k == 1) {
	                    foreach ($im_formats as $fid=>$fname) {
               		        if (strpos($l, $fname) !== false) {
	                	    $result[2] = $fid;
               		            break;
	                        }
               		    }
			} elseif ($k == 2) {
				list($w, $h) = explode('x',$l);
		                $result[0] = $w;
		                $result[1] = $h;
			}
		}
	}
    }
    if (empty($img_info)) {
	$result = @getimagesize($filename);
	$result["access_path"] = $filename;
    }
    return $result;
}

function xcm_align2gravity ($halign, $valign) {

    $gravity = array();
    $gravity["left"]["top"] = "NorthWest";
    $gravity["left"]["center"] = "West";
    $gravity["left"]["bottom"] = "SouthWest";
    $gravity["center"]["top"] = "North";
    $gravity["center"]["center"] = "Center";
    $gravity["center"]["bottom"] = "South";
    $gravity["right"]["top"] = "NorthEast";
    $gravity["right"]["center"] = "East";
    $gravity["right"]["bottom"] = "SouthEast";

    if (!in_array($halign, array("left", "center", "right")))
        $halign = "center";
    if (!in_array($valign, array("top", "center", "bottom")))
        $valign = "center";

    return $gravity[$halign][$valign];
}

function xcm_resize_img($output_width, $output_height, $dst_x, $dst_y, $dst_width, $dst_height, $src_img, $result_img, $halign='center', $valign='center')  {

	$result = false;
	@unlink($result_img);

	$imagemagick_bin_path = xcm_get_config("imagemagick_bin_path");

	if (!empty($imagemagick_bin_path)) {

		if (file_exists($src_img)) {

			$exec_line =  xcm_get_config("imagemagick_bin_path")."/convert $src_img -thumbnail '".$dst_width."x".$dst_height."' -quality ".xcm_get_config("xcm_jpeg_quality")." -background white -gravity ".xcm_align2gravity($halign , $valign)." -extent ".$output_width."x".$output_height." $result_img";

			$im_res = shell_exec($exec_line);

			$result = file_exists($result_img);
		}
	}
	if (!$result) {

        list($width_orig, $height_orig, $orig_type) = @getimagesize($src_img);
	    switch ($orig_type) {
            case 1:
            $src_image = imagecreatefromgif($src_img);
            break;
            case 2:
            $src_image = imagecreatefromjpeg($src_img);
            break;
			case 3:
            $src_image = imagecreatefrompng($src_img);
        	break;
		}
		if ($src_image) {
		    $image_p = imagecreatetruecolor($output_width, $output_height);

    	    $white = imagecolorallocate($image_p, 255, 255, 255);
        	imagefill($image_p, 0, 0, $white);
	        imagecopyresampled($image_p, $src_image, $dst_x, $dst_y, 0, 0, $dst_width, $dst_height, $width_orig, $height_orig);
    	    imagejpeg($image_p, $result_img, intval(xcm_get_config("xcm_jpeg_quality")));
        	imagedestroy($image_p);
		}
		$result = file_exists($result_img);
	}
	return $result;
}

function xcm_get_wm_image($width_orig, $height_orig) {

    $app_fs_dir = xcm_get_dir();
    $wm_image = xcm_slash_sum($app_fs_dir, xcm_get_config("xcm_watermark_image"));

    $imagemagick_bin_path = xcm_get_config("imagemagick_bin_path");
    if ($wm_img_info = pathinfo($wm_image)) {
        if (strtolower($wm_img_info['extension']) != "png" && !empty($imagemagick_bin_path)) {
            $wm_image_png = xcm_slash_sum(xcm_slash_sum($app_fs_dir, xcm_get_config("cached_thumbs_dir")), $wm_img_info['filename'].".png");
            $exec_line = xcm_get_config("imagemagick_bin_path")."/convert $wm_image $wm_image_png";
            shell_exec($exec_line);
            if (file_exists($wm_image_png))
                $wm_image = $wm_image_png;
        }
    }

	$water = imagecreatefrompng($wm_image);

	if ($water) {
        if (xcm_get_config("xcm_watermark_scale") > 0) {
	        $max_wm_width = $width_orig*xcm_get_config("xcm_watermark_scale")/100;
            $max_wm_height = $height_orig*xcm_get_config("xcm_watermark_scale")/100;

            $wm_width_orig = imagesx($water);
            $wm_height_orig = imagesy($water);

            $wm_ratio_box = $max_wm_width/$max_wm_height;
            $wm_ratio_orig = $wm_width_orig/$wm_height_orig;

            if ($wm_ratio_orig < $wm_ratio_box) {
//keep height
            	$wm_scaled_width = $max_wm_height*$wm_ratio_orig;
                $wm_scaled_height = $max_wm_height;
            } else {
//keep width
	            $wm_scaled_height = $max_wm_width/$wm_ratio_orig;
                $wm_scaled_width = $max_wm_width;
            }

//preserve alpha
            $water_tmp = imagecreatetruecolor($wm_scaled_width, $wm_scaled_height);
            imagecolortransparent($water_tmp, imagecolorallocate($water_tmp, 0, 0, 0));
            imagealphablending($water_tmp, false);
            imagesavealpha($water_tmp, true);

            imagecopyresampled($water_tmp, $water, 0, 0, 0, 0, $wm_scaled_width, $wm_scaled_height, $wm_width_orig, $wm_height_orig);
            imagedestroy($water);
/*
        $tmp_wm_image_path = "var/tmp/".md5("SW$wm_scaled_width SH$wm_scaled_height PATH".xcm_get_config("xcm_watermark_image")).".png";

        imagepng($water_tmp, $tmp_wm_image_path);
        imagedestroy($water_tmp);
        $water = imagecreatefrompng($tmp_wm_image_path);
  */
            $water = $water_tmp;
        }
    }

	return $water;
}

function smarty_function_xcm_thumb($params, &$smarty)
{
    ini_set("memory_limit", "128M");

    //global $xcm_thumb_cache_tbl;
    $xcm_vars = xcm_get_vars();
    extract($xcm_vars);

    $app_http_host = xcm_get_host();
    $app_location = xcm_get_curr_location();
    $app_fs_dir = xcm_get_dir();

    $htaccess_block_detect = true;

    $thumb_width = $params['width'];

    $thumb_height = $params['height'];


//    if (!isset($xcm_thumb_cache_tbl))
//        $xcm_thumb_cache_tbl = $xcm_thumb_cache_tbl;


    if ($xcm_thumb_self_install)
	xcm_install_all_config();

    if (!isset($params['force_no_shadow'])) {
        $params['force_no_shadow'] = $params['force_no_wm'];
    }

    $src_url = $params['src_url'];

    $dbg_output = "";

    $orig_src_url = $src_url;

    if (xcm_get_config("force_fs_path") == "Y")	{
		
	$src_url = xcm_url_to_fs ($src_url);
	if (!xcm_thumb_file_accessible($src_url))
            $src_url = $orig_src_url;
    }

    if (!xcm_thumb_file_accessible($src_url)) {
        if (strpos($src_url, $app_location) !== false) {
            $src_url = xcm_url_to_fs ($src_url);
        } else {
            $src_url = "http://".str_replace("http://","",$app_http_host.$src_url);
        }
    }

    if (!xcm_thumb_file_accessible($src_url)) {
        if ($params["get_dims"] == "Y") {
            $smarty->assign($params["assign"],array(0,0,$src_url));
            return "";
        } else {
            $src_url = $orig_src_url;
            if (!$htaccess_block_detect) {
                return  xcm_thumb_prepare_output($app_location."/default_image.gif", $thumb_width, $params, $smarty, "empty or invalid param 'src_url' passed to xcm_thumb function: file '".$src_url."' does not exist");
            } else {
                return  xcm_thumb_prepare_output($src_url, $thumb_width, $params, $smarty, "empty or invalid param 'src_url' passed to xcm_thumb function: file '".$src_url."' does not exist");
            }
        }
    }

    if ($params["get_dims"] == "Y") {
        $smarty->assign($params["assign"],getimagesize($src_url));
        return "";
    }

    $real_cache_path = xcm_slash_sum($app_fs_dir, xcm_get_config("cached_thumbs_dir"));

//die("app_fs_dir $app_fs_dir");

    if (!file_exists($real_cache_path)) {
	if (!xcm_create_cache_dir ($real_cache_path))
		return xcm_thumb_prepare_output($src_url, $thumb_width, $params, $smarty, "cannot create cache dir specified in general settings: $real_cache_path");
    }

    $thumb_url = "";

    $result = "";//$src_url;

    $key_array = array (
        "url"=>$src_url,
        "width"=>$thumb_width,
        "height"=>$thumb_height,
        "keep_file_h2w"=>$params["keep_file_h2w"],
        "no_zoom"=>$params["no_zoom"],
        "xcm_jpeg_quality"=>xcm_get_config("xcm_jpeg_quality"),
        "valign"=>$params["valign"],
        "xcm_watermark_on"=>xcm_get_config("xcm_watermark_on"),
        "force_no_wm"=>$params["force_no_wm"],
        "force_no_shadow"=>$params["force_no_shadow"],
        "xcm_watermark_mode"=>xcm_get_config("xcm_watermark_mode"),
        "xcm_watermark_text"=>xcm_get_config("xcm_watermark_text"),
        "xcm_watermark_orientation"=>xcm_get_config("xcm_watermark_orientation"),
        "xcm_watermark_font"=>xcm_get_config("xcm_watermark_font"),
        "xcm_watermark_hexcolor"=>xcm_get_config("xcm_watermark_hexcolor"),
        "xcm_watermark_image"=>xcm_get_config("xcm_watermark_image"),
        "xcm_watermark_h_align"=>xcm_get_config("xcm_watermark_h_align"),
        "xcm_watermark_v_align"=>xcm_get_config("xcm_watermark_v_align"),
        "xcm_watermark_alpha"=>xcm_get_config("xcm_watermark_alpha"),
	"xcm_watermark_scale"=>xcm_get_config("xcm_watermark_scale"),
        "xcm_shadow_on"=>xcm_get_config("xcm_shadow_on")
    );

    $md5key = md5(serialize($key_array));
    $cached_image_path = xcm_db_cell("SELECT image_path FROM $xcm_thumb_cache_tbl WHERE md5key='$md5key'");

    $cached_image_filename = "";

    if (!empty($cached_image_path)) {

        if (xcm_thumb_file_accessible($cached_image_path))  {
            $cached_images_rel_path_on_fs = str_replace($app_fs_dir,"",$cached_image_path);
            $thumb_url = xcm_slash_sum($app_location,$cached_images_rel_path_on_fs);
        } else {
		xcm_cache_delete("md5key='$md5key'");
//            db_query("DELETE FROM $xcm_thumb_cache_tbl WHERE md5key='$md5key'");
//            $result .= "...cache hit but file is removed";
        }
    }

    if (empty($thumb_url)) {

	$cached_image_filename = $md5key.".jpg";
	$cached_image_path = xcm_slash_sum($real_cache_path, $cached_image_filename);

        if (xcm_get_config("keep_orig_name") == "Y") {

        	$ext = end( explode('.',urldecode($src_url)));
	        $b_name = basename(urldecode($src_url));

        	$b_main = substr($b_name, 0, strlen($b_name)-strlen($ext)-1);

	        $unique_idx = 0;
        	do {
            		$cached_image_filename = "$b_main-$unique_idx.jpg";
	                $cached_image_path = rtrim($real_cache_path,"/")."/$cached_image_filename";
                	$unique_idx++;
            	} while (file_exists($cached_image_path));
        }

	$image_info = xcm_getimagesize($src_url);

	$width_orig = $image_info[0];
	$height_orig = $image_info[1];
	$orig_type = $image_info[2];

        if (!isset($params['width']) && !isset($params['height'])) {
            $thumb_width = $width_orig;
            $thumb_height = $height_orig;
        }

	if (isset($params['width']) && empty($params['width']))
		$thumb_width = $width_orig;

	if (isset($params['height']) && empty($params['height']))
        	$thumb_height = $height_orig;

        if ($orig_type) {

            $dst_x = 0;
            $dst_y = 0;
            $output_width = $thumb_width;

            $ratio_orig = $width_orig/$height_orig;
            if (empty($thumb_height)) {
                $height = $thumb_width/$ratio_orig;
                $output_height = $height;
                if (($params["no_zoom"] == "Y") && ($height > $height_orig)) {
                    $output_width = $width_orig;
                    $output_height = $height_orig;
                    $thumb_width = $width_orig;
                    $height = $height_orig;
                }
            } else {
                $ratio_box = $thumb_width/$thumb_height;
                $output_height = $thumb_height;
                $output_width = $thumb_width;
                if ($ratio_orig < $ratio_box) {
//keep height
                    $thumb_width = $thumb_height*$ratio_orig;
                    $height = $thumb_height;
                    if ($params["keep_file_h2w"] == "Y") {
                        $output_width = $thumb_width;
                    }
                    if (($params["no_zoom"] == "Y") && ($thumb_width > $width_orig)) {
                        $output_width = $width_orig;
                        $output_height = $height_orig;
                        $thumb_width = $width_orig;
                        $height = $height_orig;
                    }
                    $dst_x = ($output_width-$thumb_width)/2;
                } else {
//keep width
                    $height = $thumb_width/$ratio_orig;
                    if ($params["keep_file_h2w"] == "Y") {
                        $output_height = $height;
                    }
                    if (($params["no_zoom"] == "Y") && ($height > $height_orig)) {
                        $output_width = $width_orig;
                        $output_height = $height_orig;
                        $thumb_width = $width_orig;
                        $height = $height_orig;
                    }
                    if ($params["valign"]=="top") {
                        $dst_y = 0;
                    } elseif ($params["valign"]=="bottom") {
                        $dst_y = abs($output_height-$height);
                    } else {
                        $dst_y = abs($output_height-$height)/2;
                    }
                }
            }

	    xcm_resize_img($output_width, $output_height, $dst_x, $dst_y, $thumb_width, $height, $src_url, $cached_image_path, "center", $params["valign"]);

            if (xcm_get_config("xcm_watermark_on") == "Y" && $params["force_no_wm"] != "Y") {

                $src_image = imagecreatefromjpeg($cached_image_path);
                if ($src_image) {
                    if (xcm_get_config("xcm_watermark_mode") == "text") {
                        $wm_color = xcm_thumb_HexToRGB(xcm_get_config("xcm_watermark_hexcolor"));
                        $_alpha = 127*xcm_get_config("xcm_watermark_alpha")/100;
                        $res_image = xcm_thumb_create_watermark_text($src_image,
                        xcm_get_config("xcm_watermark_text"),
                        xcm_get_config("xcm_watermark_font"),
                        $wm_color['r'], $wm_color['g'], $wm_color['b'],
                        $_alpha);
                    } elseif (xcm_get_config("xcm_watermark_mode") == "png8") {
                        if ($water = xcm_get_wm_image($thumb_width, $height)) {
                            imagecolortransparent($water, imagecolorat($water, 0, 0));
                            $res_image = xcm_thumb_create_watermark_png8($src_image,
                            $water,
                            100-xcm_get_config("xcm_watermark_alpha"));
                            imagedestroy($water);
                        }
                    } elseif (xcm_get_config("xcm_watermark_mode") == "png24") {
                        if ($water = xcm_get_wm_image($thumb_width, $height)) {
                            $res_image = xcm_thumb_create_watermark_png24($src_image,
                            $water,
                            100-xcm_get_config("xcm_watermark_alpha"));
                            imagedestroy($water);
                        }
                    }
                    imagejpeg($res_image, $cached_image_path, intval(xcm_get_config("xcm_jpeg_quality")));
                    imagedestroy($res_image);
                    @imagedestroy($src_image);
                }
            }
	    $cached_images_rel_path_on_fs = str_replace($app_fs_dir,"",$cached_image_path);
            $thumb_url = xcm_slash_sum($app_location, $cached_images_rel_path_on_fs);

            $ins_array = array(
                "image_path"=>addslashes($cached_image_path),
                "image_type"=>'image/jpeg',
                "image_x"=>intval($thumb_width),
                "image_y"=>intval($height),
                "image_size"=>intval(filesize($cached_image_path)),
                "filename"=>addslashes($cached_image_filename),
                "date"=>time(),
                "md5key"=>$md5key
            );
            xcm_cache_insert($ins_array);
        }
    }

    if (!empty($thumb_url)) {
        $result = $dbg_output.xcm_thumb_prepare_output($thumb_url, $thumb_width, $params, $smarty);
    } else {
        $result = $dbg_output.xcm_thumb_prepare_output($orig_src_url, $thumb_width, $params, $smarty, 'cant create thumbn');
    }

    //remove the oldest thumbnails which do not fit the total size limit

    if (xcm_get_config("cached_thumbs_dir_limit") > 0) {
        $bytes_limit = xcm_get_config("cached_thumbs_dir_limit")*1024*1024;

        while (xcm_db_cell("select sum(image_size) from $xcm_thumb_cache_tbl") > $bytes_limit && xcm_db_cell("select count(*) from $xcm_thumb_cache_tbl") > 1) {
            $img_id_to_del = xcm_db_cell("select imageid from $xcm_thumb_cache_tbl order by date ASC, imageid ASC limit 1");
            $file_to_del = xcm_db_cell("select image_path from $xcm_thumb_cache_tbl where imageid='$img_id_to_del'");
            @unlink($file_to_del);
	    xcm_cache_delete ("imageid='$img_id_to_del'");
        }
    }

    return $result;

}


function xcm_set_config_category_id($category = null) {
	static $category_ids;
	global $tables;


	if (empty($category)) {
		$category = xcm_get_smarty_category_name();
	}

	if (isset($category_ids) && !empty($category_ids)) {
		if (isset($category_ids[$category])) {
			return $category_ids[$category];
		}
	}

	$category_ids[$category] = cw_query_first_cell("SELECT config_category_id FROM $tables[config_categories] WHERE category = '$category'");
	if (empty($category_ids[$category])) {
		db_query("INSERT INTO $tables[config_categories] SET category = '$category'");
		$category_ids[$category] = db_insert_id();
	}

	return $category_ids[$category];
}


function xcm_get_smarty_category_name() {
	return 'Images';
}

function xcm_get_detimages_category_name() {
	return 'detailed_product_images';
}


/* vim: set expandtab: */

?>
