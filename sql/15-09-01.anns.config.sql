REPLACE INTO `cw_config` (`name`, `comment`, `value`, `config_category_id`, `orderby`, `type`, `defvalue`, `variants`) VALUES ('images_dimensions', 'Images dimensions', '', 4, 150, 'separator', '', '');
UPDATE cw_config SET orderby=152 WHERE name='categories_images_thumb_height';
UPDATE cw_config SET orderby=153 WHERE name='products_images_thumb_width';
UPDATE cw_config SET orderby=154 WHERE name='products_images_thumb_height';
UPDATE cw_config SET orderby=155 WHERE name='products_images_det_width';
UPDATE cw_config SET orderby=156 WHERE name='size_user_avatar';
UPDATE cw_config SET orderby=136 WHERE name='send_to_friend_enabled';
UPDATE cw_config SET orderby=137 WHERE name='display_productcode_in_list';
UPDATE cw_config SET orderby=138 WHERE name='social_buttons';
UPDATE cw_config SET orderby=139 WHERE name='short_descr_truncate';
UPDATE cw_config SET orderby=139 WHERE name='show_views_on_product_page';