<?php
if (defined('IS_AJAX')) {
cw_load('category');

$i = 0;
$tree_attributes = array();
$key  = (isset($_GET['key']) ? $_GET['key'] : null);
$node = (isset($_GET['node']) ? $_GET['node'] + 1 : 0);
$attributes = cw_func_call('cw_attributes_get', array('item_type' => 'C','attribute_fields'=>array('ebay_category')));

if($key == 'root') {
    foreach ($attributes["ebay_category"]["default_value"] as $attribute) {

        $elements = explode('>', $attribute['value']);
        foreach ($elements as $k => $v) {
            $elements[$k] = trim($v);
        }

        if (count($elements) == $node) {
            array_push($tree_attributes, array('key' => $attribute['attribute_value_id'], 'title' => $elements[$node - 1], 'full_path' => $attribute['value']));
            $i++;

        }else{
            if( ($elements[$node] && is_array($tree_attributes[$i-1]) && !in_array($elements[$node], $tree_attributes[$i-1], true))
                ||(!isset($tree_attributes[$i-1]['isFolder']))) {
                array_push($tree_attributes, array('title' => $elements[$node],'path' => $elements[$node], 'expand' => 'true', 'isFolder' => 'true', 'isLazy' => 'true','node' => $node));
                $i++;
            }
        }
    }

}elseif(isset($_GET['title'])) {
    $category = trim($_GET['title']);
    $p_path   = (isset($_GET['path']) ? $_GET['path'] : null);

    foreach ($attributes["ebay_category"]["default_value"] as $attribute) {
        $elements = explode('>', $attribute['value']);
        foreach ($elements as $k => $v) {
            $elements[$k] = trim($v);
        }

        $path='';
        for($n=0; $n<=$node; $n++){
            $path .= ($n == $node ? $elements[$n] : $elements[$n].' > ');
        }

        if(($category == addslashes($elements[$node-1])) && ((preg_match('/' . stripcslashes($p_path) . '/i', $attribute['value'])))) {

            if(count($elements) == $node) {
                array_push($tree_attributes, array('key' => $attribute['attribute_value_id'], 'title' => $elements[$node - 1], 'full_path' => $attribute['value']));
                $i++;

            }else{
                if((($elements[$node]) && (!in_array($elements[$node], $tree_attributes[$i-1], true)))) {
                    array_push($tree_attributes, array('title' => $elements[$node], 'path' => $path, 'expand' => 'true', 'isFolder' => 'true', 'isLazy' => 'true', 'node' => $node));
                    $i++;
                }
            }
        }
    }
}

echo json_encode($tree_attributes);
exit();
}else{


}
