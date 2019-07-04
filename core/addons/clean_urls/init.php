<?php

const clean_urls_attributes_item_type = 'PA';
const clean_urls_attributes_values_item_type = 'AV';

$tables['clean_urls_custom_facet_urls'] = 'cw_clean_urls_custom_facet_urls';
$tables['clean_urls_custom_facet_urls_options'] = 'cw_clean_urls_custom_facet_urls_options';

$tables['facet_categories_images'] = 'cw_facet_categories_images';
$tables['clean_urls_history'] = 'cw_clean_urls_history';

$tables['manufacturers_categories'] = 'cw_manufacturers_categories';

$cw_allowed_tunnels[] = 'cw_clean_url_alt_tags';

cw_include('addons/clean_urls/include/func.clean_urls.php');

cw_addons_set_controllers(
    array('pre', 'init/smarty.php', 'addons/clean_urls/init/abstract.php')
);

cw_event_listen('on_product_delete'	,'cw_clean_url_on_product_delete');

// CMS addon
cw_event_listen('on_cms_check_restrictions','cw_clean_url_on_cms_check_restrictions_URL');

// Event handler to update clean url for attribute values
cw_event_listen('on_after_attribute_options_modify_att_options','cw_clean_url_on_attribute_options');

cw_set_hook('cw_manufacturer_delete',       'cw_clean_url_manufacturer_delete',         EVENT_POST);
cw_set_hook('cw_category_delete',           'cw_clean_url_category_delete',             EVENT_POST);
cw_set_hook('cw_attributes_delete',         'cw_clean_url_attributes_delete',           EVENT_PRE);
cw_set_hook('cw_attributes_delete_values',  'cw_clean_url_attributes_delete_values',    EVENT_PRE);
cw_set_hook('cw_core_get_html_page_url',    'cw_clean_url_get_html_page_url',           EVENT_PRE);
cw_set_hook('cw_core_get_meta',             'cw_clean_url_get_meta',                    EVENT_PRE);
cw_set_hook('cw_attributes_save',           'cw_clean_url_attributes_save',             EVENT_PRE);

cw_addons_set_hooks(
    array('post',   'cw_attributes_create_attribute',   'cw_clean_url_attributes_create_attribute'),
    array('post',   'cw_attributes_save_attribute',     'cw_clean_url_attributes_save_attribute'),
    array('post',   'cw_attributes_get_attribute',      'cw_clean_url_attributes_get_attribute'),
    array('replace','cw_product_navigation_filter_url', 'cw_clean_url_product_navigation_filter_url')
);

cw_addons_set_template(array('replace', 'common/image_alt.tpl', 'addons/clean_urls/image_alt.tpl'));

if (APP_AREA == 'admin') {
	cw_addons_set_controllers(
		array('replace', 'admin/clean_url_show_history.php', 'addons/clean_urls/admin/show_history.php'),
		array('replace', 'admin/clean_url_delete_url.php', 'addons/clean_urls/admin/delete_history_url.php'),
		array('replace', 'admin/clean_urls_list.php', 'addons/clean_urls/admin/clean_urls_list.php'),
		array('replace', 'admin/custom_facet_urls.php', 'addons/clean_urls/admin/custom_facet_urls.php')
	);

	cw_addons_set_template(
		array('post', 'main/attributes/default_types.tpl', 'addons/clean_urls/history_link.tpl'),
		array('replace', 'admin/main/clean_url_history_list.tpl', 'addons/clean_urls/history_list.tpl'),
		array('replace', 'admin/seo/clean_urls_list.tpl', 'addons/clean_urls/clean_urls_list.tpl'),
		array('replace', 'admin/attributes/attribute_field.tpl', 'addons/clean_urls/attribute_field.tpl'),
		array('replace', 'admin/attributes/attribute_value_field.tpl', 'addons/clean_urls/attribute_value_field.tpl'),
		array('replace', 'admin/attributes/attribute_value_th_title.tpl', 'addons/clean_urls/attribute_value_th_title.tpl'),
        array('replace', 'admin/attributes/attribute_value_td_field.tpl', 'addons/clean_urls/attribute_value_td_field.tpl'),
		array('replace', 'admin/attributes/attribute_preset_item.tpl', 'addons/clean_urls/attribute_preset_item.tpl'),
		array('replace', 'admin/seo/custom_facet_urls.tpl', 'addons/clean_urls/custom_facet_urls.tpl'),
		array('replace', 'admin/seo/custom_facet_url.tpl', 'addons/clean_urls/custom_facet_url.tpl')
	);

// Integration with manufacturers
	cw_set_controller('addons/manufacturers/admin/manufacturers.php','addons/clean_urls/manufacturers.php',EVENT_PRE);
//	cw_addons_set_template(array('post','addons/manufacturers/manufacturer.tpl','addons/clean_urls/manufacturer.tpl'));

}

	cw_set_controller('addons/manufacturers/customer/manufacturers.php','addons/clean_urls/manufacturers.php',EVENT_POST);
