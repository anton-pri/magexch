<?php

//error_reporting(E_ALL ^ E_NOTICE);
set_time_limit(86400);

cw_load('import_export_csv','export','product', 'category');
$import = &cw_session_register('import');

// List of tables for export (could be changed any time)
$export_list="categories,products,product_variants,product_variant_items,
		product_options,product_options_values,customers,customers_addresses,docs,docs_info";

$export_list=preg_replace("'\s+'s","",$export_list);
$export_list=explode(',',$export_list);

cw_event('on_export_tables_list', array(&$export_list)); // Append your tables to $export_list array

foreach ($export_list as $k => $v) 
	if (!isset($tables[$v])) 
		unset($export_list[$k]);

if ($delimiter=='tab') $delimiter = "\t";

if ($action == 'export') {
    
    $saved_searches = array(
        'products' => array('field'=>'product_id',
                            'tables'=>array('products','product_variants','product_variant_items','product_options','product_options_values'),
                            'type'=>'P',
                            ),
        'customers' => array('field'=>'customer_id',
                            'tables'=>array('customers','customers_addresses'),
                            'type'=>'C',
                            ),                            
        'docs'      => array('field'=>'doc_id',
                            'tables'=>array('docs','docs_info'),
                            'type'=>'O',
                            ),
    );
    
    cw_objects_reset('P-'.$preset['products']);
    cw_objects_reset('C-'.$preset['customers']);
    cw_objects_reset('O-'.$preset['docs']);
    
    foreach($saved_searches as $kk=>$vv) {
        if ($preset[$kk] && $query=cw_query_first_cell("select sql_query from $tables[saved_search] where ss_id={$preset[$kk]}")) {
            $res = db_query($query);
            $ids = array();
            while ($val = db_fetch_array($res)) {
                $ids[] = $val[$vv['field']];
            }
            
            // Objects set type is <saved_search_type>-<saved_search_id>, eg P-10
            cw_objects_add_to_set($ids, $vv['type'].'-'.$preset[$kk]);

        }
    }
    
    $export_table = $request_prepared['export_table'];
    
    foreach ($export_list as $v) {
        if (isset($export_table[$v]) && $export_table[$v]==1) {
            $query = '';
            foreach($saved_searches as $kk=>$vv) {
                if (in_array($v,$vv['tables']) && $preset[$kk]) {
                    $query = "select main_table.* from {$tables[$v]} as main_table INNER JOIN $tables[objects_set] as aux_table 
                    ON main_table.{$vv['field']}=aux_table.object_id AND aux_table.customer_id=$customer_id AND aux_table.set_type='{$vv['type']}-{$preset[$kk]}'";
                }
                // docs_info - special case becuase it is linked with docs by "doc_info_id"
                if ($v == 'docs_info') {
                    $query = "select main_table.* from {$tables[$v]} as main_table 
                    INNER JOIN $tables[docs] as d ON main_table.doc_info_id=d.doc_info_id
                    INNER JOIN $tables[objects_set] as aux_table 
                    ON d.{$vv['field']}=aux_table.object_id AND aux_table.customer_id=$customer_id AND aux_table.set_type='{$vv['type']}-{$preset[$kk]}'";
                }
            }
            
            cw_table2csv($v,$delimiter,$query);
        }
    }
    
    cw_call_delayed('cw_objects_reset', array('O-'.$preset['docs']));
    cw_call_delayed('cw_objects_reset', array('P-'.$preset['products']));
    cw_call_delayed('cw_objects_reset', array('C-'.$preset['customers']));
    
    cw_add_top_message('Export completed');
    cw_header_location('index.php?target=import&mode=expdata');
}

if ($action == 'delete' && isset($filenames) && is_array($filenames)) {
    foreach ($filenames as $v) {
        if (file_exists(csv_path.'/'.$v)) {
            unlink(csv_path.'/'.$v);
        }
    }
    cw_add_top_message('Files deleted successfully');
    cw_header_location('index.php?target=import&mode=expdata');
}

$files=cw_list_csv_dir();
$smarty->assign('files', $files);
$path=$var_dirs_web['files']."/csv";
$smarty->assign('path', $path);

$smarty->assign('main', 'export_data');

$saved_searches = array(
    'docs'      => cw_query("select name, ss_id from $tables[saved_search] where type='O' order by name, ss_id"),
    'customers' => cw_query("select name, ss_id from $tables[saved_search] where type='C' order by name, ss_id"),
    'products'  => cw_query("select name, ss_id from $tables[saved_search] where type='P' order by name, ss_id"),
);

$smarty->assign('saved_searches',$saved_searches);
$smarty->assign('export_list', $export_list);
