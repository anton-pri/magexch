-- Username
INSERT INTO `cw_register_fields` ( `field_id` , `section_id` , `field` , `type` , `variants` , `def` , `orderby` , `is_protected`) VALUES ( NULL , '1', 'username', 'T', '', '0', '-95', '1');
SET @uname=LAST_INSERT_ID();
REPLACE INTO `cw_register_fields_lng` ( `field_id` , `code` , `field`) VALUES ( @uname, 'EN', 'Username');

REPLACE INTO `cw_register_fields_avails` (`field_id`, `area`, `is_avail`, `is_required`) VALUES (@uname, 'V', 1, 1), (@uname, '#V', 1, 1);

REPLACE INTO `cw_languages` (
`code` ,
`name` ,
`value` ,
`tooltip` ,
`topic`
)
VALUES (
'EN', 'lbl_email_already_used', 'Email address already used', '', 'Labels'
);

replace into cw_register_fields_values (customer_id, field_id, value) select customer_id, @uname ,substr(email,1,LOCATE('@',email)-1) from cw_customers where usertype='V';

-- Fix menu
SELECT @mid:=menu_id FROM cw_menu WHERE title='lbl_other' AND parent_menu_id=0;
SELECT @flag:=count(*) FROM cw_menu WHERE target='digital_products' AND area='A' AND parent_menu_id=@mid;
DELETE FROM cw_menu WHERE target='digital_products' AND area='A' AND parent_menu_id=@mid AND @flag>1 LIMIT 1;
SELECT @flag:=count(*) FROM cw_menu WHERE target='import' AND area='A' AND parent_menu_id=@mid;
DELETE FROM cw_menu WHERE target='import' AND area='A' AND parent_menu_id=@mid AND @flag>1 LIMIT 1;
DELETE FROM cw_menu WHERE target='products' AND area='A' AND parent_menu_id=@mid LIMIT 1;

SELECT @midV:=menu_id FROM cw_menu WHERE title='lbl_tools' AND parent_menu_id=0 AND area='V';
UPDATE cw_menu SET parent_menu_id=@midV WHERE parent_menu_id=@mid AND area='V';

DELETE FROM cw_menu WHERE target='seller_about_title_basic' AND title='lbl_mag_magazineexchange' AND area='V' LIMIT 1;

-- Promo pages
CREATE TABLE `cw_magazine_sellers_pages` (
  `contentsection_id` int(11) NOT NULL COMMENT 'static page id',
  `customer_id` int(11) NOT NULL COMMENT 'seller id',
  KEY `contentsection_id` (`contentsection_id`),
  KEY `customer_id` (`customer_id`)
) ENGINE=InnoDB COMMENT='Assignments of staticpages to sellers';
