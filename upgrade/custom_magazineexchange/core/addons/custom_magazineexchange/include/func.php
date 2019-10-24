<?php

function magexch_get_attribute_value ($item_type, $item_id, $attribute_field='') {

    if (!empty($attribute_field)) {
        $_result = cw_func_call('cw_attributes_get', array('item_id'=>$item_id, 'item_type'=>$item_type, 'attribute_fields'=>array($attribute_field)));        $result = $_result[$attribute_field]['values_str'][0]; 
    } else {
        $_result = cw_func_call('cw_attributes_get', array('item_id'=>$item_id, 'item_type'=>$item_type,'attribute_addons'=>array('custom_magazineexchange'))); 

        $result = array();
        foreach ($_result as $attr_field => $attr_data) {
            $result[$attr_field] = $attr_data['values_str'][0];
        }

    }

    return $result;
}


function magexch_get_section_category_id ($category_id) {
    global $tables;

    $safety_cnt = 10;

    $parent_id = 0;

    do {
//        print("A category_id: $category_id| parent_id: $parent_id| cat_type: $cat_type<br>");

        if ($parent_id) 
            $category_id = $parent_id;

        $cat_type = cw_call('magexch_get_attribute_value', array('C', $category_id, 'magexch_category_type'));
        $parent_id = cw_query_first_cell("select parent_id from $tables[categories] where category_id = '$category_id'");

        $safety_cnt--;
//        print("B category_id: $category_id| parent_id: $parent_id| cat_type: $cat_type<br>");
    } while ($cat_type != 'Section' && $safety_cnt > 0);
   
    if ($cat_type == 'Section') 
        return $category_id;
    else 
        return 0;     

}

function magexch_get_cms_by_tab_content_id ($tab_content) {
    global $tables;
//    $contentsection_id = cw_query_first_cell("select av.item_id from $tables[attributes_values] av, $tables[attributes] a where av.attribute_id=a.attribute_id and a.field='magexch_xc_pageid' and av.item_type='AB' and av.value='$tab_content'");
    $contentsection_id = $tab_content;
    if ($contentsection_id) {
        $contentsection = cw_call('cw_ab_get_contentsection', array($contentsection_id));
    }
    return $contentsection['content'];
}


function magexch_sort_by_month_time_of_year ($params, $return) {
    global $tables, $current_language, $config;

    global $allowed_products_sort_fields;
    $allowed_products_sort_fields[] = 'month_id';

//global $REMOTE_ADDR;
//if ($REMOTE_ADDR != '87.120.150.77') {
/*
    $return['query_joins']['month_attr'] = array(
        'tblname' => 'attributes_values',
        'on' => "$tables[products].product_id=month_attr.item_id and month_attr.item_type='P' and month_attr.attribute_id=179",
    );

    $return['fields'][] = "IF(LOCATE('january', month_attr.value),0,
                             IF(LOCATE('february', month_attr.value),1,
                               IF(LOCATE('march', month_attr.value),2,
                                 IF(LOCATE('april', month_attr.value),3,
                                   IF(LOCATE('may', month_attr.value),4,
                                     IF(LOCATE('june', month_attr.value),5,
                                       IF(LOCATE('july', month_attr.value),6,
                                         IF(LOCATE('august', month_attr.value),7,
                                           IF(LOCATE('sept', month_attr.value),8,
                                             IF(LOCATE('oct', month_attr.value),9,
                                               IF(LOCATE('nov', month_attr.value),10,
                                                 IF(LOCATE('december', month_attr.value),11,
                                                   IF(LOCATE('winter', month_attr.value)=1,0,
                                                     IF(LOCATE('spring', month_attr.value)=1,1,
                                                       IF(LOCATE('summer', month_attr.value)=1,2,
                                                         IF(LOCATE('autumn', month_attr.value)=1,3,0)
                                                         )
                                                       )
                                                     ) 
                                                   )
                                                 ) 
                                               )
                                             )
                                           ) 
                                         )   
                                       ) 
                                     )
                                   )
                                 )
                               )
                             ) as month_id";
*/
//}

    return new EventReturn($return, $params); 

}

