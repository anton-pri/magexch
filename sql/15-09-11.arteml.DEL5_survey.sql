delete from cw_languages where name IN ('lbl_shipping_cause','lbl_products_aspects');
delete from cw_config where name IN ('shipping_causes','products_aspects');
alter table cw_docs_info drop shipping_cause_id;
alter table cw_docs_info drop aspect_id;

-- Delete survey addon
delete from cw_languages where name like '%_survey_%';
delete from cw_languages where name like '%_surveys_%';
delete from cw_languages where name IN ('addon_descr_survey','addon_name_survey','config_survey','lbl_create_survey','lbl_menu_survey','lbl_modify_survey','lbl_survey','option_title_survey');
delete from cw_navigation_sections where addon='survey';
delete from cw_navigation_tabs where link like '%target=surveys%';
delete from cw_navigation_tabs where title='lbl_modify_survey';
delete from cw_navigation_targets where target='surveys';

select @cid:=config_category_id from cw_config_categories where category='survey';
delete from cw_config where config_category_id=@cid;
delete from cw_config where name in ('spambot_arrest_on_surveys','survey_sending_remainder');

drop table if exists cw_surveys, cw_surveys_lng, cw_survey_answers, cw_survey_answers_lng, cw_survey_events, cw_survey_maillist, cw_survey_questions, cw_survey_questions_lng, cw_survey_results, cw_survey_result_answers;
