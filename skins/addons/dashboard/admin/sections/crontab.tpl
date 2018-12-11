Scheduled Tasks are not configured for this installation.<br />
{if $crontab.last_run_date ne ''}The last Scheduled Task was executed at {$crontab.last_run_date}.{/if}
 Email notifications and other background tasks are currently suspended. Create the following Cron Job in your server control panel:
<br /><b>*/2 * * * * php {$app_main_dir}/cron/index.php {$config.Security.cron_code} &gt;/dev/null</b>
