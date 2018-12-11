ALTER TABLE `cw_config_categories` CHANGE `is_main` `is_local` INT( 1 ) NOT NULL DEFAULT '0' COMMENT 'show only on specified pages';
update cw_config_categories SET is_local=0;
update cw_config_categories SET is_local=1 where category IN ('CMPI','manufacturers','SEO','sn','special_sections','shipping_docs','ppd');
 delete from cw_navigation_settings where config_category_id=0;
update cw_available_images set default_image='default_image_150.png' where name='products_images_det';
update cw_available_images set default_image='default_image_70.gif' where name='ad_banners_images';

