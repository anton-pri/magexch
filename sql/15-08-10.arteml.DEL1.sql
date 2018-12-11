DROP TABLE IF EXISTS cw_cc_gestpay_data;
ALTER TABLE `cw_map_countries` ADD `currency_code` CHAR( 3 ) NOT NULL AFTER `code_N3` ;
update cw_map_countries mc INNER JOIN cw_country_currencies cc SET mc.currency_code = cc.code WHERE mc.code=cc.country_code;
DROP TABLE IF EXISTS cw_country_currencies;
DROP TABLE IF EXISTS cw_customers_chamber_certificates;
DROP TABLE IF EXISTS cw_company_types;
ALTER TABLE `cw_customers_customer_info` DROP `company_type`;
DROP TABLE IF EXISTS `cw_customers_banks`;
DELETE FROM cw_languages WHERE name IN ('lbl_company_bank_details','lbl_abi_code','lbl_cab_code','lbl_cin_code','lbl_iban_code','lbl_swift_code');
DROP TABLE IF EXISTS `cw_customers_ccinfo`;
DROP TABLE IF EXISTS `cw_customers_contacts`;
ALTER TABLE `cw_customers_customer_info` DROP `contact_date`;
DROP TABLE IF EXISTS cw_customers_generated_docs;
DROP TABLE IF EXISTS cw_customers_letters;
DELETE FROM cw_languages WHERE name IN ('lbl_register_field_sections_letters','lbl_letters','lbl_section_contacts','lbl_contact_date','lbl_create_relation');
DROP TABLE IF EXISTS cw_customers_payment_methods;
DROP TABLE IF EXISTS cw_customers_relations;
ALTER TABLE `cw_customers_settings` COMMENT = 'Store "items per page" navigation for different targets';
DROP TABLE IF EXISTS cw_customers_taxes;
DROP TABLE IF EXISTS cw_docs_causes;
ALTER TABLE `cw_docs_info` DROP `cause_id`;
DELETE FROM cw_languages WHERE name IN ('opt_default_cause_D','opt_default_cause_G','opt_default_cause_P','lbl_causele_document_required','lbl_cause_invoice_date','lbl_cause_invoice_id');

DROP TABLE IF EXISTS cw_docs_settings;

