<?php
die('no access');
//this is the good version
//http://saratogawine.com/mysql_access/import/z.php
system('mysqldump -u saratoga_dbuser -pvm5BTzB=4QXn saratoga_live_hub | mysql -u saratoga_dbuser -pvm5BTzB=4QXn --host=saratogawine.com -C saratoga_dev_hub');