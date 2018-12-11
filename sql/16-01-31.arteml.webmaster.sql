REPLACE INTO `cw_config` (`name`, `comment`, `value`, `config_category_id`, `orderby`, `type`, `defvalue`, `variants`) VALUES
('webmaster_features_sep', 'Features', '', 130, 100, 'separator', '', ''),
('webmaster_A', 'Enable webmaster in admin area', 'N', 130, 105, 'checkbox', 'N', ''),
('webmaster_langvar', 'Edit language variables', 'Y', 130, 110, 'checkbox', 'Y', ''),
('webmaster_other_sep', 'Other', '', 130, 500, 'separator', '', '');
UPDATE cw_config SET orderby=510 WHERE name='robots';

REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES
('EN', 'lbl_webmaster_title_cms', 'content section "{{popup_title}}" #{{key}}', '', 'Labels'),
('EN', 'lbl_webmaster_title_langvar', 'language variable "{{key}}"', '', 'Labels');
