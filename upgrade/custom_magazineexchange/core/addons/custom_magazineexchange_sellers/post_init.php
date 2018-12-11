<?php
namespace cw\custom_magazineexchange_sellers;

if (APP_AREA == 'seller') {
    // Disable product search restriction by sellers
    cw_addons_unset_hooks(
            array('pre', 'cw_product_search', 'cw_seller_product_search')
    );

    
}
if (APP_AREA == 'customer') {

    // Correct shipping cost
    cw_addons_set_hooks(
        array('post', 'cw_shipping_get_rates', 'cw\\'.addon_name.'\\cw_shipping_get_rates')
    );

}