function magexch_on_prepare_search_products($params, &$fields, &$from_tbls, &$query_joins, &$where, &$groupbys, &$having, &$orderbys) {
    global $current_area, $tables;
 
//global $REMOTE_ADDR;
//if ($REMOTE_ADDR == '87.120.150.77') {

    if ($params['data']['category_id'] && $current_area == 'C') {

        $query_joins['month_attr'] = array(
            'tblname' => 'attributes_values',
            'on' => "$tables[products].product_id=month_attr.item_id and month_attr.item_type='P' and month_attr.attribute_id=179",
        );
        $query_joins['cat_sort'] = array(
            'tblname' => 'products_categories',
            'on' => "cat_sort.category_id='".$params['data']['category_id']."' and $tables[products].product_id=cat_sort.product_id",
            'is_inner' => false
        );

        $fields[] = "IF(LOCATE('january', month_attr.value),1000,
                             IF(LOCATE('february', month_attr.value),2000,
                               IF(LOCATE('march', month_attr.value),3000,
                                 IF(LOCATE('april', month_attr.value),4000,
                                   IF(LOCATE('may', month_attr.value),5000,
                                     IF(LOCATE('june', month_attr.value),6000,
                                       IF(LOCATE('july', month_attr.value),7000,
                                         IF(LOCATE('august', month_attr.value),8000,
                                           IF(LOCATE('sept', month_attr.value),9000,
                                             IF(LOCATE('oct', month_attr.value),10000,
                                               IF(LOCATE('nov', month_attr.value),11000,
                                                 IF(LOCATE('december', month_attr.value),12000,
                                                   IF(LOCATE('winter', month_attr.value)=1,1000,
                                                     IF(LOCATE('spring', month_attr.value)=1,2000,
                                                       IF(LOCATE('summer', month_attr.value)=1,3000,
                                                         IF(LOCATE('autumn', month_attr.value)=1,4000,0)
                                                         )
                                                       )
                                                     ) 
                                                   )
                                                 ) 
                                               )
                                             )
                                           ) 
                                         )   
                                       ) 
                                     )
                                   )
                                 )
                               )
                             ) + COALESCE(cat_sort.orderby, 0) as month_id";


    } else {
       $orderbys = array_filter($orderbys, function($v) {return (strpos($v, 'month_id')===false);});
    }
//}

}

function magexch_get_breadcrumbs () {
    global $config, $tables, $current_language,$target,$cat;

    global $vendorid;

    $location = array();

    if ($target == 'search') 
        return array();

    if ($target == 'pages') {
        global $smarty;
        $page_data = $smarty->_tpl_vars['page_data'];

        $force_staticpages_breadcrumbs = $smarty->_tpl_vars['force_staticpages_breadcrumbs'];

        if ($force_staticpages_breadcrumbs == "Y") {
            $location[] = array($config['Company']['company_name'], 'index.php');
            $location[] = array($page_data['name'], '');  
        } 
    }

    if ($target == 'pages') 
        return $location;

    $location[] = array($config['Company']['company_name'], 'index.php');
    if (!empty($config['custom_magazineexchange']['magexch_default_root_category']) && $target != 'product') { 
         
        $location[] = array('ALL SELLERS', cw_call('cw_core_get_html_page_url', array(array('var' => 'index', 'cat' => $config['custom_magazineexchange']['magexch_default_root_category']))));
 
         if ($vendorid) { 
             $vendor_bc_name = cw_call('magexch_get_vendor_text_name', array($vendorid));
             $location[] = array($vendor_bc_name, cw_call('cw_core_get_html_page_url', array(array('var' => 'index', 'cat' => $config['custom_magazineexchange']['magexch_default_root_category'], 'force_vendorid' => true)))); 
         }
    }

    if ($target == 'product' && empty($cat)) { 
        global $smarty;
        $product = $smarty->_tpl_vars['product'];
        $cat = $product['category_id'];
    }

    if (($target == 'index' || $target == 'product') && !empty($cat)) {
/*
        if ($cat != 0 && $cat != $config['custom_magazineexchange']['magexch_default_root_category']) {
            $safety_cnt = 10;
            $parent_path = array();
            $category_id = $cat;
            $cat_path = array(array(cw_query_first_cell("select IFNULL(lng.category, c.category) as category from $tables[categories] as c
                              left join $tables[categories_lng] as lng on lng.category_id=c.category_id and lng.code='$current_language' 
                              where c.category_id = '$category_id'"),
                              cw_call('cw_core_get_html_page_url', array(array('var' => 'index', 'cat' => $category_id))))); 
            do {

                if (!empty($parent_path)) {       
                    $cat_path[] = array($parent_path[0]['category'], 
                                        cw_call('cw_core_get_html_page_url', 
                                                 array(array('var' => 'index', 'cat' => $parent_path[0]['category_id']))
                                               )
                                       );  
                    $category_id = $parent_path[0]['category_id'];  
                }
                $parent_path = cw_call('cw_category_get_path_categories', array($category_id));
print_r(array('cat_path'=>$cat_path, 'parent_path' => $parent_path, 'category_id'=>$category_id));
                $safety_cnt--;
            } while ($parent_path[0]['category_id'] != 0 && $parent_path[0]['category_id'] != $config['custom_magazineexchange']['magexch_default_root_category'] && $safety_cnt > 0);
        }
*/
        
        $parent_path = cw_call('cw_category_get_path_categories', array($cat));
        if (!empty($parent_path)) {
            $cat_path = array();  
            $parent_path = array_reverse($parent_path);
            foreach ($parent_path as $parent_cat) {
                if ($parent_cat['category_id'] == 0 || 
                  $parent_cat['category_id'] == $config['custom_magazineexchange']['magexch_default_root_category']) {
                    break;   
                } else {
                    $cat_path[] = array($parent_cat['category'], 
                                        cw_call('cw_core_get_html_page_url', 
                                            array(array('var' => 'index', 'cat' => $parent_cat['category_id']))
                                        ));
                }  
            } 
        }  

        if (is_array($cat_path))
            $location = array_merge($location, array_reverse($cat_path));

        if ($target == 'product') {
            global $smarty;
            $product = $smarty->_tpl_vars['product'];
            $location[] = array($product['product'], '');
        }
    }   

    return $location;
}

