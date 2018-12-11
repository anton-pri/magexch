-- MySQL dump 10.13  Distrib 5.5.52, for Linux (x86_64)
--
-- Host: localhost    Database: saratdev_cw
-- ------------------------------------------------------
-- Server version	5.5.52-cll

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
-- Table structure for table `cw_datahub_import_buffer`
--

DROP TABLE IF EXISTS `cw_datahub_import_buffer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cw_datahub_import_buffer` (
  `table_id` int(11) NOT NULL AUTO_INCREMENT,
  `item_xref` varchar(50) NOT NULL DEFAULT '',
  `Source` varchar(50) DEFAULT NULL,
  `wholesaler` varchar(50) DEFAULT NULL,
  `Wine` varchar(255) DEFAULT NULL,
  `Producer` varchar(255) DEFAULT NULL,
  `Name` varchar(255) DEFAULT NULL,
  `Vintage` varchar(50) DEFAULT NULL,
  `size` varchar(50) DEFAULT NULL,
  `ITEMID` varchar(50) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `Region` varchar(50) DEFAULT NULL,
  `varietal` varchar(50) DEFAULT NULL,
  `Appellation` varchar(50) DEFAULT NULL,
  `sub-appellation` varchar(50) DEFAULT NULL,
  `Parker_rating` varchar(50) DEFAULT NULL,
  `Parker_review` mediumtext,
  `Spectator_rating` varchar(50) DEFAULT NULL,
  `Spectator_review` mediumtext,
  `Tanzer_rating` varchar(50) DEFAULT NULL,
  `Tanzer_review` mediumtext,
  `W&S_rating` varchar(50) DEFAULT NULL,
  `W&S_review` mediumtext,
  `Description` mediumtext,
  `store_id` int(11) DEFAULT NULL,
  `qty_in_stock` int(11) DEFAULT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `feed_short_name` varchar(10) DEFAULT NULL,
  `item_xref_qty_avail` int(11) DEFAULT NULL,
  `item_xref_min_price` decimal(19,2) NOT NULL DEFAULT '0.00',
  `item_xref_bot_per_case` int(11) DEFAULT NULL,
  `item_xref_cost_per_case` decimal(19,2) NOT NULL DEFAULT '0.00',
  `item_xref_cost_per_bottle` decimal(19,2) NOT NULL DEFAULT '0.00',
  `split_case_charge` decimal(19,4) NOT NULL DEFAULT '0.0000',
  `avail_code` int(11) NOT NULL DEFAULT '0',
  `manual_price` decimal(19,2) NOT NULL DEFAULT '0.00',
  `twelve_bot_manual_price` decimal(19,2) NOT NULL DEFAULT '0.00',
  `store_sku` varchar(255) NOT NULL DEFAULT '',
  `competitor_site` varchar(255) NOT NULL DEFAULT '',
  `competitor_price` decimal(19,2) NOT NULL DEFAULT '0.00',
  `Match Items` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`table_id`),
  KEY `supplier_id` (`supplier_id`),
  KEY `ib_item_xref` (`item_xref`)
) ENGINE=MyISAM AUTO_INCREMENT=429853 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cw_datahub_main_data`
--

DROP TABLE IF EXISTS `cw_datahub_main_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cw_datahub_main_data` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `catalog_id` int(11) NOT NULL DEFAULT '0',
  `dup_catid` int(11) DEFAULT '0',
  `Producer` varchar(50) DEFAULT NULL,
  `name` varchar(225) DEFAULT NULL,
  `Vintage` varchar(50) DEFAULT NULL,
  `Size` varchar(50) DEFAULT NULL,
  `cimageurl` int(11) NOT NULL DEFAULT '0',
  `TareWeight` double DEFAULT '4',
  `LongDesc` text,
  `Region` varchar(50) DEFAULT NULL,
  `drysweet` varchar(50) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `keywords` varchar(225) DEFAULT NULL,
  `varietal` varchar(50) DEFAULT NULL,
  `Appellation` varchar(50) DEFAULT NULL,
  `sub_appellation` varchar(50) DEFAULT NULL,
  `RP_Rating` int(11) NOT NULL DEFAULT '0',
  `RP_Review` text,
  `WS_Rating` int(11) NOT NULL DEFAULT '0',
  `WS_Review` text,
  `WE_Rating` int(11) NOT NULL DEFAULT '0',
  `WE_Review` text,
  `DC_Rating` int(11) NOT NULL DEFAULT '0',
  `DC_Review` text,
  `ST_Rating` int(11) NOT NULL DEFAULT '0',
  `ST_Review` text,
  `W_S_Rating` int(11) NOT NULL DEFAULT '0',
  `W_S_Review` text,
  `BTI_Rating` int(11) NOT NULL DEFAULT '0',
  `BTI_Review` text,
  `Winery_Rating` int(11) NOT NULL DEFAULT '0',
  `CG_Rating` int(11) NOT NULL DEFAULT '0',
  `CG_Review` text NOT NULL,
  `JH_Rating` int(11) NOT NULL DEFAULT '0',
  `JH_Review` text NOT NULL,
  `MJ_Rating` int(11) NOT NULL DEFAULT '0',
  `MJ_Review` text NOT NULL,
  `TWN_Rating` int(11) NOT NULL DEFAULT '0',
  `TWN_Review` text NOT NULL,
  `Winery_Review` text,
  `bot_per_case` int(11) DEFAULT '12',
  `initial_xref` varchar(50) DEFAULT NULL,
  `price` decimal(19,2) NOT NULL DEFAULT '0.00',
  `twelve_bot_price` decimal(19,2) NOT NULL DEFAULT '0.00',
  `cost` decimal(19,2) NOT NULL DEFAULT '0.00',
  `cost_per_case` decimal(19,2) NOT NULL DEFAULT '0.00',
  `stock` int(11) NOT NULL DEFAULT '0',
  `store_stock` int(11) NOT NULL DEFAULT '0',
  `split_case_charge` decimal(19,4) NOT NULL DEFAULT '0.0000',
  `manual_price` decimal(19,2) NOT NULL DEFAULT '0.00',
  `twelve_bot_manual_price` decimal(19,2) NOT NULL DEFAULT '0.00',
  `supplier_id` int(11) NOT NULL DEFAULT '0',
  `min_price` decimal(19,2) NOT NULL DEFAULT '0.00',
  `weight` decimal(19,2) NOT NULL DEFAULT '0.00',
  `avail_code` int(11) NOT NULL DEFAULT '1',
  `hide` int(1) NOT NULL DEFAULT '0',
  `store_sku` varchar(255) NOT NULL DEFAULT '',
  `minimumquantity` int(11) NOT NULL DEFAULT '0',
  `meta_description` text NOT NULL,
  `Source` varchar(50) DEFAULT NULL,
  `wholesaler` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`ID`),
  KEY `Producer` (`Producer`),
  KEY `name` (`name`),
  KEY `dup_catid` (`dup_catid`),
  KEY `initial_xref` (`initial_xref`),
  KEY `i_name` (`name`),
  KEY `i_producer` (`Producer`),
  KEY `i_vintage` (`Vintage`),
  KEY `i_region` (`Region`),
  KEY `i_country` (`country`),
  KEY `dmd_cimageurl` (`cimageurl`)
) ENGINE=MyISAM AUTO_INCREMENT=787378 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cw_datahub_import_buffer_blacklist`
--

