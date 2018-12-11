delete from cw_breadcrumbs where link = '/index.php?target=attribute_options&attribute_id=[[ANY]]';
replace into cw_breadcrumbs set title='lbl_feature_options_modify', parent_id=1, link = '/index.php?target=attribute_options&attribute_id=[[ANY]]';
replace into cw_languages (code,name,value,topic) values ('EN', 'lbl_feature_options_modify', 'Feature Options Modify', 'Labels');

delete from cw_navigation_tabs where title = 'lbl_feature_options_modify';
insert into cw_navigation_tabs (title, link, orderby) values ('lbl_feature_options_modify', 'index.php?target=attribute_options&attribute_id={$attribute.attribute_id}', 648);
SET @tab_id = LAST_INSERT_ID();
delete from cw_navigation_targets where target='attribute_options';
SELECT @section_id:=section_id FROM cw_navigation_sections WHERE title='lbl_products' AND area='A' LIMIT 1;
insert into cw_navigation_targets (target, params, visible, section_id, tab_id, orderby) values ('attribute_options', "!empty($attribute_id) && $target == 'attribute_options'", "!empty($attribute_id) && $target == 'attribute_options'", @section_id, @tab_id, 910);
