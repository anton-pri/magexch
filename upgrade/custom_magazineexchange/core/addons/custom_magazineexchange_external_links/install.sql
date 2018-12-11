-- Addon
REPLACE INTO `cw_addons` (`addon`, `descr`, `active`, `status`, `parent`, `version`, `orderby`) 
VALUES ('custom_magazineexchange_external_links', 'Magazineexchange.com: External Product Links', 1, 1, 'custom_magazineexchange', '0.1', 0);

-- configuration options
REPLACE INTO `cw_config_categories` (`config_category_id` ,`category` ,`is_local`) VALUES (NULL , 'custom_magazineexchange_external_links', '0');
SET @config_category_id = LAST_INSERT_ID();
REPLACE INTO cw_config SET name='mag_external_links_flag', comment='Flag explanation', value='Y', config_category_id=@config_category_id, orderby='200', type='checkbox', defvalue='Y', variants='';

-- Addon name/description
REPLACE INTO cw_languages SET code='EN', name='addon_descr_custom_magazineexchange_external_links', value='Magazineexchange.com: External Product Links', topic='Addons';
REPLACE INTO cw_languages SET code='EN', name='addon_name_custom_magazineexchange_external_links', value='External Links', topic='Addons';
REPLACE INTO cw_languages SET code='EN', name='option_title_custom_magazineexchange_external_links', value='External Links Options', topic='Options';

-- Langvars
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_external_links', 'External Links', 'Labels');

-- Table
CREATE TABLE `cw_magazine_external_links` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `product_id` int(11) NOT NULL,
 `price` decimal(12,2) NOT NULL,
 `seller` varchar(32) NOT NULL,
 `profile` varchar(512) NOT NULL,
 `link` varchar(512) NOT NULL,
 `format` varchar(32) NOT NULL,
 `comment` varchar(1024) NOT NULL,
 `category` varchar(64) NOT NULL,
 `action` varchar(64) NOT NULL,
 `value` varchar(64) NOT NULL,
 PRIMARY KEY (`id`),
 KEY `product_id` (`product_id`)
) COMMENT='External product links';
