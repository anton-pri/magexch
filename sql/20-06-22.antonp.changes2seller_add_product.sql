REPLACE into cw_languages (code, name, value, topic) values ('EN', 'lbl_seller_add_product', 'Create Product Page', 'Labels');

-- replace into cw_menu (parent_menu_id, title, link, target, orderby, area, access_level, func_visible, addon, skins_subdir, is_loggedin) values (319, 'lbl_collections_available', 'index.php?target=seller_collections_available', 'seller_collections_available', 20, 'V', '', '', 'seller', '', 1);

delete from cw_menu where title='lbl_collections_available';
replace into cw_languages (code, topic, name, value) values ('EN', 'Labels', 'lbl_seller_create_new_product_page', 'Create new product page');


delete from cw_bredcrumbs where link like '%index.php?target=seller_add_product';
update cw_breadcrumbs set title='Edit Product Page' where link='/index.php?target=seller_add_product&product_id=[[ANY]]';
REPLACE into cw_breadcrumbs (link, title, parent_id, area) values ('/index.php?target=seller_add_product', 'Create New Product Page', 0, 'seller');

replace into cw_languages (code, topic, name, value) values ('EN', 'Labels', 'lbl_seller_create_new_product_page', 'Create new product page');
replace into cw_languages (code, topic, name, value) values ('EN', 'Labels', 'lbl_seller_add_product_note', '<span style="font-weight: bold; color: rgb(255, 0, 8);">Fro Trade & Professional Sellers only </span> - Create a new product page directly on the website');

replace into cw_languages (code, topic, name, value) values ('EN', 'Labels', 'lbl_seller_product_preview_note', 'Preview will open a new tab.<br>Browser popups must be enabled.');
replace into cw_languages (code, topic, name, value) values ('EN', 'Labels', 'lbl_seller_product_published_note', 'Once published the page cannot be deleted<br>but can be edited by it\'s original creator.');
