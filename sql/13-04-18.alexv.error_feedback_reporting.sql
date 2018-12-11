ALTER TABLE `cw_mail_spool` ADD `files` TEXT NOT NULL COMMENT 'Links to files, separated by commas';

SELECT @config_category_id:=config_category_id FROM cw_config_categories WHERE category='General' AND is_main=0;
REPLACE INTO cw_config SET name='last_urls_tracked_in_session', comment='The number last visited URLs must be tracked in session', value='0', config_category_id = @config_category_id, orderby='650', type='numeric', defvalue='0', variants='';

REPLACE INTO cw_config SET name='feedback_error_log', comment='', value='0|0', config_category_id = 1, orderby='0', type='text', defvalue='', variants='';


-- new module record
REPLACE INTO `cw_modules` (`module`, `module_descr`, `active`, `status`, `parent_module`,`version`)
VALUES ('Feedback_report', 'Feedback report', 1, 1, '', '0.1');


-- configuration options
DELETE FROM cw_config_categories WHERE category='feedback-report';

INSERT INTO `cw_config_categories` (`config_category_id` ,`category` ,`is_main`) VALUES (NULL , 'Feedback_report', '0');
SET @config_category_id = LAST_INSERT_ID();

REPLACE INTO cw_config SET name='fbr_email_to_send', comment='Email to send', value='', config_category_id = @config_category_id, orderby='10', type='text', defvalue='', variants='';
REPLACE INTO cw_config SET name='fbr_secret_hash', comment='Security key for access to feedback content', value='', config_category_id = @config_category_id, orderby='20', type='text', defvalue='', variants='';
REPLACE INTO cw_config SET name='fbr_create_feedback_on_error', comment='Create a feedback on PHP or SQL error', value='Y', config_category_id = @config_category_id, orderby='30', type='checkbox', defvalue='Y', variants='';
REPLACE INTO cw_config SET name='fbr_errors_per_day', comment='Errors per day, converted into feedback', value='10', config_category_id = @config_category_id, orderby='40', type='numeric', defvalue='10', variants='';

INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_send_feedback', 'Send feedback', 'Labels');