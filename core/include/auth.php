<?php
global $arr_auth;
$arr_auth['A'] = array(
    /*'01' => array(
        'name' => 'lbl_inner_employes', 'sub' => array(
            '0100' => array(
                'name' => 'lbl_inner_employes', 
                'php' => array(
                    array('target' => 'user_I', 'actions' => array('update', 'delete', 'update_address', 'delete_photos', 'customer_images', 'upload', 'update_transation', 'update_discount', 'delete_discount', 'update_taxes', 'delete_taxes'))
                )
            ),
        )
    ),*/
    '02' => array(
        'name' => 'lbl_section_admins', 
        'php' => array(
            array('target' => 'user_A', 'actions' => array('update', 'delete', 'update_address', 'delete_photos', 'customer_images', 'upload', 'update_transation', 'update_discount', 'delete_discount', 'update_taxes', 'delete_taxes'))
        )
    ),
    '04' => array(
        'name' => 'lbl_docs_info_B', 
        'php' => array(
            array('target' => 'docs_B', 'actions' => array('add', 'status_change', 'save', 'cancel', 'delete_all', 'set_template', 'create_template', 'copy_layout_template', 'delete_template', 'update', 'delete')), 
            array('target' => 'popup_docs'), 
            array('target' => 'popup_users'), 
            array('target' => 'cod_types', 'actions' => array('update', 'delete'))
        )
    ),
    '06' => array(
        'name' => 'lbl_section_user_pos', 
        'php' => array(array('target' => 'user_G', 'actions' => array('update', 'delete', 'update_address', 'delete_photos', 'customer_images', 'upload', 'update_transation', 'update_discount', 'delete_discount', 'update_taxes', 'delete_taxes')))
    ),
    '07' => array(
        'name' => 'lbl_section_cash_selling', 
        'sub' => array(
            '0700' => array(
                'name' => 'lbl_docs_info_G', 
                'php' => array(
                    array('target' => 'docs_G', 'actions' => array('add', 'status_change', 'save', 'cancel', 'delete_all', 'set_template', 'create_template', 'copy_layout_template', 'delete_template', 'update', 'delete')), 
                    array('target' => 'popup_docs'), 
                    array('target' => 'popup_users'), 
                    array('target' => 'cod_types', 'actions' => array('update', 'delete')))
            ),
        )
    ),	
    '08' => array(
        'name' => 'lbl_section_customers', 'sub' => array(
            '0801' => array(
                'name' => 'lbl_users_C', 
                'php' => array(
                    array('target' => 'user_C', 'actions' => array('update', 'delete', 'update_address', 'delete_photos', 'customer_images', 'upload', 'update_transation', 'update_discount', 'delete_discount', 'update_taxes', 'delete_taxes'))
                )
            ),
            '0802' => array(
                'name' => 'lbl_gift_certificate', 
                'php' => array(
                    array('target' => 'giftcerts', 'actions' => array('add_gc', 'modify_gc', 'delete'))
                )
            ),
        )
    ),
// target not defined yet
    '11' => array(
        'name' => 'lbl_section_sales_managers', 
        'sub' => array(
            '1100' => array(
                'name' => 'lbl_salesman', 
                'php' => array(
                    array('target' => 'user_B', 'actions' => array('update', 'delete', 'update_address', 'delete_photos', 'customer_images', 'upload', 'update_transation', 'update_discount', 'delete_discount', 'update_taxes', 'delete_taxes'))
                )
            ),	
            '1101' => array(
                'name' => 'lbl_affiliate_plans', 'php' => array(
                    array('target' => 'salesman_plans', 'actions' => array('default_plan', 'update', 'edit', 'delete_rate', 'modify', 'create'))
                )
            ),
            '1102' => array(
                'name' => 'lbl_commissions', 
                'php' => array(
                    array('target' => 'salesman_commissions', 'actions' => array('apply', 'apply_global')))
            ),
/*
        	'1103' => array(
                'name' => 'lbl_salesman_created_orders', 
                'php' => array(
                    array('target' => 'salesman_created_orders'))
            ),
*/
        	'1104' => array(
                'name' => 'lbl_salesman_accounts', 
                'php' => array(
                    array('target' => 'salesman_report', 'actions' => array('paid'))
                )
            ),
        	'1105' => array(
                'name' => 'lbl_payment_upload', 
                'php' => array(
                    array('target' => 'payment_upload', 'actions' => array('upload'))
                )
            ),
        	'1106' => array(
                'name' => 'lbl_banners', 
                'php' => array(
                    array('target' => 'salesman_banners', 'actions' => array('upload', 'add', 'delete')),
                    array('target' => 'salesman_element_list'),
                    array('target' => 'preview_banner'),
                )
            ),
        	'1107' => array(
                'name' => 'lbl_multi_tier_affiliates', 
                'php' => array(
                    array('target' => 'salesman_level_commissions', 'actions' => array('edit'))
                )
            ),
        	'1108' => array(
                'name' => 'lbl_affiliate_statistics', 
                'php' => array(
                    array('target' => 'banner_info'))
            ),
        	'1109' => array(
                'name' => 'lbl_advertising_campaigns', 
                'php' => array(
                    array('target' => 'salesman_adv_campaigns', 'actions' => array('add', 'delete'))
                )
            ),
        	/*'1110' => array(
                'name' => 'lbl_calendar', 
                'php' => array(
                    array('target' => 'appointments', 'actions' => array('update', 'add'))
                )
            ),	*/
        	'1111' => array(
                'name' => 'lbl_discounts', 
                'php' => array(
                    array('target' => 'discounts', 'actions' => array('update'))
                )
            ),
        	'1112' => array(
                'name' => 'lbl_targets_premiums', 
                'php' => array(
                    array('target' => 'targets', 'actions' => array('update_target', 'update_premiums', 'add')) 
                )
            )
        )	
    ),
    '12' => array(
        'name' => 'lbl_description', 
        'sub' => array(
            '1200' => array(
                'name' => 'lbl_categories', 
                'php' => array(array('target' => 'categories', 'actions' => array('apply', 'delete', 'update_product_section', 'delete_product_section', 'add_product_section', 'delete_one_cat', 'update', 'move')))
            ),
            '1201' => array(
                'name' => 'lbl_manufacturers', 
                'php' => array(array('target' => 'manufacturers', 'actions' => array('details', 'delete', 'delete_image', 'update')))
            ),
		    '1203' => array(
                'name' => 'lbl_products',
                'sub' => array(
                    '120300' =>  array(
                        'name' => 'lbl_products', 
                        'php' => array(
                            array('target' => 'products', 'actions' => array('delete_serials', 'update_serials', 'delete_avails', 'delete_thumbnail', 'delete_product_image', 'product_modify', 'add', 'product_images', 'update_availability', 'product_images_delete', 'product_zoomer', 'zoomer_update_availability', 'product_zoomer_delete', 'wholesales_modify', 'wholesales_delete', 'update_classification', 'product_options_modify', 'product_options_delete', 'product_options_add', 'product_variants_modify', 'product_variants_rebuild', 'delete_image', 'product_class_assign', 'product_class_modify', 'upselling_links', 'del_upsale_link', 'update_reviews', 'review_delete', 'update', 'delete', 'clone'), 'par' => "\$action != 'modify_avails'"), 
                            array('target' => 'products_orders'), 
                            array('target' => 'products_clients'), 
                            array('target' => 'image_selection'),
                            array('target' => 'popup_files'),
                            array('target' => 'price_list'),
                        ),
                    ),
                    '120301' => array(
                        'name' => 'lbl_suppliers', 
                        'php' => array(array('target' => 'products', 'actions' => array('modify_avails'))),
                    ),
                ),
            ),
	    	'1205' => array(
                'name' => 'lbl_product_feature_classes', 
                'php' => array(
                    array('target' => 'classes', 'actions' => array('update_options', 'add_option', 'delete', 'add', 'delete_image', 'delete_options', 'update'))
                )
            ),
    		'1207' => array(
                'name' => 'lbl_special_sections', 
                'php' => array(
                    array('target' => 'special_sections', 'actions' => array('update', 'add')),
                    array('target' => 'popup_products')
                )
            ),
	    	'1208' => array(
                'name' => 'lbl_offers', 
                'php' => array(
                    array('target' => 'offers', 'actions' => array('update_offer', 'delete_image', 'promo', 'update', 'delete', 'conditions', 'bonuses', 'create'))
                )
            ),
            '1209' => array(
                'name' => 'lbl_discount_coupons', 
                'php' => array(
                    array('target' => 'coupons', 'actions' => array('add', 'update'))
                )
            ),
        )
    ),
    '13' => array(
        'name' => 'lbl_section_import', 
        'sub' => array(
            '1300' => array(
                'name' => 'lbl_import', 
                'php' => array(
                    array('target' => 'import', 'actions' => array('import')), 
                    array('target' => 'popup_import_layout')
                )
            ),
        ),	
    ),
    '18' => array(
        'name' => 'lbl_section_orders', 
        'php' => array(
            array('target' => 'generator'), 
            array('target' => 'docs_O', 'actions' => array('add', 'status_change', 'save', 'cancel', 'delete_all', 'set_template', 'create_template', 'copy_layout_template', 'delete_template', 'update', 'delete')), 
            array('target' => 'popup_docs'), 
            array('target' => 'popup_users'), 
            array('target' => 'cod_types', 'actions' => array('update', 'delete'))
        )
    ),
	'20' => array(
        'name' => 'lbl_section_invoices', 
        'php' => array(
            array('target' => 'docs_I', 'actions' => array('add', 'status_change', 'save', 'cancel', 'delete_all', 'set_template', 'create_template', 'copy_layout_template', 'delete_template', 'update', 'delete')),
            array('target' => 'popup_users'), 
            array('target' => 'cod_types', 'actions' => array('update', 'delete')), 
        )
    ),
	'25' => array(
        'name' => 'lbl_general_settings', 
        'levels' => 1, 
        'sub' => array(
            '2500' => array(
                'name' => 'lbl_general_settings', 
                'php' => array(
                    array('target' => 'settings', 'actions' => array('update'))
                )
            ),
    		'2501' => array(
                'name' => 'lbl_configuration', 
                'php' => array(
                    array('target' => 'configuration', 'actions' => array('update')), 
                    array('target' => 'popup_barcode', 'actions' => array('update', 'set_template', 'delete', 'create'))
                )
            ),
    		'2502' => array(
                'name' => 'lbl_profile_options', 
                'php' => array(
                    array('target' => 'user_profiles', 'actions' => array('update_status', 'update_fields', 'delete_fields', 'update_sections', 'delete_sections'))
                )
            ),				
	    	'2503' => array(
                'name' => 'lbl_payment_section', 
                'php' => array(
                    array('target' => 'payments', 'actions' => array('update', 'delete', 'update_method'))
                )
            ),
		    '2504' => array(
                'name' => 'lbl_shipping_methods', 
                'php' => array(
                    array('target' => 'shipping', 'actions' => array('enable_all', 'disable_all', 'update', 'delete')), 
                    array('target' => 'shipping_carriers', 'actions' => array('update', 'delete')), 
                    array('target' => 'zones', 'actions' => array('details', 'delete', 'clone')), 
                    array('target' => 'cod_types', 'actions' => array('ajax_update', 'update', 'delete')), 
                    array('target' => 'shipping_rates', 'actions' => array('copy_warehouses', 'delete', 'update', 'add')), 
                    array('target' => 'shipping_zones', 'actions' => array('details', 'delete', 'clone')),
                )
            ),
    		'2505' => array(
                'name' => 'lbl_section_taxes', 
                'php' => array(
                    array('target' => 'zones', 'actions' => array('details', 'delete', 'clone')), 
                    array('target' => 'taxes', 'actions' => array('tax_options', 'delete', 'update', 'details', 'delete_rates', 'update_rates', 'rate_details', 'apply'))
                )
            ),
		    '2507' => array(
                'name' => 'lbl_countries_and_states', 
                'php' => array(
                    array('target' => 'countries', 'actions' => array('delete', 'update', 'deactivate_all', 'activate_all')), 
                )
            ),
    		'2508' => array(
                'name' => 'lbl_section_memberships', 
                'php' => array(
                    array('target' => 'memberships', 'actions' => array('update', 'delete', 'add')), 
                    array('target' => 'access_level', 'actions' => array('update')), 
                    array('target' => 'tabs', 'actions' => array('update'))
                )
            ),
		)
	),		
	'26' => array(
        'name' => 'lbl_news_management', 
        'levels' => 1, 
        'sub' => array(
            '2600' => array(
                'name' => 'lbl_news', 
                'php' => array(
                    array('target' => 'news', 'actions' => array('add', 'delete', 'import', 'export', 'send', 'send_continue', 'delete', 'update', 'modify'))
                )
            ),
    		'2601' => array(
                'name' => 'lbl_customer_side_news', 
                'php' => array(
                    array('target' => 'news_c', 'actions' => array('add', 'delete', 'import', 'export', 'send', 'send_continue', 'delete', 'update', 'modify'))
                )
            ),
        )
	),
	'28' => array(
        'name' => 'lbl_section_seo', 
        'levels' => 1, 
        'sub' => array(
            '2800' => array(
                'name' => 'lbl_meta_tags', 
                'php' => array(
                    array('target' => 'meta_tags', 'actions' => array('update'))
                )
            ),
		)
	),
	'29' => array(
        'name' => 'lbl_webmaster_tools', 
        'levels' => 1, 
        'sub' => array(
            '2900' => array(
                'name' => 'lbl_db_backup_restore', 
                'php' => array(
                    array('target' => 'db_backup', 'actions' => array('backup', 'restore'))
                )
            ),
    		'2901' => array(
                'name' => 'lbl_sessions', 
                'php' => array(
                    array('target' => 'sessions', 'actions' => array('kill'))
                )
            ),
		    '2903' => array(
                'name' => 'lbl_sections_position', 
                'php' => array(
                    array('target' => 'sections_pos', 'actions' => array('update'))
                )
            ),
    		'2904' => array(
                'name' => 'lbl_languages', 
                'php' => array(
                    array('target' => 'languages', 'actions' => array('update_languages', 'update', 'delete', 'del_lang', 'export', 'add_lang', 'change_defaults')), 
                )
            ),
	    	'2905' => array(
                'name' => 'lbl_webmaster_tools', 
                'php' => array(
                    array('target' => 'custom_css', 'actions' => array('update')), 
                    array('target' => 'special_images', 'actions' => array('update', 'delete')), 
                    array('target' => 'speed_bar', 'actions' => array('update'))
                )
            ),
		)
	),	
	'30' => array(
        'name' => 'lbl_help', 
        'levels' => 1, 
        'sub' => array(
            '3000' => array(
                'name' => 'lbl_version', 
                'php' => array(
                    array('target' => 'version')
                )
            ),
		)
	),
    '31' => array(
        'name' => 'lbl_attributes', 
        'levels' => 1, 
        'php' => array(
            array('target' => 'attributes'),
        )
    )
);

