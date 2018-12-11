<?php
cw_addons_set_hooks(
    array('post', 'cw_shipping_get_rates', 'cw_apply_special_offer_shipping')
);
cw_event_listen('on_collect_shipping_rates_hash', 'cw_ps_on_collect_shipping_rates_hash', EVENT_POST);

