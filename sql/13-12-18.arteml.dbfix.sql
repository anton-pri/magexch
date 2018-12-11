-- Delete lost product options attributes
delete from cw_attributes_default where attribute_id IN (select a.attribute_id from cw_attributes a left join cw_product_options o on a.field=concat('product_options_',o.field) where a.addon='product_options' and o.field is NULL);

delete from cw_attributes_values where attribute_id IN (select a.attribute_id from cw_attributes a left join cw_product_options o on a.field=concat('product_options_',o.field) where a.addon='product_options' and o.field is NULL);

delete from cw_attributes where field NOT IN (select concat('product_options_',o.field) from cw_product_options o) AND addon='product_options' AND item_type='P';

delete from cw_languages where name IN (
'lbl_fill_error_address', 'lbl_fill_error_administration',  'lbl_fill_error_basic', 'lbl_fill_error_commerciale', 'lbl_fill_error_customer_company', 'lbl_fill_error_customer_info','lbl_fill_error_shipping',
'txt_reseller_newbie_registration_bottom', 'txt_reseller_profile_modified', 'txt_reseller_user_registration_bottom', 'txt_salesman_newbie_registration_bottom', 'txt_salesman_user_registration_bottom',
'lbl_reseller_login_page_text', 'lbl_reseller_profile_created','txt_create_reseller_profile','txt_modify_reseller_profile',
'lbl_insider-Insider','lbl_modify_customer-Modify Customer', 
'lbl_credit_delete_confirmation_header',
'lbl_price_list_top_note','txt_regions_management_top_text',
'lbl_ship_delete_confirmation_header','txt_delete_selected_ship_warning'
);

REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES
('EN', 'lbl_month_1', 'January', '', 'Labels'), ('EN', 'lbl_month_10', 'October', '', 'Labels'), ('EN', 'lbl_month_11', 'November', '', 'Labels'), ('EN', 'lbl_month_12', 'December', '', 'Labels'), ('EN', 'lbl_month_2', 'Fabruary', '', 'Labels'), ('EN', 'lbl_month_3', 'March', '', 'Labels'), ('EN', 'lbl_month_4', 'April', '', 'Labels'), ('EN', 'lbl_month_5', 'May', '', 'Labels'), ('EN', 'lbl_month_6', 'June', '', 'Labels'), ('EN', 'lbl_month_7', 'July', '', 'Labels'), ('EN', 'lbl_month_8', 'August', '', 'Labels'), ('EN', 'lbl_month_9', 'September', '', 'Labels'),
('EN', 'lbl_cod_explanation', 'Cash On Delivery - A type of transaction in which payment for a good is made at the time of delivery', '', 'Labels'),
('EN', 'lbl_monday', 'Monday', '', 'Labels'), ('EN', 'lbl_friday', 'Friday', '', 'Labels'), ('EN', 'lbl_sunday', 'Sunday', '', 'Labels'), ('EN', 'lbl_thursday', 'Thursday', '', 'Labels'), ('EN', 'lbl_tuesday', 'Tuesday', '', 'Labels'), ('EN', 'lbl_wednesday', 'Wednesday', '', 'Labels'), ('EN', 'lbl_saturday', 'Saturday', '', 'Labels'),
('EN', 'lbl_only_registered', 'Only for registered users', '', 'Labels'),
('EN', 'txt_import_data_text', 'Import CSV file previously exported from the store. All data in store will be overwritten.', '', 'Text'),
('EN', 'txt_ps_top_text', 'Offers contains description, general parameters, condition when offer activated and bonus it gives', '', 'Text'),
('EN', 'lbl_expl_text_for_features', 'Additional fields and attributes', '', 'Labels'),
('EN', 'lbl_invoice_delete_confirmation_header', 'Do you really want to remove the invoice', '', 'Labels'),
('EN', 'txt_delete_selected_invoices_warning', 'All related information will be also deleted from database. Are you sure that you want to delete these invoices?', '', 'Text');

select @fid:=field_id from cw_register_fields where field='membership_id';
update cw_register_fields_avails set is_avail=0 where field_id=@fid and area!='A';
