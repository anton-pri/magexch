<?php
global $fi_tables;

$fi_tables = $tables;
$fi_tables['products_images_det'] = 'cw_products_images_det';
$fi_tables['products_images_thumb'] = 'cw_products_images_thumb';

global $manufacturer_attribute_id;
$manufacturer_attribute_id = intval(cw_query_first_cell("select attribute_id from $tables[attributes] where item_type='P' and field='manufacturer_id' and addon='manufacturers'"));

global $csvxc_allowed_import_sections;
$csvxc_allowed_import_sections = array(
    "[PRODUCTS]",
    "[PRODUCT_OPTIONS]",
    "[MULTILANGUAGE_PRODUCT_OPTIONS]",
    "[MULTILANGUAGE_PRODUCT_OPTION_VALUES]",
    "[PRODUCT_OPTION_EXCEPTIONS]",
    "[PRODUCT_OPTION_JSCRIPT]",
    "[PRODUCT_VARIANTS]",
    "[CATEGORIES]",
    "[CUSTOMER_REVIEWS]",
    "[PRODUCT_LINKS]",
    "[EXTRA_FIELDS]",
    "[PRODUCTS_EXTRA_FIELD_VALUES]",
);

global $csvxc_allowed_sections;
$csvxc_allowed_sections = array(
    "tmp_load_PRODUCTS" => 1,
    "tmp_load_CATEGORIES" => 1,
    "tmp_load_PRODUCT_OPTIONS" => 1,
    "tmp_load_PRODUCT_VARIANTS" => 1,
    "tmp_load_DETAILED_IMAGES" => 1,
/*
    "tmp_load_MULTILANGUAGE_PRODUCT_OPTIONS",
    "tmp_load_MULTILANGUAGE_PRODUCT_OPTION_VALUES",
    "tmp_load_PRODUCT_OPTION_EXCEPTIONS",
    "tmp_load_PRODUCT_OPTION_JSCRIPT",
*/
    "tmp_load_CUSTOMER_REVIEWS"=>1,
    "tmp_load_PRODUCT_LINKS"=>1,
/**/

    "tmp_load_EXTRA_FIELDS"=>1,
    "tmp_load_PRODUCTS_EXTRA_FIELD_VALUES"=>1,

);

global $csvxc_field_types;
$csvxc_field_types = array(
    'PRICE' => "decimal(12,2) NOT NULL DEFAULT '0.00'",
    'PRICE_MODIFIER' => "decimal(12,2) NOT NULL DEFAULT '0.00'",
    'DESCR' => "text NOT NULL DEFAULT ''",
    'FULLDESCR' => "text NOT NULL DEFAULT ''",
    'PRODUCTID' => "int(11) NOT NULL DEFAULT '0'",
    'PRODUCTID_TO' => "int(11) NOT NULL DEFAULT '0'",
    'OPTIONID' => "int(11) NOT NULL DEFAULT '0'",
    'CLASSID' => "int(11) NOT NULL DEFAULT '0'",
    'default' => "varchar(255) NOT NULL DEFAULT ''",
    'default_PRODUCTS_EXTRA_FIELD_VALUES' => "text NOT NULL DEFAULT ''"
);

global $csvxc_extend_ids;
$csvxc_extend_ids = array(
    'tmp_load_PRODUCTS' => array(array('colname'=>'PRODUCTID'), array('colname'=>'PRODUCTCODE')),
    'tmp_load_PRODUCT_OPTIONS' => array(array('colname'=>'PRODUCTID'), array('colname'=>'PRODUCTCODE'),array('colname'=>'CLASSID'), array('colname'=>'CLASS')),
    'tmp_load_PRODUCT_VARIANTS' => array(array('colname'=>'PRODUCTID'), array('colname'=>'PRODUCTCODE'), array('colname'=>'VARIANTID'), array('colname'=>'VARIANTCODE'), /*array('colname'=>'CLASS')*/),
    'tmp_load_DETAILED_IMAGES' => array(array('colname'=>'PRODUCTID'), array('colname'=>'PRODUCTCODE')),
);

