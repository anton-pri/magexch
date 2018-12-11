<?php
function cw_md_get_domains() {
    global $tables;
    $domains = cw_query("select * from $tables[domains]");
    if ($domains)
    foreach($domains as $k=>$domain)
        $domains[$k]['languages'] = unserialize($domain['languages']);
    return $domains;
}

function cw_md_get_available_domains($as_string = true) {
    global $app_config_file, $current_domain;

    if (AREA_TYPE == 'C') $domains = array(0, $app_config_file['web']['domain_id']);
    elseif($current_domain != -1) $domains = array(0,$current_domain);

    if (!$domains) return false;

    if ($as_string)
		return "('".implode("', '", $domains)."')";
    else
		return $domains;
}

function cw_md_product_search($params, $return) {
    global $tables, $domain_attributes, $config;

    $conditions = cw_md_get_available_domains();

    if ($conditions === false) return $return;

    $return['query_joins']['attributes_values'] = array(
        'on' => "$tables[products].product_id=$tables[attributes_values].item_id and $tables[attributes_values].attribute_id='$domain_attributes[P]' and $tables[attributes_values].value in ".$conditions,
       'is_inner' => 1,
    );
	if ($config['Appearance']['categories_in_products'] == '1') {
	# kornev, bugfix - here we have to check the category too
		$return['query_joins']['cav'] = array(
			'parent' => 'products_categories',
			'tblname' => 'attributes_values',
			'on' => "$tables[products_categories].category_id=cav.item_id and cav.attribute_id='$domain_attributes[C]' and cav.value in ".$conditions,
		   'is_inner' => 1,
		);
	}

    return $return;
}

function cw_md_category_search($params, $return) {
    global $tables, $domain_attributes;

    $conditions = cw_md_get_available_domains();
    if ($conditions === false) return $return;

    $return['query_joins']['attributes_values'] = array(
        'on' => "$tables[categories].category_id=$tables[attributes_values].item_id and $tables[attributes_values].item_type = 'C' and $tables[attributes_values].attribute_id='$domain_attributes[C]' and $tables[attributes_values].value in ".$conditions,
       'is_inner' => 1,
    );
    return $return;
}

/**
 * Adds altskin as source of email templates
 * 
 * @see pre-hook for cw_send_mail
 * @params null
 * @return null
 */
function cw_md_send_mail() {
    global $smarty, $app_dir, $config;
    
    $altskin = with_leading_slash_only($config['Email']['alt_skin']);
    
    if (empty($altskin)) return;
    
    if (!in_array($app_dir.$altskin,$smarty->template_dir,true)) {
        array_unshift($smarty->template_dir, $app_dir.$altskin);
        $smarty->compile_dir = $app_dir.'/var/templates'.$altskin;
        if (!file_exists($smarty->compile_dir)) {
            cw_mkdir($smarty->compile_dir);
        }
    }

    $config['General']['list_available_cdn_servers'] = '';

}

function cw_md_manufacturer_search($params, $return) {
    global $tables, $domain_attributes;

    $conditions = cw_md_get_available_domains();
    if ($conditions === false) return $return;

    $return['query_joins']['attributes_values'] = array(
        'on' => "$tables[manufacturers].manufacturer_id=$tables[attributes_values].item_id  and $tables[attributes_values].item_type = 'M' and $tables[attributes_values].attribute_id='$domain_attributes[M]' and $tables[attributes_values].value in ".$conditions,
       'is_inner' => 1,
    );

    return $return;
}

function cw_md_speed_bar_search($params, $return) {
    global $tables, $domain_attributes;

    $conditions = cw_md_get_available_domains();
    if ($conditions === false) return $return;

    $return['query_joins']['attributes_values'] = array(
        'on' => "$tables[speed_bar].item_id=$tables[attributes_values].item_id  and $tables[attributes_values].item_type = 'B' and $tables[attributes_values].attribute_id='$domain_attributes[B]' and $tables[attributes_values].value in ".$conditions,
       'is_inner' => 1,
    );
    return $return;
}

function cw_md_shipping_search($params, $return) {
    global $tables, $domain_attributes;

    $conditions = cw_md_get_available_domains();
    if ($conditions === false) return $return;

    $return['query_joins']['attributes_values'] = array(
        'on' => "$tables[shipping].shipping_id=$tables[attributes_values].item_id  and $tables[attributes_values].item_type = 'D' and $tables[attributes_values].attribute_id='$domain_attributes[D]' and $tables[attributes_values].value in ".$conditions,
       'is_inner' => 1,
    );
    return $return;
}

