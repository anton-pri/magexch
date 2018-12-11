ALTER TABLE `cw_languages` ADD `tooltip` MEDIUMTEXT NOT NULL DEFAULT '' AFTER `value`;

INSERT INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'lbl_tooltip', 'Tooltip', '', 'Labels');