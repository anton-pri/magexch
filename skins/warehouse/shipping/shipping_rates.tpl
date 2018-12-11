{if $type eq "D"}
{include file='common/page_title.tpl' title=$lng.lbl_shipping_charges}
{$lng.txt_shipping_charges_note|substitute:"weight_symbol":$config.General.weight_symbol}
{else}
{include file='common/page_title.tpl' title=$lng.lbl_shipping_markups}
{$lng.txt_shipping_markups_note|substitute:"weight_symbol":$config.General.weight_symbol}
{/if}

<form action="index.php?target=shipping_rates" method="get" name="zoneform">

<input type="hidden" name="type" value="{$type}" />

<b>{if $type eq "D"}{$lng.lbl_edit_charges_for}{else}{$lng.lbl_edit_markups_for}{/if}</b><br />

<select name="shipping_id" onchange="document.zoneform.submit()">
	<option value="">{$lng.lbl_all_methods}</option>
{section name=ship_num loop=$shipping}
	<option value="{$shipping[ship_num].shipping_id}"{if $smarty.get.shipping_id ne "" and $smarty.get.shipping_id eq $shipping[ship_num].shipping_id} selected="selected"{/if}>{$shipping[ship_num].shipping|trademark} ({if $shipping[ship_num].destination eq "I"}{$lng.lbl_intl}{else}{$lng.lbl_national}{/if})</option>
{/section}
</select>

<select name="zone_id" onchange="document.zoneform.submit()">
	<option value="">{$lng.lbl_all_zones}</option>
{section name=zone loop=$zones}
	<option value="{$zones[zone].zone_id}"{if $smarty.get.zone_id ne "" and $smarty.get.zone_id eq $zones[zone].zone_id} selected="selected"{/if}>{$zones[zone].zone}</option>
{/section}
</select>

</form>

<br /><br />

{capture name=section}

{if $shipping_rates_avail gt 0}

<table cellpadding="0" cellspacing="0" width="100%">
<tr>
	<td>
        <input type='checkbox' class='select_all' class_to_select='{$zones_list[zone].zone.zone_id}rates_item' />
    </td>
	<td align="right">
{if $type eq "D"}{include file='buttons/button.tpl' button_title=$lng.lbl_add_shipping_charge_values href="#addrate"}{else}{include file='buttons/button.tpl' button_title=$lng.lbl_add_shipping_markup_values href="#addrate"}{/if}
	</td>
</tr>
</table>

<br /><br />

<form action="index.php?target=shipping_rates" method="post" name="shippingratesform">
<input type="hidden" name="action" value="update" />
<input type="hidden" name="zone_id" value="{$smarty.get.zone_id|escape:"html"}" />
<input type="hidden" name="shipping_id" value="{$smarty.get.shipping_id|escape:"html"}" />
<input type="hidden" name="type" value="{$type}" />

<table cellpadding="0" cellspacing="1" width="100%">

{* $zones_list = array("zone"=>array(...), "shipping_methods"=>array(...)) *}
{section name=zone loop=$zones_list}

{if $zones_list[zone].shipping_methods}

<tr>
	<td>{include file="common/subheader.tpl" title=$zones_list[zone].zone.zone class="black"}</td>
</tr>

{capture name=rates_list}
{foreach key=shipid item=shipping_method from=$zones_list[zone].shipping_methods}
{* $shipping_method = array(array("shipping_id"=>..., "shipping"=>..., "rates"=>array(...))) *}

<tr>
	<td class="SubHeaderGreyLine"><img src="{$ImagesDir}/spacer.gif" class="Spc" alt="" /></td>
</tr>

<tr class="TableSubHead">
	<td>
<table cellpadding="2" cellspacing="0">

<script type="text/javascript" language="JavaScript 1.2">
<!--
checkboxes{$zones_list[zone].zone.zone_id}_{$shipid} = new Array({section name=rate loop=$shipping_method.rates}{if not %rate.first%},{/if}'posted_data[{$shipping_method.rates[rate].rate_id}][to_delete]'{/section});
-->  
</script> 

