-- MySQL dump 10.13  Distrib 5.5.50, for Linux (x86_64)
--
-- Host: localhost    Database: devsarat_cw
-- ------------------------------------------------------
-- Server version	5.5.50-cll

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
-- Table structure for table `cw_datahub_BevAccessFeeds`
--

DROP TABLE IF EXISTS `cw_datahub_BevAccessFeeds`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cw_datahub_BevAccessFeeds` (
  `table_id` int(11) NOT NULL AUTO_INCREMENT,
  `xref` varchar(50) NOT NULL,
  `univ_prod` varchar(255) DEFAULT NULL,
  `size` int(11) DEFAULT NULL,
  `vintage` varchar(255) DEFAULT NULL,
  `prod_id` varchar(255) DEFAULT NULL,
  `companies` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `bdesc` varchar(255) DEFAULT NULL,
  `case_price` double DEFAULT NULL,
  `bot_price` double DEFAULT NULL,
  `descriptio` varchar(255) DEFAULT NULL,
  `univ_cat` varchar(255) DEFAULT NULL,
  `reg_id` int(11) DEFAULT '0',
  `truevint` varchar(255) DEFAULT NULL,
  `use_vint` varchar(50) DEFAULT NULL,
  `grape` varchar(255) DEFAULT NULL,
  `kosher` varchar(255) DEFAULT NULL,
  `organic` varchar(255) DEFAULT NULL,
  `prod_type` varchar(50) DEFAULT NULL,
  `importer` varchar(255) DEFAULT NULL,
  `cat_id` int(11) DEFAULT '0',
  `type_id` int(11) DEFAULT '0',
  `rev` int(11) DEFAULT '0',
  `des` int(11) DEFAULT '0',
  `wmn` int(11) DEFAULT '0',
  `rat` int(11) DEFAULT '0',
  `fpr` int(11) DEFAULT '0',
  `tek` int(11) DEFAULT '0',
  `rec` int(11) DEFAULT '0',
  `txt` int(11) DEFAULT '0',
  `tas` int(11) DEFAULT '0',
  `lab` int(11) DEFAULT '0',
  `bot` int(11) DEFAULT '0',
  `pho` int(11) DEFAULT '0',
  `log` int(11) DEFAULT '0',
  `oth` int(11) DEFAULT '0',
  `lwbn` varchar(255) DEFAULT NULL,
  `producer` varchar(255) DEFAULT NULL,
  `cat_type` varchar(255) DEFAULT NULL,
  `reg_text` varchar(255) DEFAULT NULL,
  `sparkling` varchar(255) DEFAULT NULL,
  `rp` int(11) DEFAULT '0',
  `we` varchar(255) DEFAULT NULL,
  `ws` varchar(255) DEFAULT NULL,
  `current` varchar(255) DEFAULT NULL,
  `fortified` varchar(255) DEFAULT NULL,
  `dessert` varchar(255) DEFAULT NULL,
  `closure` varchar(255) DEFAULT NULL,
  `pack` int(11) DEFAULT '0',
  `packaging` varchar(255) DEFAULT NULL,
  `packtype` varchar(255) DEFAULT NULL,
  `skus` varchar(255) DEFAULT NULL,
  `syn` varchar(255) DEFAULT NULL,
  `tstamp` varchar(255) DEFAULT NULL,
  `confstock` varchar(255) DEFAULT NULL,
  `whvint` varchar(255) DEFAULT NULL,
  `apc` varchar(255) DEFAULT NULL,
  `bestbot` double DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `botpercase` int(11) DEFAULT NULL,
  `secpack` int(11) DEFAULT NULL,
  `wholesaler` varchar(255) DEFAULT NULL,
  `prod_item` varchar(255) DEFAULT NULL,
  `upc` varchar(255) DEFAULT NULL,
  `front_nyc` double DEFAULT NULL,
  `postoff` double DEFAULT NULL,
  `spec_price` varchar(255) DEFAULT NULL,
  `ripcode` varchar(255) DEFAULT NULL,
  `qty1` int(11) DEFAULT NULL,
  `d_type1` varchar(255) DEFAULT NULL,
  `discount1` double DEFAULT NULL,
  `qty2` int(11) DEFAULT NULL,
  `d_type2` varchar(255) DEFAULT NULL,
  `discount2` double DEFAULT NULL,
  `qty3` int(11) DEFAULT NULL,
  `d_type3` varchar(255) DEFAULT NULL,
  `discount3` double DEFAULT NULL,
  `qty4` int(11) DEFAULT NULL,
  `d_type4` varchar(255) DEFAULT NULL,
  `discount4` double DEFAULT NULL,
  `qty5` int(11) DEFAULT NULL,
  `d_type5` varchar(255) DEFAULT NULL,
  `discount5` double DEFAULT NULL,
  `qty6` int(11) DEFAULT NULL,
  `d_type6` varchar(255) DEFAULT NULL,
  `discount6` double DEFAULT NULL,
  `qty7` int(11) DEFAULT NULL,
  `d_type7` varchar(255) DEFAULT NULL,
  `discount7` double DEFAULT NULL,
  `qty8` int(11) DEFAULT NULL,
  `d_type8` varchar(255) DEFAULT NULL,
  `discount8` double DEFAULT NULL,
  `qty9` int(11) DEFAULT NULL,
  `d_type9` varchar(255) DEFAULT NULL,
  `discount9` double DEFAULT NULL,
  `div1` varchar(255) DEFAULT NULL,
  `div2` varchar(255) DEFAULT NULL,
  `div3` varchar(255) DEFAULT NULL,
  `div4` varchar(255) DEFAULT NULL,
  `div5` varchar(255) DEFAULT NULL,
  `div6` varchar(255) DEFAULT NULL,
  `div7` varchar(255) DEFAULT NULL,
  `div8` varchar(255) DEFAULT NULL,
  `div9` varchar(255) DEFAULT NULL,
  `div10` varchar(255) DEFAULT NULL,
  `div11` varchar(255) DEFAULT NULL,
  `div12` varchar(255) DEFAULT NULL,
  `asst_size` varchar(255) DEFAULT NULL,
  `productid` varchar(255) DEFAULT NULL,
  `deposit` double DEFAULT NULL,
  `cale_shelf` double DEFAULT NULL,
  `whole_desc` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`table_id`),
  KEY `prod_item` (`prod_item`),
  KEY `xref` (`xref`),
  KEY `size` (`size`),
  KEY `vintage` (`vintage`),
  KEY `botpercase` (`botpercase`),
  KEY `skus` (`skus`)
) ENGINE=MyISAM AUTO_INCREMENT=3034413 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cw_datahub_beva_UP_VPR`
--

