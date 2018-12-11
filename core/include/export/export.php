<?php
namespace cw\export;

// use $target, $mode and $action params to define subject and action to call
// e.g. $target_$mode_$action or $target_$mode or $target_$action
$action_function = join('_',array_filter(array($target,$mode,$action)));

// Default action
if (empty($action_function) || !function_exists('cw\export\\'.$action_function)) {
	$action_function = 'export_view';
}

cw_load('import_export_csv','export');

// Call action
$action_result = cw_call('cw\export\\'.$action_function);

// Action can return instance of Error via error() function
// see docs/core.error.txt
if (is_error($action_result)) {
    cw_add_top_message($action_result->getMessage(), 'E');
}

return $action_result;

/* ================================================================================== */

/* Actions */

function export_view() {
    global $smarty,$var_dirs_web;
    
    cw_load('import_export_csv');
    
    $_export_types = cw_call('cw\export\get_export_types');
    $export_types = array();
    foreach ($_export_types as $t) {
        cw_include_once('include/export/export_'.$t.'.php');
        if (function_exists('cw\export\\'.$t.'\get_export_type')) {
            $export_types[$t] = cw_call('cw\export\\'.$t.'\get_export_type');
        }        
    }

    $smarty->assign('export_types', $export_types);
    $smarty->assign('export_formats', cw_call('cw\export\get_export_formats'));
    $smarty->assign('saved_searches', cw_call('cw\export\get_saved_searches'));

    $smarty->assign('files', cw_list_csv_dir());
    $path=$var_dirs_web['files']."/csv";
    $smarty->assign('path', $path);

    $smarty->assign('current_section_dir','import_export');
    $smarty->assign('main','export');
}

function export_do_export() {
    global $request_prepared, $customer_id, $var_dirs,$current_location, $tables;
    
    set_time_limit(86400);

    cw_load('import_export_csv'); // work with csv in files/csv
    cw_load('export'); // Object sets manipulation

    cw_include('include/export/export_output.php');
    cw_include('include/export/export_field_handler.php');
    
    if (empty($request_prepared['types'])) {
        return error('Select export types');
    }

    $saved_searches = cw_call('cw\export\get_saved_searches');
    
    $log_fname = $var_dirs['tmp'].DIRECTORY_SEPARATOR.'export_log_'.$customer_id;
    
    $filled_saved_searches = array();
    
    cw_lock('export_'.$customer_id);

    $log_file = fopen($log_fname,'w');
    
    fwrite($log_file,"Start export\n");
    
    foreach ($request_prepared['types'] as $k=>$type) {
        if (empty($type['export'])) continue;
        
        $t = $type['type'];
        // Load and Init export type
        cw_include_once('include/export/export_'.$t.'.php');
        if (!function_exists('cw\export\\'.$t.'\get_export_type')) continue;
        $export_type  = cw_call('cw\export\\'.$t.'\get_export_type');
        
        // Prepare schema
        $schema = $export_type['schemas'][$type['schema']];
        if (empty($schema) && !empty($export_type['schemas'])) {
            $schema = reset($export_type['schemas']);
        }
        if (is_string($schema['fields'])) {
            $schema['fields'] = explode(',',$schema['fields']);
        }
        $schema['fields'] = array_filter(array_map('trim',$schema['fields']));

        if ($request_prepared['delimiter']) {
            $schema['options']['delimiter'] = $request_prepared['delimiter'];
        }
        $schema['options']['delimiter']  = $schema['options']['delimiter'] ?: ',';
        if ($schema['options']['delimiter']=='tab') $schema['options']['delimiter'] = "\t";

        if ($request_prepared['format']) {
            $schema['options']['format'] = $request_prepared['format'];
        }
        $format = $schema['options']['format']  = $schema['options']['format'] ?: 'csv';
        $format_info     = cw_call('cw\export\get_export_formats',array($format));
            
        fwrite($log_file,"======================================\nExport $export_type[name] (schema '$schema[name]'; format '$format'; delimiter '{$schema['options']['delimiter']}')\n");
        //fwrite($log_file,"Fields:\n".join(', ',$schema['fields'])."\n");
         // Prepare saved search if specified
        if ($type['saved_search'] && empty($filled_saved_searches[$type['saved_search']])) {

            $ss = cw_query_first("select name, sql_query from $tables[saved_search] where ss_id={$type['saved_search']}");

            fwrite($log_file,"Prepare saved search \"$ss[name]\" ...\n");
            cw_objects_reset($export_type['saved_search'].'-'.$type['saved_search']);

            $res = db_query($ss['sql_query']);
            $ids = array();
            while ($val = db_fetch_array($res)) {
                $ids[] = $val[$saved_searches[$export_type['saved_search']]['key_field']];
            }
            db_free_result($res);
            
            // Objects set type is <saved_search_type>-<saved_search_id>, eg P-10
            cw_objects_add_to_set($ids, $export_type['saved_search'].'-'.$type['saved_search']);
            $filled_saved_searches[$type['saved_search']] = $export_type['saved_search'].'-'.$type['saved_search'];
        }
 
        // Open file
        // TODO: file_name can be passed as request
        $format_info['ext'] = $schema['options']['ext']?:$format_info['ext'];
        $fn = cw_create_csv_filename($t.'-'.$type['schema'],$format_info['ext']);
        $fn = cw_allow_file($fn, true);
        
        $request_prepared['types'][$k]['filename'] = $fn;

        $h  = fopen($fn,'w');
        
        // Write header
        $header = cw_call('cw\export\output_header_'.$format, array($export_type, $schema));
        fwrite($h, $header);

        fwrite($log_file,"Fetch objects ids ... ");
        // Write data        
        $ids = cw_call('cw\export\\'.$t.'\get_key_fields',array($type['saved_search']));
        fwrite($log_file,count($ids)."\n");
        fwrite($log_file,"Export objects ... 0");
        $ii = 0;
        foreach ($ids as $id) {
            $ii++;
            if ($ii%1000 == 0) {
                fwrite($log_file,', '.$ii);
            }
            $row = array();
            $data = cw_call('cw\export\\'.$t.'\get_data', array($id));
            foreach ($schema['fields'] as $alias=>$field) {
                if (is_numeric($alias) && !empty($field)) $alias = $field;
                
                $field_handler = $export_type['fields'][$field]['handler'];
                if (empty($field_handler)) $field_handler = 'field_handler_general';
                if (function_exists('cw\export\\'.$t.'\\'.$field_handler)) {
                    $field_handler = 'cw\export\\'.$t.'\\'.$field_handler;
                } elseif (function_exists('cw\export\\'.$field_handler)) {
                    $field_handler = 'cw\export\\'.$field_handler;
                }
                
                $f = $export_type['fields'][$field]['field'];
                if (empty($f)) $f = $field;
                
                $row[$alias] = cw_call($field_handler,array($data,$f,$export_type));
            }
            $out_line = cw_call('cw\export\output_data_'.$format, array($h, $row, $data, $schema));
            if (!is_null($out_line)) fwrite($h, $out_line);
        }
        // Footer
        $footer = cw_call('cw\export\output_footer_'.$format, array($export_type, $schema));
        fwrite($h, $footer);
        
        fwrite($log_file,', '.$ii."\n");
        $flink = $current_location.'/files/csv/'.pathinfo($fn, PATHINFO_BASENAME);
        fwrite($log_file,"<a href='$flink'>$flink</a>\n");
        // Close file
        fclose($h);
    }
    
    cw_unlock('export_'.$customer_id);

    fwrite($log_file,"Clean up ...\n");
   
    // Cleanup saved_search objects
    foreach ($filled_saved_searches as $objects_set) {
        // TODO: Here must be cw_call_delayed()
        cw_call('cw_objects_reset', array($objects_set));
    }

    fwrite($log_file,"======================================\nFINISH\n");
    fclose($log_file);

    cw_add_top_message('Exported successfully');

    if (defined('IS_AJAX')) {
        export_status();
    } elseif (defined('IS_CRON')) {
        return $request_prepared['types'];
    } else {
        cw_header_location('index.php?target=export');
    }
}

