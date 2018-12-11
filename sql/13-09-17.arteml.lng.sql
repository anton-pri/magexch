UPDATE `cw_languages` SET `name` = 'lbl_upl_max_warning' WHERE `cw_languages`.`name` = 'lbl_upl_max_warning_1';
UPDATE `cw_languages` SET tooltip='If you need to upload more bigger files, you have to change settings upload_max_filesize and post_max_size in your php.ini and restart the web server.', `value` = 'You can upload files not bigger than {{upload_max_filesize}}' WHERE `cw_languages`.`name` = 'lbl_upl_max_warning';
DELETE FROM cw_languages WHERE name='lbl_upl_max_warning_2';
UPDATE `cw_languages` SET `value` = 'Upload Error: File you try to upload is not in CSV format' WHERE `cw_languages`.`name` = 'lbl_err_file_is_not_csv';
UPDATE `cw_languages` SET `value` = 'Absolute physical path to X-Cart working directory on this server',
`tooltip` = 'e.g. /var/www/xcart' WHERE `cw_languages`.`name` = 'txt_path_to_xc';
UPDATE `cw_languages` SET `value` = 'Import from X-Cart' WHERE `cw_languages`.`name` = 'lbl_import_xcart';
UPDATE `cw_languages` SET `value` = 'Import from X-Cart working installation to Cartworks. X-Cart must be installed and configured on the same server.' WHERE `cw_languages`.`name` = 'txt_imp_from_xc';
DELETE FROM `cw_languages` WHERE name='txt_cc_ccash_note';
DELETE FROM `cw_languages` WHERE name='txt_cc_paybox_note';