DROP TABLE IF EXISTS `cw_datahub_beva_UP_VPR`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cw_datahub_beva_UP_VPR` (
  `table_id` int(11) NOT NULL AUTO_INCREMENT,
  `univ_prod` varchar(255) DEFAULT NULL,
  `xref` varchar(50) DEFAULT NULL,
  `bdesc` varchar(255) DEFAULT NULL,
  `descriptio` varchar(255) DEFAULT NULL,
  `size` int(11) DEFAULT NULL,
  `vintage` varchar(255) DEFAULT NULL,
  `univ_cat` varchar(255) DEFAULT NULL,
  `lwbn` varchar(255) DEFAULT NULL,
  `apc` varchar(255) DEFAULT NULL,
  `bestbot` double DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `botpercase` int(11) DEFAULT NULL,
  `secpack` int(11) DEFAULT NULL,
  `wholesaler` varchar(255) DEFAULT NULL,
  `prod_item` varchar(255) DEFAULT NULL,
  `upc` varchar(255) DEFAULT NULL,
  `case_price` double DEFAULT NULL,
  `bot_price` double DEFAULT NULL,
  `front_nyc` double DEFAULT NULL,
  `postoff` double DEFAULT NULL,
  `spec_price` varchar(255) DEFAULT NULL,
  `ripcode` varchar(255) DEFAULT NULL,
  `qty1` int(11) DEFAULT NULL,
  `d_type1` varchar(255) DEFAULT NULL,
  `discount1` double DEFAULT NULL,
  `qty2` int(11) DEFAULT NULL,
  `d_type2` varchar(255) DEFAULT NULL,
  `discount2` double DEFAULT NULL,
  `qty3` int(11) DEFAULT NULL,
  `d_type3` varchar(255) DEFAULT NULL,
  `discount3` double DEFAULT NULL,
  `qty4` int(11) DEFAULT NULL,
  `d_type4` varchar(255) DEFAULT NULL,
  `discount4` double DEFAULT NULL,
  `qty5` int(11) DEFAULT NULL,
  `d_type5` varchar(255) DEFAULT NULL,
  `discount5` double DEFAULT NULL,
  `qty6` int(11) DEFAULT NULL,
  `d_type6` varchar(255) DEFAULT NULL,
  `discount6` double DEFAULT NULL,
  `qty7` int(11) DEFAULT NULL,
  `d_type7` varchar(255) DEFAULT NULL,
  `discount7` double DEFAULT NULL,
  `qty8` int(11) DEFAULT NULL,
  `d_type8` varchar(255) DEFAULT NULL,
  `discount8` double DEFAULT NULL,
  `qty9` int(11) DEFAULT NULL,
  `d_type9` varchar(255) DEFAULT NULL,
  `discount9` double DEFAULT NULL,
  `div1` varchar(255) DEFAULT NULL,
  `div2` varchar(255) DEFAULT NULL,
  `div3` varchar(255) DEFAULT NULL,
  `div4` varchar(255) DEFAULT NULL,
  `div5` varchar(255) DEFAULT NULL,
  `div6` varchar(255) DEFAULT NULL,
  `div7` varchar(255) DEFAULT NULL,
  `div8` varchar(255) DEFAULT NULL,
  `div9` varchar(255) DEFAULT NULL,
  `div10` varchar(255) DEFAULT NULL,
  `div11` varchar(255) DEFAULT NULL,
  `div12` varchar(255) DEFAULT NULL,
  `asst_size` varchar(255) DEFAULT NULL,
  `organic` varchar(255) DEFAULT NULL,
  `kosher` varchar(255) DEFAULT NULL,
  `sparkling` varchar(255) DEFAULT NULL,
  `productid` varchar(255) DEFAULT NULL,
  `deposit` double DEFAULT NULL,
  `cale_shelf` double DEFAULT NULL,
  `truevint` varchar(255) DEFAULT NULL,
  `prod_id` varchar(255) DEFAULT NULL,
  `whole_desc` varchar(255) DEFAULT NULL,
  `producer` varchar(255) DEFAULT NULL,
  `Fullcase` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`table_id`),
  KEY `xref` (`xref`),
  KEY `prod_id` (`prod_id`),
  KEY `prod_item` (`prod_item`)
) ENGINE=MyISAM AUTO_INCREMENT=3660919 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cw_datahub_beva_UP_prod`
--

