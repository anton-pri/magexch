<?php

if (defined('IS_AJAX')) {

    cw_load('warehouse', 'image', 'product');

    global $config, $products, $search_data, $use_search_conditions, $mode;

    $prod_resp = array();
    $substring = $_GET['search'];

    $search_data['products']['ajax_search']['substring'] = $substring;
    $search_data['products']['ajax_search']['flat_search'] = 1;
    $search_data['products']['ajax_search']['sort_field'] = 'productcode';
    $search_data['products']['ajax_search']['info_type'] = 0;
    $search_data['products']['ajax_search']['limit'] = $config['ajax_search']['as_suggested_products'];

    $mode = 'search';
    $use_search_conditions = 'ajax_search';

    cw_include('include/products/search.php');

    if (count($products) == 0) {
        array_push($prod_resp, array('value' => '', 'label' => '<i>no suggestions</i>'));

    } else {
        foreach ($products as $product) {

            $substring = str_replace('/','\/',$substring);
            $substring = str_replace("\\'","'",$substring);
            $label = preg_replace('/' . $substring . '/i', '<span class="search_match">$0</span>', $product['product']);
            if($label=='') $label = $product['product'];

            array_push($prod_resp, array('value' => $product['product'], 'label' => $label));

            if (count($prod_resp) >= $config['ajax_search']['as_suggested_products']) {
                break;
            }

        }
    }

    define('PREVENT_SESSION_SAVE', true);
    echo json_encode($prod_resp);
    exit();

}
