{strip}
{ldelim}
"update_time":{$update_time},
"order_info":{ldelim}
    "display_subtotal":"{$doc.info.display_subtotal|formatprice}",
    "total":"{$doc.info.total|formatprice}",
    "shipping_cost":"{$doc.info.shipping_cost|formatprice}" 
{rdelim},
"use_shipping_cost_alt":"{$doc.info.use_shipping_cost_alt}",
"tax_name":"{capture name=sub}{include file='addons/advanced_order_management/tax_name.tpl' order=$doc}{/capture}{$smarty.capture.sub|escape:"json"}",
"tax_cost":"{capture name=sub}{include file='addons/advanced_order_management/tax_cost.tpl' order=$doc}{/capture}{$smarty.capture.sub|escape:"json"}",
"shipping":"{capture name=sub}{include file='addons/advanced_order_management/shipping.tpl' order=$doc}{/capture}{$smarty.capture.sub|escape:"json"}",
"total":"{capture name=sub}{include file='common/currency.tpl' value=$doc.info.total display_sign=2}{/capture}{$smarty.capture.sub|escape:"json"}",
"gd_value":"{$doc.pos.gd_value|default:$doc.info.discount}",
"gd_value_persent":"{math equation="b*100/a" a=$doc.info.subtotal|default:0 b=$doc.info.discount|default:0 assign='gd_value_persent'}{$gd_value_persent|formatprice}",
"gd_type":"{$doc.pos.gd_type}",
"vd_value":"{$doc.pos.vd_value}",
"vd_value_persent":"{math equation="b*100/(a+b)" a=$doc.info.total|default:0 b=$doc.info.vd_value|default:0 assign='vd_value_persent'}{$vd_value_persent|formatprice}",
"paid_by_cc":"{$doc.pos.paid_by_cc}",
"payment":"{$doc.pos.payment|formatprice}",
"change":"{$doc.pos.change|formatprice}",
"products":"{capture name=sub}{include file='addons/advanced_order_management/products.tpl' products=$doc.products}{/capture}{$smarty.capture.sub|escape:"json"}",
"applet":"{capture name=sub}{include file='addons/pos/order.tpl' order=$doc}{/capture}{$smarty.capture.sub|escape:"json"}",
"errors":"{capture name=sub}{$errors}{/capture}{$smarty.capture.sub|escape:"json"}",
"reset_error":"{$reset_error}",
"pos_method":"{if $config.pos.is_use_printer eq 'Y'}{$config.pos.pos_method}{/if}",
"reload":"{if $config.pos.pos_reload eq 'Y'}{$catalogs.pos}/index.php?target=orders&action=add{/if}",
"preview":"{capture name=sub}{include file='addons/advanced_order_management/preview.tpl' order=$doc}{/capture}{$smarty.capture.sub|escape:"json"}",
"debug_data":"{$debug_data|escape:"json"}"
{rdelim}
{/strip}
