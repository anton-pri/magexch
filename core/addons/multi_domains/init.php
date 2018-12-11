<?php
$tables['domains']         = 'cw_domains';
$tables['domains_config']  = 'cw_domains_config';

cw_include('addons/multi_domains/include/func.md.php');

cw_addons_set_controllers(
    array('replace', 'admin/domains.php', 'addons/multi_domains/admin/domains.php'),
    array('post', 'init/abstract.php', 'addons/multi_domains/init/domains.php'),
# kornev, access levels
    array('post', 'include/auth.php', 'addons/multi_domains/include/auth.php')
);
if ($target != 'image') {
    cw_set_controller('init/post_init.php', 'addons/multi_domains/init/get_host_data.php', EVENT_POST);
    cw_set_controller('init/post_init.php', 'addons/multi_domains/post_init.php', EVENT_POST);
}
cw_set_hook('cw_core_get_config',       'cw_md_core_get_config',            EVENT_POST);
cw_set_hook('cw_attributes_save',       'cw_md_attributes_save',            EVENT_POST); // additional function for the category attributes

cw_addons_set_hooks(
// search filters, can be unset if only one domain defined, see post_init.php
    array('pre', 'cw_product_search', 'cw_md_product_search'),
    array('pre', 'cw_category_search', 'cw_md_category_search'),
    array('pre', 'cw_manufacturer_search', 'cw_md_manufacturer_search'),
    array('pre', 'cw_speed_bar_search', 'cw_md_speed_bar_search'),
    array('pre', 'cw_shipping_search', 'cw_md_shipping_search'),
    array('pre', 'cw_payment_search', 'cw_md_payment_search'),
// individual object filters
    array('pre', 'cw_product_get', 'cw_md_product_search'),
    array('pre', 'cw_category_get', 'cw_md_category_search'),
    array('pre', 'cw_manufacturer_get', 'cw_md_manufacturer_search'),
// place order - save attributes
    array('pre', 'cw_doc_place_order', 'cw_md_doc_place_order'),
// settings
    array('post', 'cw_code_get_template_dir', 'cw_md_code_get_template_dir')
);

if (APP_AREA == 'cron') {
    cw_set_hook('cw_send_mail','cw_md_send_mail', EVENT_PRE);
}

if (APP_AREA == 'admin') {
    cw_event_listen('on_prepare_search_orders', 'cw_md_prepare_search_orders');
    
    cw_set_hook('cw_send_mail','cw_md_send_mail', EVENT_PRE); // This hook adds altskin as source of email templates
    cw_set_hook('cw_spam','cw_md_send_mail', EVENT_PRE);

    cw_addons_set_template(
	array('pre', 'main/docs/additional_search_field.tpl', 'addons/multi_domains/admin/additional_domain_selector.tpl'),
       array('post', 'common/navigation_counter.tpl', 'addons/multi_domains/common/current_domain_warning.tpl'),
       array('post', 'admin/attributes/default_types.tpl', 'addons/multi_domains/types/domain-selector.tpl')

    );
}
if (APP_AREA == 'customer') {
# kornev, languages per domain
    cw_addons_set_controllers(
        array('pre', 'init/lng.php', 'addons/multi_domains/init/lng.php'),
        array('pre', 'customer/referer.php', 'addons/multi_domains/customer/referer.php')
    );
    cw_addons_set_hooks(
        array('replace', 'cw_core_get_available_languages', 'cw_md_get_available_languages')
    );
    cw_addons_set_template(
        array('post', 'main/attributes/default_types.tpl', 'addons/multi_domains/types/domain-selector.tpl')
    );
}

cw_addons_set_template(
    array('post', 'common/top-filters.tpl', 'addons/multi_domains/common/top-filters.tpl')
);

cw_addons_add_css('addons/multi_domains/general.css', 'A');
