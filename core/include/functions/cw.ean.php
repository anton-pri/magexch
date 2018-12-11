<?php
function cw_ean_clear(&$ean) {
    while(strlen($ean)) {
        if (substr($ean, 0, 1) === '0') {
            $ean = substr($ean, 1, strlen($ean)-1);
        }
        else break;
    }
}

function cw_ean_get_product_info($ean) {
    global $tables, $config, $addons;

    if (empty($ean)) return array();

    cw_ean_clear($ean);
    $where = array();
    if (in_array(1, $config['pos']['fields_for_ean']))
        $where[] = "productcode='$ean'";
    if (in_array(0, $config['pos']['fields_for_ean'])) 
        $where[] = "eancode='$ean'";
    if (!count($where))
        $where[] = "eancode='$ean'";

    $where = implode(' or ', $where);

    $product_info = array();
# kornev, TOFIX
    if ($addons['product_options'])
        $product_info = cw_query_first("select product_id, variant_id from $tables[product_variants] where $where");

    if (!$product_info['product_id'])
        $product_info = cw_query_first("select product_id, 0 as variant_id from $tables[products] where $where");

    if (!$product_info['product_id'] && in_array(2, $config['pos']['fields_for_ean']))
        $product_info = cw_query_first("select product_id, variant_id from $tables[products_supplied_amount] where productcode='$ean'");

    if (!$product_info['product_id'] && in_array(3, $config['pos']['fields_for_ean']) && $addons['sn'])
        $product_info = cw_query_first("select product_id, 0 as variant_id from $tables[serial_numbers] where sn='$ean'");

    return $product_info;
}

function cw_ean_plus_one($from) {
    $from_arr = str_split($from);
    $last = sizeof($from_arr)-1;
    $additional = true;
    while($additional) {
        if ($last == -1) {
            array_unshift($from_arr, '1');
            break;
        }
        $current = &$from_arr[$last];
        $additional = true;
        if(is_numeric($current)) {
            if ($current < 9) {
                $current++;
                $additional = false;
            }
            else {
                $current = 0;
                $last--;
            }
        }
        elseif ($current < 'Z') {
            $current = chr(ord($current)+1);
            $additional = false;
        }
        elseif ($current == 'Z') {
            $current = 'A';
            $last--;
        }
        elseif ($current < 'z') {
            $current = chr(ord($current)+1);
            $additional = false;
        }
        elseif ($current == 'z') {
            $current = 'a';
            $last--;
        }
    }

    return implode('', $from_arr);
}

function cw_ena_get_range_rec($prefix, $from, $to) {
    $ret = array();

    while(true) {
        $ret[] = $prefix.$from;
        $from = cw_ean_plus_one($from);
        if ($from == $to) break;
    }
    $ret[] = $prefix.$to;

    return $ret;
}

function cw_ean_get_range($from, $to) {
    set_time_limit(3600);

    $from = strtoupper(preg_replace('/^A-Za-z0-9/', '', $from));
    $to = strtoupper(preg_replace('/^A-Za-z0-9/', '', $to));

    $from_arr = str_split($from);
    $to_arr = str_split($to);

    $prefix = '';
    foreach ($from_arr as $i=>$char) {
        if ($char != $to[$i]) break;
        $prefix .= $char;
    }
    if ($from < $to && strlen($from) <= strlen($to))
        $ret = cw_ena_get_range_rec($prefix, substr($from, $i), substr($to, $i));
    else
        $ret = cw_ena_get_range_rec($prefix, substr($to, $i), substr($from, $i));
    return $ret;
}
?>
