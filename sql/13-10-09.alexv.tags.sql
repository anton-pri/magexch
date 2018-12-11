DROP TABLE IF EXISTS cw_tags;
CREATE TABLE `cw_tags` (
`tag_id` INT( 11 ) NOT NULL AUTO_INCREMENT ,
`name` VARCHAR( 255 ) NOT NULL ,
`product_count` INT( 11 ) NOT NULL ,
`creation` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
PRIMARY KEY ( `tag_id` ) ,
INDEX ( `tag_id` ) ,
UNIQUE ( `name` )
);

DROP TABLE IF EXISTS cw_tags_products;
CREATE TABLE `cw_tags_products` (
`tag_id` INT( 11 ) NOT NULL ,
`product_id` INT( 11 ) NOT NULL
) ENGINE = MYISAM ;

INSERT INTO `cw_config_categories` (`config_category_id`, `category`, `is_local`) VALUES (NULL, 'tags', '0');
SET @config_category_id = LAST_INSERT_ID();
INSERT INTO `cw_config` (`name`, `comment`, `value`, `config_category_id`, `orderby`, `type`, `defvalue`, `variants`) 
VALUES ('skin_for_tags_list', 'Skin to display a list of tags', 'line_list', @config_category_id, '10', 'selector', 'line_list', 'line_list:Line list of tags\n2D_canvas:2D canvas\n3D_canvas:3D canvas');
INSERT INTO `cw_config` (`name`, `comment`, `value`, `config_category_id`, `orderby`, `type`, `defvalue`, `variants`) 
VALUES ('cloud_is_weighted', 'Is the cloud weighted', 'N', @config_category_id, '20', 'checkbox', 'N', '');
INSERT INTO `cw_config` (`name`, `comment`, `value`, `config_category_id`, `orderby`, `type`, `defvalue`, `variants`) 
VALUES ('count_first_popular_tags', 'Number of popular tags for home and categories page', '10', @config_category_id, '30', 'text', '10', '');

INSERT INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'option_title_tags', 'Tags', '', 'Options');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'lbl_search_tags', 'Search tags', '', 'Labels');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'lbl_tags', 'Tags', '', 'Labels');

ALTER TABLE `cw_products` DROP `keywords`;
ALTER TABLE `cw_products_lng` DROP `keywords`;