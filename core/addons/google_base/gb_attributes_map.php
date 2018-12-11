<?php
// This map allows to use alternative attributes instead of special g:field
global $google_base_attributes_map;

$google_base_attributes_map = array(

    // Main product fields map
    'g:id' => '',
    'g:gtin' => '',
    'g:mpn'  => '',

    // Attributes map
    'g:size' => 'g:size',
    'g:color' => '',
    'g:age_group' => '',
    'g:gender' => '',
    'g:google_product_category' => '',
    'g:condition' => '',
    'g:expiration_date' => '',
);
