UPDATE `cw_navigation_sections` SET `link` = 'index.php?target=import&mode=impdata' WHERE `link` = 'index.php?target=import';
UPDATE `cw_navigation_sections` SET `link` = 'index.php?target=configuration' WHERE `link` = 'index.php?target=addons';
update cw_navigation_sections set addon=lower(addon);
update cw_navigation_menu set addon=lower(addon);
update cw_navigation_targets set addon=lower(addon);
update cw_navigation_targets set addon='' where target='speed_bar';
update cw_navigation_targets set addon='pos' where target='user_G' or target='docs_cash_report';
