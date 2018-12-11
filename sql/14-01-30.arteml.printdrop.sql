update cw_languages set name='txt_pd_guidelines_free' where name='txt_pd_guidelines';
insert into cw_languages (code,name,value,topic) select code,'txt_pd_guidelines_paid',value,topic from cw_languages where name='txt_pd_guidelines_free';
insert into cw_languages (code,name,value,topic) values ('en','lbl_sold_out','sold out','Labels');