function export_do_delete() {
    global $request_prepared;
    
        cw_load('import_export_csv'); // import csv_path constant

    if (isset($request_prepared['filenames']) && is_array($request_prepared['filenames'])) {
        foreach ($request_prepared['filenames'] as $v) {
            if (file_exists(csv_path.'/'.$v)) {
                unlink(csv_path.'/'.$v);
            }
        }
        cw_add_top_message('Files deleted successfully');
    }
    cw_header_location('index.php?target=export');
}

function export_status() {
    global $request_prepared,$customer_id,$var_dirs;
    
    define('PREVENT_SESSION_SAVE', true);

    $log_fname = $var_dirs['tmp'].DIRECTORY_SEPARATOR.'export_log_'.$customer_id;
    cw_load('file');
    $log = cw_temp_read($log_fname);
    if (defined('IS_AJAX')) {
        cw_add_ajax_block(array(
            'id' => 'export_log',
            'content' => nl2br($log),
        ));
    }
    return $log;
}


/* Service functions */

/* Hook it to add new export type. Pay attention to current namespace. */
function get_export_types() {
    return array(
        'products',
        'orders',
        'orders_items',
    );
}

function write_export_log($msg,$new_line=true) {
    
}

function get_export_formats($format=null) {
    $f = array(
        'csv' => array(
            'name' => 'CSV',
            'ext'  => 'csv',
        ),
/*
        'xml_simple' => array(
            'name' => 'XML',
            'ext'  => 'xml',
            ),
*/
    );
    
    if ($format) return $f[$format];
    else return $f;
}

function get_saved_searches() {
    global $tables;
    return $saved_searches = array(
        'O' => array(
            'presets' => cw_query("select name, ss_id from $tables[saved_search] where type='O' order by name, ss_id"),
            'name' => 'Orders',
            'key_field' => 'doc_id'
            ),
        'C' => array(
            'presets' => cw_query("select name, ss_id from $tables[saved_search] where type='C' order by name, ss_id"),
            'name' => 'Customers',
            'key_field' => 'customer_id'
            ),
         'P' => array(
            'presets' => cw_query("select name, ss_id from $tables[saved_search] where type='P' order by name, ss_id"),
            'name' => 'Products',
            'key_field' => 'product_id'
            ),
        );
}




