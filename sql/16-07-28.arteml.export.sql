ALTER TABLE `cw_objects_set` CHANGE `set_type` `set_type` VARCHAR(16) NOT NULL; 
ALTER TABLE cw_objects_set DROP INDEX object_id;
ALTER TABLE cw_objects_set DROP INDEX customer_id;
alter table cw_objects_set add UNIQUE KEY `customer_id` (`customer_id`,`set_type`,`object_id`) USING BTREE;
