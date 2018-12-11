<?php

set_time_limit(86400);

global $csvxc_allowed_import_sections, $csvxc_allowed_sections, $csvxc_field_types, $csvxc_extend_ids, $csvxc_images_translate_path, $csvxc_fmap;
//print_r(array($csvxc_allowed_import_sections, $csvxc_allowed_sections, $csvxc_field_types, $csvxc_extend_ids, $csvxc_images_translate_path, $csvxc_fmap));die;
//predefined constants list

//$default_attr_classid = 1
$default_attr_classid = cw_query_first_cell("SELECT attribute_class_id FROM $tables[attributes_classes] WHERE is_default=1 LIMIT 1");

global $cw_images_subdir;
$cw_images_subdir = "/files/images";

global $src_images_path;
//images are located in root directory
//$src_images_path = $app_dir."/..";
$src_images_path = $app_dir;

global $src_images_path_alt;
if (!empty($config['flexible_import']['fi_alt_image_src_path'])) {
    $src_images_path_alt = 
        array_filter(
            array_map(function ($s) {global $app_dir; return $app_dir.$s;}, 
                array_filter(explode("\n", $config['flexible_import']['fi_alt_image_src_path']), 
                function ($v) {return !empty(trim($v));})), 
        function ($p) {return file_exists($p);});
}
cw_log_add('src_images_path_alt', $src_images_path_alt);

global $dst_images_path;
$dst_images_path = $var_dirs['images'];

$def_domain = $app_config_file['web']['http_host'];

//$current_customer_id = 1;
$current_customer_id = intval($user_account['customer_id']);

$categories_separator = "/";
$cats_level_max = 15;

$drop_tmp_list = array();
//endof predefined constants list

$tmp_fields_arr = array();

$import_images = true;

$def_domain_id = cw_query_first_cell("SELECT domain_id FROM $tables[domains] WHERE name='$def_domain'");

if (!$def_domain_id)
    $def_domain_id = cw_query_first_cell("SELECT domain_id FROM $tables[domains] ORDER BY domain_id LIMIT 1");

//$fi_tables[products_system_info]
//$fi_tables[products_prices]
//$fi_tables[products_warehouses_amount]
//$fi_tables[products_detailed_images]
//$fi_tables[products_images_det]
//$fi_tables[products_images_thumb]
//$fi_tables[products]
//$fi_tables[attributes_values]
//$fi_tables[attributes_default]
//$fi_tables[attributes_classes_assignement]
//$fi_tables[attributes]
//$fi_tables[categories_images_thumb]
//$fi_tables[categories_parents]
//$fi_tables[categories]
//$fi_tables[manufacturers]
//$fi_tables[memberships]
//$fi_tables[product_options_values]
//$fi_tables[product_options]
//$fi_tables[product_variant_items]
//$fi_tables[product_variants]
//$fi_tables[linked_products]

if (cw_csvxc_is_table_exists('tmp_load_PRODUCTS') && $csvxc_allowed_sections['tmp_load_PRODUCTS'])  {

    cw_csvxc_set_col_exist_flag('tmp_load_PRODUCTS');

    if ($csvxc_fmap['tmp_load_PRODUCTS']['PRODUCTID']['exists_column']) {

        cw_csvxc_logged_query("ALTER TABLE tmp_load_PRODUCTS ADD COLUMN ref_product_id int(11) NOT NULL DEFAULT 0");
        cw_csvxc_logged_query("UPDATE tmp_load_PRODUCTS tlp, $fi_tables[products] SET tlp.ref_product_id = $fi_tables[products].product_id WHERE $fi_tables[products].product_id = tlp.PRODUCTID");
        cw_csvxc_logged_query("UPDATE tmp_load_PRODUCTS tlp, $fi_tables[products] SET tlp.ref_product_id = $fi_tables[products].product_id WHERE $fi_tables[products].productcode = tlp.PRODUCTCODE AND tlp.ref_product_id=0");

//        if ($csvxc_fmap['tmp_load_PRODUCTS']['FORSALE']['exists_column'])
//            cw_csvxc_logged_query("UPDATE tmp_load_PRODUCTS SET FORSALE=IF(tmp_load_PRODUCTS.FORSALE='Y',1,0)");

        $update_fields = cw_csvxc_make_update_fields_arr('tmp_load_PRODUCTS', $fi_tables['products'], 'PRODUCTID');
        cw_csvxc_logged_query("UPDATE $fi_tables[products], tmp_load_PRODUCTS SET ".implode(', ', $update_fields)." WHERE tmp_load_PRODUCTS.PRODUCTID=$fi_tables[products].product_id AND tmp_load_PRODUCTS.ref_product_id!=0");

        cw_csvxc_logged_query("UPDATE $fi_tables[products] p, $fi_tables[products_lng] pl SET pl.product=p.product, pl.descr=p.descr, pl.fulldescr=p.fulldescr WHERE p.product_id = pl.product_id AND pl.code='EN'");

        cw_csvxc_logged_query("UPDATE $fi_tables[products_system_info], tmp_load_PRODUCTS SET $fi_tables[products_system_info].modification_customer_id='$current_customer_id', $fi_tables[products_system_info].modification_date='".time()."' WHERE tmp_load_PRODUCTS.PRODUCTID=$fi_tables[products_system_info].product_id AND tmp_load_PRODUCTS.ref_product_id!=0");

        //routines for new products insert 
        list($ins_into_fields, $select_fields) = cw_csvxc_get_columns4insert($fi_tables['products'], 'tmp_load_PRODUCTS');

        cw_csvxc_logged_query("INSERT INTO $fi_tables[products] (".implode(", ",$ins_into_fields).") SELECT ".implode(", ",$select_fields)." FROM tmp_load_PRODUCTS WHERE tmp_load_PRODUCTS.ref_product_id = 0 AND tmp_load_PRODUCTS.PRODUCT != ''");

        cw_csvxc_logged_query("INSERT IGNORE INTO $fi_tables[products_system_info] (product_id, creation_customer_id, creation_date, modification_customer_id, modification_date) SELECT PRODUCTID, '$current_customer_id', ".time().", '$current_customer_id', ".time()."  FROM tmp_load_PRODUCTS WHERE tmp_load_PRODUCTS.PRODUCT != ''");

        cw_csvxc_logged_query("UPDATE tmp_load_PRODUCTS tlp, $fi_tables[products] SET tlp.ref_product_id = $fi_tables[products].product_id WHERE $fi_tables[products].product_id = tlp.PRODUCTID");
        cw_csvxc_logged_query("UPDATE tmp_load_PRODUCTS tlp, $fi_tables[products] SET tlp.ref_product_id = $fi_tables[products].product_id WHERE $fi_tables[products].productcode = tlp.PRODUCTCODE AND tlp.ref_product_id=0");
    }

}

