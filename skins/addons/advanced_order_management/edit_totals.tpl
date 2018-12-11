<script type="text/javascript" language="JavaScript 1.2">
<!--
{literal}
function MarkElement(type) {
	if (document.edit_totals_form.elements['total_details['+type+'_alt]'] && document.edit_totals_form.elements['total_details[use_'+type+'_alt]'])
		document.edit_totals_form.elements['total_details['+type+'_alt]'].disabled = !document.edit_totals_form.elements['total_details[use_'+type+'_alt]'].checked;
}

function aom_change_shipping(new_shipping_id) {
    $('input[name="total_details[use_shipping_cost_alt]"]').attr('checked', false);
    cw_submit_form('edit_totals_form');
}

{/literal}
-->
</script>

<form action="index.php?target={$current_target}" method="post" name="edit_totals_form">
<div class="box">
<input type="hidden" name="mode" value="edit" />
<input type="hidden" name="action" value="update_totals" />
<input type="hidden" name="show" value="totals" />
<input type="hidden" name="doc_id" value="{$doc_id}" />

{assign var='lng_var' value="lbl_doc_info_`$doc.type`"}
{include file='common/subheader.tpl' title=$lng.lbl_details}

<table class="table table-striped table-borderless table-header-bg vertical-center" >
{if $config.Taxes.display_taxed_order_totals eq "Y"}
<tr>
	<td colspan="3">{$lng.txt_taxed_order_totals_displayed}</td>
</tr>
{/if}

{if $shipping_lost}
<tr>
	<td colspan="3">
{assign var="t_ship_method" value=$orig_order.shipping|trademark:$insert_trademark}
<font class="ErrorMessage">{$lng.lbl_warning}!</font> {$lng.lbl_aom_unaccessible_shipmethod|substitute:"t_ship_method":$t_ship_method}
<br /><br />
	</td>
</tr>
{/if}

<tr>
	<th>&nbsp;</th>
	<th>{$lng.lbl_aom_current_value}</th>
	<th>{$lng.lbl_aom_original_value}</th>
</tr>

<tr {cycle values=', class="cycle"'}>
    <td>{$lng.lbl_order_prefix}</td>
    <td><input type="text" size="12" name="total_details[prefix]" value="{$order.prefix}" class="form-control" /></td>
    <td>{$orig_order.prefix}</td>
</tr>


<tr {cycle values=', class="cycle"'}>
    <td>Order sequence id</td>
{if !$order.new}
    <td><input type="text" size="12" name="total_details[display_doc_id]" value="{$order.display_doc_id}" class="form-control" /></td>
    <td>{$orig_order.display_doc_id}</td>
{else}
    <td>auto<input type="hidden" name="total_details[display_doc_id]" value="00" /></td>
    <td>&nbsp;</td>
{/if}
</tr>

<tr{cycle values=', class="cycle"'}>
    <td>{$lng.lbl_date}</td>
    <td>
       <input type="text" size="12" name="total_details[date]" value="{$order.date|date_format:$config.Appearance.datetime_format}" class="form-control" />
    </td>
    <td>{$orig_order.date|date_format:$config.Appearance.datetime_format}</td>
</tr>

<tr{cycle values=', class="cycle"'} style="display:none;">
    <td>{$lng.lbl_expiration_date}</td>
    <td>{include file='main/select/date.tpl' name='total_details[expiration_date]' value=$order.info.expiration_date}</td>
    <td>{$orig_order.info.expiration_date|date_format:$config.Appearance.datetime_format}</td>
</tr>

<!--tr{cycle values=', class="cycle"'}>
    <td>{$lng.lbl_salesman}</td>
    <td>{include file='main/select/salesman.tpl' name='total_details[salesman_customer_id]' value=$order.info.salesman_customer_id}</td>
    <td>{$orig_order.salesman_info.title}</td>
</tr>

<tr {cycle values=', class="cycle"'}>
    <td>{$lng.lbl_company}</td>
    <td><select name="total_details[company_id]" class="form-control">
{foreach from=$companies item=company}
    <option value="{$company.company_id}"{if $company.company_id eq $order.userinfo.company_id} selected="selected"{/if}>{$company.company_title}</option>
{/foreach}
    </select></td>
    <td>{$orig_order.company_info.company_title}</td>
</tr-->

{if $order.type ne 'G'}
<tr{cycle values=', class="cycle"'}> 
	<td>{$lng.lbl_payment_method}</td>
	<td>
        {include file='main/select/payment.tpl' name='total_details[payment_method]' value=$order.info.payment_id is_please_select=1}
	</td>
	<td>{$orig_order.info.payment_label}</td>