<tr>
	<td><input type="checkbox" id="sm_{$zones_list[zone].zone.zone_id}_{$shipid}" name="sm_{$zones_list[zone].zone.zone_id}_{$shipid}" onclick="javascript:select_all_checkboxes(this.checked, 'shippingratesform', checkboxes{$zones_list[zone].zone.zone_id}_{$shipid});" class="{$zones_list[zone].zone.zone_id}rates_item" /></td>
	<td><b><label for="sm_{$zones_list[zone].zone.zone_id}_{$shipid}">{$shipping_method.shipping|trademark} ({if $shipping_method.destination eq "I"}{$lng.lbl_intl}{else}{$lng.lbl_national}{/if})</label></b></td>
</tr>
</table>

	</td>
</tr>

<tr>
	<td class="SubHeaderGreyLine"><img src="{$ImagesDir}/spacer.gif" class="Spc" alt="" /></td>
</tr>

<tr>
	<td>

<table cellpadding="0" cellspacing="3" width="100%">

{section name=rate loop=$shipping_method.rates}
{assign var="shipping_rate" value=$shipping_method.rates[rate]}

<tr>
	<td rowspan="2" nowrap="nowrap"><img src="{$ImagesDir}/spacer.gif" width="10" height="1" alt="" /><input type="checkbox" name="posted_data[{$shipping_rate.rate_id}][to_delete]" /></td>
	<td>{$lng.lbl_weight_range}:</td>
	<td nowrap="nowrap">
<input type="text" name="posted_data[{$shipping_rate.rate_id}][minweight]" size="9" value="{$shipping_rate.minweight|formatprice}" />
-
<input type="text" name="posted_data[{$shipping_rate.rate_id}][maxweight]" size="9" value="{$shipping_rate.maxweight|formatprice}" />
	</td>
	<td>{$lng.lbl_flat_charge} ({$config.General.currency_symbol}):</td>
	<td nowrap="nowrap"><input type="text" name="posted_data[{$shipping_rate.rate_id}][rate]" size="5" value="{$shipping_rate.rate|formatprice}" /></td>
    <td>{$lng.lbl_over_weight} ({$config.General.weight_symbol}):</td>
    <td><input type="text" name="posted_data[{$shipping_rate.rate_id}][overweight]" size="5" value="{$shipping_rate.overweight|formatprice}" /></td>
    <td>{$lng.lbl_per_weight_for_overweight|substitute:"weight":$config.General.weight_symbol}:</td>
    <td><input type="text" name="posted_data[{$shipping_rate.rate_id}][overweight_rate]" size="5" value="{$shipping_rate.overweight_rate|formatprice}" /></td>

{*
	<td>{$lng.lbl_percent_charge}:</td>
	<td><input type="text" name="posted_data[{$shipping_rate.rate_id}][rate_p]" size="5" value="{$shipping_rate.rate_p|formatprice}" /></td>
*}
</tr>

{*
<tr>
	<td>{$lng.lbl_subtotal_range}:</td>
	<td nowrap="nowrap">
<input type="text" name="posted_data[{$shipping_rate.rate_id}][mintotal]" size="9" value="{$shipping_rate.mintotal|default:0|formatprice}" />
-
<input type="text" name="posted_data[{$shipping_rate.rate_id}][maxtotal]" size="9" value="{$shipping_rate.maxtotal|formatprice}" />
	</td>
	<td>{$lng.lbl_per_item_charge} ({$config.General.currency_symbol}):</td>
	<td nowrap="nowrap"><input type="text" name="posted_data[{$shipping_rate.rate_id}][item_rate]" size="5" value="{$shipping_rate.item_rate|formatprice}" /></td>
	<td>{$lng.lbl_per_weight_charge|substitute:"weight":$config.General.weight_symbol} ({$config.General.currency_symbol}):</td>
	<td nowrap="nowrap"><input type="text" name="posted_data[{$shipping_rate.rate_id}][weight_rate]" size="5" value="{$shipping_rate.weight_rate|formatprice}" /></td>
</tr>
*}

{if not %rate.last%}
<tr>
	<td colspan="7" class="SubHeaderGreyLine"><img src="{$ImagesDir}/spacer.gif" class="Spc" alt="" /></td>
</tr>
{/if}

{/section}

</table>
	</td>
</tr>

{/foreach}
{/capture}

{if $smarty.capture.rates_list}
{$smarty.capture.rates_list}
<tr>
	<td>&nbsp;</td>
</tr>
{else}
<tr>
	<td>{if $type eq "D"}{$lng.lbl_no_shipping_rates_defined}{else}{$lng.lbl_no_shipping_markups_defined}{/if}</td>
