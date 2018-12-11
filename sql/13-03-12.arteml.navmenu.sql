INSERT INTO `ars_languages` (
`code` ,
`name` ,
`value` ,
`topic`
)
VALUES (
'EN', 'lbl_catalog', 'Catalog', 'Labels'
);
INSERT INTO `ars_languages` (
`code` ,
`name` ,
`value` ,
`topic`
)
VALUES (
'EN', 'lbl_shipping_and_taxes', 'Shipping &amp; Taxes', 'Labels'
);

-- MySQL dump 10.13  Distrib 5.5.29, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: cart
-- ------------------------------------------------------
-- Server version	5.5.29-0ubuntu0.12.10.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `ars_navigation_menu`
--

DROP TABLE IF EXISTS `ars_navigation_menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ars_navigation_menu` (
  `menu_id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_menu_id` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  `link` varchar(255) NOT NULL DEFAULT '',
  `orderby` int(11) NOT NULL DEFAULT '0',
  `area` char(1) NOT NULL DEFAULT '',
  `access_level` varchar(16) NOT NULL DEFAULT '',
  `module` varchar(255) NOT NULL DEFAULT '',
  `is_loggedin` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`menu_id`)
) ENGINE=MyISAM AUTO_INCREMENT=135 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ars_navigation_menu`
--

LOCK TABLES `ars_navigation_menu` WRITE;
/*!40000 ALTER TABLE `ars_navigation_menu` DISABLE KEYS */;
INSERT INTO `ars_navigation_menu` VALUES (36,3,'lbl_domains','index.php?target=domains',360,'A','','multi-domains',1),(34,7,'lbl_sessions','index.php?target=sessions',340,'A','','',1),(33,7,'lbl_sitemap','index.php?target=sitemap_xml',330,'A','','Sitemap_XML',1),(32,7,'lbl_edit_templates','index.php?target=file_edit',320,'A','','',1),(31,7,'lbl_db_backup_restore','index.php?target=db_backup',310,'A','','',1),(30,7,'lbl_shop_logs','index.php?target=logs',300,'A','','',1),(29,7,'lbl_export','index.php?target=import&mode=expdata',290,'A','','',1),(110,123,'lbl_users_C','index.php?target=user_C',0,'B','','',1),(111,123,'lbl_docs_info_B','index.php?target=docs_B',10,'B','','',1),(112,123,'lbl_docs_info_O','index.php?target=docs_O',20,'B','','',1),(113,123,'lbl_docs_info_I','index.php?target=docs_I',30,'B','','',1),(114,123,'lbl_docs_info_S','index.php?target=docs_S',40,'B','','',1),(115,123,'lbl_section_banners','index.php?target=products',50,'B','','',1),(116,123,'lbl_section_appointments','index.php?target=affiliates',60,'B','','',1),(117,124,'lbl_section_movements','',0,'P','10','',1),(118,124,'lbl_section_anagraphic','index.php?target=products',10,'P','11','',1),(119,124,'lbl_section_orders','index.php?target=docs_O',20,'P','12','',1),(120,124,'lbl_section_invoices','index.php?target=docs_I',30,'P','13','',1),(121,124,'lbl_section_shipment_docs','',40,'P','14','',1),(123,0,'lbl_sections','index.php',0,'B','','',1),(124,0,'lbl_sections','index.php',0,'P','','',1),(125,0,'lbl_start_new_order','index.php?target=orders&action=add',0,'G','1000','',1),(126,0,'lbl_printer_function','index.php?target=printer_functions',0,'G','11','',1),(127,0,'lbl_general_settings','#',0,'P','15','',1),(128,127,'lbl_shipping_zones','index.php?target=zones',10,'P','1500','',1),(129,127,'lbl_shipping_rates','index.php?target=shipping',20,'P','1501','',1),(130,0,'lbl_help','index.php?target=help',0,'P','','',1),(131,0,'lbl_general_settings','#',0,'B','','',1),(132,131,'lbl_registration_links','index.php?target=registration_links',10,'B','','',1),(133,131,'lbl_discounts','index.php?target=discounts',20,'B','','',1),(134,0,'lbl_help','index.php?target=help',0,'B','','',1),(28,7,'lbl_import','index.php?target=import&mode=impdata',280,'A','','',1),(27,6,'lbl_taxes','index.php?target=taxes',270,'A','','',1),(26,6,'lbl_shipping_methods','index.php?target=shipping',260,'A','','shipping-system',1),(25,5,'lbl_speed_bar','index.php?target=speed_bar',250,'A','','',1),(24,5,'lbl_languages','index.php?target=languages',240,'A','','',1),(22,5,'lbl_banners','index.php?target=ad_banners&mode=list',220,'A','','Ad_Banners',1),(21,5,'lbl_customer_side_news','index.php?target=news_c',210,'A','','news',1),(20,5,'lbl_news_management','index.php?target=news',200,'A','','news',1),(19,5,'lbl_static_pages','index.php?target=pages',190,'A','','',1),(18,4,'lbl_memberships','index.php?target=memberships',180,'A','','',1),(17,4,'lbl_users_A','index.php?target=user_A',170,'A','','',1),(16,4,'lbl_users_C','index.php?target=user_C',160,'A','','',1),(15,3,'lbl_products','index.php?target=products',150,'A','','',1),(14,3,'lbl_categories','index.php?target=categories',140,'A','','',1),(13,2,'lbl_invoices','index.php?target=doc_I',130,'A','','',1),(12,2,'lbl_gift_certificates','index.php?target=giftcerts',120,'A','','EStoreGift',1),(10,2,'lbl_orders','index.php?target=doc_O',100,'A','','',1),(9,0,'lbl_help','',90,'A','','',1),(8,0,'lbl_settings','index.php?target=settings',80,'A','','',1),(7,0,'lbl_tools','',70,'A','','',1),(5,0,'lbl_content','',50,'A','','',1),(6,0,'lbl_shipping_and_taxes','index.php?target=shipping',60,'A','','',1),(4,0,'lbl_users','index.php?target=user_C',40,'A','','',1),(3,0,'lbl_catalog','index.php?target=products',30,'A','','',1),(2,0,'lbl_orders','index.php?target=docs_O',20,'A','','',1),(37,7,'lbl_google_base','index.php?target=google_base',292,'A','','GoogleBase',1),(38,7,'lbl_amazon_export','index.php?target=amazon_export',294,'A','','Amazon',1),(39,7,'lbl_ebay_export','index.php?target=ebay_export',296,'A','','Ebay',1),(40,8,'lbl_general_settings','index.php?target=settings',400,'A','','',1),(41,8,'lbl_modules','index.php?target=configuration',410,'A','','',1),(42,8,'lbl_payment_methods','index.php?target=payments&mode=methods',420,'A','','payment-system',1),(43,8,'lbl_dashboard','index.php?target=dashboard',430,'A','','dashboard',1),(44,8,'lbl_search_engine_settings','index.php?target=meta_tags',440,'A','','',1),(45,4,'lbl_profile_options','index.php?target=user_profiles',450,'A','','',1),(46,8,'lbl_countries_and_states','index.php?target=countries',460,'A','','',1),(47,5,'lbl_sections_position','index.php?target=sections_pos',470,'A','','',1);
/*!40000 ALTER TABLE `ars_navigation_menu` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-03-12 17:09:37
