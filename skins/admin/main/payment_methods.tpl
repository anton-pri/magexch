<div class="dialog_title">{$lng.txt_payment_methods_top_text}</div>

{capture name=section}

{include file="main/select/edit_lng.tpl" script="index.php?target=payment_methods"}

<form action="index.php?target=payment_methods" method="post" name="pmform">
<input type="hidden" name="action" value="update" />

<table class="header" width="100%">
<tr>
	<th><input type='checkbox' class='select_all' class_to_select='payment_method_item' /></th>
	<th width="40%">{$lng.lbl_methods}</th>
	<th width="20%">{$lng.lbl_special_instructions}</th>
	<th width="20%">{$lng.lbl_protocol}</th>
	<th width="10%">{$lng.lbl_membership}</th>
    <th width="10%">{$lng.lbl_company}</th>
    <th width="10%">{$lng.lbl_shipping}</th>
	<th width="10%">{$lng.lbl_pos}</th>
</tr>

{section name=method loop=$payment_methods}
{cycle values=', class="TableSubHead"' assign=trcolor}

{if $payment_methods[method].disable_checkbox eq "Y"}<input type="hidden" name="posted_data[{$payment_methods[method].payment_id}][active]" value="Y" />{/if}

<tr{$trcolor}>
	<td valign="top"{if $payment_methods[method].addon ne ""} rowspan="2"{/if}>
	<input type="checkbox" name="posted_data[{$payment_methods[method].payment_id}][active]" value="Y"{if $payment_methods[method].active eq "Y"} checked="checked"{/if}{if $payment_methods[method].disable_checkbox eq "Y"} disabled="disabled"{/if} class="payment_method_item" />
	</td>
	<td valign="top">
	<input type="text" size="30" name="posted_data[{$payment_methods[method].payment_id}][payment_method]" value="{$payment_methods[method].payment_method|escape:"html"}" />
<br />
{*
<table cellpadding="1" cellspacing="0">
<tr>
	<td class="FormButton">{$lng.lbl_cod_extra_charge}:</td>
	<td><input type="text" size="8" name="posted_data[{$payment_methods[method].payment_id}][surcharge]" value="{$payment_methods[method].surcharge|default:"0"|formatprice}" /></td>
	<td>
	<select name="posted_data[{$payment_methods[method].payment_id}][surcharge_type]">
		<option value="%"{if $payment_methods[method].surcharge_type eq "%"} selected="selected"{/if}>%</option>
		<option value="$"{if $payment_methods[method].surcharge_type eq "$"} selected="selected"{/if}>{$config.General.currency_symbol}</option>
	</select>
	</td>
</tr>
</table>
<table cellpadding="1" cellspacing="0">
<tr>
	<td class="FormButton">{$lng.lbl_cod_extra_charge}:</td>
	<td><input type="text" size="8" name="posted_data[{$payment_methods[method].payment_id}][surcharge]" value="{$payment_methods[method].surcharge|default:"0"|formatprice}" /></td>
	<td>
	<select name="posted_data[{$payment_methods[method].payment_id}][surcharge_type]">
		<option value="%"{if $payment_methods[method].surcharge_type eq "%"} selected="selected"{/if}>%</option>
		<option value="$"{if $payment_methods[method].surcharge_type eq "$"} selected="selected"{/if}>{$config.General.currency_symbol}</option>
	</select>
	</td>
</tr>
</table>
*}
<table cellpadding="1" cellspacing="0">
<tr>
    <td class="FormButton">{$lng.lbl_min_plimit}:</td>
    <td><input type="text" size="5" name="posted_data[{$payment_methods[method].payment_id}][min_limit]" value="{$payment_methods[method].min_limit}" /></td>
    <td class="FormButton">{$lng.lbl_max_plimit}:</td>
    <td><input type="text" size="5" name="posted_data[{$payment_methods[method].payment_id}][max_limit]" value="{$payment_methods[method].max_limit}" /></td>
</tr>
</table>

<div class="FormButton">
<input type="checkbox" name="posted_data[{$payment_methods[method].payment_id}][apply_tax]" value="Y"{if $payment_methods[method].apply_tax eq 'Y'} checked="checked"{/if} />
{$lng.lbl_apply_tax_on_payment}
</div>

{if $payment_methods[method].processor_file eq ""}
<table cellpadding="1" cellspacing="0">
<tr>
	<td><input type="checkbox" id="is_cod_{$payment_methods[method].payment_id}" name="posted_data[{$payment_methods[method].payment_id}][is_cod]" value="Y"{if $payment_methods[method].is_cod eq 'Y'} checked="checked"{/if} /></td>
	<td class="FormButton"><label for="is_cod_{$payment_methods[method].payment_id}">{$lng.lbl_cash_on_delivery_method}</label></td>