function cw_md_payment_search($params, $return) {
    global $tables, $domain_attributes;

    $conditions = cw_md_get_available_domains();
    if ($conditions === false) return $return;

    $return['query_joins']['attributes_values'] = array(
        'on' => "$tables[payment_methods].payment_id=$tables[attributes_values].item_id  and $tables[attributes_values].item_type = 'G' and $tables[attributes_values].attribute_id='$domain_attributes[G]' and $tables[attributes_values].value in ".$conditions,
       'is_inner' => 1,
    );
    return $return;
}

function cw_md_taxes_search($params, $return) {
    global $tables, $domain_attributes;

    $conditions = cw_md_get_available_domains();
    if ($conditions === false) return $return;

    $return['query_joins']['attributes_values'] = array(
        'on' => "$tables[taxes].tax_id=$tables[attributes_values].item_id  and $tables[attributes_values].item_type = 'T' and $tables[attributes_values].attribute_id='$domain_attributes[T]' and $tables[attributes_values].value in ".$conditions,
       'is_inner' => 1,
    );
    return $return;
}

function cw_md_check_skin($skin) {
    return true;
}

function cw_md_domain_create($data) {
    global $app_config_file, $addons;

    if (!$data['skin']) $data['skin'] = 'skins';
    $data['skin'] = with_leading_slash_only($data['skin']);
    $data['web_dir'] = with_leading_slash_only($data['web_dir']);
    $data['languages'] = serialize($data['languages']);
    $fields = array(
		'name', 
		'http_host', 
		'https_host', 
		'http_alias_hosts', 
		'web_dir', 
		'skin', 
		'language', 
		'languages'
	);
	$domain_id = cw_array2insert('domains', $data, TRUE, $fields);
	
	if (isset($data['attribute'])) {
		cw_call('cw_attributes_save', array('item_id' => $domain_id, 'item_type' => 'DM', 'attributes' => $data['attribute']));
	}

    if ($data['skin'] != $app_config_file['web']['skin']) cw_md_copy_skin($app_config_file['web']['skin'], $data['skin'], '/');
    return cw_md_check_skin($data['skin']);
}

function cw_md_domain_delete($domain_id) {
    global $domain_attributes, $tables;
    db_query("delete from $tables[domains] where domain_id='$domain_id'");
    db_query("delete from $tables[attributes_values] where attribute_id in ('".implode("', '", $domain_attributes)."') AND (value='$domain_id')");
}

function cw_md_domain_get($params) {
    global $tables;
    return cw_query_first("select * from $tables[domains] where domain_id='$params[domain_id]'");
}

function cw_md_domain_update($domain_id, $data) {
	global $addons;
    $data['languages'] = serialize($data['languages']);
    $data['skin'] = with_leading_slash_only($data['skin']);
    $data['web_dir'] = with_leading_slash_only($data['web_dir']);
    $fields = array(
		'name', 
		'http_host', 
		'https_host', 
		'http_alias_hosts', 
		'web_dir', 
		'skin', 
		'language', 
		'languages'
	);

	if (isset($data['attribute'])) {
		cw_call('cw_attributes_save', array('item_id' => $domain_id, 'item_type' => 'DM', 'attributes' => $data['attribute']));
	}

    cw_array2update('domains', $data,	"domain_id='$domain_id'", $fields);
    return cw_md_check_skin($data['skin']);
}

function cw_md_get_available_languages() {
    global $host_data, $tables, $current_language;
    if (empty($host_data['languages']))
        $host_data['languages'] = array();
    return cw_query_hash("select ls.*, lng.value as language from $tables[languages_settings] as ls left join $tables[languages] as lng ON lng.code = '$current_language' and lng.name = CONCAT('language_', ls.code) where ls.code in ('".implode("', '", $host_data['languages'])."')", 'code', false);
}

function cw_md_doc_place_order($params, $return = null) {
    global $host_data;

    if (!$return) $return = array();
    $return['attributes'] = array('domains' => $host_data['domain_id']);
    return $return;
}

function cw_md_get_config_values($domain_id) {
    global $tables, $current_language;

    $options = cw_query("select c.*, dc.value as domain_value, IFNULL(lng.value, c.comment) as title from $tables[domains_config] as dc, $tables[config] as c left join $tables[languages] as lng on lng.code = '$current_language' and lng.name = CONCAT('opt_', c.name) where c.name=dc.name and c.config_category_id=dc.config_category_id and dc.domain_id='$domain_id' order by c.orderby");

    return cw_config_process_options($options);
}

