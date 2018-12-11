<?php
if (!defined('APP_START')) die('Access denied');

function cw_import_chipstation_login() {
    global $config;

    cw_load('http');

    $post = array(
        'op=login',
        'name=Your_Account',
        'username='.$config['import_chipstation']['chipstation_login'],
        'password='.$config['import_chipstation']['chipstation_password'],
        'ricorda=0',
    );
    $result = cw_http_post_request('www.chipstation.it', '/modules.php', implode('&', $post), array());
    return $result[2];
}

function cw_import_chipstation_get_product_info($cookie, $list_page) {

    preg_match_all('/<a href="javascript:popup_foto\(\'(.*)\'\)"><img src=\'(.*)\' border=0 ><\/a>.*<a href=\'(.*)\' title="Codice Articolo:(.*) -  > " onClick="window\.open\(\'(.*)\',.*>(.*)<\/a><\/td>.*<td  bgcolor="#.*" align=center>&euro; (.*)<\/td>/Uims', $list_page, $fnd);


    $products = array();
    if ($fnd[1])
    foreach($fnd[1] as $k=>$v) {
        $get = array(
            'name=Chipstation',
            'op=ricerca_veloce',
            'q='.$substring,
        );
        $product_page = cw_http_get_request('www.chipstation.it', '/'.trim($fnd[5][$k]), '', $cookie);
        preg_match('/<a href=\'javascript:popup\("(.*)"\)\'>.*<img src="(.*)" border=0><\/a>.*<div style="text-align: justify">(.*)<\/div>/Uims', $product_page[1], $details);

        $product = array(
            'product' => utf8_encode(trim($fnd[6][$k])),
            'descr' => utf8_encode(trim($details[3])),
            'fulldescr' => utf8_encode(trim($details[3])),
            'specifications' => '',
            'eancode' => trim($fnd[4][$k]),
            'productcode' => trim($fnd[4][$k]),
            'weight' => 0,
            'price' => cw_convert_numeric($fnd[7][$k], '2,'),
            'wholesale' => array(
                'quantity' => 1,
                'price' => cw_convert_numeric($fnd[7][$k], '2,.'),
            ),
            'image' => 'http://www.chipstation.it/'.trim($details[2]),
        );
        $products[] = $product;
    }
    return $products;
}

function cw_import_chipstation_get_short_list_by_string($substring) {
    $cookie = cw_import_chipstation_login();

    $get = array(
        'name=Chipstation',
        'op=ricerca_veloce',
        'q='.$substring,
    );
    $products = cw_http_get_request('www.chipstation.it', '/modules.php', implode('&', $get), $cookie);

    return cw_import_chipstation_get_product_info($cookie, $products[1]);
}
?>
