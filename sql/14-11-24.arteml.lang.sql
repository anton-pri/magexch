REPLACE INTO `cw_languages` ( `code` , `name` , `value` , `tooltip` , `topic`) VALUES ( 'EN', 'lbl_proceed_to_cart', 'Proceed to cart <i class="icon-chevron-right"></i>', '', 'Labels');

update cw_languages set value = replace(value,'func_','cw_') where name='lbl_osc_terms_and_conditions_note';

REPLACE INTO `cw_languages` ( `code` , `name` , `value` , `tooltip` , `topic`) VALUES ( 'EN', 'lbl_ps_times_to_repeat', 'Times to repeat', 'Same offer can be repeated several times. Applicable for products related conditions only, such as specified products, categories, manufacturers in cart.', 'Labels');
