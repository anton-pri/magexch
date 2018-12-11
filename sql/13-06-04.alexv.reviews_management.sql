INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'txt_rate_this_product', 'Please rate this product first', 'Text');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_reviews_management', 'Reviews management', 'Labels');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_flag', 'Flag', 'Labels');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_modify_review', 'Modify review', 'Labels');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_review', 'Review', 'Labels');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'txt_review_nas_been_deleted', 'Review(s) has been deleted ', 'Text');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'eml_review_product_notification', 'Review product notification', 'E-Mail');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_resend_reminder_email', 'Send reminder email to review the product', 'Labels');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_email_sent_successfully', 'Email sent successfully', 'Labels');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_email_not_sent', 'Email is not sent for some reason', 'Labels');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_products_no_reviews', 'With products without reviews', 'Labels');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'txt_already_purchased', 'You''ve already purchased this product, place a review now', 'Text');

ALTER TABLE `cw_products_votes` ADD `review_id` INT( 11 ) NOT NULL , ADD INDEX ( `review_id` );
ALTER TABLE `cw_products_reviews` ADD `status` INT( 1 ) NOT NULL;

UPDATE cw_products_votes v SET v.review_id = (SELECT r.review_id FROM cw_products_reviews r WHERE r.product_id = v.product_id AND r.customer_id = v.customer_id AND r.customer_id <> 0 LIMIT 1);
UPDATE `cw_products_reviews` SET `status` = 1;

UPDATE `cw_config` SET `comment` = 'Allow adding reviews and votes'
WHERE `cw_config`.`name` = 'writing_reviews' AND `cw_config`.`config_category_id`=10 LIMIT 1;
UPDATE `cw_languages` SET `value` = 'Allow adding reviews and votes' 
WHERE `cw_languages`.`code` = 'EN' AND `cw_languages`.`name` = 'opt_writing_reviews' LIMIT 1;

-- get menu_id for navigation menu
SELECT @sections:=menu_id FROM cw_navigation_menu WHERE title='lbl_content' AND parent_menu_id=0 AND area='A' LIMIT 1;
-- insert new entry to menu
DELETE FROM cw_navigation_menu WHERE title='lbl_reviews_management';
INSERT INTO `cw_navigation_menu` (`menu_id`, `parent_menu_id`, `title`, `link`, `orderby`, `area`, `access_level`, `module`, `is_loggedin`) 
VALUES (NULL, @sections, 'lbl_reviews_management', 'index.php?target=estore_reviews_management', 490, 'A', '', 'EStoreProductsReview', 1);

ALTER TABLE `cw_products_reviews` ADD `ctime` INT( 11 ) NOT NULL;
UPDATE `cw_products_reviews` SET `ctime` = UNIX_TIMESTAMP() - 100 + CAST(review_id AS UNSIGNED);

CREATE TABLE `cw_products_reviews_reminder` (
	`product_id` INT( 11 ) NOT NULL ,
	`customer_id` INT( 11 ) NOT NULL ,
	`ctime` INT( 11 ) NOT NULL ,
	INDEX ( `product_id` , `customer_id` )
) ENGINE = MYISAM ;

ALTER TABLE `cw_docs` ADD `status_change` INT( 11 ) NOT NULL AFTER `date`;
UPDATE `cw_docs` SET `status_change` = `date`;


SELECT @config_category_id:=config_category_id FROM cw_config_categories WHERE category='EStoreProductsReview' AND is_main=0;
INSERT INTO `cw_config` (`name`, `comment`, `value`, `config_category_id`, `orderby`, `type`, `defvalue`, `variants`) 
VALUES ('status_created_reviews', 'The status of created reviews', '1', @config_category_id, '40', 'selector', '1', '0:lbl_pending\n1:lbl_approved');
INSERT INTO `cw_config` (`name`, `comment`, `value`, `config_category_id`, `orderby`, `type`, `defvalue`, `variants`) 
VALUES ('amount_days_order_review_product', 'Amount of days after order is considered complete, before sending email with a request to review the product', '0', @config_category_id, '50', 'numeric', '0', '');
INSERT INTO `cw_config` (`name`, `comment`, `value`, `config_category_id`, `orderby`, `type`, `defvalue`, `variants`) 
VALUES ('order_status_start_reminder', 'Order status that order must be at before it\'s considered complete', 'Q', @config_category_id, '60', 'selector', 'Q', 'I:lbl_not_finished\nQ:lbl_queued\nP:lbl_processed\nB:lbl_backordered\nD:lbl_declined\nL:lbl_deposited\nF:lbl_failed\nC:lbl_complete');

INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'eml_review_product_text', 'Thank you for purchasing {{count}} product(s), we\'d be very grateful if you would take the time to place a review of the product(s) here.', 'E-Mail');