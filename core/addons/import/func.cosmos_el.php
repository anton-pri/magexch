<?php
function cw_import_cosmos_el_login() {
    global $config;

    cw_load('http');

    $post = array(
        'user='.$config['import_cosmos_el']['cosmos_el_login'],
        'pwd='.$config['import_cosmos_el']['cosmos_el_password'],
        'master=0',
        'submit= Login',
    );
    $result = cw_http_post_request('www.cosmosel.it', '/Portale/Cosmos/B2B/it/do-login', implode('&', $post), array());
    return $result[2];
}

function cw_import_cosmos_el_get_product_info($cookie, $list_page) {

    preg_match_all('/<img width="100" border="0" src="(.*)">.*<a onClick="window.location = \'articolidettvisArea.xml\?S1=(.*)&amp;S2=\' \+ encodeURIComponent\(\'(.*)\'\); return false;" href="#"><font face="Verdana" size="1" color="#00009c"><b>(.*)<\/b><\/font><\/a>.*Prezzo:.*<font color="red"><b>(.*)&nbsp;.*Codice prodotto:.*<b>(.*)<\/b>/Uims', $list_page, $fnd);

    $products = array();
    if ($fnd[1])
    foreach($fnd[1] as $k=>$v) {

        $product = array(
            'product' => utf8_encode(trim($fnd[4][$k])),
            'descr' => '',
            'fulldescr' => '',
            'specifications' => '',
            'eancode' => trim($fnd[3][$k]),
            'productcode' => trim($fnd[3][$k]),
            'weight' => 0,
            'price' => cw_convert_numeric($fnd[5][$k], '2.'),
            'wholesale' => array(
                'quantity' => 1,
                'price' => cw_convert_numeric($fnd[5][$k], '2.'),
            ),
            'image' => 'http://www.cosmosel.it/Portale/Cosmos/B2B/it/AreaRiservata/'.trim($fnd[1][$k]),
        );
        $products[] = $product;
    }
    return $products;
}

function cw_import_cosmos_el_get_short_list_by_string($substring) {
    $cookie = cw_import_cosmos_el_login();
    $post = array(
        'S1=',
        'S2='.$substring,
    );
    $products = cw_http_get_request('www.cosmosel.it', '/Portale/Cosmos/B2B/it/AreaRiservata/ricerca.xml', implode('&', $post), $cookie);

    return cw_import_cosmos_el_get_product_info($cookie, $products[1]);
}
?>
