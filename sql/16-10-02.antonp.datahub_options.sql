INSERT INTO cw_config SET name='bottle_size_do_not_display', comment='bottle_size_do_not_display', value='187ml,3.0Ltr,4.0Ltr,4Ltr,5Ltr,6Ltr,9Ltr,18Ltr,19Ltr', config_category_id=(select config_category_id from cw_config_categories where category='flexible_import' limit 1), orderby='130', type='textarea', defvalue='', variants='';
