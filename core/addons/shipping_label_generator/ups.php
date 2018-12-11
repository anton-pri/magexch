<?php
if(empty($order) || empty($order['products']))
	return false;

$stype = cw_ups_check_shipping_id($order['order']['shipping_id']);
if(!$stype)
	return false;

$delimiter = ',';

$hash = array();
$p_head = array();
$strs = array();

$hash['orderID'] = $order['order']['doc_id'];
$hash['s_company'] = $order['userinfo']['company'];
$hash['name'] = $order['userinfo']['s_firstname']." ".$order['userinfo']['s_lastname'];
$hash['s_email'] = $order['userinfo']['email'];
$hash['s_phone'] = $order['userinfo']['phone'];
$hash['s_address'] = $order['userinfo']['s_address'];
$hash['s_city'] = $order['userinfo']['s_city'];
$hash['s_state'] = $order['userinfo']['s_state'];
$hash['s_zip'] = $order['userinfo']['s_zipcode'];
$hash['s_country'] = $order['userinfo']['s_country'];
$hash['shipmethod'] = $stype;
$hash['insuredvalue'] = $order['order']['total'];
$hash['weight'] = 0;
if (in_array($hash['s_country'], array("DO","PR","US"))) {
	$UPS_wunit = "LBS";
} else {
	$UPS_wunit = "KGS";
}
if(!empty($order['products'])) {
	foreach($order['products'] as $p) {
		$hash['weight'] += $p['weight']*$p['amount'];
	}
	 $hash['weight'] = max(0.1,round(cw_weight_in_grams($hash['weight'])/($UPS_wunit=="LBS"?453.6:1000),1));
}
$strs[] = implode($delimiter,$hash);

# Create header
$header = implode($delimiter,cw_array_merge(array_keys($hash), $p_head));

# Create response
$response = array(
	"result" => 'ok',
	"image" => $header."\n".implode("\n", $strs),
	"image_type" => "text/csv"
);

if ($is_first_ups_label) {
	$all_ups_shipping_labels['result'] = 'ok';
	$all_ups_shipping_labels['image'] = $header."\n".implode("\n", $strs);
	$all_ups_shipping_labels['image_type'] = 'text/csv';
	$is_first_ups_label = false;
} else {
	$all_ups_shipping_labels['image'] .= "\n".implode("\n", $strs);
}
?>