global $csvxc_images_translate_path;
$csvxc_images_translate_path = array(
    'tmp_load_PRODUCTS_IMAGE' => array('dir' => 'products_images_det'),
    'tmp_load_PRODUCTS_THUMBNAIL' => array('dir' => 'products_images_thumb'),
    'tmp_load_CATEGORIES_ICON' => array('dir' => 'categories_images_thumb'),
    'tmp_load_DETAILED_IMAGES_IMAGE' => array('dir' => 'products_detailed_images')
);

global $csvxc_fmap;
$csvxc_fmap = array();

$csvxc_fmap['tmp_load_PRODUCTS'] = array(
    'PRODUCTID' => array('field'=>'product_id', 'table'=>$fi_tables['products'], 'keylink'=>1),
    'PRODUCTCODE' => array('field'=>'productcode', 'table'=>$fi_tables['products']),
    'PRODUCT' => array('field'=>'product', 'table'=>$fi_tables['products']),
    'WEIGHT' => array('field'=>'weight', 'table'=>$fi_tables['products']),
    'LIST_PRICE' => array('field' => 'list_price', 'table'=>$fi_tables['products'], 
                          'alter'=>array('field' => 'list_price', 'table'=>$fi_tables['products_prices'], 
                          'replace_fields'=>array('product_id'=>'PRODUCTID', 'variant_id'=>0, 'membership_id'=>0, 'quantity'=>1, 'price'=>'PRICE'))),

    'DESCR' => array('field'=>'descr', 'table'=>$fi_tables['products']),
    'FULLDESCR' => array('field'=>'fulldescr', 'table'=>$fi_tables['products']),
    //'KEYWORDS' => array('field'=>'keywords', 'table'=>$fi_tables['products']),

    'AVAIL' => array('field'=>'avail', 'table'=>$fi_tables['products_warehouses_amount'],
               'replace_fields'=>array('product_id'=>'PRODUCTID','variant_id'=>0, 'warehouse_customer_id'=>0)),

    //'RATING' => array('field'=>'vote_value', 'table'=>'cw_products_votes'), - to investigate meaning, ignore for now
    'FORSALE' => array('field'=>'status', 'table' => $fi_tables['products']/*, 'transform'=>"IF(tmp_load_PRODUCTS.FORSALE='Y',1,0)"*/), 

    'SHIPPING_FREIGHT' => array('field'=>'shipping_freight', 'table'=>$fi_tables['products']),
    'FREE_SHIPPING' => array('field'=>'free_shipping', 'table'=>$fi_tables['products']),
    'DISCOUNT_AVAIL' => array('field'=>'discount_avail', 'table'=>$fi_tables['products']),
    'MIN_AMOUNT' => array('field'=>'min_amount', 'table'=>$fi_tables['products']),
    'DIM_X' => array('field'=>'dim_x', 'table'=>$fi_tables['products']),
    'DIM_Y' => array('field'=>'dim_y', 'table'=>$fi_tables['products']),
    'DIM_Z' => array('field'=>'dim_z', 'table'=>$fi_tables['products']),
    'LOW_AVAIL_LIMIT' => array('field'=>'low_avail_limit', 'table'=>$fi_tables['products']),
    'FREE_TAX' => array('field'=>'free_tax', 'table'=>$fi_tables['products']),
    'COST' => array('field'=>'cost', 'table'=>$fi_tables['products']),
    'CATEGORYID' => array('field'=>'category_id', 'table'=>$fi_tables['products_categories'],
                          'replace_fields' => array('product_id'=>'PRODUCTID','main'=>1,'orderby'=>0)), 

//CATEGORY -> convert to CATEGORYID when empty
    'MEMBERSHIPID' => array('table' => $fi_tables['products_memberships'], 'field'=>'membership_id',
                  'replace_fields' => array('product_id'=>'PRODUCTID', 'membership_id'=>0)),

    'MEMBERSHIP' => array(),

    'SUPPLIERID' => array('table' => $fi_tables['products_system_info'], 'field'=>'supplier_customer_id',
                  'replace_fields' => array('product_id'=>'PRODUCTID')),

    'PRICE' => array('field' => 'price', 'table'=>$fi_tables['products_prices'],
                     'replace_fields'=>array('product_id'=>'PRODUCTID', 'variant_id'=>0, 'membership_id'=>0, 'quantity'=>1, 'list_price'=>'LIST_PRICE')),
 
   'THUMBNAIL' => array(),
   'IMAGE' => array(),
//TAXES ?
//ADD_DATE -?
//VIEWS_STATS cw_products_stats.views_stats
//SALES_STATS cw_products_stats.sales_stats
//DEL_STATS cw_products_stats.del_stats

    'MANUFACTURERID' => array('table'=>$fi_tables['attributes_values'], 'field'=>'value',
                        'replace_fields' => array('attribute_id'=>$manufacturer_attribute_id, 'code'=>"'EN'", 'item_type'=>"'P'", 'item_id'=>'PRODUCTID')), //!!!hardcode!!! 

    'MANUFACTURER' => array() //- convert to MANUFACTURERID if empty
);

