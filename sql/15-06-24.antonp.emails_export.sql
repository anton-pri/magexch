replace into cw_languages (code, name, value, topic) values ('EN', 'lbl_export_email_addresses', 'Export Email Addresses','Labels');
replace into cw_config (name, comment, value, config_category_id, orderby, type, defvalue, variants) values ('user_emails_export_delimiter', 'Customer emails export CSV delimiter', ';', 20, 653, 'selector', ';', ';:Semicolon\n,:Comma\n\t:Tab');