DROP TABLE IF EXISTS `cw_datahub_import_buffer_blacklist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cw_datahub_import_buffer_blacklist` (
  `table_id` int(11) NOT NULL AUTO_INCREMENT,
  `item_xref` varchar(50) NOT NULL DEFAULT '',
  `Source` varchar(50) DEFAULT NULL,
  `wholesaler` varchar(50) DEFAULT NULL,
  `Wine` varchar(255) DEFAULT NULL,
  `Producer` varchar(255) DEFAULT NULL,
  `Name` varchar(255) DEFAULT NULL,
  `Vintage` varchar(50) DEFAULT NULL,
  `size` varchar(50) DEFAULT NULL,
  `ITEMID` varchar(50) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `Region` varchar(50) DEFAULT NULL,
  `varietal` varchar(50) DEFAULT NULL,
  `Appellation` varchar(50) DEFAULT NULL,
  `sub-appellation` varchar(50) DEFAULT NULL,
  `Parker_rating` varchar(50) DEFAULT NULL,
  `Parker_review` mediumtext,
  `Spectator_rating` varchar(50) DEFAULT NULL,
  `Spectator_review` mediumtext,
  `Tanzer_rating` varchar(50) DEFAULT NULL,
  `Tanzer_review` mediumtext,
  `W&S_rating` varchar(50) DEFAULT NULL,
  `W&S_review` mediumtext,
  `Description` mediumtext,
  `store_id` int(11) DEFAULT NULL,
  `qty_in_stock` int(11) DEFAULT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `feed_short_name` varchar(10) DEFAULT NULL,
  `item_xref_qty_avail` int(11) DEFAULT NULL,
  `item_xref_min_price` decimal(19,2) NOT NULL DEFAULT '0.00',
  `item_xref_bot_per_case` int(11) DEFAULT NULL,
  `item_xref_cost_per_case` decimal(19,2) NOT NULL DEFAULT '0.00',
  `item_xref_cost_per_bottle` decimal(19,2) NOT NULL DEFAULT '0.00',
  `split_case_charge` decimal(19,4) NOT NULL DEFAULT '0.0000',
  `avail_code` int(11) NOT NULL DEFAULT '0',
  `manual_price` decimal(19,2) NOT NULL DEFAULT '0.00',
  `twelve_bot_manual_price` decimal(19,2) NOT NULL DEFAULT '0.00',
  `store_sku` varchar(255) NOT NULL DEFAULT '',
  `competitor_site` varchar(255) NOT NULL DEFAULT '',
  `competitor_price` decimal(19,2) NOT NULL DEFAULT '0.00',
  `Match Items` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`table_id`),
  KEY `supplier_id` (`supplier_id`),
  KEY `ib_item_xref` (`item_xref`)
) ENGINE=MyISAM AUTO_INCREMENT=509852 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-11-01  3:54:56
