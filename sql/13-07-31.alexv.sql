INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_seo_settings', 'SEO settings', 'Labels');

UPDATE `cw_navigation_sections` SET `main` = 'N' WHERE `link` = 'index.php?target=message_box' AND `addon` = 'messaging_system' LIMIT 1;
