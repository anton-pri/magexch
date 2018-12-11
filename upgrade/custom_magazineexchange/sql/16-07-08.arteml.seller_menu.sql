SELECT @flag:=count(*) FROM cw_menu WHERE title='lbl_avail_type_incoming' AND area='V';
DELETE FROM cw_menu WHERE title='lbl_avail_type_incoming' AND area='V' AND @flag>1 LIMIT 1;
SELECT @flag:=count(*) FROM cw_menu WHERE title='lbl_sent' AND area='V';
DELETE FROM cw_menu WHERE title='lbl_sent' AND area='V' AND @flag>1 LIMIT 1;
SELECT @flag:=count(*) FROM cw_menu WHERE title='lbl_archive' AND area='V';
DELETE FROM cw_menu WHERE title='lbl_archive' AND area='V' AND @flag>1 LIMIT 1;

SELECT @midV:=menu_id FROM cw_menu WHERE title='lbl_catalog' AND parent_menu_id=0 AND area='V';
INSERT INTO `cw_menu` (`menu_id`, `parent_menu_id`, `title`, `link`, `target`, `orderby`, `area`, `access_level`, `func_visible`, `addon`, `skins_subdir`, `is_loggedin`) VALUES (NULL, @midV, 'lbl_promotion_pages', 'index.php?target=cms', 'cms', '370', 'V', '', '', 'seller', '', '1');
REPLACE INTO `cw_languages` (
`code` ,
`name` ,
`value` ,
`tooltip` ,
`topic`
)
VALUES (
'EN', 'lbl_promotion_pages', 'Promotion pages', '', 'Labels'
);



