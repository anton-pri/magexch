select @cid:=config_category_id from cw_config_categories where category='General';
update cw_config set config_category_id=@cid where name='enable_meta_kaywords';

delete from cw_languages where name IN ('lbl_css_styles','lbl_shipping_cause','lbl_cause_invoice_id','lbl_cause_invoice_date','lbl_causale','lbl_contracts','lbl_contract','lbl_contracts_list','lbl_departments','lbl_department','err_field_department');

select @cid2:=config_category_id from cw_config_categories where category='CSS';
delete from cw_config where config_category_id=@cid2;
delete from cw_config_categories where config_category_id=@cid2;

DROP TABLE IF EXISTS cw_user_contracts;
/*
CREATE TABLE `cw_user_contracts` (
  `contract_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(32) NOT NULL DEFAULT '0',
  `content` mediumtext NOT NULL,
  PRIMARY KEY (`contract_id`)
);
*/

DROP TABLE IF EXISTS cw_user_departments;
ALTER TABLE cw_customers_customer_info DROP department_id;
/*
CREATE TABLE IF NOT EXISTS `cw_user_departments` (
  `department_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(32) NOT NULL DEFAULT '0',
  PRIMARY KEY (`department_id`)
);
*/

-- Delete accounting/movements/transactions functionality
DROP TABLE IF EXISTS cw_accounting_records, cw_accounting_categories;
delete from cw_languages where name like 'lbl_accounting%';
delete from cw_languages where name IN ('lbl_section_accounting','accounting_settings','option_title_accounting_settings','accounting_type_','accounting_type_P','lbl_general_accounting','lbl_add_accounting_record','lbl_modify_accounting_record','accounting_descr_type_O','accounting_descr_type_P','err_field_accounting_category_id','lbl_entry_unique_per_accounting_year','lbl_valorization','lbl_charge','lbl_decharge','lbl_existence','lbl_cmp_short','lbl_starting_stock','lbl_end_remaining_stock','lbl_warehouse_situation','lbl_register_field_sections_transactions');
delete from cw_languages where name like 'lbl%movement%';