//--------------------categories process----
if (cw_csvxc_is_table_exists('tmp_load_CATEGORIES') && $csvxc_allowed_sections['tmp_load_CATEGORIES']) {

    cw_csvxc_set_col_exist_flag('tmp_load_CATEGORIES');

//    if ($csvxc_fmap['tmp_load_CATEGORIES']['AVAIL']['exists_column'])
//        cw_csvxc_logged_query("UPDATE tmp_load_CATEGORIES SET AVAIL=IF(tmp_load_CATEGORIES.AVAIL='Y',1,0)");

    if ($csvxc_fmap['tmp_load_CATEGORIES']['PATH']['exists_column'] && $csvxc_fmap['tmp_load_CATEGORIES']['CATEGORY']['exists_column']) {
       $cat_ids_names = cw_query_hash("SELECT CATEGORYID, CATEGORY, PATH FROM tmp_load_CATEGORIES", "CATEGORYID", false);
       cw_csvxc_logged_query("alter table tmp_load_CATEGORIES modify column CATEGORY text not null default ''");
       foreach ($cat_ids_names as $cat_id=>$cat_data) {
           $path_ids = explode('/', $cat_data['PATH']);
           $catnames_path = array(); 
           $cat_names_err = false;
           foreach ($path_ids as $_cid) {
               if (isset($cat_ids_names[$_cid])) {
                   $catnames_path[] = $cat_ids_names[$_cid]['CATEGORY'];
               } else {
                   cw_log_add('category_import', "Cannot find category by id #$_cid (processing category $cat_id)", false);
                   $cat_names_err = true;
                   break;
               }
           }
           if (!$cat_names_err) 
               cw_csvxc_logged_query("UPDATE tmp_load_CATEGORIES SET CATEGORY='".addslashes(implode('/', $catnames_path))."' WHERE CATEGORYID='$cat_id'");
       } 


//       cw_csvxc_logged_query("drop table tmp_load_CATEGORIES_pathbuild");
    }


    cw_csvxc_logged_query("ALTER TABLE tmp_load_CATEGORIES ADD COLUMN ref_category_id int(11) NOT NULL DEFAULT 0");

    cw_csvxc_logged_query("UPDATE tmp_load_CATEGORIES tlc, $fi_tables[categories] cc SET tlc.ref_category_id = cc.category_id WHERE tlc.CATEGORYID = cc.category_id");

    cw_csvxc_duplicate_table($fi_tables['categories'], 'tmp_cw_categories', true);
    $drop_tmp_list['tmp_cw_categories'] = 1;
    cw_csvxc_logged_query("ALTER TABLE tmp_cw_categories ADD COLUMN lvl int(11) NOT NULL DEFAULT 0");
    cw_csvxc_logged_query("ALTER TABLE tmp_cw_categories ADD COLUMN ref_parent_id INT(11) NOT NULL DEFAULT 0");
    cw_csvxc_logged_query("ALTER TABLE tmp_cw_categories ADD COLUMN category_path varchar(255) NOT NULL DEFAULT ''");
    cw_csvxc_logged_query("ALTER TABLE tmp_cw_categories ADD COLUMN ref_tlc_id int(11) NOT NULL DEFAULT 0");

    $cat_ids = cw_query_column("SELECT category_id FROM tmp_cw_categories");
    foreach ($cat_ids as $c_id) {
        $cat_names = array();
        $safety_cnt = 0;
        $p_id = -1;
        $_c_id = $c_id;
        while ($p_id != 0 && $safety_cnt < 15) {
            $c_data = cw_query_first("SELECT parent_id, category_id, category  FROM tmp_cw_categories WHERE category_id = '$c_id'");
            $p_id = $c_data['parent_id'];
            $c_id = $c_data['category_id'];
            $cat_name = $c_data['category'];
            $cat_names[] = $cat_name;
            $c_id = $p_id;
            $safety_cnt++;
        }

        $cat_names = array_reverse($cat_names);
        $cat_path = implode("/", $cat_names);
        cw_csvxc_logged_query("UPDATE tmp_cw_categories SET category_path='".addslashes($cat_path)."' WHERE category_id='$_c_id'",". ");
    }
    cw_flush("<br>");


    $cat_fields_arr = cw_csvxc_get_table_fields($fi_tables['categories']);
    $cat_l_join_flds_arr = cw_csvxc_get_fields_combination($fi_tables['categories'], $cat_fields_arr, 'tmp_load_CATEGORIES');

    $cat_fields_arr[] = 'tmp_cw_categories.`category_path`';
    $cat_l_join_flds_arr[] = 'tmp_load_CATEGORIES.`CATEGORY`';

    $cat_fields_arr[] = 'tmp_cw_categories.`ref_tlc_id`';
    $cat_l_join_flds_arr[] = 'tmp_load_CATEGORIES.`id_categories`';

    cw_csvxc_logged_query("REPLACE INTO tmp_cw_categories (".implode(", ",$cat_fields_arr).") SELECT ".implode(", ", $cat_l_join_flds_arr)." FROM tmp_load_CATEGORIES LEFT JOIN $fi_tables[categories] ON tmp_load_CATEGORIES.CATEGORYID=$fi_tables[categories].category_id WHERE tmp_load_CATEGORIES.ref_category_id != 0 AND tmp_load_CATEGORIES.CATEGORY != ''");


    //routines for new categories insert 
    list($ins_into_fields, $select_fields) = cw_csvxc_get_columns4insert($fi_tables['categories'], 'tmp_load_CATEGORIES', 'tmp_cw_categories');

    $ins_into_fields[] = 'tmp_cw_categories.`category_path`';
    $select_fields[] = 'tmp_load_CATEGORIES.`CATEGORY`';

    $ins_into_fields[] = 'tmp_cw_categories.`ref_tlc_id`';
    $select_fields[] = 'tmp_load_CATEGORIES.`id_categories`';

    cw_csvxc_logged_query("INSERT INTO tmp_cw_categories (".implode(", ",$ins_into_fields).") SELECT ".implode(", ",$select_fields)." FROM tmp_load_CATEGORIES WHERE ref_category_id=0");

//add lvl field to tmp_cw_categories

    $cat_levels_check = array();
    $cat_levels_check = array_pad($cat_levels_check, $cats_level_max, '%');
    for ($i = $cats_level_max; $i>1; $i--) {
        $lvl = $i-1;
        cw_csvxc_logged_query("UPDATE tmp_cw_categories SET lvl=$lvl WHERE category_path LIKE ('".implode($categories_separator,$cat_levels_check)."') AND lvl=0");
        array_pop($cat_levels_check);
    }
    cw_csvxc_duplicate_table('tmp_cw_categories', 'tmp_cw_categories_2', true);

    $drop_tmp_list['tmp_cw_categories_2'] = 1;
    for ($i = $cats_level_max; $i>1; $i--) {
        $lvl = $i-1;
        $lvl_parent = $i-2;
        cw_csvxc_logged_query("UPDATE tmp_cw_categories ctc SET ref_parent_id = (SELECT category_id FROM tmp_cw_categories_2 WHERE lvl=$lvl_parent AND ctc.category_path LIKE CONCAT(category_path,'/%') LIMIT 1) WHERE lvl=$lvl");
    }

    cw_csvxc_logged_query("ALTER TABLE tmp_cw_categories ADD COLUMN new_category_name VARCHAR(255) NOT NULL DEFAULT ''");


    cw_csvxc_logged_query("UPDATE tmp_cw_categories ctc SET new_category_name = REPLACE(category_path, CONCAT((SELECT category_path FROM tmp_cw_categories_2 WHERE ctc.ref_parent_id = category_id),'".$categories_separator."'), '') where ctc.lvl > 0");


    cw_csvxc_logged_query("UPDATE tmp_cw_categories ctc SET new_category_name = category_path, ref_parent_id=0 WHERE lvl=0");


    cw_csvxc_logged_query("UPDATE tmp_cw_categories, tmp_load_CATEGORIES SET tmp_cw_categories.tm_active=1 WHERE tmp_load_CATEGORIES.CATEGORYID = tmp_cw_categories.category_id AND tmp_load_CATEGORIES.CATEGORY!='' AND tmp_cw_categories.lvl IN (0,1)");


    cw_csvxc_logged_query("DROP TABLE tmp_cw_categories_2");

    cw_csvxc_logged_query("UPDATE tmp_cw_categories SET category = new_category_name");
    cw_csvxc_logged_query("UPDATE tmp_cw_categories SET parent_id = ref_parent_id");

    cw_csvxc_logged_query("REPLACE INTO $fi_tables[categories_parents] (category_id, parent_id, level) SELECT category_id, parent_id, lvl FROM tmp_cw_categories");

    cw_csvxc_logged_query("ALTER TABLE tmp_cw_categories DROP COLUMN lvl");
    cw_csvxc_logged_query("ALTER TABLE tmp_cw_categories DROP COLUMN ref_parent_id");
    cw_csvxc_logged_query("ALTER TABLE tmp_cw_categories DROP COLUMN category_path");
    cw_csvxc_logged_query("ALTER TABLE tmp_cw_categories DROP COLUMN ref_tlc_id");
    cw_csvxc_logged_query("ALTER TABLE tmp_cw_categories DROP COLUMN new_category_name");

    cw_csvxc_logged_query("DELETE FROM $fi_tables[categories]");
    cw_csvxc_logged_query("INSERT INTO $fi_tables[categories] SELECT * FROM tmp_cw_categories");

    if ($csvxc_fmap['tmp_load_CATEGORIES']['META_DESCR']['exists_column'])
        cw_csvxc_update_linked_table('tmp_load_CATEGORIES', 'META_DESCR');

    if ($csvxc_fmap['tmp_load_CATEGORIES']['META_KEYWORDS']['exists_column'])
        cw_csvxc_update_linked_table('tmp_load_CATEGORIES', 'META_KEYWORDS');

    if ($csvxc_fmap['tmp_load_CATEGORIES']['VIEWS_STATS']['exists_column'])
        cw_csvxc_update_linked_table('tmp_load_CATEGORIES', 'VIEWS_STATS');

    if ($csvxc_fmap['tmp_load_CATEGORIES']['MEMBERSHIPID']['exists_column'])
        cw_csvxc_update_linked_table('tmp_load_CATEGORIES', 'MEMBERSHIPID');

    $domain_attribute_id = cw_query_first_cell("SELECT attribute_id FROM $fi_tables[attributes] WHERE item_type='C' AND addon='multi_domains' and field='domains'");
    if (!empty($def_domain_id) && !empty($domain_attribute_id)) {
        cw_csvxc_logged_query("DELETE $fi_tables[attributes_values] FROM $fi_tables[attributes_values], tmp_load_CATEGORIES WHERE $fi_tables[attributes_values].attribute_id='$domain_attribute_id' AND $fi_tables[attributes_values].item_id=tmp_load_CATEGORIES.CATEGORYID AND tmp_load_CATEGORIES.CATEGORYID != '' AND $fi_tables[attributes_values].item_type='C'");
        cw_csvxc_logged_query("INSERT INTO $fi_tables[attributes_values] (value, attribute_id, code, item_type, item_id) SELECT $def_domain_id, $domain_attribute_id, 'EN', 'C', CATEGORYID FROM tmp_load_CATEGORIES WHERE CATEGORYID!='' AND CATEGORY!=''");
    }

    if ($csvxc_fmap['tmp_load_CATEGORIES']['ICON']['exists_column']) {
        cw_csvxc_delete_old_images("SELECT $fi_tables[categories_images_thumb].image_path AS img_full_path, $fi_tables[categories_images_thumb].id AS img_key_fld FROM $fi_tables[categories_images_thumb], tmp_load_CATEGORIES WHERE tmp_load_CATEGORIES.CATEGORYID=$fi_tables[categories_images_thumb].id AND tmp_load_CATEGORIES.CATEGORY!=''", 'categories_images_thumb');
        cw_csvxc_transfer_import_images('tmp_load_CATEGORIES', 'ICON', 'CATEGORYID');
    }

}

