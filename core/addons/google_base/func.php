<?php
function cw_google_base_cron() {
    global $domain_id;
    
    $log = array();
    $domains = cw_func_call('cw_md_get_domains');
    
    foreach ($domains as $domain) {
        $domain_id = $domain['domain_id'];
        $count = cw_include('addons/google_base/create_gb_xml.php');
        $log[] = $count.' records were exported to feed';
    }
    return join("\n",$log);
}

function cw_gb_get_category_value($category_id, $default_value) {
	global $tables;

	if (empty($category_id)) {
		return empty($default_value) ? FALSE : $default_value;
	}

	$attr_val_id = cw_query_first_cell("SELECT av.value
		                    FROM $tables[attributes_values] av, $tables[attributes] a
		                    WHERE av.item_id=$category_id
		                    	AND av.item_type='C'
		                    	AND a.attribute_id=av.attribute_id
		                    	AND a.field='g:google_product_category'");

	if (empty($attr_val_id)) {
		// Get parent categories
		$parent_categories = array();
		cw_category_generate_path($category_id, $parent_categories);

		if (count($parent_categories)) {

			foreach ($parent_categories as $parent_category_id) {
				$attr_val_id = cw_query_first_cell("SELECT av.value
		                    FROM $tables[attributes_values] av, $tables[attributes] a
		                    WHERE av.item_id=$parent_category_id
		                    	AND av.item_type='C'
		                    	AND a.attribute_id=av.attribute_id
		                    	AND a.field='g:google_product_category'");

				if (!empty($attr_val_id)) {
					break;
				}
			}
		}
	}

	if ($attr_val_id) {
		$attr_val = cw_query_first_cell("SELECT value FROM $tables[attributes_default] WHERE attribute_value_id = " . $attr_val_id);

		if (empty($attr_val)) {
			return empty($default_value) ? FALSE : $default_value;
		}

		return $attr_val;
	}

	return empty($default_value) ? FALSE : $default_value;
}


function cw_gb_write_xml($file, $data) {

    
    /*
     * Perform XML content
     */


    $xml = <<<XML
<?xml version="1.0" ?>
<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0" xmlns:c="http://base.google.com/cns/1.0">
<channel>
<title>Products Catalog</title>
<link>$http_location/$_filename</link>
<description>Google Product Search feed for $http_location</description>

XML;

    fwrite($file, $xml); // Header
    
    foreach ($data as $p) {
        $item = cw_hash2xml($p,1);
        $xml = "<item>\n$item\n</item>\n";
        fwrite($file, $xml); // Item
    }

    $xml = '</channel>'."\n".'</rss>';

//    $xml = cw_xml_format($xml);
    fwrite($file, $xml); // Footer
    
}

function cw_gb_write_csv($file, $data) {
    
    $header = array();
    foreach ($data as $p) {
        $header = array_merge($header, $p);
    }
    
    $header = array_keys($header);
    $pattern = array_fill_keys($header, '');
    
    fputcsv($file, $header);
    
    foreach ($data as $p) {
        $csv = array_merge($pattern, $p);
        fputcsv($file, $csv);
    }
    
}

function cw_gb_product_microdata($product_id) {
    global $tables, $config;

    $data = array();
    
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

    $attr = cw_query_hash("select av.attribute_id as attribute_id, IF(ISNULL(ad.value),av.value,ad.value) as value 
            FROM $tables[attributes_values] av LEFT JOIN $tables[attributes_default] ad on attribute_value_id=av.value and ad.attribute_id=av.attribute_id
            WHERE item_id=$product_id AND av.attribute_id IN ('".implode("','",$gb_attr)."')",'attribute_id',false, true);
    
    $data['g:condition']    = (!empty($attr[$gb_attr['g:condition']])?$attr[$gb_attr['g:condition']]:$config['google_base']['gb_condition']);
    return $data;
}
