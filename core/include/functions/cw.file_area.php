<?php
function cw_file_get_info($type, $file) {
    global $current_location, $tables;

    $file['file_url'] = $current_location.'/index.php?target=document&type='.$type.'&file_id='.$file['file_id'];
    $file['file_path'] = cw_realpath($file['file_path']);

    $res = cw_query_first("select c.customer_id, ca.firstname, ca.lastname from $tables[customers] as c left join $tables[customers_addresses] as ca on ca.customer_id=c.customer_id and ca.main=1 where c.customer_id='$file[by_customer_id]'");
    $file['uploaded_by'] = $res['firstname'].' '.$res['lastname'].'('.$res['customer_id'].')';

    return $file;
}

function cw_file_get_doc($type, $file_id) {
    global $tables;

    if (!$tables[$type]) return false;

    $file = cw_query_first("select * from ".$tables[$type]." where file_id='$file_id'");
    return cw_file_get_info($type, $file);
}

function cw_file_area_get_by_id($type, $id, $where = '') {
    global $tables;

    $files = cw_query("select * from ".$tables[$type]." where id='$id' $where order by orderby, file_id");
    if (is_array($files))
    foreach($files as $k=>$val)
        $files[$k] = cw_file_get_info($type, $val);
    else $files = array();

    return $files;
}

function cw_file_area_get_list($type, $customer_id, $where = '') {
    global $tables;

    $files = cw_query("select * from ".$tables[$type]." where customer_id='$customer_id' $where order by orderby, file_id");
    if ($files)
        foreach($files as $k=>$val)
            $files[$k] = cw_file_get_info($type, $val);

    return $files;
}

function cw_file_area_save($type, $for_customer_id, $data) {
    global $tables, $customer_id, $var_dirs, $app_dir;

    $insert = array(
    'customer_id' => $for_customer_id,
    'by_customer_id' => $customer_id,
    'filename' => $data['filename'],
    'date' => cw_core_get_time(),
    'md5' => md5(file_get_contents($data['file_path'])),
    );
    if ($data['descr'])
        $insert['descr'] = $data['descr'];
    if ($data['id'])
        $insert['id'] = $data['id'];
    $file_id = cw_array2insert($type, $insert);
    if ($file_id) {
        $file_info = explode('.', $data['filename'], 2);
        $stored_file_name = $file_info[0].'_'.$file_id.'.'.$file_info[1];
        $files_dir = $var_dirs['documents'].'/'.$type;
        if (!is_dir($files_dir)) @mkdir($files_dir);
        $new_file_path = $files_dir.'/'.$stored_file_name;
        @copy($data['file_path'], $new_file_path);
        @unlink($data['file_path']);
        $new_file_path = cw_relative_path($new_file_path, $app_dir);
        db_query("update ".$tables[$type]." set file_path='".addslashes($new_file_path)."' where file_id='$file_id'");
    }
    return $file_id;
}

function cw_file_area_delete($type, $file_id) {
    global $tables;

    $file = cw_query_first_cell("select file_path from ".$tables[$type]." where file_id='$file_id'");
    if (is_file($file))
        @unlink($file);
    db_query("delete from ".$tables[$type]." where file_id='$file_id'");
}

function cw_file_area_delete_list($type, $id, $field = 'customer_id') {
    global $tables;

    $list = cw_query_column("select file_id from ".$tables[$type]." where $field='$id'");
    if (is_array($list))
    foreach($list as $v)
        cw_file_area_delete($type, $v);
}
?>