$csvxc_fmap['tmp_load_CATEGORIES'] = array(
    'CATEGORYID' => array('table'=>$fi_tables['categories'], 'field'=>'category_id','keylink'=>1),
    'CATEGORY' => array('table'=>'cw_categories_tmp', 'field'=>'category_path'),
    'DESCR' => array('table'=>$fi_tables['categories'], 'field'=>'description'),

    'META_DESCR' => array('table'=>$fi_tables['attributes_values'], 'field'=>'value',
               'replace_fields' => array('attribute_id'=>45, 'code'=>"'EN'", 'item_type'=>"'C'", 'item_id'=>'CATEGORYID')), //!!!hardcode!!!

    'AVAIL' => array('table' => $fi_tables['categories'], 'field'=>'status' /*, 'transform'=>"IF(tmp_load_CATEGORIES.AVAIL='Y',1,0)"*/),
    'ORDERBY' => array('table' => $fi_tables['categories'], 'field'=>'order_by'),

    'META_KEYWORDS' => array('table'=>$fi_tables['attributes_values'], 'field'=>'value',
               'replace_fields' => array('attribute_id'=>41, 'code'=>"'EN'", 'item_type'=>"'C'", 'item_id'=>'CATEGORYID')), //!!!hardcode!!!

    'VIEWS_STATS' => array('table' => $fi_tables['categories_stats'], 'field'=>'views_stats', 
                'replace_fields' => array('category_id'=>'CATEGORYID')), 

//'PRODUCT_COUNT' => array('table'=>'cw_categories_subcount', 'field'=>'product_count',
//                'replace_fields'=>array('status'=>1, 'category_id'=>'CATEGORYID', 'membership_id'=>0)),

    'MEMBERSHIPID' => array('table' => $fi_tables['categories_memberships'], 'field'=>'membership_id',
                  'replace_fields' => array('category_id'=>'CATEGORYID', 'membership_id'=>0)),
    'ICON' => array()

);

