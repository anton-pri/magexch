<?php
cw_load('sections');

$search_data = &cw_session_register("search_data", array());
$category_page = &cw_session_register('category_page', array());

$search_data['sections']['objects_per_page'] = $category_page['objects_per_page'];
$search_data['sections']['sort_field'] = $category_page['sort_field'];
$search_data['sections']['sort_direction'] = $category_page['sort_direction'];
$search_data['sections']['attributes'] = $att;
$search_data['sections']['flat_search'] = 1;

list($products, $navigation, $product_filter) = cw_call('cw_sections_get', array('section' => 'arrivals', 'data' => $search_data['sections'],'', 'info_type' =>  8+32+128+256+1024));
$navigation['script'] = cw_call('cw_core_get_html_page_url', array(array('var' => $target, 'delimiter' => '&', 'att' => $att)));
$smarty->assign('navigation', $navigation);
$smarty->assign('products', $products);
$smarty->assign('product_filter', $product_filter);

$smarty->assign('sort_fields', cw_product_get_sort_fields());
$smarty->assign('search_prefilled', $search_data['sections']);

$location[] = array(cw_get_langvar_by_name('lbl_arrivals'), '');
$location = array_merge($location, cw_product_get_filter_location($product_filter, $navigation['script']));

$smarty->assign('current_section_dir', 'special_sections');
$smarty->assign('main', 'arrivals');