if (cw_csvxc_is_table_exists('tmp_load_PRODUCTS') && $csvxc_allowed_sections['tmp_load_PRODUCTS'])  {
// - update rest of the data with tmp_load_PRODUCTS columns
    if ($csvxc_fmap['tmp_load_PRODUCTS']['CATEGORYID']['exists_column'] || $csvxc_fmap['tmp_load_PRODUCTS']['CATEGORY']['exists_column']) {
        if (!$csvxc_fmap['tmp_load_PRODUCTS']['CATEGORYID']['exists_column']) {
            if (cw_csvxc_is_table_exists('tmp_cw_categories')) {
                cw_csvxc_logged_query("ALTER TABLE tmp_load_PRODUCTS ADD COLUMN CATEGORYID INT(11) NOT NULL DEFAULT 0");
                cw_csvxc_logged_query("UPDATE tmp_load_PRODUCTS tlp SET CATEGORYID=(SELECT category_id FROM tmp_cw_categories WHERE tlp.CATEGORY=category_path LIMIT 1)");
                $csvxc_fmap['tmp_load_PRODUCTS']['CATEGORYID']['exists_column'] = 1;
            } else {
               // build tmp_cw_category_path and use it for categoryid set
            }
        }
        if ($csvxc_fmap['tmp_load_PRODUCTS']['CATEGORYID']['exists_column']) {

            if (!$csvxc_fmap['tmp_load_PRODUCTS']['PRODUCTS_CATEGORIES_ORDERBY']['exists_column']) {
                cw_csvxc_logged_query("ALTER TABLE tmp_load_PRODUCTS ADD COLUMN PRODUCTS_CATEGORIES_ORDERBY INT(11) NOT NULL DEFAULT 0");
                $csvxc_fmap['tmp_load_PRODUCTS']['PRODUCTS_CATEGORIES_ORDERBY']['exists_column'] = 1;
            }

            cw_csvxc_update_linked_table('tmp_load_PRODUCTS', 'CATEGORYID');
        }
    }

    if ($csvxc_fmap['tmp_load_PRODUCTS']['MANUFACTURERID']['exists_column'] || $csvxc_fmap['tmp_load_PRODUCTS']['MANUFACTURER']['exists_column']) {
//print(" 1: ".$csvxc_fmap['tmp_load_PRODUCTS']['MANUFACTURERID']['exists_column']." 2: ".$csvxc_fmap['tmp_load_PRODUCTS']['MANUFACTURER']['exists_column']);
        if (!$csvxc_fmap['tmp_load_PRODUCTS']['MANUFACTURERID']['exists_column']) {
            // process feed with no manufacturerid column   
            cw_csvxc_logged_query("ALTER TABLE tmp_load_PRODUCTS ADD COLUMN MANUFACTURERID INT(11) NOT NULL DEFAULT 0");

            // add new manufacturers
            cw_csvxc_logged_query("INSERT INTO $fi_tables[manufacturers] (manufacturer, avail) SELECT distinct(MANUFACTURER),1 FROM tmp_load_PRODUCTS WHERE MANUFACTURER NOT IN (SELECT manufacturer FROM $fi_tables[manufacturers]) AND MANUFACTURER!=''");

            cw_csvxc_logged_query("UPDATE tmp_load_PRODUCTS, $fi_tables[manufacturers] SET tmp_load_PRODUCTS.MANUFACTURERID=$fi_tables[manufacturers].manufacturer_id WHERE tmp_load_PRODUCTS.MANUFACTURER = $fi_tables[manufacturers].manufacturer AND tmp_load_PRODUCTS.PRODUCTID!=''");
        } else {

            cw_csvxc_logged_query("INSERT INTO $fi_tables[manufacturers] (manufacturer_id, manufacturer, avail) SELECT distinct(MANUFACTURERID), MANUFACTURER, 1 FROM tmp_load_PRODUCTS WHERE MANUFACTURERID NOT IN (SELECT manufacturer_id FROM $fi_tables[manufacturers]) AND MANUFACTURER!=''");

            cw_csvxc_logged_query("UPDATE $fi_tables[manufacturers], tmp_load_PRODUCTS SET $fi_tables[manufacturers].manufacturer = tmp_load_PRODUCTS.MANUFACTURER WHERE tmp_load_PRODUCTS.PRODUCT != '' AND $fi_tables[manufacturers].manufacturer_id=tmp_load_PRODUCTS.MANUFACTURERID");

        }
//        cw_csvxc_update_linked_table('tmp_load_PRODUCTS', 'MANUFACTURERID'); 
        cw_csvxc_logged_query("DELETE $fi_tables[attributes_values] FROM $fi_tables[attributes_values], tmp_load_PRODUCTS WHERE $fi_tables[attributes_values].attribute_id=$manufacturer_attribute_id AND $fi_tables[attributes_values].item_id=tmp_load_PRODUCTS.PRODUCTID AND tmp_load_PRODUCTS.PRODUCT != '' AND $fi_tables[attributes_values].item_type='P'");
        cw_csvxc_logged_query("INSERT INTO $fi_tables[attributes_values] (value, attribute_id, code, item_type, item_id) SELECT MANUFACTURERID, $manufacturer_attribute_id, 'EN', 'P', PRODUCTID FROM tmp_load_PRODUCTS WHERE PRODUCT!=''");

        $domain_attribute_id = cw_query_first_cell("SELECT attribute_id FROM $fi_tables[attributes] WHERE item_type='M' AND addon='multi_domains' and field='domains'");
        if (!empty($def_domain_id) && !empty($domain_attribute_id)) {
            cw_csvxc_logged_query("DELETE $fi_tables[attributes_values] FROM $fi_tables[attributes_values], tmp_load_PRODUCTS WHERE $fi_tables[attributes_values].attribute_id='$domain_attribute_id' AND $fi_tables[attributes_values].item_id=tmp_load_PRODUCTS.MANUFACTURERID AND tmp_load_PRODUCTS.MANUFACTURER != '' AND $fi_tables[attributes_values].item_type='M'");
            cw_csvxc_logged_query("INSERT INTO $fi_tables[attributes_values] (value, attribute_id, code, item_type, item_id) SELECT $def_domain_id, $domain_attribute_id, 'EN', 'M', manufacturer_id from $fi_tables[manufacturers] inner join tmp_load_PRODUCTS on tmp_load_PRODUCTS.MANUFACTURERID = $fi_tables[manufacturers].manufacturer_id and tmp_load_PRODUCTS.MANUFACTURER != '' group by manufacturer_id");
        }


    }

    if ($csvxc_fmap['tmp_load_PRODUCTS']['LIST_PRICE']['exists_column'] && $csvxc_fmap['tmp_load_PRODUCTS']['PRICE']['exists_column']) {
        cw_csvxc_logged_query("DELETE $fi_tables[products_prices] FROM $fi_tables[products_prices], tmp_load_PRODUCTS WHERE $fi_tables[products_prices].product_id=tmp_load_PRODUCTS.PRODUCTID AND $fi_tables[products_prices].variant_id=0 AND $fi_tables[products_prices].membership_id=0 AND $fi_tables[products_prices].quantity=1"); 
        cw_csvxc_update_linked_table('tmp_load_PRODUCTS','LIST_PRICE');
    }

    if ($csvxc_fmap['tmp_load_PRODUCTS']['AVAIL']['exists_column'])
        cw_csvxc_update_linked_table('tmp_load_PRODUCTS','AVAIL');


    if ($csvxc_fmap['tmp_load_PRODUCTS']['SUPPLIERID']['exists_column'])
        cw_csvxc_update_linked_table('tmp_load_PRODUCTS','SUPPLIERID');


    if ($csvxc_fmap['tmp_load_PRODUCTS']['MEMBERSHIP']['exists_column']) {
        cw_csvxc_logged_query("ALTER TABLE tmp_load_PRODUCTS ADD COLUMN MEMBERSHIPID INT(11) NOT NULL DEFAULT 0");
        //update MEMBERSHIPID according to MEMBERSHIP
        cw_csvxc_logged_query("UPDATE tmp_load_PRODUCTS, $fi_tables[memberships] SET tmp_load_PRODUCTS.MEMBERSHIPID=$fi_tables[memberships].membership_id WHERE tmp_load_PRODUCTS.MEMBERSHIP=$fi_tables[memberships].membership AND tmp_load_PRODUCTS.PRODUCT!=''");
        cw_csvxc_update_linked_table('tmp_load_PRODUCTS','MEMBERSHIPID');
    } elseif ($csvxc_fmap['tmp_load_PRODUCTS']['MEMBERSHIPID']['exists_column']) {
        cw_csvxc_update_linked_table('tmp_load_PRODUCTS','MEMBERSHIPID');     
    }




    $domain_attribute_id = cw_query_first_cell("SELECT attribute_id FROM $fi_tables[attributes] WHERE item_type='P' AND addon='multi_domains' and field='domains'");
    if (!empty($def_domain_id) && !empty($domain_attribute_id)) {
        cw_csvxc_logged_query("DELETE $fi_tables[attributes_values] FROM $fi_tables[attributes_values], tmp_load_PRODUCTS WHERE $fi_tables[attributes_values].attribute_id='$domain_attribute_id' AND $fi_tables[attributes_values].item_id=tmp_load_PRODUCTS.PRODUCTID AND tmp_load_PRODUCTS.PRODUCT != '' AND $fi_tables[attributes_values].item_type='P'");
        cw_csvxc_logged_query("INSERT INTO $fi_tables[attributes_values] (value, attribute_id, code, item_type, item_id) SELECT $def_domain_id, $domain_attribute_id, 'EN', 'P', PRODUCTID FROM tmp_load_PRODUCTS WHERE PRODUCT!=''");
    }

    if ($csvxc_fmap['tmp_load_PRODUCTS']['IMAGE']['exists_column'] && $import_images) {
        cw_csvxc_delete_old_images("SELECT $fi_tables[products_images_det].image_path AS img_full_path, $fi_tables[products_images_det].id AS img_key_fld FROM $fi_tables[products_images_det], tmp_load_PRODUCTS WHERE tmp_load_PRODUCTS.PRODUCTID=$fi_tables[products_images_det].id AND tmp_load_PRODUCTS.PRODUCT!=''", 'products_images_det');
        cw_csvxc_transfer_import_images('tmp_load_PRODUCTS', 'IMAGE', 'PRODUCTID');
    }

    if ($csvxc_fmap['tmp_load_PRODUCTS']['THUMBNAIL']['exists_column'] && $import_images) {
        cw_csvxc_delete_old_images("SELECT $fi_tables[products_images_thumb].image_path AS img_full_path, $fi_tables[products_images_thumb].id AS img_key_fld FROM $fi_tables[products_images_thumb], tmp_load_PRODUCTS WHERE tmp_load_PRODUCTS.PRODUCTID=$fi_tables[products_images_thumb].id AND tmp_load_PRODUCTS.PRODUCT!=''", 'products_images_thumb');
        cw_csvxc_transfer_import_images('tmp_load_PRODUCTS', 'THUMBNAIL', 'PRODUCTID');
    }
}

