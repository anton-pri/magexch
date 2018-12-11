UPDATE `cw_menu` SET `addon` = 'ebay' WHERE `cw_menu`.`target` = 'ebay_export';
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'txt_delete_selected_objects', 'Are you sure you want to delete the selected items?', '', 'Text');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'warning_email_exists', 'You\'ve already placed an order with us. You can login to automatically enter your address details or leave password empty to continue anonymous purchase.', '', 'Errors');


