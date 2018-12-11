<?php

if (defined('IS_AJAX') && constant('IS_AJAX')) {
    cw_load('warehouse', 'image', 'product');

    global $config, $products, $search_data, $use_search_conditions, $mode;

    $prod_resp = array();
    $substring = $_GET['search'];

    $search_data['products']['ajax_search']['substring'] = $substring;
    $search_data['products']['ajax_search']['flat_search'] = 1;
    $search_data['products']['ajax_search']['by_productcode'] = 1;
    $search_data['products']['ajax_search']['sort_field'] = 'productcode';
    $search_data['products']['ajax_search']['info_type'] = 0;
    $search_data['products']['ajax_search']['limit'] = $config['ajax_search']['as_suggested_products'];

    $mode = 'search';
    $use_search_conditions = 'ajax_search';
    cw_include('include/products/search.php');


   /* Search in deleted ordered products */
   if ($request_prepared['origin']=='user_C') {
        $substring = $request_prepared['search'];

        $search_words = explode(" ", $substring);
        if (!empty($search_words))  {
            $search_words = array_map('trim', $search_words);
            $prodname_cond = array();
            foreach ($search_words as $s_word) {
                if ($s_word)
                    $prodname_cond[] = "p.product LIKE '%$s_word%'";
            }
            $prodname_condition = " OR (".implode(" AND ", $prodname_cond).")";
        }

        $deleted_products = cw_query("SELECT d.product_id, d.product, '1' as deleted
        FROM $tables[docs_items] as d
        LEFT JOIN  $tables[products] as p ON d.product_id = p.product_id
        WHERE (d.product LIKE '%$substring%' $prodname_condition)
            AND (p.product_id IS NULL)
        ORDER BY d.product
        LIMIT ".$config['ajax_search']['as_suggested_products']);

        $products = cw_array_merge($products, $deleted_products);
    }
    
    /* Prepare output */
    if (count($products) == 0) {
        array_push($prod_resp, array('id' => 0, 'value' => '', 'label' => '<i>no suggestions</i>'));
    } else {
        foreach ($products as $product) {
            $substring = str_replace('/', '\/', $substring);
            $substring = str_replace("\\'", "'", $substring);
            $label = preg_replace('/' . $substring . '/i', '<span class="search_match">$0</span>', $product['product']);
            if ($label == '') {
				$label = $product['product'];
			}
            if ($product['deleted']) $label .= ' (deleted)';

            array_push(
				$prod_resp,
				array(
					'id' => $product['product_id'],
					'value' => $product['product'],
					'label' => $label
				)
			);

            if (count($prod_resp) >= $config['ajax_search']['as_suggested_products']) {
                break;
            }
        }
    }

    echo json_encode($prod_resp);
    exit();
}
