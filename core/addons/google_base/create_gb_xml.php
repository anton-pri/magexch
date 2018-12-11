<?php

$success = false;

if (!isset($domain_id)) {
	cw_flush('No domains specified');
    $top_message = array(
			'content' => 'No domains specified',
			'type' => 'W'
		);
	return false;
}

cw_load('product','xml','category','image','attributes');

// Google Product Search fields accordint to the spec
// http://www.google.com/support/merchants/bin/answer.py?answer=188494
$xml_fields = array(
    'g:id',
    'title',
    'description',
    'g:google_product_category',
    'g:product_type',
    'link',
    'g:image_link',
    'g:additional_image_link',
    'g:condition',
    'g:availability',
    'g:price',
//    'g:sale_price',
//    'g:sale_price_effective_date',
    'g:brand',
    'g:gtin',
    'g:mpn',

// Variants/options are not supported yet
    'g:item_group_id',
    'g:color',
    'g:material',
    'g:pattern',
    'g:size',
// /variants

    'g:gender',
    'g:age_group',
    'g:shipping_weight',
    'g:expiration_date',
);

if ($addons['multi_domains']) {
    $domain = cw_func_call('cw_md_domain_get',array('domain_id'=>$domain_id));
}

$default_thumb = cw_get_default_image('products_images_thumb');
$default_det = cw_get_default_image('products_images_det');

// Get all attributes of this addon
$gb_attr = array();
global $google_base_attributes_map;
cw_include_once('addons/google_base/gb_attributes_map.php');
$gb_attributes_full = cw_call('cw_get_attributes_by_addon',array('google_base'));
foreach($gb_attributes_full as $gbv) {
    $gb_attr[$gbv['field']] = $gbv['attribute_id'];
    if (!empty($google_base_attributes_map[$gbv['field']])) {
        $mapped_attr = cw_func_call('cw_attributes_get_attributes_by_field',array('field'=>$google_base_attributes_map[$gbv['field']]));
        if (!empty($mapped_attr['P']))
            $gb_attr[$gbv['field']] = $mapped_attr['P'];
    }
}
unset($gb_attributes_full, $gbv, $mapped_attr);

$_filename = $config['google_base']['gb_filename'];

if ($domain_id) {
	$http_location = "http://".$domain['http_host'].$domain['web_dir'];
	$_filename = $domain['http_host'].'.'.$_filename;
}

$_filename = 'files/googlebase/'.$_filename;

