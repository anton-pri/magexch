replace into cw_config set name='defer_load_js_code', comment='Deferred loading of javascript code', value='Y', config_category_id=20, orderby=708, type='checkbox', defvalue='Y';
replace into cw_config set name='use_js_packer', comment='Pack Javascript code (works with deferred load only)', value='Y', config_category_id=20, orderby=709, type='checkbox', defvalue='Y';
