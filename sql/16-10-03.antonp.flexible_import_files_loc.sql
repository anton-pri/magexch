INSERT INTO cw_config SET name='flex_import_files_folder', comment='Flexible Import feeds folder', value='/home/saratdev/public_html/files/import_feeds', config_category_id=(select config_category_id from cw_config_categories where category='flexible_import' limit 1), orderby='140', type='text', defvalue='', variants='';