if (cw_csvxc_is_table_exists('tmp_load_PRODUCT_OPTIONS') && $csvxc_allowed_sections['tmp_load_PRODUCT_OPTIONS']) {

    cw_csvxc_set_col_exist_flag('tmp_load_PRODUCT_OPTIONS');

   //planned: delete from tmp_load_PRODUCT_OPTIONS the lines that refer to absent products
   //-

    cw_csvxc_logged_query("UPDATE tmp_load_PRODUCT_OPTIONS SET AVAIL=IF(AVAIL='Y',1,0)");
    cw_csvxc_logged_query("UPDATE tmp_load_PRODUCT_OPTIONS SET OPTION_AVAIL=IF(OPTION_AVAIL='Y',1,0)");

    if (!$csvxc_fmap['tmp_load_PRODUCT_OPTIONS']['CLASSID']['exists_column']) {
        cw_csvxc_logged_query("ALTER TABLE tmp_load_PRODUCT_OPTIONS ADD COLUMN CLASSID int(11) NOT NULL DEFAULT 0");
    }

    cw_csvxc_logged_query("UPDATE tmp_load_PRODUCT_OPTIONS, $fi_tables[product_options] SET tmp_load_PRODUCT_OPTIONS.CLASSID=$fi_tables[product_options].product_option_id WHERE tmp_load_PRODUCT_OPTIONS.CLASS=$fi_tables[product_options].field AND tmp_load_PRODUCT_OPTIONS.PRODUCTID=$fi_tables[product_options].product_id AND tmp_load_PRODUCT_OPTIONS.PRODUCT!=''");

    cw_csvxc_logged_query("ALTER TABLE tmp_load_PRODUCT_OPTIONS ADD COLUMN ref_class_id int(11) NOT NULL DEFAULT 0");
    cw_csvxc_logged_query("UPDATE tmp_load_PRODUCT_OPTIONS tlpo, $fi_tables[product_options] cpo SET tlpo.ref_class_id = cpo.product_option_id WHERE tlpo.CLASSID = cpo.product_option_id");

    $update_fields = cw_csvxc_make_update_fields_arr('tmp_load_PRODUCT_OPTIONS', $fi_tables['product_options'], array('CLASSID','PRODUCTID'));
    cw_csvxc_logged_query("UPDATE $fi_tables[product_options], tmp_load_PRODUCT_OPTIONS SET ".implode(', ', $update_fields)." WHERE tmp_load_PRODUCT_OPTIONS.CLASSID=$fi_tables[product_options].product_option_id AND tmp_load_PRODUCT_OPTIONS.PRODUCTID = $fi_tables[product_options].product_id AND tmp_load_PRODUCT_OPTIONS.ref_class_id != 0");

    list($ins_into_fields, $select_fields) = cw_csvxc_get_columns4insert($fi_tables['product_options'], 'tmp_load_PRODUCT_OPTIONS', $fi_tables['product_options']);

    cw_csvxc_logged_query("INSERT INTO $fi_tables[product_options] (".implode(", ",$ins_into_fields).") SELECT ".implode(", ",$select_fields)." FROM tmp_load_PRODUCT_OPTIONS WHERE ref_class_id=0 AND PRODUCT != ''");

    if (!$csvxc_fmap['tmp_load_PRODUCT_OPTIONS']['OPTIONID']['exists_column']) {
        cw_csvxc_logged_query("ALTER TABLE tmp_load_PRODUCT_OPTIONS ADD COLUMN OPTIONID int(11) NOT NULL DEFAULT 0");
    }

    cw_csvxc_logged_query("UPDATE tmp_load_PRODUCT_OPTIONS, $fi_tables[product_options_values] SET tmp_load_PRODUCT_OPTIONS.OPTIONID=$fi_tables[product_options_values].option_id WHERE tmp_load_PRODUCT_OPTIONS.OPTION=$fi_tables[product_options_values].name AND tmp_load_PRODUCT_OPTIONS.CLASSID=$fi_tables[product_options_values].product_option_id");

    cw_csvxc_logged_query("ALTER TABLE tmp_load_PRODUCT_OPTIONS ADD COLUMN ref_option_id int(11) NOT NULL DEFAULT 0");
    cw_csvxc_logged_query("UPDATE tmp_load_PRODUCT_OPTIONS tlpo, $fi_tables[product_options_values] cpov SET tlpo.ref_option_id = cpov.option_id WHERE tlpo.OPTIONID = cpov.option_id");

    $update_fields = cw_csvxc_make_update_fields_arr('tmp_load_PRODUCT_OPTIONS', $fi_tables['product_options_values'], array('OPTIONID','CLASSID'));
    cw_csvxc_logged_query("UPDATE $fi_tables[product_options_values], tmp_load_PRODUCT_OPTIONS SET ".implode(', ', $update_fields)." WHERE tmp_load_PRODUCT_OPTIONS.OPTIONID=$fi_tables[product_options_values].option_id AND tmp_load_PRODUCT_OPTIONS.CLASSID=$fi_tables[product_options_values].product_option_id AND tmp_load_PRODUCT_OPTIONS.ref_option_id != 0");

    list($ins_into_fields, $select_fields) = cw_csvxc_get_columns4insert($fi_tables['product_options_values'], 'tmp_load_PRODUCT_OPTIONS', $fi_tables['product_options_values']);

    cw_csvxc_logged_query("INSERT INTO $fi_tables[product_options_values] (".implode(", ",$ins_into_fields).") SELECT ".implode(", ",$select_fields)." FROM tmp_load_PRODUCT_OPTIONS WHERE ref_option_id=0 AND OPTIONID!=0");

}

