<?php
cw_load('web', 'barcode', 'product');
$saved_template_id = &cw_session_register('saved_template_id');

if ($action == 'set_template') {
    $saved_template_id = $template_id;
    cw_header_location("index.php?target=$target");
}

if ($action == 'delete' && $saved_template_id) {
    cw_web_delete_layout($saved_template_id);
    $saved_template_id = 0;
    cw_header_location("index.php?target=$target");
}

if ($action == 'create' && $title) {
    $saved_template_id = cw_barcode_create_template($title);
    cw_header_location("index.php?target=$target");
}

if ($action == 'update' && $saved_template_id) {
    $data = addslashes(serialize($label_data));
    cw_array2update('layouts', array('data' => $data), "layout_id='$saved_template_id'");
    cw_header_location("index.php?target=$target");
}

if ($saved_template_id) {
    $product = array(
        'eancode' => cw_product_generate_sku($config['barcode']['gen_product_code'], 'eancode'),
        'display_price' => '10.00',
        'display_discounted_price' => '9.00',
        'list_price' => '15.00',
        'supplier_code' => cw_product_generate_sku($config['barcode']['gen_product_code'], 'eancode'),
        'discount' => '1.00',
        'productcode' => 'Product SKU',
        'sn' => cw_product_generate_sku($config['barcode']['gen_product_code'], 'eancode'),
        'product' => 'Product Title',
    );
    $smarty->assign('product', $product);

    $template = cw_web_get_layout_by_id($saved_template_id);
    if ($template['layout'] != 'barcode') {
        $saved_template_id = 0;
        cw_header_location("index.php?target=$target");
    }
    $smarty->assign('layout_data', $template);
}

$smarty->assign('template_id', $saved_template_id);

$smarty->assign('home_style', 'popup');
$smarty->assign('current_main_dir', 'addons');
$smarty->assign('current_section_dir', 'barcode');
$smarty->assign('main', 'popup_barcode');
