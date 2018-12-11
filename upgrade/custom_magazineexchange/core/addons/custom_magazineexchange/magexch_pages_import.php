<?php

$all_pages = cw_query("select * from antonp_magexch.xcart_pages");

$xc_path = "../magexch/";
$xc_pages_path = "skin1/pages/";

$xc_pages_attr_id = 
cw_query_first_cell("select attribute_id from cw_attributes where item_type='AB' and addon='custom_magazineexchange' and field='magexch_xc_pageid'");
$xc_show_in_menu_attr_id = 
cw_query_first_cell("select attribute_id from cw_attributes where item_type='AB' and addon='custom_magazineexchange' and field='magexch_xc_show_in_menu'");
$xc_background_url_attr_id = 
cw_query_first_cell("select attribute_id from cw_attributes where item_type='AB' and addon='custom_magazineexchange' and field='magexch_xc_background_url'");
$xc_page_template_attr_id = 
cw_query_first_cell("select attribute_id from cw_attributes where item_type='AB' and addon='custom_magazineexchange' and field='magexch_xc_page_template'");

$domain_attribute_id = cw_query_first_cell("SELECT attribute_id FROM cw_attributes WHERE item_type='AB' AND addon='multi_domains' and field='domains'");


foreach ($all_pages as $xc_page) {
    $xc_page['title'] = html_entity_decode($xc_page['title']);
    $cms_page = cw_query_first("select * from cw_cms where name = '".addslashes($xc_page['title'])."'");
    $cms_id = 0;
    if (!empty($cms_page)) {
        $cms_id = $cms_page['contentsection_id'];
//        print_r($xc_page);
//        print("<br>");
//        print("<br><br>");


//        print("name=".$cms_page['name']);print("<br>");
//        print("<br>");
    } else {
        $page_content = "";
        $file_name = $xc_path.$xc_pages_path.$xc_page['language']."/".$xc_page['filename'];
        if (file_exists($file_name)) {
            $page_content = file_get_contents($file_name);
        }
        $cms_id = cw_array2insert("cms", array(
            'content' => addslashes($page_content),
            'type' => 'staticpage',
            'skin' => 'vertical',
            'name' => addslashes($xc_page['title']),
            'target' => '_self',
            'active' => $xc_page['active'],
            'parse_smarty_tags' => '0'
        ));

    } 

    if ($cms_id) {
        cw_csvxc_logged_query("delete from cw_attributes_values where item_type='AB' and item_id='$cms_id' and attribute_id='$xc_pages_attr_id'");
        cw_csvxc_logged_query("insert into cw_attributes_values (item_type, item_id, code, attribute_id, value) values ('AB', '$cms_id', 'EN', '$xc_pages_attr_id', '$xc_page[pageid]')");    

        cw_csvxc_logged_query("delete from cw_attributes_values where item_type='AB' and item_id='$cms_id' and attribute_id='$xc_show_in_menu_attr_id'");
        cw_csvxc_logged_query("insert into cw_attributes_values (item_type, item_id, code, attribute_id, value) values ('AB', '$cms_id', 'EN', '$xc_show_in_menu_attr_id', '$xc_page[show_in_menu]')");

        cw_csvxc_logged_query("delete from cw_attributes_values where item_type='AB' and item_id='$cms_id' and attribute_id='$xc_background_url_attr_id'");
        cw_csvxc_logged_query("insert into cw_attributes_values (item_type, item_id, code, attribute_id, value) values ('AB', '$cms_id', 'EN', '$xc_background_url_attr_id', '$xc_page[background_url]')");

        cw_csvxc_logged_query("delete from cw_attributes_values where item_type='AB' and item_id='$cms_id' and attribute_id='$xc_page_template_attr_id'");
        cw_csvxc_logged_query("insert into cw_attributes_values (item_type, item_id, code, attribute_id, value) values ('AB', '$cms_id', 'EN', '$xc_page_template_attr_id', '$xc_page[page_template]')");

        cw_csvxc_logged_query("delete from cw_attributes_values where item_type='AB' and item_id='$cms_id' and attribute_id='$domain_attribute_id'");
        cw_csvxc_logged_query("insert into cw_attributes_values (item_type, item_id, code, attribute_id, value) values ('AB', '$cms_id', 'EN', '$domain_attribute_id', '0')");

    } 

/*
    $file_name = $xc_path.$xc_pages_path.$xc_page['language']."/".$xc_page['filename'];
    if (file_exists($file_name)) {
        $page_content = file_get_contents($file_name);
        print($file_name);
        print("<br>");
//        print($page_content);
    }
*/
//    print("<br><br>");



}

die;

