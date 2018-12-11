<?php
namespace cw\webmaster;

/** =============================
 ** Addon functions, API
 ** =============================
 **/


/** =============================
 ** Hooks
 ** =============================
 **/



/** =============================
 ** Events handlers
 ** =============================
 **/
function webmaster_view_cms($contentsection_id) {

    $cms = cw_ab_get_contentsection($contentsection_id);

    if ($cms['type'] != 'html') return error('This type can\'t be edited inline. Use full edit form in admin area.');
    
    $value = $cms['content'];
    
    return array('value' => $value, 'popup_title' => $cms['service_code']);
    
}

function webmaster_modify_cms($contentsection_id, $post) {
    global $current_language, $config;

    $data = array(
      'content' => htmlspecialchars_decode(trim($post['value']))
    );
    if ($current_language == $config['default_customer_language']) {
      cw_array2update('cms', $data, "contentsection_id = '".$contentsection_id."'");
    }
    cw_array2update('cms_alt_languages', $data, "contentsection_id = '".$contentsection_id."' AND code = '".$current_language."'");

}

function webmaster_view_langvar($name) {

    $value = cw_get_langvar_by_name($name,null,false, true);
    
    $value = preg_replace('/<webmaster.+webmaster>/','',$value);
    
    return array('value' => $value, 'popup_title' => $name);
    
}

function webmaster_modify_langvar($langvar, $post) {
    global $current_language, $config;

    $data = array(
      'value' => htmlspecialchars_decode(trim($post['value']))
    );
    if ($current_language == $config['default_customer_language']) {
      cw_array2update('languages', $data, "name = '".$langvar."'");
    }
    cw_array2update('languages_alt', $data, "name = '".$langvar."' AND code = '".$current_language."'");

    cw_cache_clean('lang');
}

function webmaster_view_attribute_name($attribute_id) {

    $attribute = cw_attributes_get_attribute(array('attribute_id'=>$attribute_id));

    return array('value' => $attribute['name']);
}

function webmaster_modify_attribute_name($attribute_id, $post) {
    global $current_language, $config;

    $data = array(
        'name' => htmlspecialchars_decode(trim($post['value']))
    );
     
    if ($current_language == $config['default_customer_language']) {
        cw_array2update('attributes', $data, "attribute_id = '".$attribute_id."'");
    }

    $data['code'] = $current_language;
    $data['attribute_id'] = $attribute_id;

    cw_array2insert('attributes_lng', $data, true);
    cw_cache_clean('PF_home'); cw_cache_clean('PF_search');
}

function webmaster_view_attribute_value($attribute_value_id) {
    global $tables, $current_language; 

    $attribute_value = cw_query_first_cell("select ifnull(adl.value, ad.value) as value from $tables[attributes_default] as ad left join $tables[attributes_default_lng] as adl on ad.attribute_value_id=adl.attribute_value_id and adl.code='$current_language' where ad.attribute_value_id='$attribute_value_id'");

    return array('value' => $attribute_value); 
}

function webmaster_modify_attribute_value($attribute_value_id, $post) {
    global $current_language, $config;

    $data = array(
        'value' => htmlspecialchars_decode(trim($post['value']))
    );
     
    if ($current_language == $config['default_customer_language']) {
        cw_array2update('attributes_default', $data, "attribute_value_id = '".$attribute_value_id."'");
    }

    $data['code'] = $current_language;
    $data['attribute_value_id'] = $attribute_value_id;

    cw_array2insert('attributes_default_lng', $data, true);
    cw_cache_clean('PF_home'); cw_cache_clean('PF_search');
}

function webmaster_view_custom_facet_title($url_id) {
    global $tables;

    if (strpos($url_id, ',') === false) 
        $facet_title = cw_query_first_cell("select title from $tables[clean_urls_custom_facet_urls] where url_id='".intval($url_id)."'");
    return array('value' => $facet_title);
}

