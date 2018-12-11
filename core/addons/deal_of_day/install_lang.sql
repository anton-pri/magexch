INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_dod_generators', 'Deal of the day generators', 'Labels');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_dod_modify_generator', 'Modify generator', 'Labels');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_dod_details', 'Generator details', 'Labels'), ('EN', 'lbl_dod_generator_date', 'Generator period', 'Labels');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_dod_generator_title', 'Title', 'Labels'), ('EN', 'lbl_dod_unknown', 'n/a', 'Labels');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_dod_generator_desc', 'Description', 'Labels'), ('EN', 'lbl_dod_generator_active', 'Active', 'Labels');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_dod_generator_position', 'Position', 'Labels'), ('EN', 'lbl_dod_generator_image', 'Image', 'Labels');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_dod_button_save', 'Save', 'Labels');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_dod_manage_generators', 'Manage DOD generators', 'Labels'), ('EN', 'msg_dod_empty_fields', 'All the required generator fields should be filled in.', 'Text');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'msg_dod_incorrect_field_type', 'Incorrect type of the field: {{field_name}}.', 'Text'), ('EN', 'msg_dod_updated_succes', 'DOD Generators have been successfully updated.', 'Text');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_dod_generator_details', 'Details', 'Labels'), ('EN', 'lbl_dod_generator_bonuses', 'Bonuses', 'Labels');

REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES
('EN', 'lbl_dod_bonuses', 'Bonuses', 'Labels'),
('EN', 'lbl_dod_bonus_coupon', 'Give a discount coupon', 'Labels'),
('EN', 'lbl_dod_bonus_discount', 'Give a discount', 'Labels'),
('EN', 'lbl_dod_bonus_forfree', 'Give products for free', 'Labels'),
('EN', 'lbl_dod_bonus_freeship', 'Give free shipping', 'Labels'),
('EN', 'lbl_dod_delete_selected', 'Delete selected', 'Labels'),
('EN', 'lbl_dod_disc_for_selcted_products', 'apply a discount for selected below products', 'Labels'),
('EN', 'lbl_dod_disc_for_whole_cart', 'apply a discount for whole cart', 'Labels'),
('EN', 'lbl_dod_generator_enddate', 'End date', 'Labels'),
('EN', 'lbl_dod_generator_startdate', 'Start date', 'Labels'),
('EN', 'txt_dod_no_elements', 'No records were found.', 'Text'),
('EN', 'lbl_dod_new_generator','New generator','Labels'),
('EN', 'lbl_dod_discount_value','Discount:','Labels'),
('EN', 'lbl_dod_dtype_fixed','$','Labels'),
('EN', 'lbl_dod_dtype_percent','%','Labels'),
('EN', 'lbl_dod_products','Products','Labels'),
('EN', 'lbl_dod_categories','Categories','Labels'),
('EN', 'txt_dod_top_text', 'txt_dod_top_text', 'Text');

REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES
('EN', 'msg_dod_deleted', 'Selected DOD generators have been successfully deleted.', 'Text'),
('EN', 'msg_dod_incorrect_field_type', 'Incorrect type of the field: {{field_name}}.', 'Text');

REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES
('EN', 'msg_dod_bonus_incorrect', '"{{bonus}}" bonus type was filled in incorrectly.', 'Text');


REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_dod_new_coupon', 'Add new Coupon', 'Labels');

REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_dod_not_saved', 'Incorrect values entered. Generator has not been saved!', 'Labels');

REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_dod_generator_interval', 'Interval', 'Labels');

REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_dod_no_item_repeat', 'Avoid repeating dod items', 'Labels');

REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_dod_select_products_from', 'Select products from:', 'Labels');

REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_dod_manufacturers', 'Manufacturers', 'Labels');

replace into cw_languages set code='EN', name='txt_dod_alphabetical_order', topic='Text', value='alphabetical comparison', tooltip='If values are not numeric, they will be treated as string and will be compared in alphabetical order. E.g. "A" < "B", but "1 GB" < "10 GB" < "2 GB"';
