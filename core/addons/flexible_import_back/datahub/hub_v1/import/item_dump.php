<?php
$mysql_acc_str = 'mysqldump -u saratoga_dbuser -pvm5BTzB=4QXn saratoga_live_hub';
$mysql_acc_str = "mysqldump -usaratdev_dbuser -p'hxoDTTD8R]6^' saratdev_cw2";
$dump_path = "../";
$date_str = date('Y-m-d__H-i-s');
system($s = $mysql_acc_str." item item_xref item_price > ".$dump_path."dump_hub_item_$date_str.sql");
print($s);
