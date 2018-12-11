<a name="rates"></a>

{include file='common/subheader.tpl' title=$lng.lbl_tax_rates}

<form action="index.php?target={$current_target}" method="post" name="tax_rates_form">
<input type="hidden" name="action" value="update_rates" />
<input type="hidden" name="tax_id" value="{$tax_details.tax_id}" />

<table class="table table-striped dataTable vertical-center" width="100%">
<thead>
<tr>
	<th width="10"><input type='checkbox' class='select_all' class_to_select='tax_rates_item' /></th>
	<th width="30%">{$lng.lbl_zone}</th>
	<th width="20%" class="text-center">{$lng.lbl_membership}</th>
	<th width="30%" class="text-center">{$lng.lbl_tax_rate_value}</th>
	<th width="20%" class="text-center">{$lng.lbl_tax_apply_to}</th>
</tr>
</thead>
{if $tax_rates}
{section name=tax loop=$tax_rates}
<tr {cycle values=", class='cycle'"}>
	<td><input type="checkbox" name="to_delete[{$tax_rates[tax].rate_id}]" class="tax_rates_item" /></td>
	<td>{if $tax_rates[tax].zone_id eq 0}{$lng.lbl_zone_default}{else}<a href="index.php?target=zones&zone_id={$tax_rates[tax].zone_id}">{$tax_rates[tax].zone_name}</a>{/if}</td>
	<td align="center">
<a href="index.php?target=taxes&tax_id={$tax_details.tax_id}&amp;rate_id={$tax_rates[tax].rate_id}#rates">{foreach from=$tax_rates[tax].membership_ids item=m}
{$m}<br />
{foreachelse}
{$lng.lbl_all}
{/foreach}</a>
</td>
	<td align="center" nowrap="nowrap">
<input type="text" class="form-control form-control-inline" size="20" maxlength="13" name="posted_data[{$tax_rates[tax].rate_id}][rate_value]" value="{$tax_rates[tax].rate_value|formatprice:false:false:3}" />
<select class="form-control form-control-inline" name="posted_data[{$tax_rates[tax].rate_id}][rate_type]">
	<option value="%"{if $tax_rates[tax].rate_type eq "%"} selected="selected"{/if}>%</option>
	<option value="$"{if $tax_rates[tax].rate_type eq "$"} selected="selected"{/if}>{$config.General.currency_symbol}</option>
</select>
	</td>
	<td align="center"><a href="index.php?target=taxes&tax_id={$tax_details.tax_id}&amp;rate_id={$tax_rates[tax].rate_id}#rates">{if $tax_rates[tax].formula eq ""}{$tax_details.formula}{else}{$tax_rates[tax].formula}{/if}</a></td>
</tr>
{/section}

{else}
<tr>
    <td colspan="5" align="center">{$lng.txt_no_tax_rates_defined}</td>
</tr>
{/if}
</table>

{if $tax_rates}
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_update href="javascript:cw_submit_form('tax_rates_form');" style="btn-green push-20 push-5-r"}
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_delete_selected href="javascript:cw_submit_form('tax_rates_form', 'delete_rates');" style="btn-green push-20"}
{/if}
</form>

{if $rate_details}
{include file='common/subheader.tpl' title=$lng.lbl_edit_tax_rate}
{else}
{include file='common/subheader.tpl' title=$lng.lbl_add_tax_rate}
{/if}
{include file='main/taxes/tax_rate_edit.tpl' mode="mode" tax_id=$tax_details.tax_id rate_details=$rate_details}

