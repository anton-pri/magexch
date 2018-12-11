UPDATE `cw_config` SET `variants` = '0:lbl_please_select 
g_metric: Metric
meter:   meter [m]
cm:   centimeter [cm]
mm:   millimeter [mm]
g_imperial: Imperial
yd:   yard [yd]
ft:   foot [ft]
in:   inch [in]
th:   thou [th]
g_custom: Custom
fill:   Please fill units below...' WHERE `cw_config`.`name` = 'length_unit_select' AND `cw_config`.`config_category_id` = 20;

UPDATE `cw_config` SET `variants` = '0:lbl_please_select
g_metric: Metric
kg:   kilogram [kg]
g:   gram [g]
mg:   milligram [mg]
g_imperial: Imperial
lb:   pound [lb]
oz:   ounce [oz]
dr:   dram [dr]
gr:   grain [gr]
g_custom: Custom
fill:   Please fill units below...' WHERE `cw_config`.`name` = 'weight_unit_select' AND `cw_config`.`config_category_id` = 20;
