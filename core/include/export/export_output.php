<?php
namespace cw\export;


/**
 * CSV
 */
function output_header_csv($export_type, $schema) {
    $out = '';
    $delimiter = $schema['options']['delimiter'];
    if (!$schema['options']['no_type_header'])
        $out = "[{$export_type['codename']}]\n";

    if (!$schema['options']['no_csv_header']) {
        foreach ($schema['fields'] as $alias=>$field) {
            if (is_numeric($alias) && !empty($field)) $alias = $field;
            $out .= "$alias$delimiter";
        }
        $out .= "\n";
    }
    
    return $out;
}
function output_data_csv($file_handler, $row, $data, $schema) {
    $delimiter = $schema['options']['delimiter'];
   
    fputcsv ($file_handler, $row, $delimiter,'"');
    return null;
}

function output_footer_csv($export_type, $schema) {
        
}



