<?php
namespace cw\flat_attributes_search;

/** =============================
 ** Addon functions, API
 ** =============================
 **/

/**
 * Get flatten attributes
 *  
 * @return array([attribute_id]=>field, ...)
 */
function flat_attributes_get_config() {
	global $config;
    $att_ids = unserialize($config[addon_name]['flat_attributes']);
    
    return (array)$att_ids;
}

/** 
 * Return preset of fields to be flatted
 * 
 * Set attributes fields in your config file as
  
[flat_attributes_search]
fields=field_name_1,field_name_2,field_name_3,field_name_4
 
 * @return array([attribute_id]=>field, ...)
 */
function flat_attributes_get_preset() {
    global $app_config_file;
    
    $att_fields = explode(',',$app_config_file[addon_name]['fields']);
    $att_fields = array_map('trim', $att_fields);
    $att_fields = array_filter($att_fields);
    
    // Convert fields to attribute_id
    $att_ids = array();
    foreach($att_fields as $att_field) {
        if ($att = cw_call('cw_attributes_filter', array(array('field'=>$att_field,'item_type'=>'P'),true))) {
            
            // Check fields must have single value, otherwise it can't be flatten
            if ($att['type'] != 'multiple_selectbox')
                $att_ids[intval($att['attribute_id'])] = $att['field'];
        }
    }    
    
    return $att_ids;
}

/**
 * Get attributes currently flatten (exist in table)
 */
function flat_attributes_flatten() {
    static $attributes;
    
    if (!empty($attributes)) return $attributes;
    
    global $tables, $cw_attributes;
    $fields = cw_check_field_names(null, $tables['flat_attributes_search']);
    $attributes = array();
 
    foreach ($fields as $f) {
        $attr_id = intval(str_replace('a','',$f));
        if (isset($cw_attributes['all'][$attr_id])) 
            $attributes[$attr_id] = $attr_id;
    }
    
    return $attributes;
}

/**
 * Recreate table with flat attributes based on INI config
 * 
 * @return int $result:
 * -5 - fail to lock process
 * -4 - nothing to create or no rigths for CREATE a table
 * -3 - failure on insertion of new data
 *  1 - success
 */
function flat_create_table() {
    global $config, $tables, $current_language;
    global $cw_attributes;
    
    $result = -5;
    
    if (cw_lock('flat_create_table', 180, 10)) {

        $result = -4;
                
        cw_config_update(addon_name,array('flat_attributes'=>''));
        
        sleep(3); // wait while possible concurrent search request finished

        // Drop old table
        db_query("DROP TABLE IF EXISTS $tables[flat_attributes_search]");
        
        $att_ids = cw_call('cw\\'.addon_name.'\flat_attributes_get_preset'); // take fields which must be flatten
        
        // Create new table
        $create_fields = $create_index = $insert_fields = $select_fields = $join_tables = array();
        $create_query = 'CREATE TABLE `'.$tables['flat_attributes_search'].'` (
        `product_id` int(11) NOT NULL,
        ';

        foreach ($att_ids as $id=>$field) {
            $type = 'int(11)';
            if (in_array($cw_attributes['all'][$id]['type'], array('text'))) $type='varchar(255)';
            $create_fields[] = '`a'.$id.'` '.$type.' COMMENT "'.$field.'"';
            $create_index[] = 'KEY `a'.$id.'` (`a'.$id.'`)';
            
            $insert_fields[] = '`a'.$id.'`';
            $select_fields[] = "t$id.value a$id";
            $join_tables[] = "LEFT JOIN $tables[attributes_values] t$id ON t$id.item_id=p.product_id AND t$id.attribute_id=$id AND t$id.code IN ('','EN')";
        }
        
        // Point for extension by addons. Event cw\flat_attributes_search\on_flat_attributes_prepare
        cw_event('cw\\'.addon_name.'\on_flat_attributes_prepare', array(&$create_fields,&$create_index,&$insert_fields,&$select_fields,&$join_tables));
        
        $create_query .= join(",\n",$create_fields).",\n PRIMARY KEY (`product_id`), \n".join(",\n",$create_index);
        $create_query .= ') ENGINE=InnoDB';
        
        
        if (!empty($create_fields) && db_query($create_query)) {
            
            $result = -3;
            
            // Populate data
            $insert_query = "INSERT LOW_PRIORITY IGNORE INTO $tables[flat_attributes_search]
            (product_id, ".join(',',$insert_fields).")
            SELECT p.product_id, \n".join(",\n",$select_fields)."
            FROM $tables[products] as p \n".join("\n",$join_tables);
            
            if (db_query($insert_query) && cw_query_first_cell("SELECT count(*) FROM $tables[flat_attributes_search]")) {
                // Save to config
                cw_config_update(addon_name,array('flat_attributes'=>serialize($att_ids)));
                $result = 1;
            }

        }

    };
    cw_unlock('flat_create_table');
    return $result;
}

