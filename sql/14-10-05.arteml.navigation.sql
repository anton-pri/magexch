-- CP-153

delete from cw_navigation_tabs where link='index.php?target=docs_O&action=add';
delete from cw_navigation_tabs where link='index.php?target=products&mode=add';
delete from cw_navigation_tabs where link='index.php?target=promosuite&action=form&offer_id=';
delete from cw_navigation_tabs where link='index.php?target=user_C&mode=add';
delete from cw_navigation_tabs where link='index.php?target=user_A&mode=add';
delete from cw_navigation_tabs where link='index.php?target=user_S&mode=add';
delete from cw_navigation_tabs where link='index.php?target=user_V&mode=add';
delete from cw_navigation_tabs where link='index.php?target=cms&mode=add';
delete from cw_navigation_tabs where link='index.php?target=shipping&shipping_id=';
delete from cw_navigation_tabs where link='index.php?target=message_box&mode=new';
delete from cw_navigation_tabs where link='index.php?target=addons_manager';
delete from cw_navigation_tabs where link='index.php?target=payments&mode=methods&payment_id=';

select @tid:=tab_id from cw_navigation_tabs where link='index.php?target=giftcerts';
select @sid:=section_id from cw_navigation_sections where title='lbl_section_orders' and area='A' limit 1;
update cw_navigation_targets set section_id = @sid where tab_id=@tid;

delete from cw_navigation_tabs where link='index.php?target=domains';
delete from cw_navigation_tabs where link='index.php?target=promosuite';
delete from cw_navigation_tabs where link='index.php?target=user_C';
delete from cw_navigation_tabs where link='index.php?target=user_A';
delete from cw_navigation_tabs where link='index.php?target=user_S';
delete from cw_navigation_tabs where link='index.php?target=user_V';
delete from cw_navigation_tabs where link='index.php?target=memberships';

delete from cw_navigation_tabs where link='index.php?target=news';

delete from cw_navigation_tabs where link='index.php?target=languages';

delete from cw_navigation_tabs where link='index.php?target=countries';

delete from cw_navigation_tabs where link='index.php?target=logging';

delete from cw_navigation_tabs where link='index.php?target=sitemap_xml';

delete from cw_navigation_tabs where link='index.php?target=settings';

delete from cw_navigation_tabs where link='index.php?target=configuration';
delete from cw_navigation_tabs where link='index.php?target=configuration&mode=addons';

delete from cw_navigation_tabs where link='index.php?target=payments&mode=methods';

delete from cw_navigation_tabs where link='index.php?target=license';

delete from cw_navigation_tabs where link='index.php?target=orders_statuses';

delete from cw_navigation_tabs where title='lbl_access_level';

REPLACE INTO `cw_languages` ( `code` , `name` , `value` , `tooltip` , `topic`) VALUES ( 'en', 'txt_warranty_desciption', 'Warranty is available as product property', '', 'Text'); 
update cw_languages set value='*' where name='lbl_ppd_field_required';
