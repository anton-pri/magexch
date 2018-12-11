select @cid:=config_category_id from cw_config_categories where category='docs';
REPLACE INTO `cw_config` ( `name` , `comment` , `value` , `config_category_id` , `orderby` , `type` , `defvalue` , `variants`) VALUES ( 'display_id_format', 'Sequence of order/invoices ID', 'A', @cid, '0', 'selector', '', "A:Autoincremental sequence (#NN)\nY:Start from 1 annually (#YYYY/NN)");

-- Delete special_offer from breadcrumbs
delete from cw_breadcrumbs where link like '%target=offers%';

-- Delete google_checkout config
delete from cw_config_categories where category='google_checkout';


-- Delete news_c controller
delete from cw_navigation_menu where link='index.php?target=news_c';
delete from cw_breadcrumbs where link like '%target=news_c%';
delete from cw_navigation_sections where link like '%target=news_c%';
delete from cw_navigation_tabs where link like '%target=news_c%';
delete from cw_navigation_targets where target='news_c';


-- Enable control over photo field
update cw_register_fields set is_protected=0 where field='image';

-- News breadcrumbs
delete from cw_breadcrumbs where link='/index.php?target=news&list_id=[[ANY]]&js_tab=message&messageid=[[ANY]]' or link='/index.php?target=news&messageid=[[ANY]]&list_id=[[ANY]]&js_tab=message';
select @bid:=breadcrumb_id from cw_breadcrumbs where link='/index.php?target=news' and title='lbl_news_management';
INSERT INTO `cw_breadcrumbs` (`breadcrumb_id`, `link`, `title`, `parent_id`, `addon`, `uniting`) VALUES (null, '/index.php?target=news&list_id=[[ANY]]&js_tab=message&messageid=[[ANY]]', 'lbl_modify_news', @bid, 'news', 0), (null, '/index.php?target=news&messageid=[[ANY]]&list_id=[[ANY]]&js_tab=message', 'lbl_modify_news', @bid, 'news', 0);
