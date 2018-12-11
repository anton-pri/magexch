ALTER TABLE cw_docs_user_info DROP INDEX customer_id_2;
DELETE FROM cw_languages WHERE name IN ('lbl_im_checking_out_anonymously','lbl_enter_your_email',
'lbl_real_email_is','lbl_i_want_to_buy_anonymously','lbl_anonymous','lbl_anonymous_customer','lbl_anonymous_customer_s',
'msg_anonymous_profile_add','msg_anonymous_profile_upd','opt_disable_anonymous_checkout','txt_anonymous_account_msg'
);

UPDATE cw_register_fields_avails SET is_required=0 WHERE (area like 'C%' or area like '#C%') AND 
field_id IN (SELECT field_id FROM cw_register_fields WHERE field='password');

REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES 
('EN', 'err_field_email_exists', "Email already exists, please login or <a href='index.php?target=help&amp;section=password&amp;action=recover_password&amp;email={{email}}'>reset your password</a>", '', 'Errors');

DELETE FROM cw_config WHERE `name`='anonymous_allowed';

UPDATE `cw_languages` SET `value` = 'Default customer options' WHERE `cw_languages`.`code` = 'EN' AND `cw_languages`.`name` = 'opt_sep18'; 