</tr>
{/if}

{if $shipping_calc_error ne ""}
<tr class="TableHead">
	<td colspan="3">{$shipping_calc_service} {$lng.lbl_err_shipping_calc}<br /><font class="ErrorMessage">{$shipping_calc_error}</font>
</tr>
{/if}

<tr{cycle values=', class="cycle"'}> 
	<td>{$lng.lbl_delivery}</td>
	<td><div id="aom_details_shipping">
        {include file="addons/advanced_order_management/shipping.tpl"}
        </div></td>
	<td>{$orig_order.info.shipping_label|trademark:$insert_trademark|default:$lng.lbl_aom_shipmethod_notavail}</td>
</tr>

<tr{cycle values=', class="cycle"'} style="display:none;">
    <td>{$lng.lbl_cod_delivery_type}</td>
    <td>
    {if $cod_types}
    {include file="main/select/cod_type.tpl" name="total_details[cod_type_id]" value=$order.info.cod_type_id}
    {else}
    {$lng.lbl_aom_cod_type_not_avail}
    {/if}
    </td>
    <td>{$orig_order.info.cod_type_label}</td>
</tr>

{if $dhl_ext_countries}
<tr{cycle values=', class="cycle"'}>
	<td height="18">{$lng.lbl_dhl_ext_country}</td>
	<td>
<select name="dhl_ext_country" id="dhl_ext_country" class="form-control">
	<option value="">{$lng.lbl_please_select_one}</option>
{foreach from=$dhl_ext_countries item=c}
	<option value="{$c}"{if $c eq $dhl_ext_country} selected="selected"{/if}>{$c}</option>
{/foreach}
</select>
	</td>
	<td>{$orig_order.extra.dhl_ext_country}</td>
</tr>
{/if}

<tr{cycle values=', class="cycle"'}>
	<td height="18">{$lng.lbl_subtotal}</td>
	<td><input type="text" id="aom_display_subtotal" value="{$order.info.display_subtotal}" size="15" disabled class="form-control" ></td>
	<td>{include file='common/currency.tpl' value=$orig_order.info.display_subtotal}</td>
</tr>

<tr{cycle values=', class="cycle"'}>
	<td>{$lng.lbl_discount}</td>
	<td>
       <div class="row">
         <div class="col-xs-1"><input type="checkbox" class="push-10-t" name="total_details[use_discount_alt]" onclick="javascript: MarkElement('discount')"{if $order.info.use_discount_alt eq 'Y'} checked="checked"{/if} /></div>
         <div class="col-xs-11"><input type="text" size="12" maxlength="12" class="form-control" name="total_details[discount_alt]" value="{$order.info.discount}"{if $order.info.use_discount_alt ne 'Y'} disabled="disabled"{/if} /></div>
       </div>
       </td>
	<td>{include file='common/currency.tpl' value=$orig_order.info.discount}</td>
</tr>

{if $order.coupon_type ne "free_ship"}
<tr{cycle values=', class="cycle"'}>
	<td>{$lng.lbl_coupon_saving}</td>
	<td>
       <div class="row">

         <div class="col-xs-1 push-5-t">{include file='common/currency.tpl' value=$order.info.coupon_discount}</div>
         <div class="col-xs-11">
	    <select name="total_details[coupon_alt]" class="form-control">
	      <option value="">{$lng.lbl_new_coupon}</option>
	      {foreach from=$coupons item=v}
	        <option value="{$v.coupon}"{if $order.info.coupon eq $v.coupon} selected="selected"{/if}>{$v.coupon} -{$v.discount}{if $v.coupon_type eq 'percent'}%{else}${/if}</option>
	      {/foreach}
	      {if $order.info.coupon ne '' && $coupon_exists ne 'Y'}
	        <option value="__old_coupon__"{if $order.info.coupon eq $orig_order.info.coupon} selected="selected"{/if}>{$orig_order.info.coupon} -{$orig_order.info.coupon_discount}$ ({$lng.lbl_aom_coupon_not_found})</option>
	        {assign var="coupon_found" value="Y"}
	      {/if}
	    </select>
           <div class="float-left"  style="line-height: 30px;"></div>
         </div>
      </div>
      </td>
	<td>{include file='common/currency.tpl' value=$orig_order.info.coupon_discount} ({$order.info.coupon})</td>
