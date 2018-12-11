SELECT @config_category_id:=config_category_id FROM cw_config_categories WHERE category='General';
REPLACE INTO cw_config SET name='google_ab_test_code_home', comment='Google Analytics Content Experiment code: Home page', value='', config_category_id=@config_category_id, orderby='800', type='textarea', defvalue='', variants='';
