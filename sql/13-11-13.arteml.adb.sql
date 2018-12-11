-- Rename ad_banner addon to adb
UPDATE `cw_addons` SET `addon` = 'adb', `descr` = 'Ad Banners. Allows to manage banners or any other extra content in frontend' WHERE `cw_addons`.`addon` = 'ad_banners';
UPDATE `cw_navigation_sections` SET `title` = 'lbl_ab_adb',`link` = 'index.php?target=adb',`addon` = 'adb',`access_level` = '',`area` = 'A',`main` = 'Y',`orderby` = 0,`skins_subdir` = 'adb' WHERE `title` like 'lbl_ab_ad_banner%';
UPDATE `cw_navigation_tabs` SET `title` = 'lbl_ab_adb',`link` = 'index.php?target=adb&mode=list',`orderby` = 100 WHERE `title` like 'lbl_ab_ad_banner%';

update cw_navigation_tabs set link=replace(link,'ad_banners','adb');
update cw_navigation_tabs set link=replace(link,'ad_banner','adb');

update `cw_navigation_targets`  set target='adb' where target like 'ad_banner%';
UPDATE `cw_navigation_menu` SET `link` = 'index.php?target=adb&mode=list',`addon` = 'adb',`is_loggedin` = 1 WHERE `title` = 'lbl_banners';
update cw_languages set name=replace(name,'ad_banners','adb');
update cw_languages set name=replace(name,'ad_banner','adb');

RENAME TABLE cw_ad_banners TO cw_adb, 
cw_ad_banners_alt_languages TO cw_adb_alt_languages,
cw_ad_banners_categories TO cw_adb_categories,
cw_ad_banners_images TO cw_adb_images,
cw_ad_banners_manufacturers TO cw_adb_manufacturers,
cw_ad_banners_products TO cw_adb_products,
cw_ad_banners_user_counters TO cw_adb_user_counters;

UPDATE `cw_available_images` SET `name` = 'adb_images' WHERE `cw_available_images`.`name` = 'ad_banners_images';

UPDATE cw_breadcrumbs set link=replace(link,'ad_banners','adb'), title=replace(title,'ad_banners','adb'), addon=replace(addon,'ad_banners','adb');
UPDATE cw_breadcrumbs set link=replace(link,'ad_banner','adb'), title=replace(title,'ad_banner','adb');

