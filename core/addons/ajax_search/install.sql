REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'addon_name_ajax_search', 'Ajax Search', 'Addons');
-- lang var
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_ajax_search', 'Ajax Search', 'Labels');
-- new addon record
REPLACE INTO `cw_addons` (`addon`, `descr`, `active`) VALUES ('ajax_search', 'Allows customers to run the quick ajax based search. ', '1');
-- configuration options
INSERT INTO `cw_config_categories` (`config_category_id` ,`category` ,`is_main`) VALUES (NULL , 'ajax_search', '0');
SET @config_category_id = LAST_INSERT_ID();
INSERT INTO cw_config SET name='as_suggested_products', comment='Number of suggested products in ajax search drop down list', value='10', config_category_id = @config_category_id, orderby='15', type='text', defvalue='10', variants='';
INSERT INTO cw_config SET name='as_number_chars', comment='Number of chars needed to start the ajax search', value='3', config_category_id = @config_category_id, orderby='20', type='selector', defvalue='3', variants='1:1\r\n2:2\r\n3:3\r\n4:4\r\n5:5\r\n6:6\r\n7:7\r\n7:7\r\n8:8\r\n9:9\r\n10:10\r\n11:11\r\n12:12\r\n13:13\r\n14:14\r\n15:15';