if (cw_csvxc_is_table_exists('tmp_load_PRODUCT_VARIANTS') && $csvxc_allowed_sections['tmp_load_PRODUCT_VARIANTS']) {

    cw_csvxc_set_col_exist_flag('tmp_load_PRODUCT_VARIANTS');

    cw_csvxc_logged_query("ALTER TABLE tmp_load_PRODUCT_VARIANTS ADD INDEX tlpv_VARIANTID (VARIANTID)");
    cw_csvxc_logged_query("ALTER TABLE tmp_load_PRODUCT_VARIANTS ADD INDEX tlpv_PRODUCTID (PRODUCTID)");
    cw_csvxc_logged_query("ALTER TABLE tmp_load_PRODUCT_VARIANTS ADD INDEX tlpv_CLASS (CLASS)");
   // cw_csvxc_logged_query("ALTER TABLE $fi_tables[product_variant_items] ADD INDEX cpv_variant_id (variant_id)");

    cw_csvxc_logged_query("ALTER TABLE $fi_tables[product_variants] MODIFY COLUMN eancode varchar(64) NOT NULL DEFAULT ''");

    cw_csvxc_logged_query("ALTER TABLE tmp_load_PRODUCT_VARIANTS ADD COLUMN ref_variant_id INT(11) NOT NULL DEFAULT 0");
    cw_csvxc_logged_query("UPDATE tmp_load_PRODUCT_VARIANTS tlpv, $fi_tables[product_variants] cpv SET tlpv.ref_variant_id=cpv.variant_id WHERE cpv.variant_id=tlpv.VARIANTID");

//update variant for variants with ref_variant_id!=0

    $update_fields = cw_csvxc_make_update_fields_arr('tmp_load_PRODUCT_VARIANTS', $fi_tables['product_variants'], 'VARIANTID');
    cw_csvxc_logged_query("UPDATE $fi_tables[product_variants], tmp_load_PRODUCT_VARIANTS SET ".implode(', ', $update_fields)." WHERE tmp_load_PRODUCT_VARIANTS.VARIANTID=$fi_tables[product_variants].variant_id AND tmp_load_PRODUCT_VARIANTS.ref_variant_id != 0");

//insert variants with ref_variant_id=0
    list($ins_into_fields, $select_fields) = cw_csvxc_get_columns4insert($fi_tables['product_variants'], 'tmp_load_PRODUCT_VARIANTS');

    $ins_into_fields[] = $fi_tables['product_variants'].'.eancode';
    $select_fields[] = 'tmp_load_PRODUCT_VARIANTS.VARIANTCODE';
    cw_csvxc_logged_query("INSERT INTO $fi_tables[product_variants] (".implode(", ",$ins_into_fields).") SELECT ".implode(", ",$select_fields)." FROM tmp_load_PRODUCT_VARIANTS WHERE ref_variant_id=0 AND PRODUCT != ''");

    //define classid
    cw_csvxc_logged_query("ALTER TABLE tmp_load_PRODUCT_VARIANTS ADD COLUMN CLASSID INT(11) NOT NULL DEFAULT 0");
    cw_csvxc_logged_query("UPDATE tmp_load_PRODUCT_VARIANTS tlpv, $fi_tables[product_options] cpo SET tlpv.CLASSID=cpo.product_option_id WHERE tlpv.PRODUCTID=cpo.product_id AND tlpv.CLASS=cpo.field");
    //define optionid
    cw_csvxc_logged_query("ALTER TABLE tmp_load_PRODUCT_VARIANTS ADD COLUMN OPTIONID INT(11) NOT NULL DEFAULT 0");
    cw_csvxc_logged_query("UPDATE tmp_load_PRODUCT_VARIANTS tlpv, $fi_tables[product_options_values] cpov SET tlpv.OPTIONID=cpov.option_id WHERE tlpv.CLASSID=cpov.product_option_id AND tlpv.`OPTION`=cpov.name");

    cw_csvxc_logged_query("DELETE $fi_tables[product_variant_items] FROM $fi_tables[product_variant_items], tmp_load_PRODUCT_VARIANTS WHERE $fi_tables[product_variant_items].variant_id=tmp_load_PRODUCT_VARIANTS.VARIANTID");
    cw_csvxc_logged_query("REPLACE INTO $fi_tables[product_variant_items] (option_id, variant_id) SELECT OPTIONID, VARIANTID FROM tmp_load_PRODUCT_VARIANTS");

//set ref_price_id
    cw_csvxc_logged_query("ALTER TABLE tmp_load_PRODUCT_VARIANTS ADD COLUMN ref_price_id INT(11) NOT NULL DEFAULT 0");

    cw_csvxc_logged_query("UPDATE tmp_load_PRODUCT_VARIANTS tlpv, $fi_tables[products_prices] cpp SET tlpv.ref_price_id=cpp.price_id WHERE cpp.variant_id=tlpv.VARIANTID AND cpp.product_id=tlpv.PRODUCTID AND cpp.quantity=1 AND cpp.membership_id=0 AND tlpv.PRODUCT!=''");

    cw_csvxc_logged_query("UPDATE $fi_tables[products_prices], tmp_load_PRODUCT_VARIANTS SET $fi_tables[products_prices].price=tmp_load_PRODUCT_VARIANTS.PRICE WHERE $fi_tables[products_prices].price_id=tmp_load_PRODUCT_VARIANTS.ref_price_id");

    cw_csvxc_logged_query("INSERT INTO $fi_tables[products_prices] (product_id, variant_id, membership_id, quantity, price) SELECT PRODUCTID, VARIANTID, 0, 1, PRICE FROM tmp_load_PRODUCT_VARIANTS WHERE ref_price_id=0 AND PRODUCT!=''");

//set ref_avail_id
    cw_csvxc_logged_query("ALTER TABLE tmp_load_PRODUCT_VARIANTS ADD COLUMN ref_avail_id VARCHAR(128) NOT NULL DEFAULT ''");

    cw_csvxc_logged_query("UPDATE tmp_load_PRODUCT_VARIANTS tlpv, $fi_tables[products_warehouses_amount] cpwa SET tlpv.ref_avail_id=CONCAT(cpwa.variant_id, '_', cpwa.product_id, '_', cpwa.warehouse_customer_id) WHERE cpwa.variant_id=tlpv.VARIANTID AND cpwa.product_id=tlpv.PRODUCTID AND cpwa.warehouse_customer_id=0 AND PRODUCT!=''");

    cw_csvxc_logged_query("UPDATE $fi_tables[products_warehouses_amount] cpwa, tmp_load_PRODUCT_VARIANTS tlpv SET cpwa.avail=tlpv.AVAIL WHERE cpwa.variant_id=tlpv.VARIANTID AND cpwa.product_id=tlpv.PRODUCTID AND cpwa.warehouse_customer_id=0");

    cw_csvxc_logged_query("INSERT INTO $fi_tables[products_warehouses_amount] (product_id, warehouse_customer_id, avail, variant_id) SELECT PRODUCTID, 0, AVAIL, VARIANTID FROM tmp_load_PRODUCT_VARIANTS WHERE ref_avail_id='' AND PRODUCT!=''");
}