# 
# warehouse access levels
#
$arr_auth['P'] = array(
    '11' => array(
        'name' => 'lbl_description', 
        'sub' => array(
            '1100' => array(
                'name' => 'lbl_products', 
                'php' => array(
                    array('target' => 'products', 'actions' => array('modify_avails', 'delete_avails', 'suppliers_list')), 
                ),
            ),
            '1101' => array(
                'name' => 'lbl_ean_serials', 
                'php' => array(
                    array('target' => 'ean_serials', 'actions' => array('update_range', 'update_inventory'))
                )
            ),
        ) 
    ),
    '12' => array(
        'name' => 'lbl_section_orders', 
        'sub' => array(
            '1200' => array(
                'name' => 'lbl_doc_info_O', 
                'php' => array(
                    array('target' => 'docs_O', 'actions' => array('status_change', 'save', 'cancel', 'delete_all', 'set_template', 'create_template', 'copy_layout_template', 'delete_template', 'update', 'delete')), 
                )
            ),
        )
    ),   
    '13' => array(
        'name' => 'lbl_section_invoices', 
        'sub' => array(
            '1300' => array(
                'name' => 'lbl_doc_info_I', 
                'php' => array(
                    array('target' => 'docs_I', 'actions' => array('status_change', 'save', 'cancel', 'delete_all', 'set_template', 'create_template', 'copy_layout_template', 'delete_template', 'update', 'delete')), 
                )
            ),
        )
    ),
    '15' => array(
        'name' => 'lbl_shipping_rates', 
        'levels' => 3, 
        'sub' => array(
            '1500' => array(
                'name' => 'lbl_shipping_zones', 
                'levels' => 3, 
                'php' => array(
                    array('target' => 'zones', 'actions' => array('details', 'delete', 'clone')), 
                )
            ),
            '1501' => array(
                'name' => 'lbl_shipping_rates', 
                'levels' => 3, 
                'php' => array(
                    array('target' => 'shipping', 'actions' => array('enable_all', 'disable_all', 'update', 'delete')), 
                )
            ),
        )
    ),
);

$arr_auth['G'] = array(
    '10' => array(
        'name' => 'lbl_pos_functions', 
        'levels' => 1, 
        'sub' => array(
            '1000' => array(
                'name' => 'lbl_start_new_order', 
                'php' => array(
                    array('target' => 'orders'), 
                    array('target' => 'popup_products'), 
                    array('target' => 'popup_users')
                ), 
                'sub' => array(
                    '100000' => array(
                        'name' => 'lbl_product_discount'
                    ),
                    '100001' => array(
                        'name' => 'lbl_product_price'
                    ),
                    '100002' => array(
                        'name' => 'lbl_global_discount'
                    ),
                    '100003' => array(
                        'name' => 'lbl_value_discount'
                    ),
                ),
            ),
            '1001' => array(
                'name' => 'lbl_return', 
                'php' => array(
                    array('target' => 'popup_products'), 
                    array('target' => 'popup_users')
                )
            ),
        )
    ),
    '11' => array(
        'name' => 'lbl_printer_function', 
        'levels' => 1, 
        'sub' => array(
            '1100' => array(
                'name' => 'lbl_pos_close_day', 
                'php' => array(
                    array('target' => 'printer_functions')
                )
            ),
        )
    ),
);

?>
