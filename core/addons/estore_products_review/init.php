<?php
$tables['products_reviews_reminder'] = 'cw_products_reviews_reminder';
$tables['products_reviews_ratings']  = 'cw_products_reviews_ratings';
$tables['products_reviews_rating_types'] = 'cw_products_reviews_rating_types';
$tables['products_reviews_login_keys'] = 'cw_products_reviews_login_keys';

$cw_allowed_tunnels[] = 'cw_review_get_quick_global_info';
$cw_allowed_tunnels[] = 'cw_review_name_initials';
$cw_allowed_tunnels[] = 'cw_review_get_product_rates';

cw_include('addons/estore_products_review/include/func.review.php');
# kornev, TOFIX - move all of the css from general
cw_addons_add_css('addons/estore_products_review/general.css');

cw_addons_add_js('addons/estore_products_review/js/jquery.raty.min.js');

cw_addons_set_controllers(
    array('post', 'customer/product.php', 'addons/estore_products_review/customer/product.php'),
    array('replace', 'customer/top_rated.php', 'addons/estore_products_review/customer/top_rated.php'),
    array('replace', 'customer/estore_testimonials.php', 'addons/estore_products_review/customer/testimonials.php'),
    array('replace', 'admin/estore_stop_list.php', 'addons/estore_products_review/admin/estore_stop_list.php'),
    array('replace', APP_AREA . '/estore_reviews_management.php', 'addons/estore_products_review/' . APP_AREA . '/reviews_management.php'),
    array('replace', APP_AREA . '/estore_review_management.php', 'addons/estore_products_review/' . APP_AREA . '/review_management.php'),
    array('replace', 'admin/estore_execute_doc_action.php', 'addons/estore_products_review/admin/doc_action.php'),
    array('replace', 'customer/global_reviews.php', 'addons/estore_products_review/customer/global_reviews.php')
);
cw_set_controller('customer/review_vote.php', 'addons/estore_products_review/customer/review_vote.php', EVENT_REPLACE);


if (APP_AREA == 'admin') {
    cw_addons_set_controllers(
        array('post', 'include/orders/order.php', 'addons/estore_products_review/admin/order.php'),
        array('replace', 'admin/order_review_reminder.php', 'addons/estore_products_review/order_review_reminder.php')
    );
}

cw_set_hook('cw_delete_product','cw_review_delete_product',EVENT_POST);

# kornev, select rating to show it on the category page
cw_addons_set_hooks(
    array('post', 'cw_product_search', 'cw_review_product_search'),
    array('post', 'cw_product_get', 'cw_review_product_get'),
    array('post', 'cw_product_filter_get_slider_value', 'cw_review_product_filter_get_slider_value'),
    array('post', 'cw_attributes_delete', 'cw_review_delete_product_votes'),
    array('post', 'cw_attributes_get_types', 'cw_review_attributes_get_types')
);

cw_event_listen('on_cron_daily', 'cw_review_prepare_and_send_reminder');

cw_addons_set_template(
    array('replace', 'main/attributes/show.tpl', 'addons/estore_products_review/main/attributes/show.tpl', 'cw_review_is_rating_attribute'),
    array('post', 'help/menu-list.tpl', 'addons/estore_products_review/menu-list.tpl'),
    array('replace', 'customer/help/estore_testimonials.tpl', 'addons/estore_products_review/testimonials.tpl'),
    array('replace', 'admin/main/estore_stop_list.tpl', 'addons/estore_products_review/admin_stop_list.tpl'),
    array('replace', 'admin/main/estore_reviews_management.tpl', 'addons/estore_products_review/admin_reviews_management.tpl'),
    array('replace', APP_AREA . '/main/estore_review_management.tpl', 'addons/estore_products_review/' . APP_AREA . '_review_management.tpl'),
    array('post', 'main/docs/additional_actions.tpl', 'addons/estore_products_review/additional_doc_action.tpl'),
    array('post', 'main/docs/additional_search_field.tpl', 'addons/estore_products_review/additional_doc_search_field.tpl'),
    array('replace', 'customer/main/global_reviews.tpl', 'addons/estore_products_review/global_reviews.tpl')
);

cw_event_listen('on_prepare_search_orders', 'cw_review_prepare_search_orders');

    cw_set_hook('cw_web_get_product_layout_elements', 'cw_review_get_product_layout_elements');
    cw_set_hook('cw_doc_get', 'cw_review_doc_get');