</tr>
<tr{cycle values=', class="cycle"'}>
	<td>{$lng.lbl_new_coupon_saving}</td>
	<td>
       <div class="row">
           <div class="col-xs-1 push-5-t"><input type="checkbox" name="total_details[use_coupon_discount_alt]" onclick="javascript: MarkElement('coupon_discount')"{if $order.info.use_coupon_discount_alt eq 'Y'} checked="checked"{/if} /></div>
  	    <div class="col-xs-11"><input type="text" size="12" class="form-control" maxlength="12" id="coupon_discount_alt" name="total_details[coupon_discount_alt]" value="{$order.info.coupon_discount}"{if $order.info.use_coupon_discount_alt ne 'Y'} disabled="disabled"{/if} /></div>
       </div>
       </td>
	<td>&nbsp;</td>
</tr>
{/if}

{if $order.info.discounted_subtotal ne $order.info.subtotal}
<tr{cycle values=', class="cycle"'}>
	<td>{$lng.lbl_discounted_subtotal}</td>
	<td><input type="text" size="12" value="{$order.info.display_discounted_subtotal}" disabled class="form-control"></td>
	<td>{include file='common/currency.tpl' value=$orig_order.info.display_discounted_subtotal}</td>
</tr>
{/if}

{if $order.info.coupon_type eq "free_ship"}
{assign var="shipping_cost" value=0}
{else}
{assign var="shipping_cost" value=$order.info.display_shipping_cost}
{/if}
{if $order.info.coupon_type eq "free_ship"}
<tr{cycle values=', class="cycle"'}>
{else}
<tr{cycle values=', class="cycle"'}>
{/if}
	<td>{$lng.lbl_shipping_cost}</td>
       <td>
       <div class="row">
           <div class="col-xs-1 push-5-t"><input type="checkbox" name="total_details[use_shipping_cost_alt]" value="Y"{if $order.info.use_shipping_cost_alt eq "Y"} checked="checked"{/if} onclick="javascript:MarkElement('shipping_cost')" /></div>
           <div class="col-xs-11"><input id="aom_shipping_cost" type="text"  class="form-control" size="15" maxlength="15" name="total_details[shipping_cost_alt]" value="{if $order.info.use_shipping_cost_alt eq "Y"}{$order.info.shipping_cost_alt|formatprice}{else}{$order.info.shipping_cost|formatprice}{/if}"{if $order.info.use_shipping_cost_alt eq ""} disabled="disabled"{/if} /></div>
       </td>
	<td>{include file='common/currency.tpl' value=$orig_order.info.display_shipping_cost}</td>
</tr>

{if $order.info.coupon_type eq "free_ship"}
<tr{cycle values=', class="cycle"'}>
	<td>{$lng.lbl_coupon_saving}</td>
	<td>{include file='common/currency.tpl' value=$order.info.coupon_discount} ({$order.info.coupon})</td>
	<td>{include file='common/currency.tpl' value=$orig_order.info.coupon_discount} ({$order.info.coupon})</td>
</tr>
{/if}

<tr{cycle values=', class="cycle"'}>
    <td>{$lng.lbl_shipping_insurance}</td>
    <td>
       <div class="row">
           <div class="col-xs-1 push-5-t"><input type="checkbox" name="total_details[use_shipping_insurance_alt]" value="Y"{if $order.info.use_shipping_insurance_alt eq "Y"} checked="checked"{/if} onclick="javascript:MarkElement('shipping_insurance');" /></div>
           <div  class="col-xs-11"><input type="text" size="15" class="form-control" maxlength="15" name="total_details[shipping_insurance_alt]" value="{$order.info.shipping_insurance|formatprice}"{if $order.info.use_shipping_insurance_alt eq ""} disabled="disabled"{/if} /></div>
       </div>
    </td>
    <td>{include file='common/currency.tpl' value=$orig_order.info.shipping_insurance}</td>
</tr>

<tr{cycle values=', class="cycle"'} style="display:none;">
    <td>{$lng.lbl_shipment_paid}</td>
    <td>{include file='main/select/shipping_paid.tpl' name='total_details[shipment_paid]' value=$order.info.shipment_paid}</td>
    <td>{include file='main/select/shipping_paid.tpl' flat=1 value=$orig_order.info.shipping_paid}</td>
</tr>

<tr{cycle values=', class="cycle"'}>
    <td>{$lng.lbl_tax_exception}</td>
    <td>
    {if $special_taxes}
    {include file="main/select/special_tax.tpl" name="total_details[special_tax_id]" value=$order.info.special_tax_id}
    {else}
    {$lng.lbl_aom_special_tax_not_avail}
    {/if}
    </td>
    <td>{$orig_order.info.special_tax_label}</td>
</tr>

