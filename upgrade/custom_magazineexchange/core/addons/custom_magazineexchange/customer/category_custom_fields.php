<?php
if ($cat) {
    $_category_magexch_attributes = cw_func_call('cw_attributes_get', array('item_id'=>$cat, 'item_type'=>'C', 'attribute_addons'=>array('custom_magazineexchange')));

    $category_magexch_attributes = array();
    foreach ($_category_magexch_attributes as $attr_field => $attr_data) {
        if (!($vendorid && in_array($attr_field, array('magexch_category_tab_title_2','magexch_category_tab_title_3','magexch_category_tab_title_4','magexch_category_tab_content_2','magexch_category_tab_content_3','magexch_category_tab_content_4')))) {
            $category_magexch_attributes[$attr_field] = $attr_data['values_str'][0];
        }
    }

    $category_magexch_attributes['orig_magexch_category_type'] = $category_magexch_attributes['magexch_category_type'];    

    if ($vendorid) {
        $seller_custom_fields = cw_user_get_custom_fields($vendorid,0,'','field');

        if ($seller_custom_fields['products_disabled'] == "Y") 
            cw_header_location("index.php");
 
        $shopfront = cw_call('cw\custom_magazineexchange_sellers\mag_get_shopfront', array($vendorid));

        $category_magexch_attributes['magexch_category_tab_title_2'] = 'seller information & feedback';
        $smarty->assign('shopfront', $shopfront);
        $smarty->assign('vendorid', $vendorid);
        $category_magexch_attributes['magexch_category_tab_content_2'] = $smarty->fetch('customer/vendor_page_tab2_content.tpl');
/*
        $category_magexch_attributes['magexch_category_tab_content_2'] = 
            "<div style=\"font-size: 12px;margin-bottom:7px\">
               <span style=\"font-weight: bold; color: #ff0008;\">More information:-</span>
               feedback from customers and information provided by the seller themselves
            </div>
            <div style=\"padding: 10px; font-size: 12px; background: white;border:1px solid lightgray\">".str_replace("\n",'<br>',$shopfront[long_desc])."</div>";
*/

        $smarty->assign('shopfront', $shopfront);    
        $smarty->assign('vendorid', $vendorid);
    }

    if (!empty($current_category)) {
        if ($category_magexch_attributes['magexch_category_type'] == 'Magazine') {
            $smarty->assign('current_magazine_name', $current_category['category']); 
        } elseif ($category_magexch_attributes['magexch_category_type'] == 'Year') {
            $current_category_parent = cw_func_call('cw_category_get', array('cat' => $current_category['parent_id']));
            $smarty->assign('current_magazine_name', $current_category_parent['category']);
        }
    }

    if ($vendorid)
        $category_magexch_attributes['magexch_category_type'] = 'Magazine';
 
    $smarty->assign('category_magexch_attributes', $category_magexch_attributes);
} elseif ($target == 'index') {
   $smarty->assign('show_left_bar', 1); 
}
