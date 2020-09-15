REPLACE into cw_menu (menu_id, parent_menu_id, title, link, target, orderby, area, addon, is_loggedin) values (324, 151, 'lbl_seller_add_product', 'index.php?target=seller_add_product', 'seller_add_product', 390, 'V', 'custom_magazineexchange_sellers', 1);
REPLACE into cw_languages (code, name, value, topic) values ('EN', 'lbl_seller_add_product', 'Add My Magazine', 'Labels');
REPLACE into cw_breadcrumbs (link, title, parent_id, area) values ('/index.php?target=seller_add_product', 'Add New Product', 0, 'seller');
replace into cw_languages (code, name, value, tooltip, topic) values ('EN', 'lbl_seller_add_new_product_note', '<img src="/cw/skins_magazineexchange/images/question_wide.png">', 'lbl seller add new product note', 'Label');

replace into cw_languages (code, name, value, topic) values ('EN', 'msg_seller_product_add', 'msg_seller_product_add', 'Text');

replace into cw_languages (code, name, value, topic) values ('EN', 'msg_seller_product_upd', 'msg_seller_product_upd', 'Text');

replace into cw_attributes (name,type,field,active,orderby,addon,item_type,is_show_addon,protection) values ("Left Description Box ('Contents Listing' for  Magazine issues)",'textarea', 'magexch_product_le
ft_box', 1, 210, 'custom_magazineexchange', 'P', 1, 15);
replace into cw_attributes (name,type,field,active,orderby,addon,item_type,is_show_addon,protection) values ("Right Description Box ('Article snippets' for  Magazine issues) (Optional)",'textarea', 'magexc
h_product_right_box', 1, 220, 'custom_magazineexchange', 'P', 1, 15);


CREATE TABLE `cw_memberships_categories_edit_allowed` (
  `membership_id` int(11) NOT NULL DEFAULT '0',
  `category_id` int(11) NOT NULL DEFAULT '0',
  `permission` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`membership_id`,`category_id`),
  KEY `category_id` (`category_id`),
  KEY `membership_id` (`membership_id`),
  KEY `permission` (`permission`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


replace into 
	cw_languages (code, name, value, tooltip, topic) 
values (
'EN', 'lbl_seller_product_full_name_note', 
'<img src="/cw/skins_magazineexchange/images/question_wide.png">', 
'lbl seller product full name note', 
'Label'
);

replace into
        cw_languages (code, name, value, tooltip, topic)
values ('EN', 'lbl_seller_product_short_name_note','<img src="/cw/skins_magazineexchange/images/question_wide.png">','lbl seller product short name note','Label');

replace into
        cw_languages (code, name, value, tooltip, topic)
values ('EN', 'lbl_seller_product_main_image_note','<img src="/cw/skins_magazineexchange/images/question_wide.png">','lbl seller product main image note','Label');

replace into
        cw_languages (code, name, value, tooltip, topic)
values ('EN', 'lbl_seller_product_thumbnail_image_note','<img src="/cw/skins_magazineexchange/images/question_wide.png">','lbl seller product thumbnail image note','Label');

replace into
        cw_languages (code, name, value, tooltip, topic)
values ('EN', 'lbl_seller_product_additional_images_note','<img src="/cw/skins_magazineexchange/images/question_wide.png">','lbl seller product additional images note','Label');

replace into
        cw_languages (code, name, value, tooltip, topic)
values ('EN', 'lbl_seller_product_left_description_note','<img src="/cw/skins_magazineexchange/images/question_wide.png">','lbl seller product left description box note','Label');

replace into
        cw_languages (code, name, value, tooltip, topic)
values ('EN', 'lbl_seller_product_right_description_note','<img src="/cw/skins_magazineexchange/images/question_wide.png">','lbl seller product right description box note','Label');


delete from cw_attributes_values where attribute_id  IN (select attribute_id from cw_attributes where field = 'magexch_product_left_box');
delete from cw_attributes where field = 'magexch_product_left_box';
delete from cw_attributes_values where attribute_id  IN (select attribute_id from cw_attributes where field = 'magexch_product_right_box');
delete from cw_attributes where field = 'magexch_product_right_box';
REPLACE into cw_languages (code, name, value, topic) values ('EN', 'lbl_publish', 'Publish', 'Labels');
replace into cw_languages (code, name, value, topic) values ('EN', 'msg_seller_product_upd_published', 'msg_seller_product_upd_published', 'Text');
