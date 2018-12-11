alter table cw_attributes_classes_assignement drop primary key;
alter table cw_attributes_classes_assignement add column assignment_id int(11) primary key auto_increment;
alter table cw_attributes_classes_assignement add UNIQUE KEY (`attribute_class_id`,`attribute_id`);