if (cw_csvxc_is_table_exists('tmp_load_PRODUCT_LINKS') && $csvxc_allowed_sections['tmp_load_PRODUCT_LINKS']) {

    cw_csvxc_set_col_exist_flag('tmp_load_PRODUCT_LINKS');

    //preferred if this table wuld be cleared before import
    if ($csvxc_fmap['tmp_load_PRODUCT_LINKS']['PRODUCTID']['exists_column'] && $csvxc_fmap['tmp_load_PRODUCT_LINKS']['PRODUCTID_TO']['exists_column']) {
        //delete entries in tmp_load_PRODUCT_LINKS which refer to non-existing items
        cw_csvxc_logged_query("DELETE tmp_load_PRODUCT_LINKS.* FROM tmp_load_PRODUCT_LINKS LEFT JOIN $fi_tables[products] ON $fi_tables[products].product_id = tmp_load_PRODUCT_LINKS.PRODUCTID WHERE $fi_tables[products].product_id IS NULL");
        cw_csvxc_logged_query("DELETE tmp_load_PRODUCT_LINKS.* FROM tmp_load_PRODUCT_LINKS LEFT JOIN $fi_tables[products] ON $fi_tables[products].product_id = tmp_load_PRODUCT_LINKS.PRODUCTID_TO WHERE $fi_tables[products].product_id IS NULL");

        cw_csvxc_logged_query("ALTER TABLE tmp_load_PRODUCT_LINKS ADD COLUMN ACTIVE char(1) not null default '1'");

        cw_csvxc_logged_query("UPDATE tmp_load_PRODUCT_LINKS tlpl, $fi_tables[linked_products] clp SET tlpl.ACTIVE=clp.active WHERE tlpl.PRODUCTID=clp.product_id AND tlpl.PRODUCTID_TO=clp.linked_product_id AND clp.link_type=0");

        cw_csvxc_logged_query("REPLACE INTO $fi_tables[linked_products] (product_id, linked_product_id, orderby, active, link_type) SELECT PRODUCTID, PRODUCTID_TO, ORDERBY, ACTIVE, 0 FROM tmp_load_PRODUCT_LINKS");

    }
}

