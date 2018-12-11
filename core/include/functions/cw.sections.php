<?php
cw_load('product');

function cw_sections_get_featured($featured_type, $cat = 0, $info_type = 128) {
    global $tables, $smarty;
    global $user_account, $current_area;

    if (empty($featured_type)) $featured_type = 'featured_products';

    $data = array();
    $data['flat_search'] = 1;
    $data['all'] = 1;
    $data['status'] = array(1);
    $table = $tables[$featured_type];
    $data['sort_condition'] = $table.".product_order";
    $current_time = cw_core_get_time();
    $add_params = array();
    $add_params['query_joins'][$featured_type] = array(
        'on' => "$tables[products].product_id=".$table.".product_id AND ".$table.".avail=1 AND ".$table.".category_id='".$cat."'".($featured_type == 'featured_products'?" and ($table.from_time <= $current_time or $table.from_time = 0) and ($table.to_time >=$current_time or $table.to_time = 0)":''),
        'is_inner' => 1,
    );
    $data['where'] = ($featured_type == 'featured_products'?"$table.min_amount <= $tables[products_warehouses_amount].avail":'');

    list($products, $navigation) = cw_func_call('cw_product_search', array('data' => $data, 'user_account' => $user_account, 'current_area' => $current_area, 'info_type' => $info_type), $add_params);
    return $products;
}

# kornev, params ($section, $where = '', $info_type = 264
# kornev, data - params for the search
function cw_sections_get($section, $data=array(), $where='', $info_type = 264) {
    global $tables, $current_area, $user_account;

//    extract($params);
    $table = $tables[$section];
    if (!$info_type) $info_type = 264;

    if ($current_area == 'C') {
        $where .= ($where?' and ':' ').$table.'.active=1 ';
        if (in_array($section, array('arrivals', 'hot_deals', 'clearance', 'super_deals'))) {
            $current_time = time();
            $where .= " and ($table.from_time <= $current_time or $table.from_time = 0) and ($table.to_time >=$current_time or $table.to_time = 0) and ($table.min_amount <= $tables[products_warehouses_amount].avail or $table.min_amount = 0)";
        }

        $add_data['query_joins'][$section] = array (
            'on' => "$tables[products].product_id=$table.product_id",
            'is_inner' => 1,
        );

        $data['where'] = $where;

        if ($data['sort_field']=='orderby' || empty($data['sort_field'])) $data['sort_field'] = "$table.pos";

        $return = cw_func_call('cw_product_search', array('data' => $data, 'user_account' => $user_account, 'current_area' => $current_area, 'info_type' => $info_type), $add_data);
        if ($data['all']) $return = $return[0];
    }
    else {
        $return = cw_query("select $table.*, $tables[products].product_id, $tables[products].product from $table, $tables[products], $tables[products_warehouses_amount] where $tables[products_warehouses_amount].product_id = $tables[products].product_id and $tables[products_warehouses_amount].warehouse_customer_id = 0 and $tables[products_warehouses_amount].variant_id=0 and $tables[products].product_id=$table.product_id $where order by $table.pos, $tables[products].product");
    }

    return $return;
}

function cw_delete_from_section($section, $id) {
    global $tables, $special_sections;

    if (in_array($section, $special_sections))
        db_query("delete from ".$tables[$section]." where id='$id'");
}

function cw_delete_section_product($product_id) {
    global $tables, $special_sections;

    foreach($special_sections as $section)
        db_query("delete from ".$tables[$section]." where product_id='$product_id'");
}

function cw_delete_sections () {
    global $tables, $special_sections;

    foreach($special_sections as $section)
        db_query("delete from ".$tables[$section]);
}

?>