</tr>
{/if}

{/if}

{/section}

<tr>
	<td>
<input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick="javascript: cw_submit_form(this, 'delete');" />
&nbsp;&nbsp;&nbsp;&nbsp;
<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
	</td>
</tr>

</table>
</form>

<br /><br /><br />

<a name="addrate"></a>

{/if}

<p>{if $type eq "D"}{include file="common/subheader.tpl" title=$lng.lbl_add_shipping_charge_values}{else}{include file="common/subheader.tpl" title=$lng.lbl_add_shipping_markup_values}{/if}</p>

{if $shipping ne ""}

<form action="index.php?target=shipping_rates" method="post" name="addshippingrate">
<input type="hidden" name="action" value="add" />
<input type="hidden" name="zone_id" value="{$zone_id}" />
<input type="hidden" name="shipping_id" value="{$shipping_id}" />
<input type="hidden" name="type" value="{$type}" />

<table cellpadding="0" cellspacing="3">

<tr>
	<td><b>{$lng.lbl_shipping_method}:</b></td>
	<td>&nbsp;</td>
	<td>
	<select name="shipping_id_new">
		<option value="">{$lng.lbl_select_one}</option>
{section name=ship_num loop=$shipping}
		<option value="{$shipping[ship_num].shipping_id}">{$shipping[ship_num].shipping|trademark} ({if $shipping[ship_num].destination eq "I"}{$lng.lbl_intl}{else}{$lng.lbl_national}{/if})</option>
{/section}
	</select>
	</td>
</tr>

<tr>
	<td><b>{$lng.lbl_zone}:</b></td>
	<td>&nbsp;</td>
	<td>
	<select name="zone_id_new">
{section name=zone loop=$zones}
		<option value="{$zones[zone].zone_id}" {if $smarty.get.zone_id eq $zones[zone].zone_id}selected{/if}>{$zones[zone].zone}</option>
{/section}
	</select>
	</td>
</tr>

</table>

<table cellpadding="0" cellspacing="3" width="100%">

<tr>
	<td><b>{$lng.lbl_weight_range}:</b></td>
	<td nowrap="nowrap">
<input type="text" name="minweight_new" size="9" value="{0|formatprice}" />
-
<input type="text" name="maxweight_new" size="9" value="{$maxvalue|formatprice}" />
	</td>
	<td><b>{$lng.lbl_flat_charge} ({$config.General.currency_symbol}):</b></td>
	<td nowrap="nowrap"><input type="text" name="rate_new" size="5" value="{0|formatprice}" /></td>
    <td>{$lng.lbl_over_weight} ({$config.General.weight_symbol}):</td>
    <td><input type="text" name="overweight_new" size="5" value="{0|formatprice}" /></td>
    <td>{$lng.lbl_per_weight_for_overweight|substitute:"weight":$config.General.weight_symbol}:</td>
    <td><input type="text" name="overweight_rate_new" size="5" value="{0|formatprice}" /></td>
</tr>

{*
<tr>
	<td><b>{$lng.lbl_subtotal_range}:</b></td>
	<td nowrap="nowrap">
<input type="text" name="mintotal_new" size="9" value="{0|formatprice}" />
-
<input type="text" name="maxtotal_new" size="9" value="{$maxvalue|formatprice}" />
	</td>
	<td><b>{$lng.lbl_per_item_charge} ({$config.General.currency_symbol}):</b></td>
	<td nowrap="nowrap"><input type="text" name="item_rate_new" size="5" value="{0|formatprice}" /></td>
	<td><b>{$lng.lbl_per_weight_charge|substitute:"weight":$config.General.weight_symbol} ({$config.General.currency_symbol}):</b></td>
	<td nowrap="nowrap"><input type="text" name="weight_rate_new" size="5" value="{0|formatprice}" /></td>
</tr>
*}
</table>

<br />
<input type="submit" value=" {$lng.lbl_add|strip_tags:false|escape} ">

</form>

{elseif $type eq "D"}

{$lng.txt_shipping_charge_rtc_note}

{/if}


{/capture}
{if $type eq "D"}
{include file="common/section.tpl" title=$lng.lbl_shipping_charges content=$smarty.capture.section extra='width="100%"'}
{else}
{include file="common/section.tpl" title=$lng.lbl_shipping_markups content=$smarty.capture.section extra='width="100%"'}
{/if}

