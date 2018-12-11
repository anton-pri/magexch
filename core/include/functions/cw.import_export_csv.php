<?php

define ("csv_path", "./files/csv");

function cw_create_csv_filename ($tab, $ext='csv') {
	$path=csv_path;
	if (!is_dir($path)) mkdir($path);
	$filename=$path."/".date("Y-M-d-D-H.i.s")."-$tab.$ext";
	return $filename;
}

function cw_array2insert_esc ($tab,$arr) {
foreach ($arr as $k => $v) {
if (preg_match("'\''",$v)) $arr[$k]=addslashes($v);
}
return cw_array2insert ($tab,$arr);
}

/**
 * 
 * $tab - table alias or "select" query
 */
function cw_table2csv ($tab,$delimiter=';',$query='') {
	global $tables;

	$fn=cw_create_csv_filename ($tab);
	$h=fopen($fn,'w');
	fwrite($h,"[$tab]\n");
    
    if (!empty($query) && strpos(trim(strtolower($query)),'select')===0) {
        $res=db_query($query);
    }
    elseif (!empty($tables[$tab])) {
        $res=db_query("select * from {$tables[$tab]}");
    } 
    else {
        return false;
    }
    
	$first_time=true; $title='';
    while ($val = db_fetch_array($res)) {
		if ($first_time) {
			foreach ($val as $k => $v) $title.="!$k\t\t";
			$title=str_replace("\t\t",$delimiter,trim($title));
			fwrite($h,"$title\n"); $first_time=false;
		}
		fputcsv ($h,$val,$delimiter);
	}
    db_free_result($res);
	fclose($h); //cw_csv2table($fn);
}


function cw_csv2table ($fn,$delimiter='') {
	global $tables;
	$h=fopen($fn,'r'); $tab=''; $fields='';
	$brf_err=cw_get_langvar_by_name('lbl_imp_err_br_file');
	$wrtab_err=cw_get_langvar_by_name('lbl_imp_err_tab_not_exst');

	while ($tab=='') $tab=trim(fgets($h));
	if (!preg_match("'^\[[^\s]*\]$'i",$tab)) return $brf_err;
	$tab=preg_replace("'^\[(.*)\]$'i","$1",$tab);
	if (!isset($tables[$tab])) return $wrtab_err;

	while ($fields=='') $fields=trim(fgets($h));
	if (!preg_match("'^\![^\s\!]+\!.+'i",$fields)) return $brf_err;
	if ($delimiter=='') $delimiter=preg_replace("'^\![^\s\!]+(.)\!.+'","$1",$fields);

	$fields=explode($delimiter,$fields);
	foreach ($fields as $k => $v) {
		if (!preg_match("'^\![^\s]+$'i",trim($v))) return $brf_err;
		$v=preg_replace("'^\!(.*)$'","$1",trim($v));
		$fields[$k]=$v;
	}

	while (($data = fgetcsv($h, 0, $delimiter)) !== false) {
		if (sizeof($data)!=sizeof($fields)) return $brf_err;
		foreach ($fields as $k => $v) $data2[$v]=$data[$k];
		$arr[]=$data2;
	}

	db_query ("TRUNCATE TABLE {$tables[$tab]}");
	foreach ($arr as $data) cw_array2insert_esc ($tab, $data);
	return true;
}


function cw_list_csv_dir () {
	$path=csv_path;
	if (!is_dir($path)) mkdir($path);
	if ($h=opendir($path)) {
		while (false !== ($fn=readdir($h))) if (is_file("$path/$fn") && preg_match("'.*\.csv$'i",$fn)) $files[]=$fn;
		closedir($h);
	}
	if (isset($files) && is_array($files)) rsort($files);
	return $files;
}


function cw_list_csv_upl_dir () {
	$path=csv_path; if (!is_dir($path)) mkdir($path);
	$path.="/uploaded"; if (!is_dir($path)) mkdir($path);
	if ($h=opendir($path)) {
		while (false !== ($fn=readdir($h))) if (is_file("$path/$fn") && preg_match("'.*\.csv$'i",$fn)) $files[]=$fn;
		closedir($h);
	}
	if (isset($files) && is_array($files)) rsort($files);
	return $files;
}



?>
