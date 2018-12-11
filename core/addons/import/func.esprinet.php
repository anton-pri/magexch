<?php
function cw_import_esprinet_login() {
    global $config;

    cw_load('http');

    $post = array(
        'VarHttps=NO',
        'Codice='.$config['import_esprinet']['esprinet_customer'],
        'utente='.$config['import_esprinet']['esprinet_login'],
        'pwd='.$config['import_esprinet']['esprinet_password'],
        'B2=Effettua il Login',
    );
    $result = cw_http_post_request('it.esprinet.com', '/dealer/check_dealer.asp', implode('&', $post), array());
    return $result[2];
}

function cw_import_esprinet_get_product_info($cookie, $list_page) {
    $products = array();

    preg_match('/<!--Filtra-->(.*)<\/body>/ims', $list_page, $area_parse);

    preg_match_all('/<a.*onClick="window\.open\(\'(.*)\'.*\).*<strong>.*Codice:.*<\/strong>(.*)<br><a href="(.*)">(.*)<\/a><\/td>.*<td.*><a href="(.*)" target="_self"><img src="\/iPriOper\/.*".*><\/a>.*<\/td>.*<td colspan="16".*>(.*)<\/td>/Uims', $area_parse[1], $fnd);

    if ($fnd)
    foreach($fnd[2] as $k=>$val) {
        $tmp = parse_url($fnd[3][$k]);

        $add_info = cw_http_get_request('it.esprinet.com', $tmp['path'], $tmp['query'], $cookie);
        preg_match('/<td.*>Codice EAN<\/td>.*<td.*>(.*)<\/td>.*<td.*>Peso con Imballo.*<\/td>.*<td.*>(.*)<\/td>.*<a.*onClick="window.open\(\'(.*)\'.*\).*<td.*>Street Price \(iva esclusa\)<\/td>.*<td.*>[^\d]*([\d\.,]*)[^\.^\d^,]*<\/td>.*<td.*>IL TUO PREZZO<\/td>.*<td.*>.*<span.*>[^\d]*([\d\.,]*)[^\.^\d^,]*<\/span>.*<\/td>/Uims', $add_info[1], $info);

        $descr_url = '/Area_Operativa/disponibilita/'.trim($info[3]);
        $tmp = parse_url($descr_url);
        $descr = cw_http_get_request('it.esprinet.com', $tmp['path'], 'cod_art='.trim($val), $cookie);
        preg_match('/DESCRIZIONE PRODOTTO.*<td>(.*)<\/td>/Uims', $descr[1], $fulldescr);

        $spec_url = trim($fnd[5][$k]);
        $tmp = parse_url($spec_url);
        $spec_page = cw_http_get_request('it.esprinet.com', $tmp['path'], $tmp['query'], $cookie);
        preg_match('/<div id="schedaTecnica">(.*)<\/table>/Uims', $spec_page[1], $spec_page);
        preg_match_all('/<tr>.*<td.*><strong>(.*)<\/strong><\/td>.*<td.*>(.*)<\/td>.*<\/tr>/Uims', $spec_page[1], $spec);
        if (is_array($spec)) {
            $spec_descr = '<table width="100%" cellspacing="1" cellpadding="3" border="0" bgcolor="#cccccc" style="" class="te-2" id="Tbl_Scheda_tecnica">'."\n";
            foreach($spec[1] as $sp=>$v) {
                $spec_descr .= "<tr bgcolor=\"".($sp%2?'#ececec':'#ffffff')."\">\n<td width=\"120\" valign=\"top\" align=\"left\" class=\"te-2-b\">".$v."</td>\n<td align=\"left\">".$spec[2][$sp]."</td>\n</tr>\n";
            }
            $spec_descr .= '</table>';
        }

        $val = strtoupper(trim($val));
        $products[$val] = array(
        'product' => utf8_encode(trim($fnd[4][$k])),
        'descr' => utf8_encode(trim($fnd[6][$k])),
        'fulldescr' => utf8_encode(trim($fulldescr[1])),
        'specifications' => utf8_encode($spec_descr),
        'eancode' => strtoupper($info[1]),
        'productcode' => $val,
        'weight' => cw_convert_numeric($info[2], '2,.'),
        'price' => cw_convert_numeric($info[4], '2,.'),
        'wholesale' => array(
            'quantity' => 1,
            'price' => cw_convert_numeric($info[5], '2,.'),
        ),
        'image' => 'http://it.esprinet.com'.trim($fnd[1][$k]),
        );
    }
    return $products;
}

function cw_import_esprinet_get_short_list_by_code($substring) {
    $cookie = cw_import_esprinet_login();
    $post = array(
        'codice_ricerca='.$substring,
    );
    $result_by_code = cw_http_get_request('it.esprinet.com', '/Area_Operativa/disponibilita/Ricerca_DB3.asp', implode('&', $post), $cookie);

    return cw_import_esprinet_get_product_info($cookie, $result_by_code[1]);
}

function cw_import_esprinet_get_sections_products($esprinet_sections, $sections) {
    global $tables;

    $products = array();

    $cookie = cw_import_esprinet_login();
    if (is_array($sections))
    foreach($sections as $sec) {
        $info = $esprinet_sections[$sec];
        if ($info) {
            $tmp = parse_url($info['url']);
            $result_by_section = cw_http_get_request('it.esprinet.com', $tmp['path'], $tmp['query'], $cookie);
            $products = array_merge($products, cw_import_esprinet_get_product_info($cookie, $result_by_section[1]));
        }
    }

    return $products;
}

function cw_import_esprinet_get_sections($substring) {
    $sections = array();

    $cookie = cw_import_esprinet_login();
    $post = array(
        'testo='.$substring,
    );
    $result_by_text = cw_http_get_request('it.esprinet.com', '/Area_Operativa/disponibilita/risultatiNew.asp', implode('&', $post), $cookie);

    preg_match_all('/<td><input.*value="(.*)".*<\/td>.*<td>.*<a.*href="(.*)">(.*)<\/a>.*<span.*>(.*)<\/span>.*<\/td>/Uims', $result_by_text[1], $fnd);
    if (is_array($fnd))
    foreach($fnd[1] as $k=>$v) {
        $sections[trim($v)] = array(
            'code' => utf8_encode(trim($v)),
            'title' => utf8_encode(trim($fnd[3][$k])),
            'url' => trim($fnd[2][$k]),
            'amount' => trim($fnd[4][$k])
        );
    }
    return $sections;
}

?>
