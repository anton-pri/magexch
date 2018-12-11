<?php
$mysql_acc_str = 'mysqldump -u saratoga_dbuser -pvm5BTzB=4QXn';
$dump_path = "/home/saratoga/public_html/files/cw_import_backups/";
$date_str = date('Y-m-d__H-i-s');

$cw_tables_list = "cw_products_system_info cw_products_prices cw_products_warehouses_amount cw_products_detailed_images cw_products_images_det cw_products_images_thumb cw_products cw_attributes_values cw_attributes_default cw_attributes_classes_assignement cw_attributes cw_categories_images_thumb cw_categories_parents cw_categories cw_manufacturers cw_memberships cw_product_options_values cw_product_options cw_product_variant_items cw_product_variants cw_linked_products cw_products_memberships";

$mysql_db_name = "saratoga_cw";

system($s = "$mysql_acc_str $mysql_db_name $cw_tables_list > ".$dump_path."dump0_cw_item_$date_str.sql");
die(str_replace($mysql_acc_str,'',$s));
