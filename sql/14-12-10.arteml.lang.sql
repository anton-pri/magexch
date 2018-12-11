delete from cw_languages where name = 'lbl_welcome';
update cw_languages set value='Type characters from the picture' WHERE name='lbl_type_the_characters';

REPLACE INTO `cw_languages` (
`code` ,
`name` ,
`value` ,
`tooltip` ,
`topic`
)
VALUES (
'en', 'txt_faq_statement', 'Place FAQ for your customers here. To change this text, please edit the language variable ''txt_faq_statement'' from ''Languages'' menu in the admin area.', '', 'Text'
),
('en', 'txt_about_us_statement', 'Place information for your customers here. To change this text, please edit the language variable ''txt_about_us_statement'' from ''Languages'' menu in the admin area.', '', 'Text'
),
('en', 'txt_conditions_customer', 'Place terms and conditions for your customers here. To change this text, please edit the language variable ''txt_conditions_customer'' from ''Languages'' menu in the admin area.', '', 'Text'
),
('en', 'txt_help_zone_title', 'Place help text here. To change this text, please edit the language variable ''txt_help_zone_title'' from ''Languages'' menu in the admin area.', '', 'Text'
),
('en', 'txt_privacy_statement', 'Place privacy statement for your customers here. To change this text, please edit the language variable ''txt_privacy_statement'' from ''Languages'' menu in the admin area.', '', 'Text'
);

delete from cw_languages where name = 'txt_about';