DROP TABLE IF EXISTS `cw_datahub_beva_UP_prod`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cw_datahub_beva_UP_prod` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `xref` varchar(50) DEFAULT NULL,
  `univ_prod` varchar(255) DEFAULT NULL,
  `size` double DEFAULT NULL,
  `vintage` varchar(255) DEFAULT NULL,
  `prod_id` varchar(255) DEFAULT NULL,
  `companies` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `bdesc` varchar(255) DEFAULT NULL,
  `case` double DEFAULT NULL,
  `bottle` double DEFAULT NULL,
  `botpercase` int(11) DEFAULT NULL,
  `descriptio` varchar(255) DEFAULT NULL,
  `univ_cat` varchar(255) DEFAULT NULL,
  `reg_id` int(11) DEFAULT NULL,
  `truevint` varchar(255) DEFAULT NULL,
  `use_vint` varchar(255) DEFAULT NULL,
  `grape` varchar(255) DEFAULT NULL,
  `kosher` varchar(255) DEFAULT NULL,
  `organic` varchar(255) DEFAULT NULL,
  `prod_type` varchar(255) DEFAULT NULL,
  `importer` varchar(255) DEFAULT NULL,
  `cat_id` int(11) DEFAULT NULL,
  `type_id` int(11) DEFAULT NULL,
  `rev` int(11) DEFAULT NULL,
  `des` int(11) DEFAULT NULL,
  `wmn` int(11) DEFAULT NULL,
  `rat` int(11) DEFAULT NULL,
  `fpr` int(11) DEFAULT NULL,
  `tek` int(11) DEFAULT NULL,
  `rec` int(11) DEFAULT NULL,
  `txt` int(11) DEFAULT NULL,
  `tas` int(11) DEFAULT NULL,
  `lab` int(11) DEFAULT NULL,
  `bot` int(11) DEFAULT NULL,
  `pho` int(11) DEFAULT NULL,
  `log` int(11) DEFAULT NULL,
  `oth` int(11) DEFAULT NULL,
  `lwbn` varchar(255) DEFAULT NULL,
  `producer` varchar(255) DEFAULT NULL,
  `cat_type` varchar(255) DEFAULT NULL,
  `reg_text` varchar(255) DEFAULT NULL,
  `sparkling` varchar(255) DEFAULT NULL,
  `rp` int(11) DEFAULT NULL,
  `we` varchar(255) DEFAULT NULL,
  `ws` varchar(255) DEFAULT NULL,
  `current` varchar(255) DEFAULT NULL,
  `fortified` varchar(255) DEFAULT NULL,
  `dessert` varchar(255) DEFAULT NULL,
  `closure` varchar(255) DEFAULT NULL,
  `pack` int(11) DEFAULT NULL,
  `packaging` varchar(255) DEFAULT NULL,
  `packtype` varchar(255) DEFAULT NULL,
  `skus` varchar(255) DEFAULT NULL,
  `syn` varchar(255) DEFAULT NULL,
  `tstamp` varchar(255) DEFAULT NULL,
  `confstock` varchar(255) DEFAULT NULL,
  `whvint` varchar(255) DEFAULT NULL,
  `fullcase` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `xref` (`xref`),
  KEY `skus` (`skus`)
) ENGINE=MyISAM AUTO_INCREMENT=28794966 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cw_datahub_beva_company_supplierid_map`
--