function magexch_core_get_config() {

    $return = cw_get_return();

    if (!in_array($return['Appearance']['products_order'], array('productcode', 'title', 'orderby', 'price'))) 
        $return['Appearance']['products_order'] = 'productcode';

    return $return;
}


function magexch_get_prev_next_category_ids($current_category_id, $parent_id = null) {
    global $tables, $smarty;

    global $vendorid;
    if (!empty($vendorid)) {
        $navigation = $smarty->_tpl_vars['navigation'];
        if ($navigation['total_pages'] > 1 && $navigation['page'] < $navigation['total_pages_minus'])
           $next_url = cw_core_assign_addition_params($navigation['script'], array('page'=>$navigation['page']+1, 'vendorid'=>$vendorid));
        
        if ($navigation['total_pages'] > 1 && $navigation['page'] > 1)
            $prev_url = cw_core_assign_addition_params($navigation['script'], array('page'=>$navigation['page']-1, 'vendorid'=>$vendorid));

        return array('prev'=>array('url' => $prev_url), 'next'=>array('url' => $next_url));
    } 

    if (!isset($parent_id)) 
        $parent_id = cw_query_first_cell("select parent_id from $tables[categories] where category_id='$current_category_id'");

    global $magexch_get_subcategories_flag;
    $magexch_get_subcategories_flag = 'current';

    $cats =  cw_call('cw_category_get_subcategories', array($parent_id));
   
    global $vendorid;
    if ($vendorid) { 
        $cats = magexch_filter_categories_by_vendor($cats, $vendorid);
    }

    $magexch_get_subcategories_flag = '';
    $prev_cat = array();
    $next_cat = array();  
    $current_prev = array();
    $remember_next = false; 
    foreach ($cats as $c_data) {
        if ($remember_next) {
            $next_cat = $c_data;
            $next_cat['url'] = cw_call('cw_core_get_html_page_url', array(array('var' => 'index', 'cat' => $next_cat['category_id'])));
            $remember_next = false;
        }
        if ($c_data['category_id'] == $current_category_id) {  
            if (!empty($current_prev))  {
                $prev_cat = $current_prev;  
                $prev_cat['url'] = cw_call('cw_core_get_html_page_url', array(array('var' => 'index', 'cat' => $prev_cat['category_id']))); 
            } 
            $remember_next = true;
        }
        $current_prev = $c_data;  
    }
    return array('prev'=>$prev_cat, 'next'=>$next_cat);
}

