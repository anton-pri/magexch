<?php
define('AB_TEST_CONTENTSECTION', false); // for test purposes. create content section with 'test' servicecode. set false on production site
define('CS_TEST_TEMPLATE', 'customer/menu/menu_sections.tpl'); // where to place the test content section 

$tables['cms']               = 'cw_cms';
$tables['cms_alt_languages'] = 'cw_cms_alt_languages';
$tables['cms_categories']    = 'cw_cms_categories';
$tables['cms_images']        = 'cw_cms_images';
$tables['cms_restrictions']  = 'cw_cms_restrictions';
$tables['cms_user_counters'] = 'cw_cms_user_counters';

$cw_allowed_tunnels[] = 'cw_cms_get_staticpages';

cw_include('addons/cms/func.hooks.php', INCLUDE_NO_GLOBALS);
cw_include('addons/cms/func.php', INCLUDE_NO_GLOBALS);

    cw_event_listen('on_product_delete','cms\on_product_delete');
    cw_event_listen('on_category_delete','cms\on_category_delete');
    cw_event_listen('on_manufacturer_delete','cms\on_manufacturer_delete');

    cw_event_listen('on_cms_check_restrictions','cms\on_cms_check_restrictions_C');
    cw_event_listen('on_cms_check_restrictions','cms\on_cms_check_restrictions_P');

    cw_set_hook('cw_delete_product','cms\cw_delete_product',EVENT_POST);

if (APP_AREA == 'admin') {

    cw_addons_set_controllers(
        array('replace', 'admin/cms.php', 'addons/cms/cms.php'),
        array('pre', 'include/products/modify.php', 'addons/cms/product_modify.php')
    );

    cw_addons_set_hooks(
        array('post', 'cw_tabs_js_abstract', 'cms\tabs_js_abstract')
    );

    cw_addons_set_template(
        array('replace','admin/main/section_title.tpl', 'addons/cms/admin/section_title.tpl'),
        array('post', 'main/attributes/default_types.tpl', 'addons/cms/types/staticpage-selector.tpl'),
        array('post', 'admin/attributes/default_types.tpl', 'addons/cms/types/staticpage-selector.tpl')
    );

    if ($target == 'cms' && $mode == 'search') {
        cw_addons_set_template(array('replace', 'addons/clean_urls/history_link.tpl', 'addons/cms/history_link.tpl'));
    }

}

if (APP_AREA == 'customer') {
    cw_addons_add_css('addons/cms/cms.css');
    cw_addons_add_js('addons/cms/cms.js');
    cw_addons_set_controllers(
        array('replace', 'customer/ab_count_click.php', 'addons/cms/ab_count_click.php'),
        array('replace', 'customer/pages.php', 'addons/cms/staticpages.php'),
        array('post', 'customer/help.php', 'addons/cms/help_pages.php')
    );
 //   cw_set_controller('customer/auth.php', 'addons/cms/customer/cms.php', EVENT_POST);

    cw_addons_set_hooks(array('pre', 'cw_core_get_meta', 'cw_cms_get_meta'));

    // test content section 
    if (AB_TEST_CONTENTSECTION)
        cw_addons_set_template(array('post',CS_TEST_TEMPLATE, 'addons/cms/test.tpl'));

    cw_addons_set_template(
	array('post','customer/service_js.tpl', 'addons/cms/highlight.js.tpl'),
        array('replace', 'customer/main/pages.tpl', 'addons/cms/customer/staticpages.tpl'),
        array('pre', 'common/menu.tpl', 'addons/cms/customer/menu_pre.tpl'),
        array('post', 'common/menu.tpl', 'addons/cms/customer/menu_post.tpl'),
        array('post', 'customer/help/help.tpl@help_main_section', 'addons/cms/customer/help_sections.tpl')
    );
}
