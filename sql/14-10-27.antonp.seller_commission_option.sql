SELECT @config_category_id:=config_category_id FROM cw_config_categories WHERE category='seller';

REPLACE INTO `cw_config` set name='seller_enable_admin_commission_share', comment='Allow admin account to receive percentage with online payment transaction (works with PayPal Adaptive addon)', value='N', config_category_id=@config_category_id, orderby=0, type='checkbox';

UPDATE `cw_config` SET orderby=10 where name='seller_admin_commission_rate';
