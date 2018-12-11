<?php

function cw_csvxc_load_raw_data_from_xc_export($file2load, $delimiter, $field_types, $tmp_tables_path, $extend_ids) {

global $drop_tmp_list;

$load_data_tables_qry = array();
$coldata2extend = array();
$field_names = array();

$fp = false;
$is_first_line = false;
$table_name = '';
$table_key_name = '';

if (($handle = fopen($file2load, "r")) !== FALSE) {

    while (($data = fgetcsv($handle, 1024*1024, $delimiter, '')) !== FALSE) {
        $num = count($data);
        //echo "<p> $num fields in line $row: <br /></p>\n";

        $row++;
        if ($num == 1) {
            $sectname = $data[0];
            echo $sectname . "<br />\n";
                $import_section_name = trim($sectname, '[]');
                $is_first_line = true;
        } elseif ($num > 1) {
            if ($is_first_line) {
                $table_name = "tmp_load_$import_section_name";
                $table_key_name = "id_".strtolower($import_section_name);
                $load_data_tables_qry[] = "DROP TABLE IF EXISTS `$table_name`;";
                //create table qry 
                $is_first_line = false;
                $_data = array();
                $add_field_qry = array();
                $field_names = array();
                foreach ($data as $col_id => $dname) {
                    $_dname = trim($dname, '!');
                    if (!empty($field_types[$_dname])) {
                        $add_field_qry[] = "`$_dname` ".$field_types[$_dname];
                    } else {
                        $add_field_qry[] = "`$_dname` ".$field_types['default'];
                    }
                    $field_names[] = $_dname;
                    if (!empty($extend_ids[$table_name])) {
                        foreach ($extend_ids[$table_name] as $eik=>$eiv) {
                            if ($_dname == $eiv['colname'])
                                $extend_ids[$table_name][$eik]['colid'] = $col_id;
                        }
                    }
                }
                $coldata2extend = array();
                $load_data_tables_qry[] = "CREATE TABLE `$table_name` ($table_key_name int(11) NOT NULL AUTO_INCREMENT, ".implode(", ", $add_field_qry).", PRIMARY KEY `$table_key_name` (`$table_key_name`))ENGINE=MyISAM;";
                $load_data_tables_qry[] = "LOAD DATA LOCAL INFILE '$tmp_tables_path$table_name.csv' INTO TABLE $table_name FIELDS TERMINATED BY '$delimiter' ENCLOSED BY '\"' (`".implode('`,`', $field_names)."`)";
                //$load_data_tables_qry[] = "LOAD DATA LOCAL INFILE '$tmp_tables_path$table_name.csv' INTO TABLE $table_name FIELDS TERMINATED BY '$delimiter' (`".implode('`,`', $field_names)."`)";
                $drop_tmp_list[$table_name] = 1;
                $fp = fopen($tmp_tables_path.$table_name.".csv", 'wb');
            } else {
                if ($fp != false) {
                    if (!empty($extend_ids[$table_name])) {
                        foreach ($extend_ids[$table_name] as $eik=>$eiv) {
                            if (isset($eiv['colid'])) {
                                $_colid2extend = $eiv['colid'];
                                if (!empty($data[$_colid2extend])) {
                                    $coldata2extend[$_colid2extend] = $data[$_colid2extend];
                                } elseif (!empty($coldata2extend[$_colid2extend])) {
                                    $data[$_colid2extend] = $coldata2extend[$_colid2extend];
                                }
                            }
                        }
                    }
                    fwrite($fp, "\"".implode("\"$delimiter\"", $data)."\"\n");
                }
            }
        }
    }
    fclose($handle);
}

if ($fp != false) {
    fclose($fp);
}
return $load_data_tables_qry;
}

?>
