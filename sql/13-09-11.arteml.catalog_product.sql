-- Remove not important items from main menu
delete from cw_navigation_menu where title='lbl_breadcrumbs';
delete from cw_navigation_menu where title='lbl_stop_list';
update cw_navigation_menu set addon='estore_products_review' where addon='EStoreProductsReview';

-- Add Stop List under reviews management
INSERT INTO `cw_navigation_sections` (`title`, `link`, `addon`, `access_level`, `area`, `main`, `orderby`, `skins_subdir`)
VALUES ('lbl_reviews_management', 'index.php?target=estore_reviews_management', 'estore_products_review', '', 'A', 'N', 10, '');
SET @section_id = LAST_INSERT_ID();

INSERT INTO `cw_navigation_tabs` (`access_level`, `title`, `link`, `orderby`)
VALUES ('', 'lbl_reviews_management', 'index.php?target=estore_reviews_management', 0);
SET @tab_id = LAST_INSERT_ID();

INSERT INTO `cw_navigation_targets` (`target`, `params`, `visible`, `section_id`, `tab_id`, `orderby`, `addon`)
VALUES ('estore_reviews_management', '', '', @section_id, @tab_id, 0, 'estore_products_review');

INSERT INTO `cw_navigation_tabs` (`access_level`, `title`, `link`, `orderby`)
VALUES ('', 'lbl_stop_list', 'index.php?target=estore_stop_list', 10);
SET @tab_id = LAST_INSERT_ID();

INSERT INTO `cw_navigation_targets` (`target`, `params`, `visible`, `section_id`, `tab_id`, `orderby`, `addon`)
VALUES ('estore_stop_list', '', '', @section_id, @tab_id, 10, 'estore_products_review');

-- rename general product type from Usual_product
update cw_products_types set title='General' where product_type_id=1;

-- Drop unused tables
/*
CREATE TABLE `cw_localizations` (
  `localization_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`localization_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
*/

drop table if exists cw_localizations;

/*
CREATE TABLE `cw_tnt_files` (
  `file_id` int(11) NOT NULL AUTO_INCREMENT,
  `id` int(11) NOT NULL DEFAULT '0',
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
*/

drop table if exists cw_tnt_files;

/*
CREATE TABLE `cw_fedex_rates` (
  `r_id` int(11) NOT NULL AUTO_INCREMENT,
  `r_zone` varchar(6) NOT NULL DEFAULT '',
  `r_weight` varchar(255) NOT NULL DEFAULT '0',
  `r_meth_id` int(11) NOT NULL DEFAULT '0',
  `r_rate` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `r_ishundreds` int(1) NOT NULL DEFAULT '0',
  `r_container` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`r_id`),
  KEY `r_zone` (`r_zone`),
  KEY `r_meth_id` (`r_meth_id`),
  KEY `r_rate` (`r_rate`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;




CREATE TABLE `cw_fedex_zips` (
  `zip_id` int(11) NOT NULL AUTO_INCREMENT,
  `zip_first` varchar(5) NOT NULL DEFAULT '000',
  `zip_last` varchar(5) NOT NULL DEFAULT '',
  `zip_zone` varchar(6) NOT NULL DEFAULT '',
  `zip_meth` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`zip_id`),
  KEY `zip_first` (`zip_first`),
  KEY `zip_last` (`zip_last`),
  KEY `zip_zone` (`zip_zone`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
*/

drop table if exists cw_fedex_rates;
drop table if exists cw_fedex_zips;
