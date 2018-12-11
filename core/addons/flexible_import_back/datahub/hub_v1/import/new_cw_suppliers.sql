-- MySQL dump 10.13  Distrib 5.1.61, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: antonp_saratoga_hub
-- ------------------------------------------------------
-- Server version	5.1.61-0+squeeze1

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
-- Table structure for table `Supplier`
--

DROP TABLE IF EXISTS `Supplier`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Supplier` (
  `Country` varchar(20) DEFAULT NULL,
  `HQID` int(11) DEFAULT NULL,
  `LastUpdated` datetime DEFAULT NULL,
  `State` varchar(20) DEFAULT NULL,
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `SupplierName` varchar(30) DEFAULT NULL,
  `ContactName` varchar(30) DEFAULT NULL,
  `Address1` varchar(30) DEFAULT NULL,
  `Address2` varchar(30) DEFAULT NULL,
  `City` varchar(30) DEFAULT NULL,
  `Zip` varchar(20) DEFAULT NULL,
  `PhoneNumber` varchar(30) DEFAULT NULL,
  `FaxNumber` varchar(30) DEFAULT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `short_name` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `supplier_id` (`supplier_id`),
  KEY `short_name` (`short_name`)
) ENGINE=MyISAM AUTO_INCREMENT=184 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Supplier`
--