if (cw_csvxc_is_table_exists('tmp_load_EXTRA_FIELDS') && $csvxc_allowed_sections['tmp_load_EXTRA_FIELDS']) {

    cw_csvxc_set_col_exist_flag('tmp_load_EXTRA_FIELDS');

    if ($csvxc_fmap['tmp_load_EXTRA_FIELDS']['SERVICE_NAME']['exists_column'] && $csvxc_fmap['tmp_load_EXTRA_FIELDS']['FIELD']['exists_column']) {

//        if ($csvxc_fmap['tmp_load_EXTRA_FIELDS']['ACTIVE']['exists_column'])
//            cw_csvxc_logged_query("UPDATE tmp_load_EXTRA_FIELDS SET ACTIVE=IF(ACTIVE='Y', 1,0)");

        cw_csvxc_logged_query("ALTER TABLE tmp_load_EXTRA_FIELDS ADD COLUMN ref_attribute_id INT(11) NOT NULL DEFAULT 0");
        cw_csvxc_logged_query("ALTER TABLE tmp_load_EXTRA_FIELDS ADD COLUMN ref_is_new int(1) not null default 1");
        cw_csvxc_logged_query("UPDATE tmp_load_EXTRA_FIELDS, $fi_tables[attributes] SET tmp_load_EXTRA_FIELDS.ref_is_new=0, tmp_load_EXTRA_FIELDS.ref_attribute_id=$fi_tables[attributes].attribute_id WHERE tmp_load_EXTRA_FIELDS.SERVICE_NAME=$fi_tables[attributes].field AND $fi_tables[attributes].item_type='P'");

//        cw_csvxc_logged_query("UPDATE $fi_tables[attributes] ca, tmp_load_EXTRA_FIELDS tlef SET ca.name=tlef.FIELD, ca.orderby=tlef.ORDERBY, ca.active=tlef.ACTIVE WHERE tlef.SERVICE_NAME=ca.field AND ca.item_type='P' AND ca.addon=''");

        $update_fields = cw_csvxc_make_update_fields_arr('tmp_load_EXTRA_FIELDS', $fi_tables['attributes'], 'SERVICE_NAME');
        cw_csvxc_logged_query("UPDATE $fi_tables[attributes], tmp_load_EXTRA_FIELDS SET ".implode(', ', $update_fields)." WHERE tmp_load_EXTRA_FIELDS.SERVICE_NAME=$fi_tables[attributes].field AND $fi_tables[attributes].item_type='P' AND $fi_tables[attributes].addon=''");

        cw_csvxc_logged_query("INSERT INTO $fi_tables[attributes] (name, type, field, orderby, item_type, addon) SELECT FIELD, 'text', SERVICE_NAME, ORDERBY, 'P','' FROM tmp_load_EXTRA_FIELDS WHERE ref_attribute_id=0");

        cw_csvxc_logged_query("UPDATE tmp_load_EXTRA_FIELDS tlef, $fi_tables[attributes] ca SET tlef.ref_attribute_id=ca.attribute_id WHERE tlef.SERVICE_NAME=ca.field AND ca.item_type='P'");

        cw_csvxc_logged_query("REPLACE INTO $fi_tables[attributes_classes_assignement] (attribute_class_id, attribute_id) SELECT $default_attr_classid, ref_attribute_id FROM tmp_load_EXTRA_FIELDS WHERE ref_is_new=1");
    }
}

if (cw_csvxc_is_table_exists('tmp_load_PRODUCTS_EXTRA_FIELD_VALUES') && $csvxc_allowed_sections['tmp_load_PRODUCTS_EXTRA_FIELD_VALUES']) {

    cw_csvxc_set_col_exist_flag('tmp_load_PRODUCTS_EXTRA_FIELD_VALUES');

    if ($csvxc_fmap['tmp_load_PRODUCTS_EXTRA_FIELD_VALUES']['PRODUCTID']['exists_column']) {
        $efv_columns = cw_csvxc_get_table_fields('tmp_load_PRODUCTS_EXTRA_FIELD_VALUES');

        cw_csvxc_logged_query("DELETE tmp_load_PRODUCTS_EXTRA_FIELD_VALUES.* FROM tmp_load_PRODUCTS_EXTRA_FIELD_VALUES LEFT JOIN $fi_tables[products] ON $fi_tables[products].product_id=tmp_load_PRODUCTS_EXTRA_FIELD_VALUES.PRODUCTID WHERE $fi_tables[products].product_id IS NULL");
        cw_csvxc_logged_query("ALTER TABLE tmp_load_PRODUCTS_EXTRA_FIELD_VALUES ADD COLUMN ref_is_new int(1) NOT NULL DEFAULT 1");
        foreach ($efv_columns as $ef_colname) {
            if (in_array($ef_colname, array("id_products_extra_field_values","PRODUCTID", "PRODUCTCODE", "PRODUCT"))) continue;
               
            $attr_info = cw_query_first("SELECT * FROM $fi_tables[attributes] WHERE field='$ef_colname' AND item_type='P' AND addon in ('', ".fi_addons_names_set.")");
            $attr_id = $attr_info['attribute_id'];
            if ($attr_id) {
                if ($attr_info['type'] == 'selectbox') {
                    $id_ef_colname = $ef_colname."_id";
                    cw_csvxc_logged_query("ALTER TABLE tmp_load_PRODUCTS_EXTRA_FIELD_VALUES ADD COLUMN `$id_ef_colname` int(11) NOT NULL DEFAULT 0");

                    cw_csvxc_logged_query("UPDATE tmp_load_PRODUCTS_EXTRA_FIELD_VALUES tlpefv, $fi_tables[attributes_default] cad SET tlpefv.`$id_ef_colname`=cad.attribute_value_id WHERE cad.attribute_id='$attr_id' AND cad.value=tlpefv.`$ef_colname`");
                    cw_csvxc_logged_query("INSERT INTO $fi_tables[attributes_default] (value, attribute_id, active, facet) SELECT distinct(`$ef_colname`), $attr_id, 1, $attr_info[facet] FROM tmp_load_PRODUCTS_EXTRA_FIELD_VALUES WHERE `$id_ef_colname`=0");
                    cw_csvxc_logged_query("UPDATE tmp_load_PRODUCTS_EXTRA_FIELD_VALUES tlpefv, $fi_tables[attributes_default] cad SET tlpefv.`$id_ef_colname`=cad.attribute_value_id WHERE cad.attribute_id='$attr_id' AND cad.value=tlpefv.`$ef_colname` AND tlpefv.`$id_ef_colname`=0");
                    cw_csvxc_logged_query("UPDATE tmp_load_PRODUCTS_EXTRA_FIELD_VALUES tlpefv, $fi_tables[attributes_values] cav SET tlpefv.ref_is_new=0 WHERE tlpefv.PRODUCTID=cav.item_id AND cav.attribute_id='$attr_id' AND cav.item_type='P'", "$ef_colname ");
                    cw_csvxc_logged_query("UPDATE $fi_tables[attributes_values] cav, tmp_load_PRODUCTS_EXTRA_FIELD_VALUES tlpefv SET cav.value=tlpefv.`$id_ef_colname` WHERE tlpefv.ref_is_new=0 AND tlpefv.PRODUCTID=cav.item_id AND cav.item_type='P' AND cav.attribute_id='$attr_id'", ' ');
                    cw_csvxc_logged_query("INSERT INTO $fi_tables[attributes_values] (item_id, attribute_id, value, code, item_type) SELECT PRODUCTID, $attr_id, `$id_ef_colname`, 'EN', 'P' FROM tmp_load_PRODUCTS_EXTRA_FIELD_VALUES WHERE ref_is_new=1", ' ');

                } else {
                    cw_csvxc_logged_query("UPDATE tmp_load_PRODUCTS_EXTRA_FIELD_VALUES tlpefv, $fi_tables[attributes_values] cav SET tlpefv.ref_is_new=0 WHERE tlpefv.PRODUCTID=cav.item_id AND cav.attribute_id='$attr_id' AND cav.item_type='P'", "$ef_colname ");
                    cw_csvxc_logged_query("UPDATE $fi_tables[attributes_values] cav, tmp_load_PRODUCTS_EXTRA_FIELD_VALUES tlpefv SET cav.value=tlpefv.`$ef_colname` WHERE tlpefv.ref_is_new=0 AND tlpefv.PRODUCTID=cav.item_id AND cav.item_type='P' AND cav.attribute_id='$attr_id'", ' ');
                    cw_csvxc_logged_query("INSERT INTO $fi_tables[attributes_values] (item_id, attribute_id, value, code, item_type) SELECT PRODUCTID, $attr_id, `$ef_colname`, 'EN', 'P' FROM tmp_load_PRODUCTS_EXTRA_FIELD_VALUES WHERE ref_is_new=1", ' ');
                }  
                cw_csvxc_logged_query("UPDATE tmp_load_PRODUCTS_EXTRA_FIELD_VALUES SET ref_is_new=1", ' ');
            }
        }
    }
}