DROP TABLE IF EXISTS `cw_datahub_beva_company_supplierid_map`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cw_datahub_beva_company_supplierid_map` (
  `company` varchar(50) NOT NULL,
  `pos_supplier_id` int(11) DEFAULT '0',
  KEY `pos_supplier_id` (`pos_supplier_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cw_datahub_beva_reg_text`
--

DROP TABLE IF EXISTS `cw_datahub_beva_reg_text`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cw_datahub_beva_reg_text` (
  `reg_text` varchar(50) DEFAULT NULL,
  `region_1` varchar(255) DEFAULT NULL,
  `region_2` varchar(255) DEFAULT NULL,
  `region_3` varchar(255) DEFAULT NULL,
  `region_4` varchar(255) DEFAULT NULL,
  `region_n` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cw_datahub_beva_typetbl`
--

DROP TABLE IF EXISTS `cw_datahub_beva_typetbl`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cw_datahub_beva_typetbl` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `type_desc` varchar(255) DEFAULT NULL,
  `type_code` varchar(255) DEFAULT NULL,
  `POS_dept_id` int(11) DEFAULT '0',
  KEY `ID` (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=123 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cw_datahub_beva_up_prod_xrefs`
--

DROP TABLE IF EXISTS `cw_datahub_beva_up_prod_xrefs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cw_datahub_beva_up_prod_xrefs` (
  `prod_id` varchar(255) NOT NULL,
  `skus` varchar(255) DEFAULT NULL,
  `prod_item` varchar(255) NOT NULL,
  PRIMARY KEY (`prod_id`),
  KEY `skus` (`skus`),
  KEY `prod_item` (`prod_item`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cw_datahub_buffer_match_config`
--

DROP TABLE IF EXISTS `cw_datahub_buffer_match_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cw_datahub_buffer_match_config` (
  `mfield` varchar(127) NOT NULL DEFAULT '',
  `bfield` varchar(127) NOT NULL DEFAULT '',
  `custom_sql` text NOT NULL,
  `update_cond` char(1) NOT NULL DEFAULT 'A',
  PRIMARY KEY (`mfield`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cw_datahub_buffer_merge_config`
--

DROP TABLE IF EXISTS `cw_datahub_buffer_merge_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cw_datahub_buffer_merge_config` (
  `bfield` varchar(127) NOT NULL DEFAULT '',
  `mfield` varchar(127) NOT NULL DEFAULT '',
  `merge_cond` char(1) NOT NULL DEFAULT 'A',
  PRIMARY KEY (`bfield`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

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
  `Match Items` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`table_id`),
  KEY `supplier_id` (`supplier_id`)
) ENGINE=MyISAM AUTO_INCREMENT=270840 DEFAULT CHARSET=latin1;
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
  `Match Items` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`table_id`),
  KEY `supplier_id` (`supplier_id`)
) ENGINE=MyISAM AUTO_INCREMENT=251044 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cw_datahub_import_buffer_images`
--

DROP TABLE IF EXISTS `cw_datahub_import_buffer_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cw_datahub_import_buffer_images` (
  `filename` varchar(255) NOT NULL DEFAULT '',
  `filesize` int(11) NOT NULL DEFAULT '0',
  `web_path` varchar(255) NOT NULL DEFAULT '',
  `system_path` varchar(255) NOT NULL DEFAULT '',
  `item_xref` varchar(50) NOT NULL DEFAULT '',
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  KEY `dib_item_xref` (`item_xref`)
) ENGINE=MyISAM AUTO_INCREMENT=44 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cw_datahub_item_store2`
--

DROP TABLE IF EXISTS `cw_datahub_item_store2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cw_datahub_item_store2` (
  `item_id` int(11) NOT NULL DEFAULT '0',
  `store_id` int(11) NOT NULL DEFAULT '0',
  `store_sku` varchar(255) NOT NULL,
  `table_id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`table_id`),
  UNIQUE KEY `store_sku_2` (`store_sku`,`store_id`),
  KEY `item_id` (`item_id`),
  KEY `store_id` (`store_id`),
  KEY `store_sku` (`store_sku`)
) ENGINE=MyISAM AUTO_INCREMENT=123734 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cw_datahub_log`
--

DROP TABLE IF EXISTS `cw_datahub_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cw_datahub_log` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(11) NOT NULL DEFAULT '0',
  `event_type` char(1) NOT NULL DEFAULT 'I',
  `event_message` text NOT NULL,
  `event_source` text NOT NULL,
  `customer_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`log_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cw_datahub_main_data`
--

DROP TABLE IF EXISTS `cw_datahub_main_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cw_datahub_main_data` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
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
) ENGINE=MyISAM AUTO_INCREMENT=784538 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cw_datahub_main_data_images`
--

DROP TABLE IF EXISTS `cw_datahub_main_data_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cw_datahub_main_data_images` (
  `filename` varchar(255) NOT NULL DEFAULT '',
  `filesize` int(11) NOT NULL DEFAULT '0',
  `web_path` varchar(255) NOT NULL DEFAULT '',
  `system_path` varchar(255) NOT NULL DEFAULT '',
  `item_id` int(11) NOT NULL DEFAULT '0',
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  KEY `dmdi_item_id` (`item_id`)
) ENGINE=MyISAM AUTO_INCREMENT=136903 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cw_datahub_main_data_newpos`
--

DROP TABLE IF EXISTS `cw_datahub_main_data_newpos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cw_datahub_main_data_newpos` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
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
) ENGINE=MyISAM AUTO_INCREMENT=784538 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cw_datahub_main_data_orphpos`
--

DROP TABLE IF EXISTS `cw_datahub_main_data_orphpos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cw_datahub_main_data_orphpos` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
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
) ENGINE=MyISAM AUTO_INCREMENT=784538 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cw_datahub_main_data_possnapshot`
--

