<?php
$sql = "UPDATE acme_feed
				SET `Cost` = TRIM(REPLACE(REPLACE(`Cost`, '$', ''), ',', ''));";
mysql_query($sql) or die($sql);
//////////////////

// do not clean angels_share!
//$sql = "UPDATE angels_share_feed
//				SET Price = REPLACE(Price, '$', '')";
//mysql_query($sql) or die($sql);

//////////////
$sql = "UPDATE BevAccessFeeds
				SET `case_price` = TRIM(REPLACE(REPLACE(`case_price`, '$', ''), ',', '')),
				`bot_price` = TRIM(REPLACE(REPLACE(`bot_price`, '$', ''), ',', ''))";
mysql_query($sql) or die($sql);
///////////

$sql = "UPDATE bowler_feed
				SET `Cost` = TRIM(REPLACE(REPLACE(`Cost`, '$', ''), ',', '')),
				`Cost 1` = TRIM(REPLACE(REPLACE(`Cost 1`, '$', ''), ',', '')),
				`Cost 2` = TRIM(REPLACE(REPLACE(`Cost 2`, '$', ''), ',', ''));";
mysql_query($sql) or die($sql);
////////
$sql = "UPDATE BWL_feed
				SET `IS PRICE` = TRIM(REPLACE(REPLACE(`IS PRICE`, '$', ''), ',', ''))";
mysql_query($sql) or die($sql);
/////////////////
//$sql = "UPDATE cellar_feed
//				SET `Case` = TRIM(REPLACE(REPLACE(`Case`, '$', ''), ',', '')),
//				`Bottle` = TRIM(REPLACE(REPLACE(`Bottle`, '$', ''), ',', ''))";
//mysql_query($sql) or die($sql);
/////////////
$sql = "UPDATE Cordon_feed
				SET `Wholesale Price` = TRIM(REPLACE(REPLACE(`Wholesale Price`, '$', ''), ',', ''))";
mysql_query($sql) or die($sql);
///////////////////////
//$sql = "UPDATE Domaine_feed
//				SET `NY LIST PRICE` = TRIM(REPLACE(REPLACE(`NY LIST PRICE`, '$', ''), ',', ''));";
mysql_query($sql) or die($sql);
////////////////////////
$sql = "UPDATE DWB_store_feed
				SET `Cost` = TRIM(REPLACE(REPLACE(`Cost`, '$', ''), ',', '')),
				`DWB Min Price` = TRIM(REPLACE(REPLACE(`DWB Min Price`, '$', ''), ',', ''))";

mysql_query($sql) or die($sql);
////////////////////////
$sql = "UPDATE EBD_feed
				SET `CASE` = TRIM(REPLACE(REPLACE(`CASE`, '$', ''), ',', '')),
				`BOTTLE` = TRIM(REPLACE(REPLACE(`BOTTLE`, '$', ''), ',', '')),
				`PO CS` = TRIM(REPLACE(REPLACE(`PO CS`, '$', ''), ',', '')),
				`PO BTL` = TRIM(REPLACE(REPLACE(`PO BTL`, '$', ''), ',', '')),
				`SAVE` = TRIM(REPLACE(REPLACE(`SAVE`, '$', ''), ',', ''));";
mysql_query($sql) or die($sql);
///////////////////
$sql = "UPDATE grape_feed
				SET `Price` = TRIM(REPLACE(REPLACE(`Price`, '$', ''), ',', '')),
				`Post-off` = TRIM(REPLACE(REPLACE(`Post-off`, '$', ''), ',', ''))";

mysql_query($sql) or die($sql);
/////////////////////////////////
$sql = "UPDATE noble_feed
				SET `btl price` = TRIM(REPLACE(REPLACE(`btl price`, '$', ''), ',', '')),
				`case p/o` = TRIM(REPLACE(REPLACE(`case p/o`, '$', ''), ',', '')),
				`btl p/o` = TRIM(REPLACE(REPLACE(`btl p/o`, '$', ''), ',', ''));";
mysql_query($sql) or die($sql);
////////////////////////////////
$sql = "UPDATE Polaner_feed
				SET `Sug Retail Price` = TRIM(REPLACE(REPLACE(`Sug Retail Price`, '$', ''), ',', '')),
				`FL Price` = TRIM(REPLACE(REPLACE(`FL Price`, '$', ''), ',', ''))";
mysql_query($sql) or die($sql);
////////////////////////
//skurnik already done through transfer text
/////////////////////////////////////
$sql = "UPDATE Touton_feed
				SET `Field13` = TRIM(REPLACE(REPLACE(`Field13`, '$', ''), ',', '')),
				`Field15` = TRIM(REPLACE(REPLACE(`Field15`, '$', ''), ',', '')),
				`Field17` =TRIM(REPLACE(REPLACE(`Field17`, '$', ''), ',', ''))
				";
mysql_query($sql) or die($sql);
///////////////////

$sql = "UPDATE triage_feed
				SET `Base Price` =  TRIM(REPLACE(REPLACE(`Base Price`, '$', ''), ',', ''));";
mysql_query($sql) or die($sql);
/////////////
$sql = "UPDATE vehr_feed
				SET `FL Bttl` = TRIM(REPLACE(REPLACE(`FL Bttl`, '$', ''), ',', '')),
				`PO Bttl` = TRIM(REPLACE(REPLACE(`PO Bttl`, '$', ''), ',', ''))";
mysql_query($sql) or die($sql);
//////////////////
$sql = "UPDATE vias_feed
				SET `Line` = TRIM(REPLACE(REPLACE(`Line`, '$', ''), ',', ''))";
mysql_query($sql) or die($sql);
//////////////////
$sql = "UPDATE vision_feed
				SET `Bottle Net Cost` = TRIM(REPLACE(REPLACE(`Bottle Net Cost`, '$', ''), ',', '')),
				`Minimum Price` = TRIM(REPLACE(REPLACE(`Minimum Price`, '$', ''), ',', ''))";
mysql_query($sql) or die($sql);
//////////////////
$sql = "UPDATE vinum_feed
				SET `Base Price` = TRIM(REPLACE(REPLACE(`Base Price`, '$', ''), ',', ''))";
mysql_query($sql) or die($sql);
////
//no fields for wildman to update