if (cw_csvxc_is_table_exists('tmp_load_CATEGORIES_EXTRA_FIELD_VALUES') && $csvxc_allowed_sections['tmp_load_CATEGORIES_EXTRA_FIELD_VALUES']) {

    cw_csvxc_set_col_exist_flag('tmp_load_CATEGORIES_EXTRA_FIELD_VALUES');

    if ($csvxc_fmap['tmp_load_CATEGORIES_EXTRA_FIELD_VALUES']['CATEGORYID']['exists_column']) {
        $efv_columns = cw_csvxc_get_table_fields('tmp_load_CATEGORIES_EXTRA_FIELD_VALUES');

        cw_csvxc_logged_query("DELETE tmp_load_CATEGORIES_EXTRA_FIELD_VALUES.* FROM tmp_load_CATEGORIES_EXTRA_FIELD_VALUES LEFT JOIN $fi_tables[categories] ON $fi_tables[categories].category_id=tmp_load_CATEGORIES_EXTRA_FIELD_VALUES.CATEGORYID WHERE $fi_tables[categories].category_id IS NULL");
        cw_csvxc_logged_query("ALTER TABLE tmp_load_CATEGORIES_EXTRA_FIELD_VALUES ADD COLUMN ref_is_new int(1) NOT NULL DEFAULT 1");
        foreach ($efv_columns as $ef_colname) {
            if (in_array($ef_colname, array("id_categories_extra_field_values","CATEGORYID", "CATEGORY"))) continue;

            $attr_info = cw_query_first($qry="SELECT * FROM $fi_tables[attributes] WHERE field='$ef_colname' AND item_type='C' AND addon in ('', ".fi_addons_names_set.")");
            $attr_id = $attr_info['attribute_id'];

            cw_log_add('flexible_import', array($qry, $attr_info));

            if ($attr_id) {
                if ($attr_info['type'] == 'selectbox') {
                    $id_ef_colname = $ef_colname."_id";
                    cw_csvxc_logged_query("ALTER TABLE tmp_load_CATEGORIES_EXTRA_FIELD_VALUES ADD COLUMN `$id_ef_colname` int(11) NOT NULL DEFAULT 0");

                    cw_csvxc_logged_query("UPDATE tmp_load_CATEGORIES_EXTRA_FIELD_VALUES tlpefv, $fi_tables[attributes_default] cad SET tlpefv.`$id_ef_colname`=cad.attribute_value_id WHERE cad.attribute_id='$attr_id' AND cad.value=tlpefv.`$ef_colname`");

                    cw_csvxc_logged_query("INSERT INTO $fi_tables[attributes_default] (value, attribute_id, active, facet) SELECT distinct(`$ef_colname`), $attr_id, 1, $attr_info[facet] FROM tmp_load_CATEGORIES_EXTRA_FIELD_VALUES WHERE `$id_ef_colname`=0");

                    cw_csvxc_logged_query("UPDATE tmp_load_CATEGORIES_EXTRA_FIELD_VALUES tlpefv, $fi_tables[attributes_default] cad SET tlpefv.`$id_ef_colname`=cad.attribute_value_id WHERE cad.attribute_id='$attr_id' AND cad.value=tlpefv.`$ef_colname` AND tlpefv.`$id_ef_colname`=0");

                    cw_csvxc_logged_query("UPDATE tmp_load_CATEGORIES_EXTRA_FIELD_VALUES tlpefv, $fi_tables[attributes_values] cav SET tlpefv.ref_is_new=0 WHERE tlpefv.CATEGORYID=cav.item_id AND cav.attribute_id='$attr_id' AND cav.item_type='C'", "$ef_colname ");

                    cw_csvxc_logged_query("UPDATE $fi_tables[attributes_values] cav, tmp_load_CATEGORIES_EXTRA_FIELD_VALUES tlpefv SET cav.value=tlpefv.`$id_ef_colname` WHERE tlpefv.ref_is_new=0 AND tlpefv.CATEGORYID=cav.item_id AND cav.item_type='C' AND cav.attribute_id='$attr_id'", ' ');

                    cw_csvxc_logged_query("INSERT INTO $fi_tables[attributes_values] (item_id, attribute_id, value, code, item_type) SELECT CATEGORYID, $attr_id, `$id_ef_colname`, 'EN', 'C' FROM tmp_load_CATEGORIES_EXTRA_FIELD_VALUES WHERE ref_is_new=1", ' ');

                } else {
                    cw_csvxc_logged_query("UPDATE tmp_load_CATEGORIES_EXTRA_FIELD_VALUES tlpefv, $fi_tables[attributes_values] cav SET tlpefv.ref_is_new=0 WHERE tlpefv.CATEGORYID=cav.item_id AND cav.attribute_id='$attr_id' AND cav.item_type='C'", "$ef_colname ");
                    cw_csvxc_logged_query("UPDATE $fi_tables[attributes_values] cav, tmp_load_CATEGORIES_EXTRA_FIELD_VALUES tlpefv SET cav.value=tlpefv.`$ef_colname` WHERE tlpefv.ref_is_new=0 AND tlpefv.CATEGORYID=cav.item_id AND cav.item_type='C' AND cav.attribute_id='$attr_id'", ' ');
                    cw_csvxc_logged_query("INSERT INTO $fi_tables[attributes_values] (item_id, attribute_id, value, code, item_type) SELECT CATEGORYID, $attr_id, `$ef_colname`, 'EN', 'C' FROM tmp_load_CATEGORIES_EXTRA_FIELD_VALUES WHERE ref_is_new=1", ' ');
                }
                cw_csvxc_logged_query("UPDATE tmp_load_CATEGORIES_EXTRA_FIELD_VALUES SET ref_is_new=1", ' ');
            }
        }
    }
}



if (cw_csvxc_is_table_exists('tmp_load_DETAILED_IMAGES') && $csvxc_allowed_sections['tmp_load_DETAILED_IMAGES']) {

    cw_csvxc_set_col_exist_flag('tmp_load_DETAILED_IMAGES');

//    cw_csvxc_logged_query("UPDATE tmp_load_DETAILED_IMAGES SET IMAGE=REPLACE(IMAGE,'http://www.thestainlesssteelstore.com/images','')");
//    cw_csvxc_logged_query("UPDATE tmp_load_DETAILED_IMAGES SET IMAGE=REPLACE(IMAGE,'http://www.justaddressplaques.com/images','')");
//    cw_csvxc_logged_query("UPDATE tmp_load_DETAILED_IMAGES SET IMAGE=REPLACE(IMAGE,'http://eplanters.com/images', '')");

    cw_csvxc_delete_old_images("SELECT $fi_tables[products_detailed_images].image_path AS img_full_path, $fi_tables[products_detailed_images].id AS img_key_fld FROM $fi_tables[products_detailed_images], tmp_load_PRODUCTS WHERE tmp_load_PRODUCTS.PRODUCTID=$fi_tables[products_detailed_images].id AND tmp_load_PRODUCTS.PRODUCT!=''", 'products_detailed_images');

    for ($img_id=1; $img_id<=5; $img_id++)
        cw_csvxc_transfer_import_images('tmp_load_DETAILED_IMAGES', 'IMAGE'.$img_id, 'PRODUCTID');

}