</tr>
</table>
{/if}
	</td>
	<td valign="top" nowrap="nowrap">
	<textarea name="posted_data[{$payment_methods[method].payment_id}][payment_details]" cols="40" rows="3">{$payment_methods[method].payment_details|escape:"html"}</textarea>
	</td>
	<td valign="top">
	<select name="posted_data[{$payment_methods[method].payment_id}][protocol]" style="width:100%">
		<option value="http"{if $payment_methods[method].protocol eq "http"} selected="selected"{/if}>HTTP</option>
		<option value="https"{if $payment_methods[method].protocol eq "https"} selected="selected"{/if}>HTTPS</option>
	</select>
	</td>
	<td valign="top"{if $payment_methods[method].addon ne ""} rowspan="2"{/if}>
	{include file="main/select/membership.tpl" name="posted_data[`$payment_methods[method].payment_id`][membership_ids][]" value=$payment_methods[method].membership_ids multiple=1}
	</td>
    <td valign="top"{if $payment_methods[method].addon ne ""} rowspan="2"{/if}>
    <select name="posted_data[{$payment_methods[method].payment_id}][company_ids][]" multiple size="5">
            {assign var="selected" value=$payment_methods[method].companies}
            {foreach from=$possible_companies item=company}
            {assign var="id" value=$company.company_id}
            <option value="{$company.company_id}"{if $selected.$id eq 'Y'} selected="selected"{/if}><b>{$company.company_name}</b> ({$company.country_name})</option>
            {/foreach}
    </select>
    </td>
    <td valign="top"{if $payment_methods[method].addon ne ""} rowspan="2"{/if}>
    <select name="posted_data[{$payment_methods[method].payment_id}][shipping_ids][]" multiple size="5">
            {assign var="selected" value=$payment_methods[method].shippings}
            {foreach from=$possible_shippings item=shipping}
            {assign var="id" value=$shipping.shipping_id}
            <option value="{$id}"{if $selected.$id eq 'Y'} selected="selected"{/if}>{$shipping.shipping}</option>
            {/foreach}
    </select>
    </td>
	<td valign="top"{if $payment_methods[method].addon ne ""} rowspan="2"{/if}>
	<input type="text" size="5" maxlength="5" name="posted_data[{$payment_methods[method].payment_id}][orderby]" value="{$payment_methods[method].orderby}" />
	</td>
</tr>

{if $payment_methods[method].addon ne ""}
<tr{$trcolor}>
	<td colspan="3" valign="bottom">
{if $payment_methods[method].type eq "C"}{$lng.lbl_credit_card_processor}{elseif $payment_methods[method].type eq "H"}{$lng.lbl_check_processor}{else}{assign var=type value="ps"}{$lng.lbl_ps_processor}{/if} <b>{$payment_methods[method].addon}</b>:
<a href="index.php?target=cc_processing&mode=update&amp;cc_processor={$payment_methods[method].processor}">{$lng.lbl_configure}</a> | <a href="index.php?tareget=cc_processing&mode=delete&amp;payment_id={$payment_methods[method].payment_id}">{$lng.lbl_delete}</a>
{if $payment_methods[method].is_down or $payment_methods[method].in_testmode}
<table cellpadding="2">
{if $payment_methods[method].is_down}
<tr>
	<td><img src="{$ImagesDir}/log_type_Warning.gif" alt="" /></td>
	<td><font class="AdminSmallMessage">{$lng.txt_cc_processor_requirements_failed|substitute:"processor":$payment_methods[method].addon}</font></td>
</tr>
{/if}
{if $payment_methods[method].in_testmode}
<tr>
	<td><img src="{$ImagesDir}/log_type_Warning.gif" alt="" /></td>
	<td><font class="AdminSmallMessage">{$lng.txt_cc_processor_in_text_mode|substitute:"processor":$payment_methods[method].addon}</font></td>
</tr>
{/if}
</table>
{/if}{* $payment_methods[method].is_down or $payment_methods[method].in_testmode *}
	</td>
</tr>
{/if}

{/section}

<tr>
	<td align="center" colspan="6" class="SubmitBox"><input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" /></td>
</tr>
</table>
</form>

{/capture}
{include file="common/section.tpl" title=$lng.lbl_payment_methods content=$smarty.capture.section extra='width="100%"'}

<br /><br />{include file="admin/main/cc_processing.tpl"}