if (($filename = cw_allow_file($_filename, true)) && $file = cw_fopen($_filename, 'w', true)) {
	$success = true;

	$data = array();

    if (!empty($domain_id)) {
        $q = "SELECT p.product_id
                    FROM $tables[products] p
                    INNER JOIN $tables[attributes_values] av ON 
                        av.item_id=p.product_id AND av.value IN ('0','$domain_id') AND av.attribute_id=$domain_attributes[P] 
                    JOIN $tables[products_prices] pp ON p.product_id = pp.product_id and pp.quantity = 1 and pp.variant_id = 0 and pp.membership_id = 0
                    WHERE p.status=1 AND pp.price > " . floatval($config['google_base']['gb_low_price_limit']);
    } else {
        $q = "SELECT p.product_id FROM $tables[products] p JOIN $tables[products_prices] pp ON p.product_id = pp.product_id and pp.quantity = 1 and pp.variant_id = 0 and pp.membership_id = 0 WHERE p.status=1 AND pp.price > " . floatval($config['google_base']['gb_low_price_limit']);
    }
    $items = cw_query_column($q);
	if ($items) {
        $warnings = array();
		foreach($items as $v) {

            $prod = cw_func_call('cw_product_get',array('id'=>$v,'info_type'=>8|64|128|256|512|2048));
            $attr = cw_query_hash("select av.attribute_id as attribute_id, IF(ISNULL(ad.value),av.value,ad.value) as value 
                    FROM $tables[attributes_values] av LEFT JOIN $tables[attributes_default] ad on attribute_value_id=av.value and ad.attribute_id=av.attribute_id
                    WHERE item_id=$v AND av.attribute_id IN ('".implode("','",$gb_attr)."')",'attribute_id',false, true);

            // Id, title, description
            $data[$v]['g:id']           = $config['google_base']['gb_id_prefix'].$prod[!empty($google_base_attributes_map['g:id'])?$google_base_attributes_map['g:id']:'productcode'];
            $data[$v]['title']          = ($config['google_base']['gb_manufacturer_title']=='Y'?$prod['manufacturer'].' ':'').$prod['product'];
            $data[$v]['description']    = strip_tags($prod[$config['google_base']['gb_description']]);

            // Categories
            $data[$v]['g:google_product_category'] = cw_call('cw_gb_get_category_value', array($prod['category_id']));
            $data[$v]['g:product_type'] = implode(' > ',cw_call('cw_category_category_path', array($prod['category_id'])));

            // URL
            $url = cw_call('cw_core_get_html_page_url', array(array('var'=>'product','product_id'=>$v)));
            $url = str_replace($app_web_dir, '', $url); // remove default domain from url
            if (!empty($url)) {
                $data[$v]['link']       = $http_location.$url;
            } else {
				$data[$v]['link']       = $http_location.'/index.php?target=product&product_id=' . $v;
			}

            // Images
            if ($prod['image_thumb']['tmbn_url']!=$default_thumb) {
                $data[$v]['g:image_link'] = $prod['image_thumb']['tmbn_url'];
                if ($prod['image_det']['tmbn_url']!=$default_det)
                    $data[$v]['g:additional_image_link'] = $prod['image_det']['tmbn_url'];
            } elseif ($prod['image_det']['tmbn_url']!=$default_det) {
                $data[$v]['g:image_link'] = $prod['image_det']['tmbn_url'];
            }

            // Other fields
            $data[$v]['g:condition']    = (!empty($attr[$gb_attr['g:condition']])?$attr[$gb_attr['g:condition']]:$config['google_base']['gb_condition']);

            $data[$v]['g:availability'] = (cw_query_first_cell("SELECT SUM(avail) FROM $tables[products_warehouses_amount] WHERE product_id=$v")?'in stock':'out of stock');

            $data[$v]['g:price']        = ($prod['price']?$prod['price']:0).' '.($config['google_base']['gb_currency']?$config['google_base']['gb_currency']:'USD');

            $data[$v]['g:brand']        = $prod['manufacturer'];

            $data[$v]['g:mpn']          = $prod[!empty($google_base_attributes_map['g:mpn'])?$google_base_attributes_map['g:mpn']:'manufacturer_code'];
            $data[$v]['g:gtin']         = $prod[!empty($google_base_attributes_map['g:gtin'])?$google_base_attributes_map['g:gtin']:'eancode'];

            $data[$v]['g:shipping_weight'] = $prod['weight'].' '.$config['General']['weight_symbol'];
            if ($prod['free_tax'] == 'Y')
                $data[$v]['g:tax'] = '0';
            if ($prod['shipping_freight'] > 0 && $config['Shipping']['replace_shipping_with_freight'] == 'Y')
                $data[$v]['g:shipping'] = $prod['shipping_freight'];
            if ($prod['free_shipping'] == 'Y')
                $data[$v]['g:shipping'] = '0';

            // Apparel specific fields
            $data[$v]['g:color']        = $attr[$gb_attr['g:color']];
            $data[$v]['g:size']         = $attr[$gb_attr['g:size']];
            $data[$v]['g:gender']       = $attr[$gb_attr['g:gender']];
            $data[$v]['g:age_group']    = $attr[$gb_attr['g:age_group']];

            // Use event on_google_base_item_prepare($product_id, &$item) to add own field or validator. Handler must return warning string or null.
            $warn = cw_event('on_google_base_item_prepare', array($v,&$data[$v]), array());

            if (!empty($warn)) $warnings = array_merge($warnings, array_values($warn));

		}


        // Validation of Google Product Search buisness rules
        // http://www.google.com/support/merchants/bin/answer.py?answer=188494
        if ($config['google_base']['gb_validate']=='Y') {
            $deleted = 0;
            foreach ($data as $v=>$item) {
                $to_delete = false;
                if (empty($data[$v]['g:google_product_category']) && preg_match('/Apparel|Media|Software/',$data[$v]['g:product_type'])) {
                    $to_delete = $warnings[] = 'Product '.$data[$v]['g:id'].' Apparel/Media/Software should be supplied with Google Category';
                }
                if (empty($data[$v]['g:image_link']) || $data[$v]['g:image_link'] == $default_thumb || $data[$v]['g:image_link'] == $default_det) {
                    $to_delete = $warnings[] = 'Product '.$data[$v]['g:id'].': image is required';
                }
                if (intval(!empty($data[$v]['g:brand'])) + intval(!empty($data[$v]['g:gtin'])) + intval(!empty($data[$v]['g:mpn'])) < 2)
                    $to_delete = $warnings[] = 'Product '.$data[$v]['g:id'].': you need to submit at least two attributes of "brand", "gtin" and "mpn"';
                if (
                        (
                            empty($data[$v]['g:color']) ||
                            empty($data[$v]['g:size']) ||
                            empty($data[$v]['g:gender']) ||
                            empty($data[$v]['g:age_group'])
                        ) &&
                        strpos($data[$v]['g:google_product_category'].$data[$v]['g:product_type'],'Apparel')!==false
                   )
                    $to_delete = $warnings[] = 'Product '.$data[$v]['g:id'].' Apparels should be supplied with gender/age/color/size';
                    
                if ($to_delete) {
                    $deleted++;
                    unset($data[$v]);
                }
            }
            if ($deleted) {
                array_unshift($warnings, $deleted.' of '.count($items).' products did not pass validation. See log.');
            }
        }

        // Use event on_google_base_items_prepared(&$items) to add own field or validator. Handler must return warning string or null.
        $warn = cw_event('on_google_base_items_prepared', array(&$data), array());
        if (!empty($warn)) $warnings = array_merge($warnings, $warn);

	}

    if ($config['google_base']['gb_file_format'] == 'xml') {
        cw_call('cw_gb_write_xml', array($file, $data));
    } else {
        cw_call('cw_gb_write_csv', array($file, $data));
    }

    fclose($file);


}

if (defined('GB_XML_OUT') && constant('GB_XML_OUT')) {
//    header('Content-type: text/plain');
    header('Content-type: text/'.$config['google_base']['gb_file_format']);
    echo file_get_contents($_filename);
}

$result = count($data);

if ($success) {
    $top_message = array(
        'content' => 'The products have been exported successfully',
        'type' => 'I'
    );
    if (!empty($warnings)) {
        cw_log_add('google_base', $warnings);
        $top_message = array(
                'content' => implode('<br />',  array_slice ($warnings,0,10)).'...',
                'type' => 'W'
            );
    }
    
    $success_msg = "Google Base <a href='$http_location/$_filename'>file</a> updated, price limit: ".$config['general']['currency_symbol'].floatval($config['google_base']['gb_low_price_limit']).", records count: $result";
    cw_call('cw_system_messages_add',array('google_base', $success_msg, SYSTEM_MESSAGE_COMMON, SYSTEM_MESSAGE_INFO));
    
} // if $success
else {
    $top_message = array(
        'content' => cw_get_langvar_by_name('msg_err_file_permission_denied'),
        'type' => 'E'
    );
    
    $warn_msg = "Google Base. <acronym title='$_filename'>".cw_get_langvar_by_name('msg_err_file_permission_denied').'</acronym>';
    cw_call('cw_system_messages_add',array('google_base', $warn_msg, SYSTEM_MESSAGE_COMMON, SYSTEM_MESSAGE_WARNING));
    cw_call('cw_system_messages_show', array('google_base'));
}



unset($data, $xml, $items);
    
    
return $result;