function magexch_category_search($params, $return) {
    global $tables;
    global $magexch_get_subcategories_flag;
    global $smarty;

    if (($magexch_get_subcategories_flag == 'current' || $smarty->get_template_vars('magexch_get_subcategories_flag') == 'current') && $params['data']['parent_id']) {
        $return['query_joins']['extra_cats'] = array(
            'tblname' => 'categories_extra',
            'on' => "extra_cats.category_id = $tables[categories].category_id"
        );
        $parent_id = $params['data']['parent_id'];
        unset($params['data']['parent_id']); 
        $return['where'][] = "($tables[categories].parent_id='$parent_id' or extra_cats.parent_id = '$parent_id')";
        $return['orderbys'][] = "$tables[categories].order_by";
        $return['orderbys'][] = "$tables[categories].category";
    }

    return new EventReturn($return, $params);

}

function magexch_select_subcategories($category_id, $add_images) {

    cw_load('category','image');

    global $magexch_get_subcategories_flag;

    $magexch_get_subcategories_flag = 'current';
    $categories = cw_call('cw_category_get_subcategories', array($category_id));
    $magexch_get_subcategories_flag = '';

    if ($add_images) {
        foreach ($categories as $k=>$v) {
            $categories[$k]['image'] = cw_call('cw_image_get', array('categories_images_thumb', $v['category_id']));
        }
    }
    return $categories;
}

function magexch_get_extra_categories($category_id) {
    global $tables;

    $result = cw_query_column("select parent_id from $tables[categories_extra] where category_id='$category_id'"); 

    return $result;
}

function magexch_product_get($params, $return) {

    if (isset($return['image_thumb']) && isset($return['image_det'])) {
        if ($return['image_det']['is_default'] && !($return['image_thumb']['is_default'])) {
            $return['image_det'] = $return['image_thumb'];
        }
    } 
    return $return;
}

function magexch_load_custom_template_content($template_name) {
    global $tables;

    $result = '';

    $template_cms_id = cw_query_first_cell("select av.item_id from $tables[attributes_values] av, $tables[attributes] a where a.attribute_id=av.attribute_id and a.field='magexch_custom_page_template_name' and a.item_type='AB' and a.addon='custom_magazineexchange' and av.value='$template_name'");

    if ($template_cms_id) {
        $template_page_data = cw_call('cw_ab_staticpages_get', array('page_id' => $template_cms_id, 'active' => 1));
        $result = $template_page_data['content'];
    }

    return $result;
}

function magexch_top_menu_smarty_init() {

    $return = cw_get_return();

    foreach ($return as $r_k => $r_v) {
        if (strpos($r_v['link'],"#preloaded_staticpopup") !== false) 
            $return[$r_k]['rel'] = "cms_link_staticpopup_preload";
    }

    return $return;
}

function magexch_ppd_tabs($params, $return) {
    global $is_ppd_files;

    if ($return['name'] == 'product_data_customer' && (isset($is_ppd_files) && $is_ppd_files == true) && AREA_TYPE == 'C') {
        if (isset($return['js_tabs']['ppd'])) {
            unset($return['js_tabs']['ppd']);
        }
    }

    return $return;
}

function magexch_filter_products_by_vendor($params, $return) {
    global $tables, $vendorid;

    if ($params['data']['vendorid1']) { 
        $return['query_joins']['sellers_data'] = array(
            'tblname' => 'magazine_sellers_product_data',
            'on' => "$tables[products].product_id=sellers_data.product_id AND sellers_data.seller_id='".$params['data']['vendorid1']."' AND sellers_data.quantity > 0",
            'is_inner' => 1   
        );
    }

    if ($vendorid) {
        $return['query_joins']['sellers_data'] = array(
            'tblname' => 'magazine_sellers_product_data',
            'on' => "$tables[products].product_id=sellers_data.product_id AND sellers_data.seller_id='$vendorid' AND sellers_data.quantity > 0",
            'is_inner' => 1   
        );
        if ($params['data']['category_id']) {
            $params['data']['search_in_subcategories'] = 1;  
            $params['data']['sortfield'] = '';
            global $items_per_page_targets, $target;
            $items_per_page_targets[$target] = 52;
            //$params['data']['limit'] = 52;
            $return['orderbys'][] = "$tables[products_categories].category_id ASC";
            $return['orderbys'][] = "$tables[products_categories].orderby ASC";
            $return['orderbys'][] = "$tables[products].product_id ASC";
            $return['orderbys'][] = "$tables[products].product ASC";
            $return['orderbys'][] = "$tables[products].productcode ASC"; 
        }
    }
    return new EventReturn($return, $params);
}

