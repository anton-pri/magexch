<?php

cw_display_service_header("Resetting main items data fingerprint");
print("<h1>Resetting main items data fingerprint</h1><br><br>");

cw_csvxc_logged_query("CREATE TABLE IF NOT EXISTS cw_dh_fingerprint_old (ID int(11) not null default 0, fingerprint varchar(32), PRIMARY KEY (ID))");
cw_csvxc_logged_query("truncate cw_dh_fingerprint_old");
$main_tbl_fields = cw_check_field_names(array(), $tables['datahub_main_data']);
cw_csvxc_logged_query("insert into cw_dh_fingerprint_old (ID, fingerprint) select ID, md5(concat(coalesce(".implode(",''), coalesce(", $main_tbl_fields).",''))) from $tables[datahub_main_data]");

print("<br/><br/><h3>End...</h3><a href='index.php?target=datahub_main_edit'>Return to main edit page</a>");
die;
