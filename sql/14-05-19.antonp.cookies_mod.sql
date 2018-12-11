REPLACE INTO `cw_addons` (`addon`, `descr`, `active`) VALUES ('cookies_warning', 'Displays cookies warning to the customers', '0');
replace into cw_languages set code='EN', name='addon_name_cookies_warning', value='Cookies Warning', topic='Addons';
replace into cw_languages set code='EN', name='lbl_cookies_warning_agree', value='Agree', topic='Labels';
replace into cw_languages set code='EN', name='lbl_cookie_policy', value='Cookie Policy', topic='Labels';

replace into cw_languages set code='EN', name='txt_cookie_warning', value='This website works best using cookies. You can <a class="html_link" href="/cooikie_info_page" style="color: #F3A512"> find out more and change your settings</a> any time but by continuing you agree to this.', topic='Text';
