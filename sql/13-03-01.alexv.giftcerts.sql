ALTER TABLE `ars_docs_info` ADD `giftcert_ids` TEXT NOT NULL AFTER `giftcert_discount`;

SELECT @category_id:=config_category_id FROM ars_config_categories WHERE category='EStoreGift' LIMIT 1;
REPLACE INTO ars_config SET name='allow_customer_select_tpl', comment='Allow customers to choose a design for gift certificate cards that will be sent by postal mail', value='Y', config_category_id = @category_id, orderby='90', type='checkbox', defvalue='Y', variants='';
UPDATE `ars_config` SET type = 'selector', variants = 'template_default.tpl:template_default.tpl\ntemplate_sample_1.tpl:template_sample_1.tpl\ntemplate_sample_2.tpl:template_sample_2.tpl' WHERE name = 'default_giftcert_template' AND config_category_id = @category_id;

UPDATE `ars_config` SET `value` = 'N' WHERE `ars_config`.`name` = 'show_cart_summary' AND `ars_config`.`config_category_id` = 4 LIMIT 1;

REPLACE INTO `ars_modules` (`module`, `module_descr`, `active`, `status`, `parent_module`,`version`)
VALUES ('payment-gift-certificate', 'Gift Certificates processor', 1, 1, 'payment-system', '0.1');

INSERT INTO `ars_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_l_applied', 'applied', 'Label');
INSERT INTO `ars_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_gc_following_will_not_be_deleted', 'The following gift certificates will not be deleted', 'Label');
INSERT INTO `ars_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'err_gc_invalid_gcs', 'Order processing error !<br /> 
The following gift certificates cannot be used to pay for the order:<br /> 
 {{invalid_gcs}} <br /> 
This error may be caused by the following reasons:<br />
<ul>
<li>A gift certificate is deleted or modified by Administrator.</li>
<li>The ''Gift certificate'' payment method is not allowed.</li>
</ul>
<br /><br />', 'Errors');
INSERT INTO `ars_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_error_ccprocessor_error', 'Order processing error !<br />
Payment processor can not process your order. Data is invalid.
<br /><br />
Please contact administrator and report the error(s).<br />', 'Errors');
INSERT INTO `ars_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_error_ccprocessor_unavailable', 'The selected payment method is not available for payment.', 'Errors');