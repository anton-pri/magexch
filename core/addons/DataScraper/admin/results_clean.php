<?php

$curr_site_id = &cw_session_register('curr_site_id',0);

if ($curr_site_id) {
    db_query("DELETE FROM $tables[datascraper_result_values]$curr_site_id");
    print("Cleared data for site #$curr_site_id");
}

cw_header_location("index.php?target=datascraper_results");
