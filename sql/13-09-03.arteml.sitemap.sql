UPDATE `cw_languages` SET `value` = 'This section allows you to quickly create XML sitemap. The sitemap corresponds to Sitemap Protocol as defined by <a href=''www.sitemap.org''>sitemaps.org</a> and can be used for Google Sitemap tool. Read more at <a href=''http://www.google.com/support/webmasters/bin/topic.py?topic=8476''>http://www.google.com/support/webmasters/bin/topic.py?topic=8476</a> ' WHERE `cw_languages`.`code` = 'EN' AND `cw_languages`.`name` = 'txt_sitemap_xml_note';

UPDATE `cw_config` SET `type`='checkbox', `comment` = 'Automatically update the sitemap monthly' WHERE `cw_config`.`name` = 'sm_cron_period';

delete from cw_config where name='last_invoice_cron_run';
