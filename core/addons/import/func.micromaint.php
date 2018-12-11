<?php
if (!defined('APP_START')) die('Access denied');

function cw_import_micromaint_login() {
    global $config;

    cw_load('http');

    $post = array(
        '_ctl1:BoxLeftMenu:_ctl0:txtUserName='.$config['import_micromaint']['micromaint_login'],
        '_ctl1:BoxLeftMenu:_ctl0:txtPassword='.$config['import_micromaint']['micromaint_password'],
        '_ctl1:BoxLeftMenu:_ctl0:cmdLogin=Entra',
        '__EVENTTARGET=_ctl1:BoxLeftMenu:_ctl0:cmdLogin',
        '__EVENTARGUMENT=',
    );
    $result = cw_http_post_request('www.micromaint.it', '/Home.aspx', implode('&', $post), array());
    return $result[2];
}

function cw_import_micromaint_get_product_info($cookie, $pid) {
    $product_page = cw_http_get_request('www.micromaint.it', '/home.aspx', 'FID=13&PID='.$pid, $cookie);

    preg_match('/<td align="Center" style="width:120px;height:120px;"><img src="(.*)" alt="" border="0" height="100" width="100" \/>.*Cod.*<B>(.*)<\/B>.*Cod.*<B>(.*)<\/B>.*<span class="StdB">(.*)<\/span>.*<td class="TBPrezzo".*Dealer.*&euro; <\/FONT>(.*)<\/span><\/td>.*<span style="text-align:justify; text-justify:auto;">(.*)<\/span>/Uims', $product_page[1], $product_info);

    $product = array(
        'product' => utf8_encode(trim($product_info[4])),
        'descr' => utf8_encode(trim($product_info[6])),
        'fulldescr' => utf8_encode(trim($product_info[6])),
        'specifications' => '',
        'eancode' => trim($product_info[2]),
        'productcode' => trim($product_info[3]),
        'weight' => 0,
        'price' => cw_convert_numeric($product_info[5], '2,'),
        'wholesale' => array(
            'quantity' => 1,
            'price' => cw_convert_numeric($product_info[5], '2,'),
        ),
        'image' => 'http://www.micromaint.it/'.trim($product_info[1]),
    );
    return $product;
}

function cw_import_micromaint_get_short_list_by_string($substring, $search_type) {
    $cookie = cw_import_micromaint_login();
    $post = array(
        'Fid=16',
        'Head1:AC_TopMenu:txtSearch='.$substring,
        'Head1:AC_TopMenu:_ctl10='.$search_type,
        'Head1_AC_TopMenu_cmdLbStart=',
        '__EVENTTARGET=Head1:AC_TopMenu:cmdLbStart',
        '__EVENTARGUMENT=',
    );
    # kornev
    # seems like session is used, we store the params and then receive the page.
    $ses = cw_http_get_request('www.micromaint.com', '/home.aspx', implode('&', $post), $cookie);
    preg_match('/Object moved to <a href="(.*)">here<\/a>/U', $ses[1], $fnd);
    $products = array();
    if ($fnd[1]) {
        $url = parse_url($fnd[1]);
        $page0 = cw_http_get_request('www.micromaint.com', $url['path'], $url['query'], $cookie);
        preg_match_all('/"home\.aspx\?FID=13\&amp;PID=(.*)"/Ui', $page0[1], $pids);
        if (is_array($pids[1]))
        foreach($pids[1] as $val) {
            $products[] = cw_import_micromaint_get_product_info($cookie, $val);
        }
    }

    return $products;
}
?>