DROP TABLE IF EXISTS `cw_datahub_main_data_possnapshot`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cw_datahub_main_data_possnapshot` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
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
) ENGINE=MyISAM AUTO_INCREMENT=784538 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cw_datahub_main_data_snapshot`
--

DROP TABLE IF EXISTS `cw_datahub_main_data_snapshot`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cw_datahub_main_data_snapshot` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
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
) ENGINE=MyISAM AUTO_INCREMENT=784455 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cw_datahub_main_data_transfer`
--

DROP TABLE IF EXISTS `cw_datahub_main_data_transfer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cw_datahub_main_data_transfer` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
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
  `cimageurl_path` varchar(255) NOT NULL DEFAULT '0',
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cw_datahub_match_links`
--

DROP TABLE IF EXISTS `cw_datahub_match_links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cw_datahub_match_links` (
  `catalog_id` int(11) NOT NULL DEFAULT '0',
  `item_xref` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`catalog_id`,`item_xref`),
  KEY `match_link_item_xref` (`item_xref`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cw_datahub_match_search_cache`
--

DROP TABLE IF EXISTS `cw_datahub_match_search_cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cw_datahub_match_search_cache` (
  `ID` int(11) NOT NULL DEFAULT '0',
  `words` text NOT NULL,
  `words_count` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cw_datahub_pos`
--

DROP TABLE IF EXISTS `cw_datahub_pos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cw_datahub_pos` (
  `Item Number` int(11) NOT NULL AUTO_INCREMENT,
  `Item Name` varchar(255) NOT NULL,
  `Item Description` text NOT NULL,
  `Alternate Lookup` varchar(255) NOT NULL,
  `Attribute` varchar(20) NOT NULL,
  `Size` varchar(20) NOT NULL,
  `Average Unit Cost` decimal(19,4) NOT NULL,
  `Regular Price` decimal(19,4) NOT NULL,
  `MSRP` decimal(19,4) NOT NULL,
  `Custom Price 1` decimal(19,4) NOT NULL,
  `Custom Price 2` decimal(19,4) NOT NULL,
  `Custom Price 3` decimal(19,4) NOT NULL,
  `Custom Price 4` decimal(19,4) NOT NULL,
  `UPC` varchar(255) NOT NULL,
  `Department Name` varchar(75) NOT NULL,
  `Department Code` varchar(20) NOT NULL,
  `Vendor Code` int(11) NOT NULL,
  `Vendor Name` varchar(255) NOT NULL,
  `Qty 1` int(11) NOT NULL,
  `On Order Qty` int(11) NOT NULL DEFAULT '0',
  `Reorder Point 1` int(11) NOT NULL DEFAULT '0',
  `Custom Field 1` varchar(255) NOT NULL,
  `Custom Field 2` varchar(255) NOT NULL,
  `Custom Field 3` int(11) NOT NULL,
  `Custom Field 4` varchar(10) NOT NULL,
  `Custom Field 5` varchar(255) NOT NULL,
  PRIMARY KEY (`Item Number`),
  UNIQUE KEY `Custom Field 5_2` (`Custom Field 5`),
  KEY `Custom Field 5` (`Custom Field 5`),
  KEY `Alternate Lookup` (`Alternate Lookup`),
  KEY `Average Unit Cost` (`Average Unit Cost`),
  KEY `MSRP` (`MSRP`)
) ENGINE=MyISAM AUTO_INCREMENT=146604 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cw_datahub_pos_changed`
--

DROP TABLE IF EXISTS `cw_datahub_pos_changed`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cw_datahub_pos_changed` (
  `Item Number` int(11) NOT NULL AUTO_INCREMENT,
  `Item Name` varchar(255) NOT NULL,
  `Item Description` text NOT NULL,
  `Alternate Lookup` varchar(255) NOT NULL,
  `Attribute` varchar(20) NOT NULL,
  `Size` varchar(20) NOT NULL,
  `Average Unit Cost` decimal(19,4) NOT NULL,
  `Regular Price` decimal(19,4) NOT NULL,
  `MSRP` decimal(19,4) NOT NULL,
  `Custom Price 1` decimal(19,4) NOT NULL,
  `Custom Price 2` decimal(19,4) NOT NULL,
  `Custom Price 3` decimal(19,4) NOT NULL,
  `Custom Price 4` decimal(19,4) NOT NULL,
  `UPC` varchar(255) NOT NULL,
  `Department Name` varchar(75) NOT NULL,
  `Department Code` varchar(20) NOT NULL,
  `Vendor Code` int(11) NOT NULL,
  `Vendor Name` varchar(255) NOT NULL,
  `Qty 1` int(11) NOT NULL,
  `On Order Qty` int(11) NOT NULL DEFAULT '0',
  `Reorder Point 1` int(11) NOT NULL DEFAULT '0',
  `Custom Field 1` varchar(255) NOT NULL,
  `Custom Field 2` varchar(255) NOT NULL,
  `Custom Field 3` int(11) NOT NULL,
  `Custom Field 4` varchar(10) NOT NULL,
  `Custom Field 5` varchar(255) NOT NULL,
  PRIMARY KEY (`Item Number`),
  UNIQUE KEY `Custom Field 5_2` (`Custom Field 5`),
  KEY `Custom Field 5` (`Custom Field 5`),
  KEY `Alternate Lookup` (`Alternate Lookup`),
  KEY `Average Unit Cost` (`Average Unit Cost`),
  KEY `MSRP` (`MSRP`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cw_datahub_pos_new`
--

DROP TABLE IF EXISTS `cw_datahub_pos_new`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cw_datahub_pos_new` (
  `Item Number` int(11) NOT NULL AUTO_INCREMENT,
  `Item Name` varchar(255) NOT NULL,
  `Item Description` text NOT NULL,
  `Alternate Lookup` varchar(255) NOT NULL,
  `Attribute` varchar(20) NOT NULL,
  `Size` varchar(20) NOT NULL,
  `Average Unit Cost` decimal(19,4) NOT NULL,
  `Regular Price` decimal(19,4) NOT NULL,
  `MSRP` decimal(19,4) NOT NULL,
  `Custom Price 1` decimal(19,4) NOT NULL,
  `Custom Price 2` decimal(19,4) NOT NULL,
  `Custom Price 3` decimal(19,4) NOT NULL,
  `Custom Price 4` decimal(19,4) NOT NULL,
  `UPC` varchar(255) NOT NULL,
  `Department Name` varchar(75) NOT NULL,
  `Department Code` varchar(20) NOT NULL,
  `Vendor Code` int(11) NOT NULL,
  `Vendor Name` varchar(255) NOT NULL,
  `Qty 1` int(11) NOT NULL,
  `On Order Qty` int(11) NOT NULL DEFAULT '0',
  `Reorder Point 1` int(11) NOT NULL DEFAULT '0',
  `Custom Field 1` varchar(255) NOT NULL,
  `Custom Field 2` varchar(255) NOT NULL,
  `Custom Field 3` int(11) NOT NULL,
  `Custom Field 4` varchar(10) NOT NULL,
  `Custom Field 5` varchar(255) NOT NULL,
  PRIMARY KEY (`Item Number`),
  UNIQUE KEY `Custom Field 5_2` (`Custom Field 5`),
  KEY `Custom Field 5` (`Custom Field 5`),
  KEY `Alternate Lookup` (`Alternate Lookup`),
  KEY `Average Unit Cost` (`Average Unit Cost`),
  KEY `MSRP` (`MSRP`)
) ENGINE=MyISAM AUTO_INCREMENT=146609 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cw_datahub_pos_orphaned`
--

DROP TABLE IF EXISTS `cw_datahub_pos_orphaned`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cw_datahub_pos_orphaned` (
  `Item Number` int(11) NOT NULL AUTO_INCREMENT,
  `Item Name` varchar(255) NOT NULL,
  `Item Description` text NOT NULL,
  `Alternate Lookup` varchar(255) NOT NULL,
  `Attribute` varchar(20) NOT NULL,
  `Size` varchar(20) NOT NULL,
  `Average Unit Cost` decimal(19,4) NOT NULL,
  `Regular Price` decimal(19,4) NOT NULL,
  `MSRP` decimal(19,4) NOT NULL,
  `Custom Price 1` decimal(19,4) NOT NULL,
  `Custom Price 2` decimal(19,4) NOT NULL,
  `Custom Price 3` decimal(19,4) NOT NULL,
  `Custom Price 4` decimal(19,4) NOT NULL,
  `UPC` varchar(255) NOT NULL,
  `Department Name` varchar(75) NOT NULL,
  `Department Code` varchar(20) NOT NULL,
  `Vendor Code` int(11) NOT NULL,
  `Vendor Name` varchar(255) NOT NULL,
  `Qty 1` int(11) NOT NULL,
  `On Order Qty` int(11) NOT NULL DEFAULT '0',
  `Reorder Point 1` int(11) NOT NULL DEFAULT '0',
  `Custom Field 1` varchar(255) NOT NULL,
  `Custom Field 2` varchar(255) NOT NULL,
  `Custom Field 3` int(11) NOT NULL,
  `Custom Field 4` varchar(10) NOT NULL,
  `Custom Field 5` varchar(255) NOT NULL,
  PRIMARY KEY (`Item Number`),
  UNIQUE KEY `Custom Field 5_2` (`Custom Field 5`),
  KEY `Custom Field 5` (`Custom Field 5`),
  KEY `Alternate Lookup` (`Alternate Lookup`),
  KEY `Average Unit Cost` (`Average Unit Cost`),
  KEY `MSRP` (`MSRP`)
) ENGINE=MyISAM AUTO_INCREMENT=141679 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cw_datahub_pos_snapshot`
--

DROP TABLE IF EXISTS `cw_datahub_pos_snapshot`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cw_datahub_pos_snapshot` (
  `Item Number` int(11) NOT NULL AUTO_INCREMENT,
  `Item Name` varchar(255) NOT NULL,
  `Item Description` text NOT NULL,
  `Alternate Lookup` varchar(255) NOT NULL,
  `Attribute` varchar(20) NOT NULL,
  `Size` varchar(20) NOT NULL,
  `Average Unit Cost` decimal(19,4) NOT NULL,
  `Regular Price` decimal(19,4) NOT NULL,
  `MSRP` decimal(19,4) NOT NULL,
  `Custom Price 1` decimal(19,4) NOT NULL,
  `Custom Price 2` decimal(19,4) NOT NULL,
  `Custom Price 3` decimal(19,4) NOT NULL,
  `Custom Price 4` decimal(19,4) NOT NULL,
  `UPC` varchar(255) NOT NULL,
  `Department Name` varchar(75) NOT NULL,
  `Department Code` varchar(20) NOT NULL,
  `Vendor Code` int(11) NOT NULL,
  `Vendor Name` varchar(255) NOT NULL,
  `Qty 1` int(11) NOT NULL,
  `On Order Qty` int(11) NOT NULL DEFAULT '0',
  `Reorder Point 1` int(11) NOT NULL DEFAULT '0',
  `Custom Field 1` varchar(255) NOT NULL,
  `Custom Field 2` varchar(255) NOT NULL,
  `Custom Field 3` int(11) NOT NULL,
  `Custom Field 4` varchar(10) NOT NULL,
  `Custom Field 5` varchar(255) NOT NULL,
  PRIMARY KEY (`Item Number`),
  UNIQUE KEY `Custom Field 5_2` (`Custom Field 5`),
  KEY `Custom Field 5` (`Custom Field 5`),
  KEY `Alternate Lookup` (`Alternate Lookup`),
  KEY `Average Unit Cost` (`Average Unit Cost`),
  KEY `MSRP` (`MSRP`)
) ENGINE=MyISAM AUTO_INCREMENT=146604 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cw_datahub_pos_update_config`
--

DROP TABLE IF EXISTS `cw_datahub_pos_update_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cw_datahub_pos_update_config` (
  `mfield` varchar(127) NOT NULL DEFAULT '',
  `pfield` varchar(127) NOT NULL DEFAULT '',
  `custom_sql` text NOT NULL,
  `update_cond` char(1) NOT NULL DEFAULT 'A',
  PRIMARY KEY (`mfield`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cw_datahub_price_settings`
--

DROP TABLE IF EXISTS `cw_datahub_price_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cw_datahub_price_settings` (
  `store_id` int(11) NOT NULL DEFAULT '0',
  `min_qty_avail_code_2` int(11) DEFAULT '0',
  `oversize_surcharge` int(11) DEFAULT '0',
  `SWE_min_qty_under_cost_threshold` double DEFAULT '0',
  `SWE_cost_threshold` double DEFAULT '0',
  `SWE_min_order_profit` double DEFAULT '0',
  `SWE_min_order_profit_instock` double DEFAULT '0',
  `competitor_price_threshold` double DEFAULT '0',
  `SWE_min_markup` double DEFAULT '0',
  `SWE_max_markup` double DEFAULT '0',
  `order_days` int(11) DEFAULT '0',
  `twelve_bottle_discount` double NOT NULL DEFAULT '0',
  `in_stock_sale` double DEFAULT '0',
  `use_12bot_price_with_in_stock_sale` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Only works if there''s a value greater than 0 for the in_stock_sale.  1 denotes that 12 bot pricing will be used if in_stock_sale has a value greater than 0.',
  PRIMARY KEY (`store_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cw_datahub_pricing_IPO_avg_price`
--

DROP TABLE IF EXISTS `cw_datahub_pricing_IPO_avg_price`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cw_datahub_pricing_IPO_avg_price` (
  `ID` int(11) NOT NULL DEFAULT '0',
  `avg_items_per_order` double DEFAULT '0',
  `avg_price` double DEFAULT '0',
  `avg_line_items_per_order` double DEFAULT '0',
  KEY `ID` (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cw_datahub_pricing_aipo`
--

DROP TABLE IF EXISTS `cw_datahub_pricing_aipo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cw_datahub_pricing_aipo` (
  `item_id` int(11) NOT NULL DEFAULT '0',
  `aipo` double(16,13) DEFAULT '0.0000000000000',
  KEY `item_id` (`item_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cw_datahub_rnt_hidden_item`
--

DROP TABLE IF EXISTS `cw_datahub_rnt_hidden_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cw_datahub_rnt_hidden_item` (
  `hiddenID` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`hiddenID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cw_datahub_splitcase_charges`
--

DROP TABLE IF EXISTS `cw_datahub_splitcase_charges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cw_datahub_splitcase_charges` (
  `company` varchar(50) NOT NULL,
  `charge` double DEFAULT '0',
  PRIMARY KEY (`company`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cw_datahub_transfer_item_price`
--

DROP TABLE IF EXISTS `cw_datahub_transfer_item_price`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cw_datahub_transfer_item_price` (
  `store_id` int(11) NOT NULL DEFAULT '0',
  `item_id` int(11) NOT NULL DEFAULT '0',
  `price` decimal(19,2) DEFAULT '0.00',
  `cost` decimal(19,2) DEFAULT '0.00',
  `xref` varchar(255) DEFAULT NULL,
  `store_sku` varchar(111) DEFAULT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `stock` int(11) DEFAULT NULL,
  PRIMARY KEY (`store_id`,`item_id`),
  KEY `store_id` (`store_id`),
  KEY `item_id` (`item_id`),
  KEY `xref` (`xref`),
  KEY `store_sku` (`store_sku`),
  KEY `supplier_id` (`supplier_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cw_datahub_transfer_item_price_twelve_bottle`
--

DROP TABLE IF EXISTS `cw_datahub_transfer_item_price_twelve_bottle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cw_datahub_transfer_item_price_twelve_bottle` (
  `store_id` int(11) NOT NULL DEFAULT '0',
  `item_id` int(11) NOT NULL DEFAULT '0',
  `price` decimal(19,2) DEFAULT '0.00',
  `cost` decimal(19,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`store_id`,`item_id`),
  KEY `store_id` (`store_id`),
  KEY `item_id` (`item_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cw_datahub_words_weight`
--

DROP TABLE IF EXISTS `cw_datahub_words_weight`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cw_datahub_words_weight` (
  `word` varchar(255) NOT NULL DEFAULT '',
  `weight` decimal(19,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`word`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cw_datahub_xcart_clean_urls`
--

DROP TABLE IF EXISTS `cw_datahub_xcart_clean_urls`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cw_datahub_xcart_clean_urls` (
  `clean_url` varchar(250) NOT NULL DEFAULT '',
  `resource_type` char(1) NOT NULL DEFAULT '',
  `resource_id` int(11) NOT NULL DEFAULT '0',
  `mtime` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`clean_url`),
  KEY `rr` (`resource_type`,`resource_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cw_dh_fingerprint`
--

DROP TABLE IF EXISTS `cw_dh_fingerprint`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cw_dh_fingerprint` (
  `ID` int(11) NOT NULL DEFAULT '0',
  `fp` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cw_dh_fingerprint_current`
--

DROP TABLE IF EXISTS `cw_dh_fingerprint_current`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cw_dh_fingerprint_current` (
  `ID` int(11) NOT NULL DEFAULT '0',
  `fingerprint` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cw_dh_fingerprint_old`
--

DROP TABLE IF EXISTS `cw_dh_fingerprint_old`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cw_dh_fingerprint_old` (
  `ID` int(11) NOT NULL DEFAULT '0',
  `fingerprint` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cw_import_feed`
--

DROP TABLE IF EXISTS `cw_import_feed`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cw_import_feed` (
  `table_id` int(11) NOT NULL AUTO_INCREMENT,
  `Source` varchar(50) DEFAULT NULL,
  `wholesaler` varchar(50) DEFAULT NULL,
  `Wine` varchar(255) DEFAULT NULL,
  `Producer` varchar(255) DEFAULT NULL,
  `Name` varchar(255) DEFAULT NULL,
  `Vintage` varchar(50) DEFAULT NULL,
  `size` varchar(50) DEFAULT NULL,
  `ITEMID` varchar(50) DEFAULT NULL,
  `bottles_per_case` int(11) DEFAULT NULL,
  `cost` double DEFAULT NULL,
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
  `item_xref_min_price` decimal(19,4) DEFAULT '0.0000',
  `item_xref_bot_per_case` int(11) DEFAULT NULL,
  `item_xref_cost_per_case` decimal(19,4) DEFAULT '0.0000',
  `item_xref_cost_per_bottle` decimal(19,4) DEFAULT '0.0000',
  PRIMARY KEY (`table_id`),
  KEY `supplier_id` (`supplier_id`)
) ENGINE=MyISAM AUTO_INCREMENT=215129022 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-08-30  6:36:24
