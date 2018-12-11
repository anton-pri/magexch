{include_once_src file='main/include_js.tpl' src='js/change_doc_ajax.js'}

{*if $current_area eq 'A'}
<form action="index.php?target=ajax&mode=aom&doc_id={$doc_id}" method="post" name="edit_warehouse_form">
<input type="hidden" name="action" value="update_warehouse" />
{include file='common/subheader.tpl' title=$lng.lbl_warehouse}
<div class="input_field_1">
    <label>{$lng.lbl_warehouse}</label>
    {include file='main/select/warehouse.tpl' name='warehouse_info[customer_id]' value=$order.info.warehouse_customer_id onchange="javascript: cw_submit_form_ajax('edit_warehouse_form');"}
</div>
</form>
{/if*}

<form action="index.php?target={$current_target}&doc_id={$doc_id}" method="post" name="edit_products_form">

<div class="box">

{include file='common/subheader.tpl' title=$lng.lbl_product_info}
<div id="doc_items">
{include file='addons/advanced_order_management/products.tpl' products=$order.products}
</div>

</div>
</form>

{include_once_src file="main/include_js.tpl" src="js/popup_product.js"}

{capture name=add_products}
<form action="index.php?target={$current_target}&doc_id={$doc_id}" method="post" name="add_products_form">

<div class="form-inline">

{include file='common/subheader.tpl' title=$lng.lbl_add_product}
{if $doc.type eq 'P'}
	{product_selector amount_name='newamount' form='add_products_form' supplier_id=$cart_customer.customer_id}
{else}
	{product_selector amount_name='newamount' form='add_products_form'}
{/if}
&nbsp;{include file='admin/buttons/button.tpl' button_title=$lng.lbl_add href="javascript:cw_doc_add_item_info('`$doc_id`', '`$index`', 'add_products_form');" style="btn-green"}
<div class="clear"></div>

</div>

</form>

{/capture}

{if $order.type eq 'G'}

<div class="box">

<table class="input_table" width="100%" border="0">
<tr valign="top">
    <td width="50%">{$smarty.capture.add_products}</td>
    <td width="5%">&nbsp;</td>
    <td>
<table class="header" border="0" width="100%">
<tr>
    <th width="100">{$lng.lbl_global_discount}</th>
    <th width="100">{$lng.lbl_value_discount}</th>
    <td rowspan="3" width="10">&nbsp;</td>
    <td rowspan="3" align="right" nowrap>
<h1>{$lng.lbl_cash_total}: <span id="doc_total">{include file='common/currency.tpl' value=$order.info.total display_sign=2}</span></h1>
<div class="input_field_easy_1_1">
    <label>{$lng.lbl_paid_by_cc}:</label>
    <input type="checkbox" id="gp_paid_by_cc" value="1"{if $order.pos.paid_by_cc} checked{/if} onclick="javascript: cw_doc_update_payment('{$doc_id}', '{$index}', 'gp_paid_by_cc')"/>
</div><br/>
<div class="input_field_easy_1_1">
    <label>{$lng.lbl_payment}:</label>
    <input type="text" id="gp_payment" value="{$order.pos.payment|formatprice}" size="10" onchange="javascript: cw_doc_update_payment('{$doc_id}', '{$index}', 'gp_payment')"/>
</div><br/>
<div class="input_field_easy_1_1">
    <label>{$lng.lbl_money_change}:</label>
    <input type="text" id="gp_change" value="{$order.pos.change|formatprice}" size="10" disabled />
</div>
    </td>
</tr>
<tr valign="top">
    <td nowrap align="center">
        <input type="text" id="gd_value" value="{$order.pos.gd_value|default:$order.info.discount}" size="8" onchange="javascript: cw_doc_update_discount('{$doc_id}', '{$index}', 'gd_value')"{if $current_area eq 'G' && !$accl.100002} disabled{/if}/>
        <input type="checkbox" id="gd_type" value="1"{if $order.info.gd_type} checked{/if} onclick="javascript: cw_doc_update_discount('{$doc_id}', '{$index}', 'gd_type')"{if $current_area eq 'G' && !$accl.100002} disabled{/if} />%
    </td>
    <td align="center">
        <input type="text" id="vd_value" value="{$order.pos.vd_value|default:$order.info.discount_value}" size="8" onchange="javascript: cw_doc_update_discount('{$doc_id}', '{$index}', 'vd_value')"{if $current_area eq 'G' && !$accl.100003} disabled{/if}/>
    </td>
</tr>
<tr valign="top">
    <td align="center">
        <input type="text" id="gd_value_persent" value="{math equation="b*100/a" a=$order.info.subtotal|default:0 b=$order.info.discount|default:0 assign='gd_value_persent'}{$gd_value_persent|formatprice}" size="8" disabled />%
    </td>
    <td align="center">
        <input type="text" id="vd_value_persent" value="{math equation="b*100/(a+b)" a=$order.info.total|default:0 b=$order.info.vd_value|default:0 assign='vd_value_persent'}{$vd_value_persent|formatprice}" size="8" disabled />%
    </td>
</tr>
</table>
<div align="right" id="print_applet">{include file='addons/pos/order.tpl'}</div>
    </td>
</tr>
</table>

</div>

{else}
{$smarty.capture.add_products}
{/if}
