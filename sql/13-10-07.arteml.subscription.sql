DROP TABLE IF EXISTS `cw_subscriptions`, `cw_subscription_customers`;

delete FROM `cw_langvars_statistics` WHERE `name` LIKE '%pconf%';

delete from cw_languages where name IN ('addon_descr_subscriptions', 'msg_adm_product_subscr_del', 'lbl_subscription_plan', 'lbl_subscription_date', 'lbl_subscriptions_management', 'lbl_subscriptions_info', 'opt_active_subscriptions_processor', 'config_subscriptions', 'txt_subscr_table_format', 'txt_subscription_processor_note', 'txt_subscription_no_payment_warn', 'txt_subscription_for_product', 'txt_subscriptions_top_text', 'txt_subscriptions_management_top_text', 'txt_remove_cc_data_subscription_note', 'opt_subscriptions_key', 'msg_adm_product_subscr_upd');

delete FROM `cw_langvars_statistics` WHERE `name` IN ('addon_descr_subscriptions', 'msg_adm_product_subscr_del', 'lbl_subscription_plan', 'lbl_subscription_date', 'lbl_subscriptions_management', 'lbl_subscriptions_info', 'opt_active_subscriptions_processor', 'config_subscriptions', 'txt_subscr_table_format', 'txt_subscription_processor_note', 'txt_subscription_no_payment_warn', 'txt_subscription_for_product', 'txt_subscriptions_top_text', 'txt_subscriptions_management_top_text', 'txt_remove_cc_data_subscription_note', 'opt_subscriptions_key', 'msg_adm_product_subscr_upd');

delete from cw_config where name='subscriptions_key';
delete from cw_config_categories where category='subscriptions';

DELETE FROM `cw_products_types` WHERE `cw_products_types`.`product_type_id` = 2;
