<form action="index.php?target={$current_target}" method="post" name="tax_rate_edit" class="form-horizontal">
<input type="hidden" name="action" value="rate_details" />
<input type="hidden" name="tax_id" value="{$tax_id}" />
<input type="hidden" name="rate_id" value="{$rate_details.rate_id}" />

<div class="form-group">
	<label class="col-xs-12">{$lng.lbl_tax_rate_value}:</label>
	<div class="col-xs-12">
	<input type="text" class="form-control form-control-inline" size="20" maxlength="13" name="rate_value" value="{$rate_details.rate_value|formatprice|default:$zero}" />
	<select name="rate_type" class="form-control form-control-inline">
		<option value="%"{if $rate_details.rate_type eq "%"} selected="selected"{/if}>%</option>
		<option value="$"{if $rate_details.rate_type eq "$"} selected="selected"{/if}>{$config.General.currency_symbol}</option>
	</select>
	</div>
</div>
<div class="form-group">
	<label class="col-xs-12">{$lng.lbl_zone}:</label>
	<div class="col-xs-12">
	<select name="zone_id" class="form-control">
		<option value="0">{$lng.lbl_zone_default}</option>
{section name=zid loop=$zones}
		<option value="{$zones[zid].zone_id}"{if $rate_details.zone_id eq $zones[zid].zone_id} selected="selected"{/if}>{$zones[zid].zone_name}</option>
{/section}
	</select>
	</div>
</label>
</div>
<div class="form-group">
	<label class="col-xs-12">{$lng.lbl_membership}:</label>
	<div class="col-xs-12">
	{include file='admin/select/membership.tpl' name="membership_ids[]" data=$rate_details.membership_ids}
	</div>
</div>
<div class="form-group">
	<label class="col-xs-12">{$lng.lbl_tax_apply_to}:</label>
	<div class="col-xs-12">
	{include file='main/select/tax_formula.tpl' name="rate_formula" value=$rate_details.formula}
	</div>
</div>

{if $rate_details.rate_id}
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_save href="javascript:cw_submit_form('tax_rate_edit');" style="btn-green push-20"}
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_cancel href="index.php?target=`$current_target`&tax_id=`$tax_id`" style="btn-danger push-20"}
{else}
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_add href="javascript:cw_submit_form('tax_rate_edit');" style="btn-green push-20"}
{/if}
</form>