/* Restore

-- Lang
INSERT INTO `cw_languages` VALUES ('EN','lbl_abi_code','ABI Code','','Labels'),('EN','lbl_cab_code','CAB Code','','Labels'),('EN','lbl_causele_document_required','Document required','','Labels'),('EN','lbl_cause_invoice_date','Cause date','','Labels'),('EN','lbl_cause_invoice_id','Cause invoice #','','Labels'),('EN','lbl_cin_code','CIN Code','','Labels'),('EN','lbl_company_bank_details','Company Bank Details','','Labels'),('EN','lbl_contact_date','Contact date','','Labels'),('EN','lbl_create_relation','Create relation','','Labels'),('EN','lbl_iban_code','IBAN Code','','Labels'),('EN','lbl_letters','Letters','','Labels'),('EN','lbl_register_field_sections_letters','Letters','','Labels'),('EN','lbl_section_contacts','Contacts','','Labels'),('EN','lbl_swift_code','SWIFT Code','','Labels'),('EN','opt_default_cause_D','Default cause for warehouse movement','','config'),('EN','opt_default_cause_G','Default cause for pos sale','','config'),('EN','opt_default_cause_P','Default cause for Supplier order','','config');


-- Structure backup


CREATE TABLE `cw_cc_gestpay_data` (
  `value` char(32) NOT NULL DEFAULT '',
  `type` char(1) NOT NULL DEFAULT 'C',
  PRIMARY KEY (`value`,`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `cw_country_currencies` (
  `code` char(3) NOT NULL DEFAULT '',
  `country_code` char(2) NOT NULL DEFAULT '',
  PRIMARY KEY (`code`,`country_code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `cw_customers_chamber_certificates` (
  `file_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL DEFAULT '0',
  `by_customer_id` int(11) NOT NULL DEFAULT '0',
  `file_path` varchar(255) NOT NULL DEFAULT '',
  `filename` varchar(255) NOT NULL DEFAULT '',
  `descr` mediumtext NOT NULL,
  `date` int(11) NOT NULL DEFAULT '0',
  `orderby` int(11) NOT NULL DEFAULT '0',
  `md5` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`file_id`),
  KEY `image_path` (`file_path`),
  KEY `id` (`customer_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `cw_company_types` (
  `company_type_id` varchar(8) NOT NULL DEFAULT '',
  `title` varchar(32) NOT NULL DEFAULT '',
  `social` int(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `cw_customers_customer_info` (
  `customer_id` int(11) NOT NULL DEFAULT '0',
  `department_id` int(11) NOT NULL DEFAULT '0',
  `payment_problem` int(11) NOT NULL DEFAULT '0',
  `cart` mediumtext NOT NULL,
  `web_user` int(1) NOT NULL DEFAULT '1',
  `reseller_id` varchar(16) NOT NULL DEFAULT '',
  `ssn` varchar(32) NOT NULL DEFAULT '',
  `tax_number` varchar(50) NOT NULL DEFAULT '',
  `tax_id` int(11) NOT NULL DEFAULT '0',
  `tax_exempt` int(1) NOT NULL DEFAULT '0',
  `payment_id` int(11) NOT NULL DEFAULT '0',
  `payment_note` mediumtext NOT NULL,
  `separate_invoices` mediumtext NOT NULL,
  `company_id` int(11) NOT NULL DEFAULT '0',
  `can_change_company_id` int(1) NOT NULL DEFAULT '0',
  `chamber_certificate` varchar(255) NOT NULL DEFAULT '',
  `birthday` int(11) NOT NULL DEFAULT '0',
  `birthday_place` varchar(64) NOT NULL DEFAULT '',
  `sex` int(1) NOT NULL DEFAULT '0',
  `married` int(1) NOT NULL DEFAULT '0',
  `nationality` varchar(64) NOT NULL DEFAULT '',
  `company` varchar(64) NOT NULL DEFAULT '',
  `employees` int(11) NOT NULL DEFAULT '0',
  `foundation` varchar(16) NOT NULL DEFAULT '',
  `foundation_place` varchar(64) NOT NULL DEFAULT '',
  `company_type` varchar(64) NOT NULL DEFAULT '',
  `contact_date` int(11) NOT NULL DEFAULT '0',
  `shipping_company_from_carrier_id` int(11) NOT NULL DEFAULT '0',
  `shipping_company_to_carrier_id` int(11) NOT NULL DEFAULT '0',
  `shipment_paid` int(11) NOT NULL DEFAULT '0',
  `shipping_operated` int(11) NOT NULL DEFAULT '0',
  `cod_delivery_type_id` int(11) NOT NULL DEFAULT '0',
  `doc_prefix` varchar(8) NOT NULL DEFAULT '',
  `order_entering_format` int(1) NOT NULL DEFAULT '0',
  `division_id` int(11) NOT NULL DEFAULT '0',
  `status_note` text COMMENT 'additional note',
  PRIMARY KEY (`customer_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `cw_customers_banks` (
  `bank_id` int(11) NOT NULL AUTO_INCREMENT,
  `id` int(11) NOT NULL DEFAULT '0',
  `account_number` varchar(255) NOT NULL DEFAULT '',
  `abi_code` varchar(32) NOT NULL DEFAULT '',
  `cab_code` varchar(32) NOT NULL DEFAULT '',
  `cin_code` varchar(32) NOT NULL DEFAULT '',
  `iban_code` varchar(32) NOT NULL DEFAULT '',
  `swift_code` varchar(32) NOT NULL DEFAULT '',
  `bank_name` varchar(255) NOT NULL DEFAULT '',
  `address` varchar(255) NOT NULL DEFAULT '',
  `city` varchar(255) NOT NULL DEFAULT '',
  `country` varchar(2) NOT NULL DEFAULT '',
  `state` varchar(32) NOT NULL DEFAULT '',
  `phone` varchar(32) NOT NULL DEFAULT '',
  `fax` varchar(32) NOT NULL DEFAULT '',
  `contact` varchar(128) NOT NULL DEFAULT '',
  `fill_auto` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`bank_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `cw_customers_ccinfo` (
  `customer_id` int(11) NOT NULL DEFAULT '0',
  `card_name` varchar(255) NOT NULL DEFAULT '',
  `card_type` varchar(16) NOT NULL DEFAULT '',
  `card_number` varchar(128) NOT NULL DEFAULT '',
  `card_expire` varchar(4) NOT NULL DEFAULT '',
  `card_cvv2` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`customer_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `cw_customers_contacts` (
  `contact_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL DEFAULT '0',
  `date` int(11) NOT NULL DEFAULT '0',
  `content` mediumtext NOT NULL,
  PRIMARY KEY (`contact_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `cw_customers_generated_docs` (
  `file_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL DEFAULT '0',
  `by_customer_id` int(11) NOT NULL DEFAULT '0',
  `file_path` varchar(255) NOT NULL DEFAULT '',
  `filename` varchar(255) NOT NULL DEFAULT '',
  `descr` mediumtext NOT NULL,
  `date` int(11) NOT NULL DEFAULT '0',
  `orderby` int(11) NOT NULL DEFAULT '0',
  `md5` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`file_id`),
  KEY `image_path` (`file_path`),
  KEY `id` (`customer_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `cw_customers_payment_methods` (
  `customer_payment_method_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL DEFAULT '0',
  `payment_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`customer_payment_method_id`),
  KEY `customer_id` (`customer_id`),
  KEY `payment_id` (`payment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `cw_customers_relations` (
  `customer_id` int(11) NOT NULL DEFAULT '0',
  `salesman_customer_id` int(11) NOT NULL DEFAULT '0',
  `employee_customer_id` int(11) NOT NULL DEFAULT '0',
  `warehouse_customer_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`customer_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `cw_customers_taxes` (
  `tax_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(64) NOT NULL DEFAULT '',
  `value` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `orderby` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`tax_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `cw_docs_causes` (
  `doc_info_id` int(11) NOT NULL DEFAULT '0',
  `invoice_id` varchar(32) NOT NULL DEFAULT '',
  `invoice_date` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`doc_info_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `cw_docs_info` (
  `doc_info_id` int(11) NOT NULL AUTO_INCREMENT,
  `warehouse_customer_id` int(11) NOT NULL DEFAULT '0',
  `salesman_customer_id` int(11) NOT NULL DEFAULT '0',
  `cause_id` int(11) NOT NULL DEFAULT '0',
  `shipping_cause_id` varchar(128) NOT NULL DEFAULT '',
  `customer_notes` mediumtext NOT NULL,
  `details` mediumtext NOT NULL,
  `ship_time` varchar(64) NOT NULL DEFAULT '',
  `tracking` varchar(64) NOT NULL DEFAULT '',
  `notes` mediumtext NOT NULL,
  `payment_id` int(11) NOT NULL DEFAULT '0',
  `payment_label` varchar(255) NOT NULL DEFAULT '',
  `shipping_id` int(11) NOT NULL DEFAULT '0',
  `shipping_label` varchar(255) NOT NULL DEFAULT '',
  `cod_type_id` int(11) NOT NULL DEFAULT '0',
  `cod_type_label` varchar(64) NOT NULL DEFAULT '',
  `cod_leaving_type` int(2) NOT NULL DEFAULT '0',
  `subtotal` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `display_subtotal` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `discounted_subtotal` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `display_discounted_subtotal` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `discount` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `discount_value` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `giftcert_discount` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `giftcert_ids` text NOT NULL,
  `coupon` varchar(32) NOT NULL DEFAULT '',
  `coupon_discount` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `shipping_cost` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `display_shipping_cost` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `weight` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `shipping_insurance` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `payment_surcharge` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `tax` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `applied_taxes` mediumtext NOT NULL,
  `total` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `display_total` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `extra` mediumtext NOT NULL,
  `box_number` int(11) NOT NULL DEFAULT '0',
  `shipment_paid` int(11) NOT NULL DEFAULT '0',
  `pickup_date` int(11) NOT NULL DEFAULT '0',
  `aspect_id` varchar(128) NOT NULL DEFAULT '',
  `layout_id` int(11) NOT NULL DEFAULT '0',
  `expiration_date` int(11) NOT NULL,
  PRIMARY KEY (`doc_info_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `cw_docs_settings` (
  `doc_info_id` int(11) NOT NULL DEFAULT '0',
  `show_price` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`doc_info_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
*/
