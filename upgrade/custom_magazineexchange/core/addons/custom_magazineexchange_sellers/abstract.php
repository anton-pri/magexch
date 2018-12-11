<?php
if (is_string($config['custom_magazineexchange_sellers']['mag_seller_fees']))    
    $config['custom_magazineexchange_sellers']['mag_seller_fees'] = unserialize($config['custom_magazineexchange_sellers']['mag_seller_fees']);