/**
 * Rebuild flat data for one specific product
 */
function flat_rebuild_product($product_id) {
    global $tables, $config;
    
    $product_id = intval($product_id);
    if (empty($product_id)) return false;
    
    db_query("DELETE FROM $tables[flat_attributes_search] WHERE product_id='$product_id'");

    $att_ids = cw_call('cw\\'.addon_name.'\flat_attributes_get_config');
    
    // Insert data
    $insert_fields = $select_fields = $join_tables = array();

    foreach ($att_ids as $id=>$field) {
        $insert_fields[] = '`a'.$id.'`';
        $select_fields[] = "t$id.value a$id";
        $join_tables[] = "LEFT JOIN $tables[attributes_values] t$id ON t$id.item_id=p.product_id AND t$id.attribute_id=$id AND t$id.code IN ('','EN')";
    }
    
    $insert_query = "INSERT LOW_PRIORITY IGNORE INTO $tables[flat_attributes_search]
    (product_id, ".join(',',$insert_fields).")
    SELECT p.product_id, \n".join(",\n",$select_fields)."
    FROM $tables[products] as p \n".join("\n",$join_tables)."
    WHERE p.product_id = '$product_id'";
    
    // TODO - delayed insert
    db_query($insert_query);
        
}



/** =============================
 ** Hooks
 ** =============================
 **/

/**
 * Rebuild a product after changes
 */
function cw_product_build_flat($params, $return) {
    
    $product_id = $params['product_id'];
    
    if (empty($product_id)) {
        if ($tick > 0) cw_flush('Recreate cw_flat_attributes_search (it may take some time) ..');
        $result = cw_call('cw\\'.addon_name.'\flat_create_table');
        if ($tick > 0) {
            if ($result>0)  cw_flush('..Created');
            else            cw_flush("..Failed [result:$result]");
        }
    } else {

        if (!is_array($product_id)) $product_id = array($product_id);
        foreach ($product_id as $pid)
            cw_call('cw\\'.addon_name.'\flat_rebuild_product', array($pid));
    }
    return $return;
}



/** =============================
 ** Events handlers
 ** =============================
 **/


/**
 * Tweak arrays before generate products search query.
 * Replace multiple cw_attributes_values join to one cw_flat_attributes_search
 * 
 * @see event on_prepare_search_products in cw.products.php:cw_product_search()
 * 
 * @param array $param - params as passed cw_product_search()
 * @param &$fields, &$from_tbls, &$query_joins, &$where, &$groupbys, &$having, &$orderbys - part of furhter generated SQL query, passed by reference
 * 
 * @return null
 */
function on_prepare_search_products($params, &$fields, &$from_tbls, &$query_joins, &$where, &$groupbys, &$having, &$orderbys) {
	global $tables, $current_language, $config;
  
    $flatten = flat_attributes_get_config();
    
    if (!empty($params['data']['attributes']) && !empty($flatten)) {
        foreach ($params['data']['attributes'] as $attr_id=>$v) {
            if (isset($query_joins['atv_'.$attr_id]) && $flatten[$attr_id]) {
                
                $query_joins['flat_attr'] = array(
                    'tblname' => 'flat_attributes_search',
                    'on'    => "$tables[products].product_id=flat_attr.product_id",
                    'is_inner' => 1,
                );
                $on = $query_joins['atv_'.$attr_id]['on'];
                $on = str_replace("$tables[products].product_id=atv_$attr_id.item_id and atv_$attr_id.attribute_id = '$attr_id' and atv_$attr_id.code in ('$current_language', '') and ",'',$on);
                $on = str_replace("atv_$attr_id.value","flat_attr.a$attr_id",$on);
                $where[] = $on;
                
                unset($query_joins['atv_'.$attr_id]);
            }
        }
    }
    
    return null;
}

/**
 * Cron handler rebuilds table
 */
function on_cron_rebuild($time, $counter) {
    if (intval($counter)>0 && date('j') % 2 == 0) return null; // Every 2 days 
    $result = cw_call('cw\\'.addon_name.'\flat_create_table');
    $attr = unserialize(cw_query_first_cell("SELECT FROM $tables[config] WHERE name='flat_attributes'"));
    $log = array('flat_create_table' => $result,'attributes'=>$attr);
    return $log;
}