$csvxc_fmap['tmp_load_PRODUCT_OPTIONS'] = array(
    'PRODUCTID' => array('table'=>$fi_tables['product_options'], 'field'=>'product_id'),
//PRODUCTCODE
//PRODUCT
    'CLASSID' => array('table'=>$fi_tables['product_options'], 'field'=>'product_option_id',
                       'alter'=>array('field' => 'product_option_id', 'table'=>$fi_tables['product_options_values'])),
    'CLASS' => array('table'=>$fi_tables['product_options'], 'field'=>'field'),
    'TYPE' => array('table'=>$fi_tables['product_options'], 'field'=>'type'),
    'DESCR' => array('table'=>$fi_tables['product_options'], 'field'=>'name'),
    'ORDERBY' => array('table'=>$fi_tables['product_options'], 'field'=>'orderby'),
    'AVAIL' => array('table'=>$fi_tables['product_options'], 'field'=>'avail'),
    'OPTIONID' => array('table'=>$fi_tables['product_options_values'], 'field'=>'option_id',
                  'replace_fields'=>array('name'=>'OPTION', 'orderby'=>'OPTION_ORDERBY', 
                  'avail'=>'OPTION_AVAIL', 'price_modifier'=>'PRICE_MODIFIER', 'price_modifier'=>'MODIFIER_TYPE')),
    'OPTION' => array('table'=>$fi_tables['product_options_values'], 'field'=>'name',
                  'replace_fields'=>array('option_id'=>'OPTIONID', 'orderby'=>'OPTION_ORDERBY',
                  'avail'=>'OPTION_AVAIL', 'price_modifier'=>'PRICE_MODIFIER', 'modifier_type'=>'MODIFIER_TYPE')),
    'PRICE_MODIFIER' => array('table'=>$fi_tables['product_options_values'], 'field'=>'price_modifier',
                  'replace_fields'=>array('option_id'=>'OPTIONID', 'name'=>'OPTION', 'orderby'=>'OPTION_ORDERBY',
                  'avail'=>'OPTION_AVAIL', 'modifier_type'=>'MODIFIER_TYPE')),
    'MODIFIER_TYPE' => array('table'=>$fi_tables['product_options_values'], 'field'=>'modifier_type',
                  'replace_fields'=>array('option_id'=>'OPTIONID', 'name'=>'OPTION', 'orderby'=>'OPTION_ORDERBY',
                  'avail'=>'OPTION_AVAIL', 'price_modifier'=>'PRICE_MODIFIER')),
    'OPTION_ORDERBY' => array('table'=>$fi_tables['product_options_values'], 'field'=>'orderby',
                  'replace_fields'=>array('option_id'=>'OPTIONID', 'name'=>'OPTION', 
                  'avail'=>'OPTION_AVAIL', 'price_modifier'=>'PRICE_MODIFIER',
                  'modifier_type'=>'MODIFIER_TYPE')),
    'OPTION_AVAIL' => array('table'=>$fi_tables['product_options_values'], 'field'=>'avail',
                  'replace_fields'=>array('option_id'=>'OPTIONID', 'name'=>'OPTION', 'orderby'=>'OPTION_ORDERBY',
                  'price_modifier'=>'PRICE_MODIFIER', 'modifier_type'=>'MODIFIER_TYPE'))
); 

$csvxc_fmap['tmp_load_PRODUCT_VARIANTS'] = array(
    'PRODUCTID' => array('table'=>$fi_tables['product_variants'], 'field'=>'product_id'),
    'PRODUCTCODE' => array(),
    'PRODUCT' => array(),
    'VARIANTID' => array('table'=>$fi_tables['product_variants'], 'field'=>'variant_id'),
    'VARIANTCODE' => array('table'=>$fi_tables['product_variants'], 'field'=>'productcode'),
    'WEIGHT' => array('table'=>$fi_tables['product_variants'], 'field'=>'weight'),
    'PRICE' => array(),
    'AVAIL' => array(),
    'DEFAULT' => array('table'=>$fi_tables['product_variants'], 'field'=>'def'),
    'CLASS' => array(),
    'OPTION' => array()
);

$csvxc_fmap['tmp_load_PRODUCT_LINKS'] = array(
    'PRODUCTID' => array('table'=>$fi_tables['linked_products'], 'field'=>'product_id'),
    //PRODUCTCODE
    //PRODUCT
    'PRODUCTID_TO' => array('table'=>$fi_tables['linked_products'], 'field'=>'linked_product_id'),
    //PRODUCTCODE_TO
    //PRODUCT_TO
    'ORDERBY' => array('table'=>$fi_tables['linked_products'], 'field'=>'orderby')
); 

$csvxc_fmap['tmp_load_EXTRA_FIELDS'] = array(
    //'FIELDID'
    'SERVICE_NAME' => array('table'=>$fi_tables['attributes'], 'field'=>'field'),
    //'CODE'
    'FIELD' => array('table'=>$fi_tables['attributes'], 'field'=>'name'),
    //'DEFAULT' 
    'ORDERBY' => array('table'=>$fi_tables['attributes'], 'field'=>'orderby'),
    'ACTIVE' => array('table'=>$fi_tables['attributes'], 'field'=>'active')
);

$csvxc_fmap['tmp_load_PRODUCTS_EXTRA_FIELD_VALUES'] = array(
    'PRODUCTID' => array(),
    'PRODUCTCODE' => array()
);

?>
