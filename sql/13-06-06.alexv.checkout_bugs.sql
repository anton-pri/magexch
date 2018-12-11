INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'txt_must_be_logged_for_order', 'You must be logged in to submit your order. Please sign in or create a new account.', 'Text');
ALTER TABLE `cw_customers_customer_info` CHANGE `tax_except` `tax_exempt` INT( 1 ) NOT NULL DEFAULT '0';
