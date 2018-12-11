<?php
// Register listener depending on settings
if (!empty($config['google_base']['gb_cron_period'])) {
    cw_event_listen('on_cron_'.$config['google_base']['gb_cron_period'],'cw_google_base_cron');
}