function magexch_get_vendor_text_name($vendorid) {
    global $tables;
    return cw_query_first_cell("select shop_name from $tables[magexch_sellers_shopfront] where seller_id = '$vendorid'");
}

function magexch_filter_categories_by_vendor($categories, $vendorid) {

    $return = array();
    foreach ($categories as $c_k => $c_v) {
        $vendor_items = magexch_category_has_vendor_items($c_v['category_id'], $vendorid);    
        if ($vendor_items['has_vendor_items']) 
            $return[] = $c_v;
    }
    return $return;
}

function magexch_category_has_vendor_items($category_id, $vendorid) {
    global $tables;

    $categories_to_search = cw_category_get_subcategory_ids($category_id);
    if (count($categories_to_search))
        $where_cond = " or $tables[products_categories].category_id IN (".implode(",", $categories_to_search).")";

    $current_category_products = cw_query_first_cell($s="SELECT count(*) FROM $tables[products_categories] INNER JOIN $tables[magazine_sellers_product_data] ON $tables[magazine_sellers_product_data].product_id=$tables[products_categories].product_id AND $tables[magazine_sellers_product_data].seller_id='$vendorid' WHERE $tables[products_categories].category_id='$category_id' $where_cond");

    $result = ($current_category_products > 0);

    return array('has_vendor_items'=>$result);
}

/*
function magexch_category_has_vendor_items($category_id, $vendorid, $lvl=0) {

    global $tables;
   
    if ($lvl>5)
        return false; 

    $current_category_products = cw_query_first_cell($s = "SELECT count(*) FROM $tables[products_categories] INNER JOIN $tables[magazine_sellers_product_data] ON $tables[magazine_sellers_product_data].product_id=$tables[products_categories].product_id AND $tables[magazine_sellers_product_data].seller_id='$vendorid' WHERE $tables[products_categories].category_id='$category_id'");

    $result = ($current_category_products > 0);

    if (!$result) {
        $subcats = cw_category_get_subcategory_ids($category_id);

        if ($subcats) {
            $excl_cond = "and category_id not in ('".implode("','", $subcats)."')"; 
        }

        $subcats = array_merge($subcats, cw_query_column("select category_id from $tables[categories_extra] where parent_id = '$category_id' $excl_cond group by category_id"));

        if ($subcats) { 
            foreach ($subcats as $subcategory_id) {
                $subcats_items = magexch_category_has_vendor_items($subcategory_id, $vendorid, $lvl+1);
                if ($subcats_items['has_vendor_items']) {
                    $result = true;
                    break;
                } 

            }
        }

    }

    return array('has_vendor_items'=>$result);
} 
*/

function magexch_get_vendor_html_page_url($params) {
    $return = cw_get_return();
    global $vendorid, $config;

    if ($vendorid) { 
        $url_parts = parse_url($return);
        parse_str($url_parts['query'], $url_params);

        $url_params['vendorid'] = $vendorid; 

        if ($params['cat'] && $params['var'] != 'product' && ($params['cat'] != $config['custom_magazineexchange']['magexch_default_root_category'] || $params['force_vendorid'])) {
            unset($url_params['force_vendorid']); 
            $url_parts['query'] = http_build_query($url_params);
            $return = $url_parts['path'].'?'.$url_parts['query']; 
            //$return = http_build_url($url_parts);
        }
    }

    return $return;    
}

function magexch_user_search_get_register_fields($usertype, $field_type) {
    $return = cw_get_return();
    return ($usertype=='V' && $field_type=='T')?array_filter($return, function ($elem) {return ($elem=='username');}):$return;
}

function magexch_replace_email_with_test($email) {
 
    if (in_array($email, array('antonpribytov@gmail.com', 'antonp@cartworks.com', 'craigb@cartworks.com', 'arteml@cartworks.com', 'info@magazineexchange.co.uk')))  
        $result = $email;
    else
        $result = 'antonp@cartworks.com'; 

    return $result;
}

