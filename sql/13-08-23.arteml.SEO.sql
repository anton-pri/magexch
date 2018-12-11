replace cw_languages (code, name, value, topic) SELECT code,'lbl_site_meta_title',value,topic from cw_languages where name='lbl_site_title';
delete from cw_languages where name='lbl_site_title';


replace cw_languages (code, name, value, topic) SELECT 'EN','lbl_site_meta_keywords',value,'Labels' from cw_config where name='meta_keywords';
replace cw_languages (code, name, value, topic) SELECT 'EN','lbl_site_meta_descr',value,'Labels' from cw_config where name='meta_descr';

delete from cw_config where name in ('meta_descr','meta_keywords','include_meta_products','include_meta_categories');
