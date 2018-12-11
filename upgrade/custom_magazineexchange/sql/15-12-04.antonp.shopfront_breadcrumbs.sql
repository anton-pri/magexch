DELETE FROM cw_breadcrumbs WHERE link='/index.php?target=seller_shopfront';
REPLACE INTO cw_breadcrumbs (link, title, parent_id, addon) VALUES ('/index.php?target=seller_shopfront', 'lbl_mag_my_shopfront', 1, '');