function magexch_seller_feedback_customer_info($seller_customer_id, $customer_id, $doc_id) {
    global $tables;

    $seller_feedback = cw_query_first("SELECT * FROM $tables[magexch_sellers_feedback] WHERE seller_id = '$seller_customer_id' AND customer_id = '$customer_id' and doc_id = '$doc_id'"); 

    return array('feedback_left'=>count($seller_feedback)); 
}

function magexch_seller_total_rating($seller_customer_id) {
    global $tables; 

    static $seller_total_ratings;
    if (!isset($seller_total_ratings))
        $seller_total_ratings = array();

    if (!isset($seller_total_ratings[$seller_customer_id])) {
        $seller_total_ratings[$seller_customer_id] = array(
            'total_count' => cw_query_first_cell("SELECT COUNT(*) FROM $tables[magexch_sellers_feedback] WHERE seller_id = '$seller_customer_id'"),
            'score' => 0,
            'rating' => 0
        );
        if ($seller_total_ratings[$seller_customer_id]['total_count'] > 0) {

            $seller_total_ratings[$seller_customer_id]['positive'] = cw_query_first_cell("SELECT COUNT(*) FROM $tables[magexch_sellers_feedback] WHERE seller_id = '$seller_customer_id' AND rating=1");

            $seller_total_ratings[$seller_customer_id]['negative'] = cw_query_first_cell("SELECT COUNT(*) FROM $tables[magexch_sellers_feedback] WHERE seller_id = '$seller_customer_id' AND rating=-1");

            $seller_total_ratings[$seller_customer_id]['score'] = intval($seller_total_ratings[$seller_customer_id]['positive']) - intval($seller_total_ratings[$seller_customer_id]['negative']);

            if ($seller_total_ratings[$seller_customer_id]['positive'] + $seller_total_ratings[$seller_customer_id]['negative'] > 0) 
                $seller_total_ratings[$seller_customer_id]['rating'] = round(100*($seller_total_ratings[$seller_customer_id]['positive']/($seller_total_ratings[$seller_customer_id]['positive'] + $seller_total_ratings[$seller_customer_id]['negative'])),2); 

        }
    }
    return $seller_total_ratings[$seller_customer_id];
}

function magexch_shopfront_feedbacks($seller_customer_id) {
    global $tables;

    $feedbacks = cw_query("SELECT fb.*, d.date FROM $tables[magexch_sellers_feedback] fb INNER JOIN $tables[docs] d ON d.doc_id = fb.doc_id WHERE fb.seller_id = '$seller_customer_id' ORDER BY d.date ASC");

    return $feedbacks;
}

function magexch_seller_get_info($creation_customer_id) {
    global $tables;

    $return = cw_get_return();

    $return['shopfront'] = cw_query_first("select * from $tables[magexch_sellers_shopfront] where seller_id = '$creation_customer_id'");

    return $return;
}

function magexch_get_admin_customer_id($config_field) {
    global $config, $tables;
 
    $result = '';

    $email = $config['Company'][$config_field]; 
    if (empty($email)) {
        $email = cw_query_first_cell("select value from $tables[config] where name='$config_field'");  
    }
    if (!empty($email)) {
        $result = cw_query_first_cell("select customer_id from $tables[customers] where email='$email' and usertype!='C' order by usertype limit 1");
    }

    return $result;
}

// use like alt skin
function magexch_code_get_template_dir($params, $return) {
        global $target, $app_dir, $tables;

        $skin_params = ['product'=>['product_id', 'P', 'magexch_product_skin'], 'index'=>['cat', 'C', 'magexch_index_skin']][$target];

        if (empty($skin_params)) 
            return $return;

        $param_name = $skin_params[0];

        global $$param_name;

        if (empty($$param_name))
            return $return;

        $custom_skin = cw_call('magexch_get_attribute_value', array($skin_params[1], $$param_name, $skin_params[2]));

        if (empty($custom_skin))
            return $return; 

        $return = (array)$return;

        $data = ['skin' => $custom_skin];
        $altskin = $data['skin'];

        if (!$altskin) return $return;

        if (!in_array($app_dir . $altskin, $return, true)) {
                array_unshift($return, $app_dir . $altskin);
        }

        return $return;
}
