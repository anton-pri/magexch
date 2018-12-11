<?php
/**
 * Performance improvement for customer area.
 * Unset hooks if there is only one domain specified. Filter by domain does not make sense.
 */
if (count(cw_md_get_domains())==1) {
    cw_addons_unset_hooks(
        array('pre', 'cw_product_search', 'cw_md_product_search'),
        array('pre', 'cw_category_search', 'cw_md_category_search'),
        array('pre', 'cw_manufacturer_search', 'cw_md_manufacturer_search'),
        array('pre', 'cw_speed_bar_search', 'cw_md_speed_bar_search'),
        array('pre', 'cw_shipping_search', 'cw_md_shipping_search'),
        array('pre', 'cw_payment_search', 'cw_md_payment_search'),
        array('pre', 'cw_product_get', 'cw_md_product_search'),
        array('pre', 'cw_category_get', 'cw_md_category_search'),
        array('pre', 'cw_manufacturer_get', 'cw_md_manufacturer_search')
    );
}
