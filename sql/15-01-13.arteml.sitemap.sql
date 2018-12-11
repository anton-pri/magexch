delete from cw_config where name='anonymous_pref';

UPDATE `cw_config` SET type='selector',value='monthly',`variants` = "weekly:Weekly\nbeweekly:Beweekly\nmonthly:Monthly\nannually:Annually" WHERE `cw_config`.`name` = 'sm_cron_period';