function webmaster_modify_custom_facet_title($url_id, $post) {
    global $tables;
    $title = trim($post['value']);
    if (strpos($url_id, ',') === false && cw_query_first_cell("SELECT count(*) FROM $tables[clean_urls_custom_facet_urls] WHERE url_id='".intval($url_id)."'")) {
        cw_array2update('clean_urls_custom_facet_urls', array('title'=>$title), "url_id = '".intval($url_id)."'");
    } else {
        $attribute_value_ids = explode(',', $url_id);       
        //find related url_id
        $clean_url_values = array();
        $clean_urls_cond = array();
        foreach ($attribute_value_ids as $attribute_value_id) {
            $cu_value = cw_query_first_cell("select av.value from $tables[attributes_values] av inner join $tables[attributes] a on a.attribute_id = av.attribute_id and a.addon='clean_urls' and a.field='clean_url' and av.item_type=a.item_type and a.item_type='AV' where av.item_id='".intval($attribute_value_id)."'"); 
            if (empty($cu_value)) continue; 
            $clean_url_values[$attribute_value_id] = $cu_value;

            if (count($attribute_value_ids) > 1)    
                $clean_urls_cond[] = "clean_urls LIKE '".addslashes($cu_value)."|%' or clean_urls LIKE '%|".addslashes($cu_value)."|%' or clean_urls LIKE '%|".addslashes($cu_value)."'";
            else
                $clean_urls_cond[] = "clean_urls = '".addslashes($cu_value)."'";

        } 
        $existing_url_id = cw_query_first_cell("select url_id from $tables[clean_urls_custom_facet_urls_options] where (".implode(') and (', $clean_urls_cond).")"); 
        //or create it
        if (empty($existing_url_id)) {
            $existing_url_id = cw_array2insert('clean_urls_custom_facet_urls', array('custom_facet_url'=>'', 'description'=>'Description '.$title, 'title'=>$title));
            if ($existing_url_id)
                cw_array2insert('clean_urls_custom_facet_urls_options', array('url_id'=>$existing_url_id, 'attribute_value_ids'=>implode(',', array_keys($clean_url_values)), 'clean_urls'=>implode('|', $clean_url_values))); 
        } else {
            cw_array2update('clean_urls_custom_facet_urls', array('title'=>$title), "url_id = '$existing_url_id'");
        }
    } 
}

function webmaster_view_custom_facet_desc($url_id) {
    global $tables;

    if (strpos($url_id, ',') === false) 
        $facet_desc = cw_query_first_cell("select description from $tables[clean_urls_custom_facet_urls] where url_id='".intval($url_id)."'");

    return array('value' => $facet_desc);
}

function webmaster_modify_custom_facet_desc($url_id, $post) {
    global $tables;
    $desc = trim($post['value']);
    if (strpos($url_id, ',') === false && cw_query_first_cell("SELECT count(*) FROM $tables[clean_urls_custom_facet_urls] WHERE url_id='".intval($url_id)."'")) {
        cw_array2update('clean_urls_custom_facet_urls', array('description'=>$desc), "url_id = '".intval($url_id)."'");
    } else { 
        $attribute_value_ids = explode(',', $url_id);       
        //find related url_id
        $clean_url_values = array();
        $clean_urls_cond = array();
        foreach ($attribute_value_ids as $attribute_value_id) {
            $cu_value = cw_query_first_cell("select av.value from $tables[attributes_values] av inner join $tables[attributes] a on a.attribute_id = av.attribute_id and a.addon='clean_urls' and a.field='clean_url' and av.item_type=a.item_type and a.item_type='AV' where av.item_id='".intval($attribute_value_id)."'");                             
            if (empty($cu_value)) continue;
            $clean_url_values[$attribute_value_id] = $cu_value;

            if (count($attribute_value_ids) > 1)
                $clean_urls_cond[] = "clean_urls LIKE '".addslashes($cu_value)."|%' or clean_urls LIKE '%|".addslashes($cu_value)."|%' or clean_urls LIKE '%|".addslashes($cu_value)."'";
            else
                $clean_urls_cond[] = "clean_urls = '".addslashes($cu_value)."'";

        } 
        $existing_url_id = cw_query_first_cell("select url_id from $tables[clean_urls_custom_facet_urls_options] where (".implode(') and (', $clean_urls_cond).")"); 
        //or create it
        if (empty($existing_url_id)) { 
            $existing_url_id = cw_array2insert('clean_urls_custom_facet_urls', array('custom_facet_url'=>'', 'description'=>$desc, 'title'=>'Title '.$desc));
            if ($existing_url_id) 
                cw_array2insert('clean_urls_custom_facet_urls_options', array('url_id'=>$existing_url_id, 'attribute_value_ids'=>implode(',', array_keys($clean_url_values)), 'clean_urls'=>implode('|', $clean_url_values)));

        } else {
            cw_array2update('clean_urls_custom_facet_urls', array('description'=>$desc), "url_id = '$existing_url_id'");
        } 
    } 
}

