
DELETE FROM cw_addons WHERE addon = 'vertical_response';
REPLACE INTO `cw_addons` (`addon`, `descr`, `active`, `status`, `parent`,`version`,`orderby`)
VALUES ('vertical_response', 'Vertical Response Email', 1, 1, '', '0.1', 0);

SELECT @config_category_id=config_category_id FROM `cw_config_categories` WHERE `category` = 'vertical_response';
DELETE FROM cw_config WHERE config_category_id = @config_category_id;
DELETE FROM cw_config_categories WHERE config_category_id = @config_category_id;

INSERT INTO `cw_config_categories` (`config_category_id`, `category`, `is_local`) VALUES (NULL, 'vertical_response', '0');
SET @config_category_id = LAST_INSERT_ID();
INSERT INTO `cw_config` (`name`, `comment`, `value`, `config_category_id`, `orderby`, `type`, `defvalue`, `variants`) 
VALUES ('vertical_response_email', 'Username (email)', '', @config_category_id, '10', 'text', '', '');
INSERT INTO `cw_config` (`name`, `comment`, `value`, `config_category_id`, `orderby`, `type`, `defvalue`, `variants`) 
VALUES ('vertical_response_password', 'Password', '', @config_category_id, '20', 'text', '', '');

REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'addon_name_vertical_response', 'Vertical Response', '', 'Addons');