# kornev, it's ok for small number of params, in other case it's better to redefine the basic function
function cw_md_core_get_config() {
    global $tables, $current_domain;

    $return = cw_get_return();
    if (APP_AREA != 'customer') return $return;
    if ($config_tmp = db_query("select name, value, category from $tables[domains_config] as dc, $tables[config_categories] as cc where cc.config_category_id = dc.config_category_id and dc.domain_id = '$current_domain'")) {
        while ($arr = db_fetch_array($config_tmp)) {
            if ($arr['category'] == 'main'){
                if(isset($return[$arr['name']]))
                    $return[$arr['name']] = $arr['value'];
            }elseif($arr['type'] == 'multiselector'){
                if(isset($return[$arr['category']][$arr['name']]))
                    $return[$arr['category']][$arr['name']] = explode(';', $arr['value']);
            }else{
                if(isset($return[$arr['category']][$arr['name']]))
                    $return[$arr['category']][$arr['name']] = $arr['value'];
            }
        }
        db_free_result($config_tmp);
    }
    return $return;
}

function cw_md_code_get_template_dir($params, $return) {
    global $target, $current_domain, $app_dir, $tables;

    $return = (array)$return;

    $data = cw_func_call('cw_md_domain_get', array('domain_id' => $current_domain));
    $altskin = $data['skin'];

    if (!$altskin) return $return;

    if (APP_AREA == 'admin' && $target == 'file_edit' && $current_domain) {
        if (!$altskin) return $return;
        return $app_dir.$altskin;
    }

    if (!in_array($app_dir.$altskin,$return,true)) array_unshift($return, $app_dir.$altskin);

    return $return;
}

function cw_md_attributes_save($item_id, $item_type, $attributes, $language = null, $extra = array()) {

# kornev, for the categories we need to distribute the params sometimes
    if ($item_type == 'C') {
        $cats[] = $item_id;
# kornev, only domains should be updated
        $new_attributes = array('domains' => $attributes['domains']);
        
        // Disable this hook to avoid loop
        cw_unset_hook('cw_attributes_save',       'cw_md_attributes_save',            EVENT_POST);
        
        if ($attributes['subcats_distribution']) {
            $subcats = cw_category_get_subcategory_ids($item_id);
            foreach($subcats as $cat) {
                if ($cat != $item_id) {
                    cw_call('cw_attributes_save', array('item_id' => $cat, 'item_type' => 'C', 'attributes' => $new_attributes, 'language' => $language));
                    $cats[] = $cat;
                }
            }
        }
        if ($attributes['subproducts_distribution']) {
            global $user_account, $current_area;
            list($products, $tmp) = cw_func_call('cw_product_search', array('data' => array('categories' => $cats, 'categories_orig' => $cats, 'user_account' => $user_account, 'current_area' => $current_area, 'info_type' => 0, 'flat_search' => 1, 'all' => 1)));
            if (is_array($products))
            foreach($products as $product)
                cw_call('cw_attributes_save', array('item_id' => $product['product_id'], 'item_type' => 'P', 'attributes' => $new_attributes, 'language' => $language));
        }
        
        // Return hook back
        cw_set_hook('cw_attributes_save',       'cw_md_attributes_save',            EVENT_POST);

    }
    
    return null;
}

function cw_md_copy_skin($dir, $dir_, $int='') {
    $status = array();
    if(is_dir($dir.$int))
        if($handle = opendir($dir.$int)){

            while (($file = readdir($handle))){
                if (($file == ".") || ($file == ".."))
                    continue;

                $full = $int.$file;
                $is_dir = is_dir($dir.$full);

                if($is_dir) {
                    $full .= '/';
                }

                $file_info = pathinfo($full);
                if($is_dir) {
                    if (!is_dir($dir_.$full) && !@mkdir($dir_.$full)) $status[] = 'Not possible to create directory: '.dirname($dir_.$full);
                    $status = array_merge($status, cw_md_copy_skin($dir, $dir_, $full));
                }
                elseif(in_array($file_info['extension'], array('js', 'css', 'tpl', 'gif', 'jpg', 'png', 'jpeg', 'bmp','htaccess'))) {
                        if(!file_exists($dir_.$full)) {
                            if(is_dir(dirname($dir_.$full))){
                                if (!@copy($dir.$full, $dir_.$full))
                                    $status[] = 'Can\'t add file: '.$dir_.$full;
                            }
                            else
                                $status[] = 'No directory: '.dirname($dir_.$full);
                        }
                }
            }
            closedir($handle);
        }
        else
            $status[] = 'Can\'t open '.$dir.$int." directory  (need to check permissions)";

    return $status;
}