function webmaster_view_images($id) {
    global $smarty, $request_prepared;

    $smarty->assign('upload_max_filesize', ini_get('upload_max_filesize')); 

    if ($request_prepared['type'] == 'cms_images') {
        $cms = cw_ab_get_contentsection($id);

        return array('popup_title' => $cms['service_code']);
    }
}

function webmaster_modify_images($id, $post) {
    global $available_images;

    cw_load('image');

    $type = $_POST['type'];

    $file_upload_data = array(); 

    if (!isset($available_images[$type]) || empty($type))
        return;

    $file_upload_data = cw_process_image_save_tmp($type, $_FILES['userfiles'], $_POST['filenames'], $_POST['fileurls']);

    if (cw_image_check_posted($file_upload_data[$type]))
        cw_image_save($file_upload_data[$type], array('id' => $id));

}

function webmaster_view_product_fulldescr($product_id) {
    global $current_language;

    global $tables;

    $fulldescr = cw_query_first_cell("select ifnull(pl.fulldescr, p.fulldescr) as value from $tables[products] as p left join $tables[products_lng] as pl on p.product_id=pl.product_id and pl.code='$current_language' where p.product_id='$product_id'");

    return array('value' => $fulldescr);
} 

function webmaster_view_product_descr($product_id) {
    global $current_language, $tables;

    $descr = cw_query_first_cell("select ifnull(pl.descr, p.descr) as value from $tables[products] as p left join $tables[products_lng] as pl on p.product_id=pl.product_id and pl.code='$current_language' where p.product_id='$product_id'");

    return array('value' => $descr);
}

function webmaster_modify_product_fulldescr($product_id, $post) {
    global $current_language, $config;

    $data = array(
        'fulldescr' => htmlspecialchars_decode(trim($post['value']))
    );
    if ($current_language == $config['default_customer_language']) {
        cw_array2update('products', $data, "product_id = '".$product_id."'");
    }
    cw_array2update('products_lng', $data, "product_id = '".$product_id."' AND code = '".$current_language."'");
}

function webmaster_modify_product_descr($product_id, $post) {
    global $current_language, $config;

    $data = array(
        'descr' => htmlspecialchars_decode(trim($post['value']))
    );
    if ($current_language == $config['default_customer_language']) {
        cw_array2update('products', $data, "product_id = '".$product_id."'");
    }
    cw_array2update('products_lng', $data, "product_id = '".$product_id."' AND code = '".$current_language."'");
}

function webmaster_view_manufacturer_descr($manufacturer_id) {
    global $current_language, $tables;

    $descr = cw_query_first_cell("select ifnull(ml.descr, m.descr) as value from $tables[manufacturers] as m left join $tables[manufacturers_lng] as ml on m.manufacturer_id=ml.manufacturer_id and ml.code='$current_language' where m.manufacturer_id='$manufacturer_id'");

    return array('value' => $descr);
}

function webmaster_modify_manufacturer_descr($manufacturer_id, $post) {
    global $current_language, $config;

    $data = array(
        'descr' => htmlspecialchars_decode(trim($post['value']))
    );
    if ($current_language == $config['default_customer_language']) {
        cw_array2update('manufacturers', $data, "manufacturer_id = '".$manufacturer_id."'");
    }
    cw_array2update('manufacturers_lng', $data, "manufacturer_id = '".$manufacturer_id."' AND code = '".$current_language."'");
}
