<?php

$datahub_main_data_snapshot = "$tables[datahub_main_data]_snapshot";

cw_csvxc_logged_query("DROP TABLE IF EXISTS $datahub_main_data_snapshot");
cw_csvxc_logged_query("CREATE TABLE $datahub_main_data_snapshot LIKE $tables[datahub_main_data]");
cw_csvxc_logged_query("INSERT INTO $datahub_main_data_snapshot SELECT * FROM $tables[datahub_main_data]");

die('Done');
