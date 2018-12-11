<?php
// Register listener depending on settings
if (!empty($config['sitemap_xml']['sm_cron_period'])) {
    cw_event_listen('on_cron_'.$config['sitemap_xml']['sm_cron_period'],'cw_sitemap_cron');
}
