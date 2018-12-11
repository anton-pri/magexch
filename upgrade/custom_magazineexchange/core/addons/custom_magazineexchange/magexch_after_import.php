<?php

cw_display_service_header("lbl_after_import_setup");

$image_urls_attributes = cw_query("select attribute_id from cw_attributes where item_type='C' and addon='custom_magazineexchange' and field in ('magexch_popup_category_image', 'magexch_category_avatar_image', 'magexch_category_rollover_image')");

foreach ($image_urls_attributes as $attr) {
    cw_csvxc_logged_query("update cw_attributes_values set value=replace(value,'/skin1','xc_skin') where attribute_id = '$attr[attribute_id]' and value like '/skin1%'");
    cw_csvxc_logged_query("update cw_attributes_values set value=replace(value,'skin1','xc_skin') where attribute_id = '$attr[attribute_id]' and value like 'skin1%'");
}


$image_urls_attributes = cw_query("select attribute_id from cw_attributes where item_type='P' and addon='custom_magazineexchange' and field in ('magexch_product_avatar_image','magexch_product_rollover_image')");

foreach ($image_urls_attributes as $attr) {
    cw_csvxc_logged_query("update cw_attributes_values set value=replace(value,'/skin1','xc_skin') where attribute_id = '$attr[attribute_id]' and value like '/skin1%'");
    cw_csvxc_logged_query("update cw_attributes_values set value=replace(value,'skin1','xc_skin') where attribute_id = '$attr[attribute_id]' and value like 'skin1%'");
}


cw_csvxc_logged_query("update cw_categories c, csvxcbak_cw_categories_tm_set c_bak set c.tm_active=c_bak.tm_active where c.category_id=c_bak.category_id");

cw_csvxc_logged_query("replace into cw_products_memberships select product_id, 0 from cw_products where product_id not in (select product_id from cw_products_memberships)");
cw_csvxc_logged_query("replace into cw_categories_memberships select category_id, 0 from cw_categories where category_id not in (select category_id from cw_categories_memberships)");

die();
