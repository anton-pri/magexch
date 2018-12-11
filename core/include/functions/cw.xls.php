<?php
# kornev
# PEAR package is required
# see http://pear.php.net/package/Spreadsheet_Excel_Writer/redirected

if (!defined('APP_START')) die('Access denied');

set_time_limit(86400);

@include_once 'Spreadsheet/Excel/Writer.php';
include_once $app_main_dir.'/include/lib/Excel/reader.php';

function cw_xls_get_image($product_id) {
    global $tables, $var_dirs, $available_images, $app_dir;

    $info = cw_query_first("SELECT image_path, date FROM ".$tables['products_images_thumb']." WHERE id='$product_id'");

    if ($info)
        $file_name = $var_dirs['tmp']."/exel_image_".$product_id."_".$info['date'].'.bmp';
    else {
        $info['image_path'] = $var_dirs['images'].'/'.$available_images['products_images_thumb']['default_image'];
        $file_name = $var_dirs['tmp']."/exel_image_default.bmp";
    }

    if (!is_file($file_name)) {
        cw_load('image');
        if (!is_file($info['image_path'])) {
            $info['image_path'] = $var_dirs['images'].'/'.$available_images['products_images_thumb']['default_image'];
            $file_name = $var_dirs['tmp']."/exel_image_default.bmp";
        }

        if (is_file($info['image_path']))
            cw_image_convert($info['image_path'], $file_name);

        @unlink($tmpfname);
    }

    return $file_name;
}

function cw_xls_write_row(&$sheet, $format, $row, $data) {
    $counter = 0;
    if (is_array($data))
    foreach($data as $val) {
        if (!empty($val[1])) {
            if ($val[2])
                $sheet->$val[1]($row, $counter, $val[0], $val[2]);
            else
                $sheet->$val[1]($row, $counter, $val[0]);
        }
        else
            $sheet->writeString($row, $counter, $val[0], $format);
        $counter++;
    }
}

function cw_xls_get_price_list($posted_data, $products) {
    global $config, $tables, $app_main_dir;

    $xls = new Spreadsheet_Excel_Writer();
    $xls->setVersion(8);
    $xls->send("price_list.xls");

    $format_header = &$xls->addFormat();
    $format_header->setBold();

    $format = &$xls->addFormat();
    $format->setVAlign('top');

    $format_currency = &$xls->addFormat();
    $format_currency->setVAlign('top');

# kornev, think about another way.
    $format_currency->setNumFormat(44);
//    $char = chr(162);
//    $format_currency->setNumFormat($char.' #.00');

    $sheet =& $xls->addWorksheet(cw_get_langvar_by_name('lbl_price_list_sheet_title', null, false, true));
//    $sheet->setInputEncoding('utf-8');
# kornev, write the header.
    $header = array();
    if ($posted_data['thumb'] == 'Y')
        $header[] = array(cw_get_langvar_by_name('lbl_image', null, false, true));
    $header[] = array(cw_get_langvar_by_name('lbl_sku', null, false, true));
    $header[] = array(cw_get_langvar_by_name('lbl_title', null, false, true));
    $header[] = array(cw_get_langvar_by_name('lbl_category', null, false, true));
    if ($posted_data['manufacturer'] == 'Y')
        $header[] = array(cw_get_langvar_by_name('lbl_manufacturer', null, false, true));
    if ($posted_data['price'] == 3 || $posted_data['price'] == 1)
        $header[] = array(cw_get_langvar_by_name('lbl_market_price', null, false, true));
    if ($posted_data['price'] == 3 || $posted_data['price'] == 2)
        $header[] = array(cw_get_langvar_by_name('lbl_our_price_flat', null, false, true));
    if ($posted_data['in_stock'])
        $header[] = array(cw_get_langvar_by_name('lbl_in_stock', null, false, true));
    if ($posted_data['discount'])
        $header[] = array(cw_get_langvar_by_name('lbl_discount', null, false, true));
    $header[] = array(cw_get_langvar_by_name('lbl_product_class', null, false, true));
    cw_xls_write_row($sheet, $format_header, 0, $header);

//    if ($posted_data['thumb'] == 'Y') $start = 1;
//    else $start = 0;
    $sheet->setColumn(0, count($header)-1, 21);

# kornev, write body
    if ($products) {
        $index = 1;
        foreach ($products as $product) {
            $body = array();
            if ($posted_data['thumb'] == 'Y') {
                $bitmap = cw_xls_get_image($product['product_id']);
                $body[] = array($bitmap, 'insertBitmap');
//                $body[] = '';
# kornev, define the image row/col parameters
                $info = getimagesize($bitmap);
                $sheet->setRow($index, $info[1]); # height
                $max_width = max($max_width, $info[0]);
            }
            $body[] = array($product['productcode']);
            $body[] = array($product['product']);
            $body[] = array($product['category']);
            if ($posted_data['manufacturer'] == 'Y')
                $body[] = array($product['manufacturer']);
            if ($posted_data['price'] == 3 || $posted_data['price'] == 1) {
                if ($product['list_price'] > 0) {
                    $body[] = array($product['list_price'], 'writeNumber', &$format_currency);
                }
                else
                    $body[] = array(cw_get_langvar_by_name('lbl_not_available_short', null, false, true));
            }
            if ($posted_data['price'] == 3 || $posted_data['price'] == 2)
                $body[] = array($product['price'], 'writeNumber', &$format_currency);
            if ($posted_data['in_stock'])
                $body[] = array($product['avail']);
            if ($posted_data['discount']) {
                if ($product['taxed_price'] <= $product['list_price'] and $product['list_price'] >= 0)
                    $body[] = array(price_format(100-($product['taxed_price']/$product['list_price'])*100).'%');
                else
                    $body[] = array('');
            }
            $body[] = array($product['class']);
            cw_xls_write_row($sheet, $format, $index, $body);
            $index++;
//            break;
        }

//        if ($posted_data['thumb'] == 'Y')
//            $sheet->setColumn(0, 0, round($max_width/8), $format);
    }

//    for($i=1; $i< 6000; $i++) {
//        $sheet->write($i-1, 0, $i.' is '.chr($i));
//    }

    $xls->close();

    exit(0);
}

function cw_xls_get_serials($product_id, $serials) {
    global $config, $tables;

    $xls = new Spreadsheet_Excel_Writer();
    $xls->send("price_list.xls");
    $xls->setVersion(8);

    $format_header = &$xls->addFormat();
    $format_header->setBold();

    $sheet =& $xls->addWorksheet(cw_get_langvar_by_name('lbl_serial_numbers', null, false, true));
    $sheet->setInputEncoding('UTF-8');

    $header = array();
    $header[] = array(cw_get_langvar_by_name('lbl_sku', null, false, true));
    $header[] = array(cw_get_langvar_by_name('lbl_serial_number', null, false, true));
    cw_xls_write_row($sheet, $format_header, 0, $header);

    $sheet->setColumn(0, count($header), 20);

    $product = cw_query_first("select productcode from $tables[products] where product_id='$product_id'");
    if ($serials) {
        $index = 1;
        foreach($serials as $serial) {
            $body = array();
            $body[] = array($product['productcode']);
            $body[] = array($serial['sn']);

            cw_xls_write_row($sheet, 0, $index, $body);
            $index++;
        }
    }
    $xls->close();

    exit(0);
}

function cw_xls_is_format($file_path) {
}

function cw_xls_convert($file_path) {
    global $config;
    $data = new Spreadsheet_Excel_Reader();
    $data->setOutputEncoding('utf-8');
    $data->read($file_path);
}

?>
