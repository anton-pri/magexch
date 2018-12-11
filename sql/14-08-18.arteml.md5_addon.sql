insert into cw_addons set addon='_inithashpasswords', descr='One time run addon to init md5 hash passwords feature', active=1, status=1, orderby=110;

ALTER TABLE cw_sessions_data DROP INDEX sess_id;
ALTER TABLE cw_sessions_data DROP PRIMARY KEY;
ALTER TABLE `cw_sessions_data` ADD PRIMARY KEY(`sess_id`);
