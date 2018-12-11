INSERT INTO `cw_config` (`name`, `comment`, `value`, `config_category_id`, `orderby`, `type`, `defvalue`, `variants`) VALUES ('weight_unit_select', 'Weight units', '', '20', '224', 'selector', '', '0:lbl_please_select
g_metric:&nbsp;Metric
kg:&nbsp;&nbsp; kilogram[kg]
g:&nbsp;&nbsp; gram[g]
mg:&nbsp;&nbsp; milligram[mg]
g_imperial:&nbsp;Imperial
lb:&nbsp;&nbsp; pound[lb]
oz:&nbsp;&nbsp; ounce[oz]
dr:&nbsp;&nbsp; dram[dr]
gr:&nbsp;&nbsp; grain[gr]
g_custom:&nbsp;Custom
fill:&nbsp;&nbsp; Please fill units below...');

INSERT INTO `cw_config` (`name`, `comment`, `value`, `config_category_id`, `orderby`, `type`, `defvalue`, `variants`) VALUES ('length_unit_select', 'Length units', '', '20', '235', 'selector', '', '0:lbl_please_select
g_metric:&nbsp;Metric
meter:&nbsp;&nbsp; meter[m]
cm:&nbsp;&nbsp; centimeter[cm]
mm:&nbsp;&nbsp; millimeter[mm]
g_imperial:&nbsp;Imperial
yd:&nbsp;&nbsp; yard[yd]
ft:&nbsp;&nbsp; foot[ft]
in:&nbsp;&nbsp; inch[in]
th:&nbsp;&nbsp; thou[th]
g_custom:&nbsp;Custom
fill:&nbsp;&nbsp; Please fill units below...');

UPDATE `cw_config` SET `orderby` = '240' WHERE `name` = 'dim_units';
UPDATE `cw_config` SET `orderby` = '245' WHERE `name` = 'dimensions_symbol_cm';


