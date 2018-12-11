UPDATE `cw_attributes` SET `description` = 'Domain for orders' WHERE field='domains' and addon='multi_domains' and item_type='O';
UPDATE `cw_attributes` SET `description` = 'Owner clean urls' WHERE field='clean_url' and addon='clean_urls' and item_type='O';

UPDATE `cw_languages` SET `name` = 'lbl_from_seo_url', `value` = 'From SEO URL' WHERE `cw_languages`.`name` = 'lbl_from_dynamic_url';
UPDATE `cw_languages` SET `name` = 'lbl_to_dynamic_url', `value` = 'To dynamic URL' WHERE `cw_languages`.`name` = 'lbl_to_static_url';
