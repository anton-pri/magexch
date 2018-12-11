<?php
define('IMG_FORMAT_PNG',    1);
define('IMG_FORMAT_JPEG',   2);
define('IMG_FORMAT_WBMP',   4);
define('IMG_FORMAT_GIF',    8);

function cw_barcode_get($barcode, $type, $width, $height, $image_type = 8) {
    global $app_main_dir;

    $file = $app_main_dir.'/include/lib/barcode/'.$type.'.BarCode.php'; 
    if (!is_file($file)) return;

    if (!$width || $width > 5) $width = 2;
    if (!$height) $height = 40;

    include_once $app_main_dir.'/include/lib/barcode/BarCode.php';
    include_once $app_main_dir.'/include/lib/barcode/FColor.php';
    include_once $app_main_dir.'/include/lib/barcode/FDrawing.php';
    include_once $file;

    if ($image_type == IMG_FORMAT_GIF)
        header("Content-Type: image/gif");

    $code = new $type($barcode, $height, $width);
    $drawing = new FDrawing();
    $drawing->init();
    $drawing->add_barcode($code);
    $drawing->draw_all();

    $im = $drawing->get_im();
    $im2 = imagecreate($code->lastX, $code->lastY);
    imagecopyresized($im2, $im, 0, 0, 0, 0, $code->lastX, $code->lastY, $code->lastX, $code->lastY);
    $drawing->set_im($im2);
    $drawing->finish($image_type);
}

function cw_barcode_create_template($title) {
    $to_insert = array(
        'data' => addslashes(serialize(array('rows' => 5, 'cols' => 10, 'width' => 100, 'height' => 100))),
        'layout' => 'barcode',
        'title' => $title,
    );
    $layout_id = cw_array2insert('layouts', $to_insert);

    $to_insert = array(
        'layout_id' => $layout_id,
        'template' => 'addons/barcode/layout.tpl',
        'class' => 'barcode_layout',
    );
    cw_array2insert('layouts_templates', $to_insert);
    
    return $layout_id;
}

function cw_barcode_get_templates() {
    global $tables;
    return cw_query("select * from $tables[layouts] where layout = 'barcode'  order by title");    
}

function cw_barcode_print_doc($doc_id, $options) {
    global $tables, $user_account;

    $layout = cw_web_get_layout_by_id($options['template_id']);

    $doc = cw_doc_get($doc_id);
    $amount = $options['amount'];
    if (!$amount) $amount = 1;
    foreach($doc['products'] as $product) {
        if (in_array($doc['type'], array('P', 'R', 'Q')))
            $product['supplier_code'] = $product['productcode'];
        else
            $product['supplier_code'] = cw_query_first_cell("select productcode from $tables[products_supplied_amount] where product_id='$product_id' order by date limit 1");

        if ($layout['data']['use_tax']) {
            $taxes = $product['extra_data']['taxes'];
            cw_get_products_taxes($product, $user_account, false, $taxes, true);

            $_tmp_price = $product['price'];
            $product['price'] = $product['list_price'];
            cw_get_products_taxes($product, $user_account, false, $taxes, true);
            $product['list_price'] = $product['display_price'];
            $product['price'] = $_tmp_price;
        }
        else 
            $product['display_price'] = $product['price'];

        for($i = 0; $i < $product['amount']*$amount; $i++)
            $products[] = $product;
    }

    cw_barcode_print($products, $layout, $options, 0);
}

function cw_barcode_print_product($product_id, $options) {
    global $tables, $user_account, $current_area;

# kornev, not required to restore...
//    $current_area = 'C';

    $layout = cw_web_get_layout_by_id($options['template_id']);

    $product = cw_func_call('cw_product_get', array('id' => $product_id, 'user_account' => $user_account, 'info_type' => 17));
    if ($layout['data']['use_tax']) {
        $taxes = cw_get_product_tax_rates($product, $user_info, true, true);
        cw_get_products_taxes($product, $user_account, false, $taxes, true);

        $_tmp_price = $product['price'];
        $product['price'] = $product['list_price'];
        cw_get_products_taxes($product, $user_account, false, $taxes, true);
        $product['list_price'] = $product['display_price'];
        $product['price'] = $_tmp_price;
    }

    $amount = $options['amount'];
    if (!$amount) $amount = 1;
    for($i = 0; $i < $amount; $i++)
        $products[] = $product;

    cw_barcode_print($products, $layout, $options);
}

function cw_barcode_print($products, $layout, $options, $pages_limit = 1) {
    global $tables;
    global $var_dirs, $smarty;

    $smarty->assign('current_section', '');
    $smarty->assign('home_style', 'iframe');
    $smarty->assign('is_printing', true);
    $smarty->assign('current_main_dir', 'addons');
    $smarty->assign('current_section_dir', 'barcode');
    $smarty->assign('main', 'print');

    if (!$options['cols_from']) $options['cols_from'] = 0;
    if (!$options['cols_to']) $options['cols_to'] = $layout['data']['cols'];
    if (!$options['rows_from']) $options['rows_from'] = 0;
    if (!$options['rows_to']) $options['rows_to'] = $layout['data']['rows'];
    $smarty->assign('options', $options);

    $smarty->assign('products', $products);
    $smarty->assign('page_margin', array($layout['data']['page_top'], $layout['data']['page_right'], $layout['data']['page_bottom'], $layout['data']['page_left']));

    if ($pages_limit) $layout['data']['pages'] = $pages_limit;
    elseif ($options['cols_to'] > $options['cols_from']) $layout['data']['pages'] = ceil(count($products)/(($options['cols_to'] - $options['cols_from'])*($options['rows_to']-$options['rows_from'])));
    else $layout['data']['pages'] = 1;

    $smarty->assign('layout', $layout);
    cw_pdf_generate(cw_get_langvar_by_name('lbl_bar_codes', false, false, true), 'admin/index.tpl', false, false, $pages_limit, array(0, 0, 0, 0), false);
}

