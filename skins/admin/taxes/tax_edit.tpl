{*include file='common/page_title.tpl' title=$lng.lbl_tax_details*}
{capture name=section}
{capture name=block}

<div class="title">{$lng.txt_tax_details_general_note}</div>

<form action="index.php?target={$current_target}" method="post" name="tax_details_frm">
<input type="hidden" name="action" value="details" />
<input type="hidden" name="tax_id" value="{$tax_details.tax_id}" />

<div class="box form-horizontal">

{include file='common/subheader.tpl' title=$lng.lbl_tax_info}

<div class="form-group">
	<label class="col-xs-12">{$lng.lbl_tax_service_name}:</label>
	<div class="col-xs-12">
		<input type="text" class="form-control" maxlength="10" name="posted_data[tax_name]" value="{$tax_details.tax_name}" />
	</div>
</div>
<div class="form-group">
	<label class="col-xs-12">{$lng.lbl_tax_display_name}:</label>
	<div class="col-xs-12">
		<input type="text" class="form-control" name="posted_data[tax_display_name]" value="{$tax_details.tax_display_name}" />
	</div>
</div>
<div class="form-group">
	<label class="col-xs-12">{$lng.lbl_tax_regnumber}:</label>
	<div class="col-xs-12">
		<input type="text" class="form-control" name="posted_data[regnumber]" value="{$tax_details.regnumber}" />
	</div>
</div>
<div class="form-group">
	<label class="col-xs-12">{$lng.lbl_tax_priority}:</label>
	<div class="col-xs-12">
		<input type="text" class="form-control" name="posted_data[priority]" value="{$tax_details.priority}" />
	</div>
</div>
<div class="form-group">
	<label class="col-xs-12">{$lng.lbl_enabled}:</label>
	<div class="col-xs-12">
    	{include file='admin/select/yes_no.tpl' name='posted_data[active]' value=$tax_details.active}
    </div>
</div>
<div class="form-group">
	<label class="col-xs-12">{$lng.lbl_tax_apply_to}:</label>
	<div class="col-xs-12">
		{include file='main/select/tax_formula.tpl' name='posted_data[formula]' value=$tax_details.formula}
	</div>
</div>
<div class="form-group">
	<label class="col-xs-12">{$lng.lbl_tax_rates_depended_on}:</label>
	<div class="col-xs-12">
	<select name="posted_data[address_type]" class="form-control">
		<option value="S"{if $tax_details.address_type eq "S"} selected="selected"{/if}>{$lng.lbl_shipping_address}</option>
		<option value="B"{if $tax_details.address_type eq "B"} selected="selected"{/if}>{$lng.lbl_billing_address}</option>
        <option value="O"{if $tax_details.address_type eq "O"} selected="selected"{/if}>{$lng.lbl_both}</option>
	</select>
	</div>
</div>
<div class="form-group">
    <label class="col-xs-12">
    	{$lng.lbl_price_includes_tax}
        <input type="hidden" name="posted_data[price_includes_tax]" value="0" />
    	<input type="checkbox" id="price_includes_tax" name="posted_data[price_includes_tax]" value="1"{if $tax_details.price_includes_tax} checked="checked"{/if} />
    </label>
</div>
<div class="form-group">
    <label class="col-xs-12">
    	{$lng.lbl_display_including_tax}
        <input type="hidden" name="posted_data[display_including_tax]" value="0" />
    	<input type="checkbox" id="display_including_tax" name="posted_data[display_including_tax]" value="1" onclick="$('#display_info').attr('disabled', !this.checked);"{if $tax_details.display_including_tax} checked="checked"{/if} />
    </label>
<label>
</label>
</div>
<div class="form-group">
	<label class="col-xs-12">{$lng.lbl_display_also}:</label>
	<div class="col-xs-12">
	<select id="display_info" class="form-control" name="posted_data[display_info]"{if !$tax_details.display_including_tax} disabled="disabled"{/if}>
		<option value="">{$lng.lbl_display_tax_none}</option>
		<option value="R"{if $tax_details.display_info eq "R"} selected="selected"{/if}>{$lng.lbl_display_tax_rate}</option>
		<option value="V"{if $tax_details.display_info eq "V"} selected="selected"{/if}>{$lng.lbl_display_tax_cost}</option>
		<option value="A"{if $tax_details.display_info eq "A"} selected="selected"{/if}>{$lng.lbl_display_tax_rate_and_cost}</option>
	</select>
	</div>
</div>
{*
<div class="form-group">
    <label class="col-xs-12">
    	{$lng.lbl_tax_use_also}:
        <input type="hidden" name="posted_data[use_info]" value="0" />
    	<input type="checkbox" name="posted_data[use_info]" value="1"{if $tax_details.use_info} checked{/if}/>
    </label>

</div>
*}
</div>
{include file='admin/attributes/object_modify.tpl' show_required='Y'}
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_save href="javascript:cw_submit_form('tax_details_frm');" style="btn-green push-20"}

</form>

<div class="box">
{if $tax_details.tax_id}
{include file='main/taxes/tax_rates.tpl'}
{/if}

</div>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block}
{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_tax_details}