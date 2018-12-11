<?php
function cw_web_dom2array($node) {
    $res = array();
    if($node->nodeType == XML_TEXT_NODE){

    }
    else {
        if($node->hasAttributes()){
            $attributes = $node->attributes;
            if(!is_null($attributes)){
                $res['@attributes'] = array();
                foreach ($attributes as $index=>$attr) {
                    $res['@attributes'][$attr->name] = $attr->value;
                }
            }
        }
        if($node->hasChildNodes()){
            $children = $node->childNodes;
            for($i=0;$i<$children->length;$i++){
                $child = $children->item($i);
                $res[$child->nodeName] = cw_web_dom2array($child);
            }
        }
    }
    return $res;
}

function cw_web_get_layout_inner($sql) {
    global $tables, $smarty, $top_message;

    $data = cw_query_first("select * from $tables[layouts] where $sql");
    $data['data'] = unserialize($data['data']);
    $smarty->assign('layout', $data);

    $templates = cw_query("select * from $tables[layouts_templates] where layout_id='$data[layout_id]' order by orderby");
    if (is_array($templates))
    foreach($templates as $template) {
        $template['content'] = cw_display($template['template'], $smarty, false);
        preg_match_all('/id[ ]*=[ ]*"(.*)"/Uims', $template['content'], $out);
        $template['sub_ids'] = $out[1];
        $data['parts'][] = $template;
    }
    unset($smarty->_included_files);
    $data['elements'] = cw_query("select le.* from $tables[layouts_elements] as le where le.layout_id='$data[layout_id]' and display='none'");
    return $data;
}

function cw_web_get_layout_by_id($layout_id, $for_email = false) {
    return cw_web_get_layout_inner("layout_id='$layout_id'");
}

function cw_web_get_layout($layout) {
    return cw_web_get_layout_inner("layout='$layout' and is_default=1");
}

function cw_web_get_layouts($layout, $like = false) {
    global $tables;
    $layouts = cw_query_column("select layout_id from $tables[layouts] where layout".($like?' like ':' = ')."'$layout'");
    $return = array();
    if ($layouts)
    foreach($layouts as $layout_id)
        $return[] = cw_web_get_layout_inner("layout_id='$layout_id'");
    return $return;
}

function cw_web_set_layout_width($layout, $id, $width, $height) {
    global $tables;
    if (empty($id)) return;
   
    if (!cw_query_first_cell($sql="select count(*) from $tables[layouts_elements] where layout_id='$layout' and id = '$id'"))
        cw_array2insert('layouts_elements', array('layout_id' => $layout, 'id' => $id));
    cw_array2update('layouts_elements', array('width' => $width, 'height' => $height), "layout_id='$layout' and id = '$id'");
}

function cw_web_set_layout_xy($layout, $id, $x, $y, $display, $font, $font_size, $decoration, $font_weight, $font_style, $color) {
    if (empty($id)) return;

    $to_insert = array(
        'layout_id' => $layout, 
        'id' => $id, 
        'x' => $x, 
        'y' => $y,
        'display' => $display, 
        'font' => $font, 
        'font_size' => $font_size, 
        'decoration' => $decoration, 
        'font_weight' => $font_weight, 
        'font_style' => $font_style,
        'color' => $color,
    );
    cw_array2insert('layouts_elements', $to_insert, true);
}

function cw_web_get_layout_elements($params) {
    global $tables;
    if (!$params['layout_id'] && !$params['layout'] ) return;
    if ($params['layout_id'])
        return cw_query_hash("select le.* from $tables[layouts_elements] as le where le.layout_id='$params[layout_id]' and id != ''", 'id', false);
    return cw_query_hash("select le.* from $tables[layouts_elements] as le, $tables[layouts] as l where l.layout_id=le.layout_id and l.layout='$params[layout]' and l.is_default=1 and id != ''", 'id', false);
}

function cw_web_delete_layout($layout_id) {
    global $tables;

    $layout_id = cw_query_first_cell("select layout_id from $tables[layouts] where layout_id='$layout_id' and is_default != 1");
    if (!$layout_id) return;

    db_query("delete from $tables[layouts] where layout_id='$layout_id'");
    db_query("delete from $tables[layouts_elements] where layout_id='$layout_id'");
    db_query("delete from $tables[layouts_templates] where layout_id='$layout_id'");
}

function cw_web_copy_layout($src_layout_id, $dest_layout_id) {
    global $tables;

    if ($src_layout_id === $dest_layout_id) return;

    $data = cw_query_first_cell("select data from $tables[layouts] where layout_id='$src_layout_id'");
    db_query("update $tables[layouts] set data='".addslashes($data)."' where layout_id='$dest_layout_id'");
    
    db_query("delete from $tables[layouts_elements] where layout_id='$dest_layout_id'");
    $elements = cw_query("select * from $tables[layouts_elements] where layout_id='$src_layout_id'");
    if ($elements)
    foreach($elements as $element) {
        $element['layout_id'] = $dest_layout_id;
        cw_array2insert('layouts_elements', $element, 1);
    }

    $templates = cw_query("select * from $tables[layouts_templates] where layout_id='$src_layout_id'");
    if ($templates)
    foreach($templates as $template) {
        $template['layout_id'] = $dest_layout_id;
        cw_array2insert('layouts_templates', $template, 1);
    }
}

function cw_web_get_product_layout_elements() {
    return $product_layout_elements = array(
        'product_id' => 'lbl_product_id',
        'sku' => 'lbl_sku',
        'productcode' => 'lbl_supplier_sku',
        'product' => 'lbl_product',
        'tax' => 'lbl_tax',
        'amount' => 'lbl_quantity',
        'display_net_price' => 'lbl_price',
        'discount' => 'lbl_discount',
        'display_subtotal' => 'lbl_total',
    );
}
?>
