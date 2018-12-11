REPLACE INTO `cw_languages` (
`code` ,
`name` ,
`value` ,
`topic`
)
VALUES (
'en', 'lbl_confirm', 'Confirm', 'Label'
);

UPDATE `cw_languages` SET `value` = 'Confirm deletion' WHERE `cw_languages`.`code` = 'EN' AND `cw_languages`.`name` = 'txt_delete_users_top_text';
UPDATE `cw_languages` SET `value` = 'Are you sure you want to delete the following users?' WHERE `cw_languages`.`code` = 'EN' AND `cw_languages`.`name` = 'txt_delete_users_top_note';
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'msg_adm_users_del', 'The selected users have been successfully deleted.', 'Text');
