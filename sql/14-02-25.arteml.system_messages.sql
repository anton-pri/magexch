-- System messages
CREATE TABLE `cw_system_messages` (
 `code` varchar(64) NOT NULL,
 `date` int(10) unsigned NOT NULL,
 `hidden` tinyint(4) NOT NULL,
 `type` tinyint(4) NOT NULL,
 `message` text NOT NULL,
 PRIMARY KEY (`code`),
 KEY `type` (`type`)
) COMMENT='system messages, warnings, errors and awaitings';
ALTER TABLE `cw_system_messages` ADD `severity` CHAR( 1 ) NOT NULL AFTER `type` ;

-- Examples of awaiting actions
REPLACE INTO `cw_system_messages` (`code`, `date`, `hidden`, `type`, `severity`, `message`) VALUES
 ('product_approvals', 1393328209, 0, 1, 'I', '{$lng.lbl_product_approvals} - <a href="#">0</a>');
REPLACE INTO `cw_system_messages` (`code`, `date`, `hidden`, `type`, `severity`, `message`) VALUES 
 ('product_reviews', 1393328209, 0, 1, 'I', '{$lng.lbl_product_reviews} - <a href="#">0</a>');
REPLACE INTO `cw_system_messages` (`code`, `date`, `hidden`, `type`, `severity`, `message`) VALUES 
 ('incoming_messages', 1393328209, 0, 1, 'I', '{$lng.lbl_incoming_messages} - <a href="#">0</a>');
REPLACE INTO `cw_system_messages` (`code`, `date`, `hidden`, `type`, `severity`, `message`) VALUES 
 ('quote_requests', 1393328209, 0, 1, 'I', '{$lng.lbl_quote_requests} - <a href="#">0</a>');
