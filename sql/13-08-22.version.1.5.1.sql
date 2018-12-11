REPLACE INTO `cw_layouts` (`layout_id`, `data`, `layout`, `title`, `is_default`) VALUES
(1, 'a:2:{s:17:"products_per_page";s:0:"";s:8:"elements";a:4:{i:0;s:7:"product";i:4;s:17:"display_net_price";i:5;s:6:"amount";i:6;s:16:"display_subtotal";}}', 'docs_O', '', 1);

UPDATE cw_config SET value='1.5.1' where name='version';
