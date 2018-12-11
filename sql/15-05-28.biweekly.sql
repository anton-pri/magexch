update cw_config set value=replace(value,'beweekly','biweekly') where name='last_cron_run';
update cw_config set value=replace(value,'beweekly','biweekly'), variants=replace(variants,'beweekly:Beweekly','biweekly:Biweekly')  where name='gb_cron_period';
update cw_config set value=replace(value,'beweekly','biweekly'), variants=replace(variants,'beweekly:Beweekly','biweekly:Biweekly')  where name='sm_cron_period';
