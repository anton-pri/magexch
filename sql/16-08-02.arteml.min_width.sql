ALTER TABLE `cw_available_images` ADD `min_width` SMALLINT NOT NULL COMMENT 'extend canvas at least to this size' AFTER `max_width`;
update cw_available_images set min_width=100 where name in ('products_images_thumb','products_images_det','products_images_var','products_detailed_images');