LOCK TABLES `Supplier` WRITE;
/*!40000 ALTER TABLE `Supplier` DISABLE KEYS */;
INSERT INTO `Supplier` VALUES (NULL,NULL,'2005-01-10 13:55:34','NY',5,'Service Liquor (Charmer)','Susan Baer 857-6757','PO Box 12369','Customer #101436','Albany','12212','800-724-3475',NULL,5,''),(NULL,NULL,'2005-01-10 13:51:24','NY',6,'Eber W&L/Prem.& Paramount','Melanie Largeteau 343-2351','PO Box 76','Customer #401334','Guilderland Center','12085','800-333-6032',NULL,6,''),(NULL,NULL,'2005-01-10 13:52:18',NULL,7,'Finger Lakes (order by 1 Tue)','Ron Reels',NULL,NULL,NULL,NULL,'877-717-8806','716-394-3908',7,''),(NULL,NULL,'2005-01-10 13:55:54','NY',8,'Wine Merchants (Cazanove)','Shawn 316-1588/587-7347H','Northeast Industrial park','Customer #180117','Guilderland Center','12085','518-861-5111','Deliver Mon/Wed',8,''),('USA',NULL,'2001-11-07 13:33:00','NY',9,'Elmira Distributing Co.','Richard Annis','PO Box 86',NULL,'Elmira','14902','800-724-2150 Cell - 524-3513',NULL,9,''),('USA',NULL,'2005-01-10 13:50:49','NY',10,'Colony L&W/Peerless','Bob 796-1971/Rich 365-1875','PO Box 1608','Customer #054486','Kingston','12402','800-724-3960','Press 5',10,''),('USA',NULL,'2007-05-04 10:39:01','NY',11,'Lauber ','Joe Armstrong Cust#02701425','225 West 43th St.','Suite 1912','New York','10122','800-247-9243 Cell 441-5649',NULL,11,''),(NULL,NULL,'2005-01-10 13:53:58','NY',12,'Letchworth Wine','Darren Dulong','140 N. Main St.',NULL,'Mt. Morris','14510','800-767-5034','cell484-8043',12,''),(NULL,NULL,'2005-01-10 13:56:03',NULL,13,'Winebow','Mike 573-6796',NULL,NULL,NULL,NULL,'800-445-0620',NULL,13,''),(NULL,NULL,'2001-12-04 16:18:05','NY',14,'Paramont Brands/Eber Bros Wine',NULL,'305 S Regent St.',NULL,'Port Chester ','10573','800-999-2903',NULL,14,''),(NULL,NULL,'2003-10-03 11:23:57',NULL,15,'Fulkerson Winery',NULL,'*order through Finger Lakes*',NULL,NULL,NULL,'607-243-7883',NULL,15,''),(NULL,NULL,'2005-01-10 13:52:37',NULL,16,'Four Chimney',NULL,NULL,NULL,NULL,NULL,'607-243-7502','607-243-8156',16,''),(NULL,NULL,'2005-01-10 13:49:20',NULL,17,'Cayuga Ridge Winery',NULL,NULL,NULL,NULL,NULL,'800-598-9463',NULL,17,''),(NULL,NULL,'2005-01-10 13:48:50','NY',19,'BNP    (5 c/$300 minimum)','ORDER BY 1PM MONDAY',NULL,NULL,'NEW YORK',NULL,'212-419-1400',NULL,19,''),(NULL,NULL,'2002-03-07 19:26:02','NY',20,'Tbilisi Georgian Wines',NULL,'297 Butler St',NULL,'Brooklyn','11215','718-260-8200','718-260-8135',20,''),(NULL,NULL,'2003-10-06 17:25:18','NY',23,'Frontier Wine Imports','Karen Slater','P.O. Box 357','*10 case mixed discount*','Suffern','10901','469-0899 - Cell',NULL,23,''),(NULL,NULL,'2005-01-10 13:54:52',NULL,24,'Martin Scott','Mike Barry',NULL,NULL,NULL,NULL,'852-2182','866-327-0808',24,''),(NULL,NULL,'2002-08-10 09:20:00','NJ',25,'WPS','joseph armstrong','24 Columbia Road',NULL,'Somerville','08876-3519','800-247-9243','908-725-3863',25,''),(NULL,NULL,'2002-08-29 18:47:59',NULL,26,'TWE Wholesalers','Josh Farrell',NULL,NULL,NULL,NULL,'1-800-822-8846',NULL,26,''),(NULL,NULL,'2002-08-30 20:45:10','New York',27,'Monsieur Touton',NULL,'129 West 27th Street Suite 9B',NULL,'New York','10001','212-255-0674','212-255-2628',27,''),('usa',NULL,'2008-06-04 15:27:57','NY',28,'Michael Skurnik','Doug Bernthal','272 Plandome Road','.','Manhasset ','11030','99999','99999',28,''),(NULL,NULL,'2002-09-19 16:51:44','NY',29,'Village Wine Ltd.','Mike Petrillo','718 Broadway',NULL,'New York','10003',NULL,NULL,29,''),(NULL,NULL,'2002-12-12 22:47:07',NULL,30,'Wines for Food','karen slater','272 Plandome Road',NULL,'Manhasset ','11030','516-869-9170 (office)',NULL,30,''),(NULL,NULL,'2003-02-24 15:00:39','New York',31,'Domaine Select Wine Estates','John Wilson  ','555 8th Ave.','Suite 2302','New York','10018','1-212-279-0799','1-212-279-0499',31,''),(NULL,NULL,'2003-05-23 15:32:32','NY',32,'Bayfield Importing','Brian','37 Greenpoint Ave.',NULL,'Brooklyn','11215','1-718-609-1910','1-718-609-1913',32,''),(NULL,NULL,'2003-10-03 11:25:06',NULL,33,'Lenz Winery','Tom Morgan','no minimum/comes UPS',NULL,NULL,NULL,'800-974-9899',NULL,33,''),(NULL,NULL,'2003-11-06 14:55:33','NY',34,'Ganz Inc.','Andrew Ponda','60 Industrial Parkway',NULL,'Cheektowaga','14227','1800-724-5902',NULL,34,''),(NULL,NULL,'2003-11-07 17:36:19','Connecticut',35,'Moran USA, LLC',NULL,'60 Connolly Parkway',NULL,'Hamden','06514','203-281-1130',NULL,35,''),(NULL,NULL,'2003-11-13 15:04:24','IL',36,'Collins Brothers',NULL,'2113 W. Greenleaf Street',NULL,'Evanston','60202',NULL,NULL,36,''),(NULL,NULL,'2003-11-23 17:37:32',NULL,37,'Montezuma Winery',NULL,NULL,NULL,NULL,NULL,NULL,NULL,37,''),(NULL,NULL,'2004-09-16 13:12:43','New York',38,'Macari Vineyards & Winery',NULL,'P.O. Box 2','150 Bergen Avenue','Mattituck','11952','631-298-0100',NULL,38,''),(NULL,NULL,'2003-12-04 15:43:32','CA',39,'Epic Products, Inc.',NULL,'17370 Mt. Herrmann Street',NULL,'Fountain Valley','92708','800-548-9791',NULL,39,''),('US',NULL,'2003-12-08 16:43:39','NY',40,'Casa Larga Vineyards, Inc.',NULL,'2287 Turk Hill Rd.',NULL,'Fairport','14450','(585)223-4210','(585)223-8899',40,''),('US',NULL,'2004-08-07 14:31:09','NY',41,'David Bowler Collection','Sarah Gallaher','119 W23rd St.','Suite 703','NY','10011','(914) 374-9997',NULL,41,''),('US',NULL,'2004-08-07 14:32:49','NY',42,'Pelloneda','Matt Kelly','125 Main St.','Suite 207','Mt. Kisco','10549','(845) 532-6500',NULL,42,''),(NULL,NULL,'2004-08-07 14:33:49','NY',43,'V.O.S. Selections',NULL,'555 8th Ave',NULL,'NY','10018',NULL,NULL,43,''),(NULL,NULL,'2004-09-08 11:14:22','NY',44,'John Givens',NULL,'972 Centre Rd',NULL,'Staatsburg','12580','845-266-3554',NULL,44,''),(NULL,NULL,'2004-09-14 11:49:52','NY',45,'Wine Cellars Ltd.',NULL,'314 Chappaqua Rd','P.O. Box 206','Briarcliff Manor','10510','914-762-6540','914-762-6515',45,''),(NULL,NULL,'2004-09-14 12:44:20','NY',46,'USA Wine Imports, Inc.',NULL,'285 West Broadway',NULL,'New York','10013',NULL,NULL,46,''),('USA',NULL,'2004-09-30 13:14:33','NY',47,'Tovtry Importing Inc. ',NULL,'PO Box 511',NULL,'Slingerlands','12159-0511','518-229-6050','518-475-7599',47,''),(NULL,NULL,'2004-10-19 11:18:14','NY',48,'Polaner Selections','Andrew','19 North Moger Avenue',NULL,'Mt. Kisco','10549','914-244-0404',NULL,48,''),(NULL,NULL,'2004-11-02 14:36:06','NY',49,'Romano Brands',NULL,'272 Plandome Rd',NULL,'Manhasset','11030',NULL,NULL,49,''),('USA',NULL,'2004-11-09 15:53:42','NY',50,'VOS Selections, Inc.',NULL,'555 8th Ave',NULL,'New York','10018','212-967-6948','212-967-6986',50,''),(NULL,NULL,'2004-11-11 14:29:37',NULL,51,'Private Seller',NULL,NULL,NULL,NULL,NULL,NULL,NULL,51,''),('USA',NULL,'2004-12-04 16:20:25','NY',52,'New York Harvest Cellars, Inc',NULL,'55 Baker Rd',NULL,'Granville','12832',NULL,NULL,52,''),('USA',NULL,'2004-12-11 14:43:27','NY',53,'MCM Myssura Trading Co',NULL,'P.O. Box 311',NULL,'Brooklyn ','11222','718-349-9475','718-349-9875',53,''),('USA',NULL,'2004-12-15 14:53:53','CA',54,'Fenn & Associates',NULL,'28 Lessay',NULL,'Newport Coast','92657','949-720-0811','949-706-7931',54,''),(NULL,NULL,'2004-12-15 16:42:00',NULL,55,'Matt Swapp',NULL,NULL,NULL,NULL,NULL,NULL,NULL,55,''),(NULL,NULL,'2004-12-26 13:23:07','CA',56,'Leo Fenn',NULL,'28 Lessay',NULL,'Newport Coast','92657','949 720-0811',NULL,56,''),(NULL,NULL,'2004-12-30 14:35:53',NULL,57,'Private Collector',NULL,NULL,NULL,NULL,NULL,NULL,NULL,57,''),(NULL,NULL,'2005-01-10 13:53:32',NULL,58,'Glenora',NULL,NULL,NULL,NULL,NULL,'800-243-5513',NULL,58,''),(NULL,NULL,'2005-01-10 13:59:09',NULL,59,'PAYROLL',NULL,NULL,NULL,NULL,NULL,'877-9727','Fax 877-5052',59,''),(NULL,NULL,'2005-01-14 20:05:44','CT',60,'BMC Imports',NULL,'45 Church St','Suite 204','Stamford','06906','203 359-4410','203 359-4352',60,''),('USA',NULL,'2005-02-15 09:43:31','NY',61,'SMD Selections, LLC',NULL,'425 New Karner Rd',NULL,'Albany ','12205','518-452-6049','518-765-3145',61,''),(NULL,NULL,'2005-03-03 09:51:52','NY',62,'Heron Hill Vineyards',NULL,'9301 County Route 76',NULL,'Hammondsport','14840','607-868-4241',NULL,62,''),(NULL,NULL,'2006-10-11 13:27:01','NY',63,'Southern Wine & Spirits','Liz ','120 Madison St',NULL,'Syracuse ','13202','518-524-0495',NULL,63,''),(NULL,NULL,'2005-05-17 18:03:18','LA',64,'Merlin Wines of Louisiana ',NULL,'337 Brooklyn Ave',NULL,'Jefferson ','70121','504-832-1100','504-832-9069',64,''),(NULL,NULL,'2005-06-16 12:45:35','NY',65,'Frederick Wildman and Sons, Lt',NULL,'307 East 53rd Street',NULL,'New York','10022','2123550700',NULL,65,''),(NULL,NULL,'2005-12-20 12:54:08',NULL,66,'Wildman ',NULL,NULL,NULL,NULL,NULL,NULL,NULL,66,''),('USa',NULL,'2005-07-14 15:17:10','NY',67,'Swedish Hill',NULL,'4565 Route 414',NULL,'Romulus','14514','315-549-8326','315-549-8477',67,''),(NULL,NULL,'2005-08-26 14:39:15',NULL,68,'BWL',NULL,NULL,NULL,NULL,NULL,NULL,NULL,68,''),(NULL,NULL,'2005-09-08 11:29:20',NULL,69,'Angel\'s Share',NULL,NULL,NULL,NULL,NULL,NULL,NULL,69,''),(NULL,NULL,'2005-09-17 12:37:14','NY',70,'Decrescente','M. Krasz','211 N. Main Street','P.O. Box 231','Mechanicville','12118','5186649866','5186648259 Fax',70,''),(NULL,NULL,'2005-10-21 10:01:22','NY',71,'T. Edward Wines, Ltd.',NULL,'66 West Broadway','Suite 406','New York','10007','212-233-1504','646-349-5073',71,''),(NULL,NULL,'2005-11-17 12:02:07','NY',72,'Warm Lake Estates',NULL,'3868 Lower Mountain Road',NULL,'Lockport','14094','7167315900','7167312926',72,''),(NULL,NULL,'2006-03-13 13:04:14','NY',73,'Haro Imports','Jesus','94 Demars Blvd','Suite 6 PO Box 1135','Tupper Lake','12896',NULL,NULL,73,''),(NULL,NULL,'2006-03-25 19:19:01','NY',74,'Admiral Wine',NULL,'246 5th Ave','Suite 408','NY','10001',NULL,NULL,74,''),(NULL,NULL,'2006-04-20 16:40:53',NULL,75,'Supreme Wines','Rich Smith','420 Lexington Avenue','Suite 1639','New York, NY','10170','917-696-8167',NULL,75,''),(NULL,NULL,'2006-05-09 14:08:21','VA',76,'Fortessa, Inc.',NULL,'22601 Davis Drive',NULL,'Sterling','20164-4471','800-296-7508','703-787-6645',76,''),(NULL,NULL,'2006-07-13 12:09:22',NULL,77,'WineBid.com','info@winebid.com',NULL,NULL,NULL,NULL,'707-226-5893',NULL,77,''),(NULL,NULL,'2006-07-18 12:25:41',NULL,78,'ACKER, MERRALL & CONDIT',NULL,'**AUCTIONS**',NULL,NULL,NULL,NULL,NULL,78,''),(NULL,NULL,'2006-08-01 19:06:48','NY',79,'Global Wine Imports',NULL,'21 Mt Harmony Road',NULL,'Bernardsville','07924',NULL,NULL,79,''),(NULL,NULL,'2006-09-23 15:30:02','NY',80,'Nancy Burgoyne','Nancy Burgoyne','600 Park Place',NULL,'Schenectady','12306','winetags4u@yahoo.com',NULL,80,''),(NULL,NULL,'2006-11-21 20:59:16','WA',81,'True Fabrications',NULL,'PO BOX 12159',NULL,'Seattle ','98102','800-7508783',NULL,81,''),(NULL,NULL,'2006-12-07 19:19:32',NULL,82,'Southwest Cellars','Jeff Blake',NULL,NULL,NULL,NULL,NULL,NULL,82,''),(NULL,NULL,'2007-05-24 18:26:23','NY',83,'Opici Wine Company',NULL,'1 Dupont south ','suite 101','Planiview','11803','800-648-wine',NULL,83,''),('.',NULL,'2007-09-05 16:20:04','NY',88,'Empire Merchants North','Susam, Luke','132 Flatbush Ave.','.','Kingston','12401','800-724-3960','845-338-9385',88,''),('+',NULL,'2007-11-20 19:09:28','+',93,'magnum Wines','+','+','+','+','+','+','+',93,''),('.',NULL,'2007-11-24 18:03:34','.',96,'Book Depot','.','.','.','.','.','.','.',96,''),('.',NULL,'2007-12-01 19:09:06','.',97,'John Behrendt','.','.','.','.','.','.','.',97,''),('',NULL,'2008-04-25 18:01:38','IL',100,'The Chicago Wine Co.','.','835 N. Central','.','Wood Dale ','60190','630-594-2972','630-594-2978',100,''),('usa',NULL,'2008-06-04 15:28:24','NY',103,'Skurnik','Doug Bernthal','.','.','.','.','99999','99999',103,''),('x',NULL,'2008-07-09 14:23:56','x',106,'Genesis Beverage','x','x','x','x','x','516 869-9170','x',106,''),('x',NULL,'2008-07-09 15:11:43','x',109,'Newman Wines','Dino Di Mauro','x','x','x','x','484-682-4585','x',109,''),('usa',NULL,'2008-07-24 14:51:56','NY',110,'MHW, LTD','NONE','272 Plandome Road','.','Manhasset ','11030','516-869-9170','516-869-9171',110,''),('',NULL,'2008-10-21 16:24:33','NY',115,'Vias Imports LTD','Seth Gregory','875 Sixth Avenue','Suite 2200','New York','10001','212-629-0200','212-629-0262',115,'VIAS'),('USA',NULL,'2009-01-19 13:06:13','NY',122,'E Beaver','Howard','141 25th Rd','141-25','Flushing','11354','718-324-7288','718-231-3572',122,''),('',NULL,'2009-02-03 14:01:10','NY',123,'Bev Access','N/A','116 John St','23rd Floor','New York','10038','212-571-3232','212-555-5555',123,''),('',NULL,'2009-02-03 17:40:05','WA',126,'Pride Polymers LLC','Lisa Bertelstein','50 W Arlington St','N/A','Yakima','98902','509-452-3330','509-452-8850',126,''),('US',NULL,'2009-08-20 13:41:38','NY',133,'Vision Wine Brands','Peter Sloan - 413-559-1838','10 Midland Ave','Suite 210','Port Chester','10573','914-481-5170 - order board','914-481-5172',133,''),(' ',NULL,'2009-11-12 18:33:47',' ',145,'Tempranillo',' ',' ',' ',' ',' ',' ',' ',145,''),(' ',NULL,'2010-04-05 13:40:46',' ',148,'Dancing Bear','Michael Belanger','  ',' ',' ',' ',' ',' ',148,''),(' ',NULL,'2010-04-13 15:36:44',' ',149,'Vision Brands',' ',' ',' ',' ',' ',' ',' ',149,''),(' ',NULL,'2010-06-07 23:27:05',' ',150,'Bowler Wines','Joshn Koris',' ',' ',' ',' ',' ',' ',150,''),('XXX',NULL,'2010-12-06 15:38:03','NY',155,'VERITY WINE PARTNERS','BRENDA HANSON','PO','BOX 1826','NEW YORK','10156','212-683-8763','646-706-0509',155,''),(' ',NULL,'2010-08-13 14:39:39','NY',162,'Chateau LaFayette Reneau',' ','PO Box 238',' ','Hector','14841','800-469-9463',' ',162,''),(' ',NULL,'2010-11-04 17:19:29','NY',165,'Tri-Vin Imports','John','1 Park Ave',' ','Mt. Vernon','10550',' ',' ',165,''),(NULL,NULL,NULL,NULL,166,'ACME',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1000,''),(NULL,NULL,NULL,NULL,167,'CORD',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1001,''),(NULL,NULL,NULL,NULL,168,'NOBL',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1002,''),(NULL,NULL,NULL,NULL,169,'EBD',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1003,''),(NULL,NULL,NULL,NULL,170,'TRIA',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1004,''),(NULL,NULL,NULL,NULL,171,'CELL',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1005,''),(NULL,NULL,NULL,NULL,172,'GRPE',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1006,''),(NULL,NULL,NULL,NULL,173,'VEHR',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1007,''),(NULL,NULL,NULL,NULL,174,'BWL',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1008,''),('USA',NULL,NULL,NULL,175,'Fruit of the Vines, Inc.',NULL,'51-02 Vernon Blvd','2nd Floor','Long Island City','11101','718.392.5640','718.784.5059',166,''),('USA',NULL,NULL,NULL,176,'Apollo Fine Spirits',NULL,'191 Hanse Ave',NULL,'Freeport','11520','516.564.5600','516.223.1167',170,''),(NULL,NULL,NULL,NULL,177,'VINM',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1009,''),(NULL,NULL,NULL,NULL,178,'CRU',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1010,''),(NULL,NULL,NULL,NULL,179,'CAVA',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1011,''),(NULL,NULL,'2012-07-15 00:00:00',NULL,180,'Wildman',NULL,NULL,NULL,NULL,NULL,NULL,NULL,180,'WDMN'),(NULL,NULL,NULL,NULL,181,'Cynthia Hurley French Wines',NULL,NULL,NULL,NULL,NULL,NULL,NULL,181,'CYN'),(NULL,NULL,NULL,NULL,182,'Angels',NULL,NULL,NULL,NULL,NULL,NULL,NULL,182,'ANG'),(NULL,NULL,NULL,NULL,183,'Wilson Daniels',NULL,NULL,NULL,NULL,NULL,NULL,NULL,183,'WLS');
/*!40000 ALTER TABLE `Supplier` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-10-29 17:46:15