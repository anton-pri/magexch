REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'lbl_area_seller', 'Seller area', '', 'Labels'); 
ALTER TABLE `cw_breadcrumbs` ADD `area` VARCHAR(32) NOT NULL DEFAULT 'admin' AFTER `uniting`;
ALTER TABLE `cw_breadcrumbs` ADD INDEX(`area`);

SELECT @parent:=breadcrumb_id FROM cw_breadcrumbs WHERE link='/index.php' AND area='admin';
UPDATE cw_breadcrumbs SET parent_id=0 WHERE parent_id=@parent;
DELETE FROM cw_breadcrumbs WHERE breadcrumb_id=@parent;

SELECT @parent:=breadcrumb_id FROM cw_breadcrumbs WHERE link='/index.php' AND area='seller';
UPDATE cw_breadcrumbs SET parent_id=0 WHERE parent_id=@parent;
DELETE FROM cw_breadcrumbs WHERE breadcrumb_id=@parent;
