ALTER TABLE `cw_products_reviews` ADD `testimonials` INT( 1 ) NOT NULL;
ALTER TABLE `cw_products_reviews` ADD `stoplist` INT( 1 ) NOT NULL;
INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_testimonials', 'Testimonials', 'Labels');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'txt_no_testimonials', 'No testimonials was found', 'Text');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_allow_to_purchasers', 'Allow to purchasers', 'Labels');

UPDATE `cw_config` SET `comment` = 'Allow writing reviews (N - disallow to all, A - allow to all, R - allow to registered, P - allow to purchasers)',
`variants` = 'N:lbl_disallow_to_all\nA:lbl_allow_to_all\nR:lbl_allow_to_registered\nP:lbl_allow_to_purchasers' 
WHERE `cw_config`.`name` = 'writing_reviews' AND `cw_config`.`config_category_id`=10 LIMIT 1;
UPDATE `cw_languages` SET `value` = 'Allow adding reviews <br />(N - disallow to all, A - allow to all, <br />R - allow to registered, P - allow to purchasers)' 
WHERE `cw_languages`.`code` = 'EN' AND `cw_languages`.`name` = 'opt_writing_reviews' LIMIT 1;

-- get menu_id for Sections
SELECT @sections:=menu_id FROM cw_navigation_menu WHERE title='lbl_content' AND parent_menu_id=0 AND area='A' LIMIT 1;
-- insert new entry to menu
DELETE FROM cw_navigation_menu WHERE title='lbl_stop_list';
INSERT INTO `cw_navigation_menu` (`menu_id`, `parent_menu_id`, `title`, `link`, `orderby`, `area`, `access_level`, `module`, `is_loggedin`) 
VALUES (NULL, @sections, 'lbl_stop_list', 'index.php?target=estore_stop_list', 480, 'A', '', 'EStoreProductsReview', 1);