{if ($orig_order.info.applied_taxes or $order.info.taxes) and $config.Taxes.display_taxed_order_totals ne "Y"}
<tr{cycle values=', class="cycle"'}>
	<td><div id="aom_tax_name">
        {include file="addons/advanced_order_management/tax_name.tpl"}
        </div></td>
	<td><div id="aom_tax_cost">
        {include file="addons/advanced_order_management/tax_cost.tpl"}
	</div></td>
	<td nowrap="nowrap">
{foreach key=tax_name item=tax from=$orig_order.info.applied_taxes}
{include file='common/currency.tpl' value=$tax.tax_cost}<br />
{/foreach}
	</td>
</tr>
{/if}

{if $order.info.payment_surcharge}
<tr{cycle values=', class="cycle"'}>
	<td class="LabelStyle" nowrap="nowrap">{if $order.info.payment_surcharge gt 0}{$lng.lbl_payment_method_surcharge}{else}{$lng.lbl_payment_method_discount}{/if}</td>
	<td>{include file='common/currency.tpl' value=$order.info.payment_surcharge}</td>
	<td>{include file='common/currency.tpl' value=$orig_order.info.payment_surcharge}</td>
</tr>
{/if}

{if $order.info.giftcert_discount gt 0}
<tr{cycle values=', class="cycle"'}>
	<td class="LabelStyle" nowrap="nowrap">{$lng.lbl_giftcert_discount}</td>
	<td>{include file='common/currency.tpl' value=$order.info.giftcert_discount}</td>
	<td>{include file='common/currency.tpl' value=$orig_order.info.giftcert_discount}</td>
</tr>
{/if}

<tr{cycle values=', class="cycle"'}>
	<td><b style="text-transform: uppercase;">{$lng.lbl_total}</b></td>
	<td><input type="text" class="form-control" id="aom_total" size="15" value="{$order.info.total}" disabled ></td>
	<td><b>{include file='common/currency.tpl' value=$orig_order.info.total}</b></td>
</tr>

{if ($orig_order.info.applied_taxes or $order.info.taxes) and $config.Taxes.display_taxed_order_totals eq "Y"}
<tr>
	<td colspan="2"><b>{$lng.lbl_including}:</b></td>
	<td><b>{$lng.lbl_including}:</b></td>
</tr>
<tr class="cycle">
	<td>
{foreach key=tax_name item=tax from=$order.info.taxes}
{$tax.tax_display_name}{if $tax.rate_type eq "%"} {$tax.rate_value}%{/if}:<br />
{/foreach}
	</td>
	<td>
{foreach key=tax_name item=tax from=$order.info.taxes}
{include file='common/currency.tpl' value=$tax.tax_cost}<br />
{/foreach}
	</td>
	<td nowrap="nowrap">
{foreach key=tax_name item=tax from=$orig_order.info.applied_taxes}
{$tax.tax_display_name}{if $tax.rate_type eq "%"} {$tax.rate_value}%{/if}: {include file='common/currency.tpl' value=$tax.tax_cost}<br />
{/foreach}
	</td>
</tr>
{/if}

{section name=rn loop=$order.info.reg_numbers}
{if %rn.first%}
<tr{cycle values=', class="cycle"' advance=false}>
	<td valign="top" colspan="3" class="LabelStyle" nowrap="nowrap">{$lng.lbl_registration_number}:	</td>
</tr>
{/if}

<tr{cycle values=', class="cycle"'}>
	<td valign="top" colspan="3" nowrap="nowrap">&nbsp;&nbsp;{$order.info.reg_numbers[rn]}</td>
</tr>
{/section}

{if $order.info.applied_giftcerts}
<tr>
	<td colspan="3" height="14">&nbsp;</td>
</tr>

<tr class="TableHead">
	<td colspan="3" height="16" class="LabelStyle"><b>{$lng.lbl_applied_giftcerts}:</b></td>
</tr>

{section name=gc loop=$order.info.applied_giftcerts}
<tr{cycle values=', class="cycle"'}>
	<td>&nbsp;&nbsp;{$order.info.applied_giftcerts[gc].giftcert_id}:</td>
	<td>{include file='common/currency.tpl' value=$order.info.applied_giftcerts[gc].giftcert_cost}</td>
	<td>{include file='common/currency.tpl' value=$orig_order.info.applied_giftcerts[gc].giftcert_cost}</td>
</tr>
{/section}
{/if}


</table>

</div>

<div class="buttons">{include file='admin/buttons/button.tpl' button_title=$lng.lbl_update href="javascript:cw_submit_form('edit_totals_form');" style="btn-green"}</div>

</form>
