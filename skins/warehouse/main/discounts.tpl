{include file='common/page_title.tpl' title=$lng.lbl_discounts}

{$lng.txt_discounts_note}

{capture name=section}

<form action="index.php?target=discounts" method="post" name="discountsform">
<input type="hidden" name="action" value="update" />

<table cellpadding="3" cellspacing="1" width="100%">
<tr class="TableHead">
	<td width="10"><input type='checkbox' class='select_all' class_to_select='discounts_item' /></td>
	<td width="25%">{$lng.lbl_order_subtotal}</td>
	<td width="25%">{$lng.lbl_discount}</td>
	<td width="25%">{$lng.lbl_discount_type}</td>
	<td width="25%">{$lng.lbl_membership}</td>
</tr>

{if $discounts}

{foreach from=$discounts item=discount}

<tr {cycle values=", class='cycle'"}
	<td><input type="checkbox" name="posted_data[{$discount.discount_id}][to_delete]" class="discounts_item" /></td>
	<td><input type="text" name="posted_data[{$discount.discount_id}][minprice]" size="12" value="{$discount.minprice|formatprice}" /></td>
	<td><input type="text" name="posted_data[{$discount.discount_id}][discount]" size="12" value="{$discount.discount|formatprice}" /></td>
	<td>
	<select name="posted_data[{$discount.discount_id}][discount_type]">
		<option value="percent"{if $discount.discount_type eq "percent"} selected="selected"{/if}>{$lng.lbl_percent}, %</option>
		<option value="absolute"{if $discount.discount_type eq "absolute"} selected="selected"{/if}>{$lng.lbl_absolute}, {$config.General.currency_symbol}</option>
	</select>
	</td>
	<td>{include file="main/select/membership.tpl" field="posted_data[`$discount.discount_id`][membershipids][]" data=$discount is_short='Y'}</td>
</tr>

{/foreach}

<tr>
	<td colspan="5" class="SubmitBox">
	<input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick="javascript: cw_submit_form(this, 'delete');" />
	<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
	</td>
</tr>

{else}

<tr>
	<td colspan="5" align="center">{$lng.lbl_no_discounts_defined}</td>
</tr>

{/if}

<tr>
	<td colspan="5">&nbsp;</td>
</tr>

<tr>
	<td colspan="5">{include file="common/subheader.tpl" title=$lng.lbl_add_new_discount}</td>
</tr>

<tr>
	<td>&nbsp;</td>
	<td><input type="text" name="minprice_new" size="12" value="{$zero}" /></td>
	<td><input type="text" name="discount_new" size="12" value="{$zero}" /></td>
	<td>
	<select name="discount_type_new">
		<option value="percent">{$lng.lbl_percent}, %</option>
		<option value="absolute">{$lng.lbl_absolute}, {$config.General.currency_symbol}</option>
	</select>
	</td>
	<td>{include file="main/select/membership.tpl" field="discount_membershipids_new[]" data="" is_short='Y'}</td>
</tr>

<tr>
	<td colspan="5" class="SubmitBox"><input type="button" value="{$lng.lbl_add_update|strip_tags:false|escape}" onclick="javascript: cw_submit_form(this, 'add');" /></td>
</tr>

</table>
</form>

{/capture}
{include file="common/section.tpl" title=$lng.lbl_edit_purchase_discounts content=$smarty.capture.section extra='width="100%"'}
