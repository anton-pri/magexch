<?php
if (!$addons['Salesman'])
    cw_header_location("index.php");

if (empty($type))
	$type = 'js';

$iframe_referer = '';
if ($type == 'iframe')
	$iframe_referer = urlencode($HTTP_REFERER);

#
# Get banner data
#
if ($bid)
	$data = cw_query_first("SELECT * FROM $tables[salesman_banners] WHERE banner_id='$bid'");

if ($preview && $type == 'preview') {
	$data['banner_type'] = 'M';
	$data['body'] = $preview;

} elseif (!$data)
	exit;

#
# Add statistic record (banner view)
#
$tmp_location = preg_quote(preg_replace("/^http[s]?:\/\//S", "", $current_location), "/");
if ($salesman && preg_replace("/^(http[s]?:\/\/".$tmp_location.")\/.*$/S", "\\1", $HTTP_REFERER) != $current_location && $salesman != $customer_id) {
	$query_data = array(
		"salesman_customer_id" => $customer_id,
		"add_date" => time(),
		"banner_id" => $bid,
		"product_id" => $product_id
	);
	cw_array2insert("salesman_views", $query_data);
}

#
# If product_id is empty - get temporary product_id
#
if ($data['banner_type'] == 'P' && !$product_id && !$salesman)
	$product_id = cw_query_first_cell("SELECT product_id FROM $tables[products] ORDER BY RAND() LIMIT 1");

#
# Data preparing according banner type
#
if ($data['banner_type'] == 'T') {

	# Text benner
	$data['body'] = "<#A#>".$data['body']."<#/A#>";

} elseif ($data['banner_type'] == 'G' && $type == 'iframe') {

	# Image banner in <iframe> box
	$data['body'] = "<table border=\"0\">\n<tr>";
	if($data['legend'] != '') {
		if($data['direction'] == 'U')
			$data['body'] .= "<td align=\"center\">$data[legend]</td></tr><tr>";
		if($data['direction'] == 'L')
			$data['body'] .= "<td valign=\"middle\">$data[legend]</td>";
	}
	$data['body'] .= "<td><#A#><img src=\"$current_location/index.php?target=banner&bid=$bid\" border=\"0\" alt=\"$data[alt]\" /><#/A#></td>";
	if($data['legend'] != '') {
		if($data['direction'] == 'D')
			$data['body'] .= "</tr><tr><td align=\"center\">$data[legend]</td>";
		if($data['direction'] == 'R')
			$data['body'] .= "<td valign=\"middle\">$data[legend]</td>";
	}
	$data['body'] .= "</tr></table>";

} elseif ($data['banner_type'] == 'G') {

	# Image banner
	header("Content-type: ".$data['image_type']);
	echo $data['body'];
	exit;

} elseif ($data['banner_type'] == 'P' && $product_id) {

	# Product banner
	$product = cw_query_first("SELECT product, descr FROM $tables[products] WHERE product_id = '$product_id'");
	$data['body'] = "<table border=\"0\">";
	if ($data['is_image'] == 'Y')
		$data['body'] .= "<tr><td align=\"center\"><#A#><img src=\"$current_location/index.php?target=image&id=$product_id&type=T\" border=\"0\" alt=\"".str_replace("\"", '\"', $product['product'])."\" /><#/A#></td></tr>";

    if ($data['is_name'] == 'Y' || ($data['is_image'] != 'Y' && $data['is_descr'] != 'Y' && $data['is_add'] != 'Y'))
        $data['body'] .= "<tr><td align=\"center\"><#A#>$product[product]<#/A#></td></tr>";

    if ($data['is_descr'] == 'Y')
        $data['body'] .= "<tr><td align=\"center\">$product[descr]</td></tr>";

    if ($data['is_add'] == 'Y') {
		if (empty($label))
			$label = "CLICK HERE TO ORDER";
        $data['body'] .= "<tr><td align=\"center\"><a href=\"$app_catalogs[customer]/cart.php?mode=add&amp;salesman=$salesman&amp;product_id=$product_id&amp;amount=1&amp;from=salesman&amp;bid=$bid".($iframe_referer?"&amp;iframe_referer=".$iframe_referer:"")."\" style=\"border: 0px;\" ".($data['open_blank'] == 'Y'?" target=\"_blank\"":"").">".$label."<#/A#></td></tr>";
	}

	$data['body'] .= "</table>";

} elseif ($data['banner_type'] == 'M') {

	# Media rich banner
	if (preg_match_all("/<#([a-zA-Z]?)(\d+)#>/S", $data['body'], $preg) && !empty($preg[2])) {
		foreach($preg[2] as $k => $v) {
			$e = cw_query_first("SELECT * FROM $tables[salesman_banners_elements] WHERE elementid = '$v'");
			if ($e['data_type'] == "application/x-shockwave-flash") {
				$data['body'] = str_replace(
					"<#".$preg[1][$k].$e['elementid']."#>",
					'<object type="application/x-shockwave-flash" data="'.$current_location.'/banner_element.php?eid='.$e['elementid'].'" width="'.$e['data_x'].'" height="'.$e['data_y'].'">
<param name="movie" value="'.$current_location.'/banner_element.php?eid='.$e['elementid'].'" />
<param name="menu" value="false" />
<param name="loop" value="false" />
<param name="quality" value="high" />
<param name="pluginspage" value="http://www.macromedia.com/go/getflashplayer" />
</object>',
					$data['body']
				);

			} else {
				if ($preg[1][$k] == 'A')
					$data['body'] = str_replace("<#A".$e['elementid']."#>", "<#A#><#".$e['elementid']."#><#/A#>", $data['body']);
				$data['body'] = str_replace("<#".$e['elementid']."#>", "<img src=\"$current_location/banner_element.php?eid=".$e['elementid']."\" border=\"0\" width=\"".$e['data_x']."\" height=\"".$e['data_y']."\" alt=\"\" />", $data['body']);
			}
		}
		$data['body'] = preg_replace("/<#\w?\d+#>/S", "", $data['body']);
	}
}

#
# Replace service tag to HTML tag (<a> and </a>) in the banner body
#
if ($data['banner_type'] == 'P' && $product_id) {
	$href = "product.php?salesman=$salesman&product_id=$product_id";
} else {
	$href = "home.php?salesman=$salesman";
}

$data['body'] = str_replace("<#A#>", "<a href=\"$app_catalogs[customer]/$href&amp;bid=$bid".($iframe_referer?"&amp;iframe_referer=".$iframe_referer:"")."\" style=\"border: 0px;\" ".($data['open_blank'] == 'Y'?" target=\"_blank\"":"").">", $data['body']);
$data['body'] = str_replace("<#/A#>", "</a>", $data['body']);

if ($type == 'iframe')
	$data['body'] = "<html><body>".$data['body']."</body></html>";

#
# Output banner body
#
if ($type == 'preview') {

	# As preview
	echo $data['body'];

} elseif ($type == 'js') {

	# As JS code
    header("Content-type: text/javascript");
	$data['body'] = str_replace("'", "\'", str_replace("\n", " ", str_replace("\r", "", $data['body'])));
	echo "document.write('$data[body]');";

} else {

	# As HTML code
    header("Content-type: text/html");
	echo $data['body'];
}
die();

?>
