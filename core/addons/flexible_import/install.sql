REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'addon_name_flexible_import', 'Flexible Import', 'Addons');
-- lang var
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_flexible_import', 'Flexible Import', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_profiles', 'Profiles', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_select_profile', 'Select profile', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_from_your_pc', 'from your local PC', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_from_server', 'from server', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_csv_files', 'CSV files', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_parsing_options', 'Parsing options', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_file_first_lines', 'Import file (first 10 lines)', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_parsed_file_lines', 'Parsed file (first 10 lines)', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_id', 'ID', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_standard_options', 'Standard options', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_lines_terminate', 'Lines terminate', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_advanced_file_options', 'Advanced file options', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_number_of_columns', 'Number of columns', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_enclose_char', 'Enclose char', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_escape_char',  'Escape char', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_column_num', 'Column num', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_chars_to_trim', 'Chars to trim', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_numeric', 'Numeric', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_additional_parser_params', 'Additional parser parameters', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_custom_options', 'Custom options', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_column_type', 'Column type', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_use_values_from_single_column', 'Use values from single column lines as categories', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_comma_delimiter', 'comma delimiter', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_semicolon_delimiter', 'semicolon delimiter', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_lines_to_skip', 'Number of first lines to skip', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_save_switch_column_layout', 'Save and switch to Columns layout', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_column_names_line_id', 'Column name line id', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_import_success', 'Import Successful!', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_test_profile', 'Test profile', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_example_file', 'Example file', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_no_files_to_import', 'There are no files to import from the server', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'err_field_import_option', 'Please choose parsing option', 'Errors');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'err_field_select_file', 'Please select file', 'Errors');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'err_field_names', 'Error parsing field names', 'Errors');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'err_creating_temp_table', 'Error creating temp table', 'Errors');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'err_field_profile_id', 'Please select profile', 'Errors');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'err_field_import_type', 'Please select local PC/server', 'Errors');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'err_parsing_error_at', 'Parsing error at line:', 'Errors');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'err_wrong_column_type', 'Wrong column type', 'Errors');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'err_unable_to_open_file', 'Unable to open file {{file}}', 'Errors');
--
-- Table structure for table `cw_flexible_import_files`
--
CREATE TABLE IF NOT EXISTS `cw_flexible_import_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file_name` varchar(255) NOT NULL,
  `table_name` varchar(50) NOT NULL,
  `profile_id` int(11) NOT NULL default 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

--
-- Table structure for table `cw_flexible_import_profiles`
--
CREATE TABLE IF NOT EXISTS `cw_flexible_import_profiles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `type` varchar(11) NOT NULL,
  `options` text NOT NULL,
  `mapping_data` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- new addon record
REPLACE INTO `cw_addons` (`addon`, `descr`, `active`) VALUES ('flexible_import', 'Allows to upload custom import feeds containing products and categories data.', '1');
