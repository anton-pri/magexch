
DELETE FROM cw_addons WHERE addon = 'anti_scrapping_robot';
REPLACE INTO `cw_addons` (`addon`, `descr`, `active`, `status`, `parent`,`version`,`orderby`)
VALUES ('anti_scrapping_robot', 'Anti Scrapping Robot', 1, 1, '', '0.1', 0);

INSERT INTO `cw_config_categories` (`config_category_id`, `category`, `is_local`) VALUES (NULL, 'anti_scrapping_robot', '0');
SET @config_category_id = LAST_INSERT_ID();
INSERT INTO `cw_config` (`name`, `comment`, `value`, `config_category_id`, `orderby`, `type`, `defvalue`, `variants`) 
VALUES ('scrapper_user_agent_likes', 'Scraper user agent likes', '', @config_category_id, '10', 'textarea', '', '');
INSERT INTO `cw_config` (`name`, `comment`, `value`, `config_category_id`, `orderby`, `type`, `defvalue`, `variants`) 
VALUES ('scrapper_ips', 'Scraper IPs', '', @config_category_id, '20', 'textarea', '', '');
INSERT INTO `cw_config` (`name`, `comment`, `value`, `config_category_id`, `orderby`, `type`, `defvalue`, `variants`) 
VALUES ('percent_for_price_change', 'Percent for price change', '0', @config_category_id, '30', 'numeric', '0', '');

INSERT INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'addon_name_anti_scrapping_robot', 'Anti Scrapping Robot', '', 'Addons');