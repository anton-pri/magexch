insert into cw_register_fields (section_id, field, type, orderby, is_protected) values ('1', 'suspend_account', 'C', 200, 1);
insert into cw_register_fields_lng (field_id, code, field) values ((select field_id from cw_register_fields where field='suspend_account'), 'EN', 'Suspend Account');
insert into cw_register_fields_avails (field_id, area, is_avail) VALUES ((select field_id from cw_register_fields where field='suspend_account'), '#C', 1), ((select field_id from cw_register_fields where field='suspend_account'), 'C', 1), ((select field_id from cw_register_fields where field='suspend_account'), 'C_0', 0), ((select field_id from cw_register_fields where field='suspend_account'), 'C_1', 0), ((select field_id from cw_register_fields where field='suspend_account'), 'C_2', 0);

replace into cw_languages (code, name, value, topic) values ('EN', 'eml_suspended_account_notification_subj', 'Suspend account requested', 'E-Mail');

replace into cw_languages (code, name, value, topic) values ('EN', 'eml_customer_suspend_account_admin_notification', 'The following user has requested to suspend the account. Further actions can be taken on the user profile page here {{user_page_link}}', 'E-Mail');