function cw_md_cleanup_skin($dir, $dir_, $int=''){
    global $app_dir;

    $int = with_leading_slash($int);

    if(!cw_allowed_path($app_dir,$dir.$int)) return false;
    if(!cw_allowed_path($app_dir,$dir_.$int)) return false;

    $status = array();
    if(is_dir($dir.$int))
        if($handle = opendir($dir.$int)) {

            while (($file = readdir($handle))){

                if (($file == ".") || ($file == ".."))
                    continue;

                $full = $int.$file;
                $is_dir = is_dir($dir.$full);

                if($is_dir) {
                    $status = array_merge($status, cw_md_cleanup_skin($dir, $dir_, with_slash($full)));
                    if (cw_is_empty_dir($dir_.$full)) {
                        cw_rm_dir($dir_.$full);
                        $status[] = '[ ] Dir '.$dir_.$full.' removed';
                    }
                }
                elseif (in_array(pathinfo($full,PATHINFO_EXTENSION),array('tpl','css','js','gif','png','jpg','jpeg','bmp'),true)) {
                        if (file_exists($dir_.$full)) {
                            $md5 = md5_file($dir.$full);
                            $md5_= md5_file($dir_.$full);
                            $same = ($md5==$md5_);

                            if ($same) {
                                if (!unlink($dir_.$full))
                                $status[] = '[!] Can\'t remove file: '.$dir_.$full;
                                else
                                $status[] = '[ ] File '.$dir_.$full.' removed';
                            } else {
                                $status[] = '[*] File '.$dir_.$full.' differs';
                            }
                        }
                }
            }
            closedir($handle);
        }
        else
            $status[] = '[!] Can\'t open '.$dir.$int." directory  (need to check permissions)";
    return $status;
}

// get clean hostname
function cw_md_get_host() {
	global $HTTP_HOST, $CLIENT_IP, $REMOTE_ADDR;

	$host = $HTTP_HOST;
	
	if (empty($host)) {
		$host = @gethostbyaddr($CLIENT_IP);		
	}

	if (empty($host)) {
		$host = @gethostbyaddr($REMOTE_ADDR);		
	}
	
	return strtolower($host);
}

// get domain aliases array
function cw_md_get_domain_aliases($domain_id) {
	global $tables;

	$result = array();

	$aliases = cw_query_first_cell("SELECT http_alias_hosts FROM $tables[domains] WHERE domain_id = " . $domain_id);

	if (!empty($aliases)) {
		$result = explode("\n", $aliases);
	}

	return $result;
}

// function try get domain data by host alias
function cw_md_get_domain_data_by_alias(&$host_data) {
	global $tables, $HTTPS;

	$host_value = cw_md_get_host();
	// Get all domains like HTTP_HOST
	$result = cw_query("SELECT * FROM $tables[domains] 
						WHERE http_alias_hosts LIKE'%" . $host_value . "%'");

	if (!empty($result)) {

		foreach ($result as $data) {
			// get aliases for the domain
			$hosts = cw_md_get_domain_aliases($data['domain_id']);

			if (!empty($hosts)) {
				
				foreach ($hosts as $host) {
					
					// if host equal then return domain data
					if (trim($host) == $host_value) {
						$host_data = $data;
                        $host_data['http_host'] = $host_value;
						return $host_data;
					}
				}
			}
		}
	}
	
	return array();
}

// change search query params for order search
function cw_md_prepare_search_orders($data, $docs_type, &$fields, &$query_joins, &$where, &$groupbys, &$having, &$orderbys) {
	global $tables;

	if ($data['search_sections']['tab_search_orders_advanced'] && !empty($data['advanced']['domain_id'])){
		$attribute_id = cw_query_first_cell("
			SELECT attribute_id
			FROM $tables[attributes]
			WHERE field = 'domains' AND active = 1 AND item_type = 'O' AND addon = 'multi_domains'
		");
		if ($attribute_id) {
			$query_joins['attributes_values']['on'] = "$tables[attributes_values].item_id = $tables[docs].doc_id";
			$where[] = "$tables[attributes_values].item_type = 'O'";
			$where[] = "$tables[attributes_values].attribute_id = '$attribute_id'";
			$where[] = "$tables[attributes_values].value IN ('" . implode("', '", $data['advanced']['domain_id']) . "')";
		}
	}
}
