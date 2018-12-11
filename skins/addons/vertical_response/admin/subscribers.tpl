<form action="index.php?target={$current_target}" method="post" name="subscribers_form" enctype="multipart/form-data">
	<input type="hidden" name="mode" value="subscribers" />
	<input type="hidden" name="action" value="" />
	<input type="hidden" name="list_id" value="{$list_id}" />

	<div class="box">
		{include file='common/subheader.tpl' title=$lng.lbl_type_list_control}
		<div class="input_field_1">
			<label>{$lng.lbl_type|capitalize}</label>
			<select name="type_list_control" onchange="vr_change(this.value)">
				<option value="0"{if $list.avail eq 0} selected="selected"{/if}>{$lng.lbl_manual_list}</option>
				<option value="1"{if $list.avail eq 1} selected="selected"{/if}>{$lng.lbl_saved_customers_search}</option>
			</select>
		</div>
	</div>

	<div class="box" id="saved_search_box"{if $list.avail eq 0} style="display: none"{/if}>
		<div class="input_field_1">
			<label>{$lng.lbl_saved_search}</label>
			<select name="saved_search">
				<option value="0">{$lng.lbl_select}...</option>
				{if $saved_searches}
				{foreach from=$saved_searches item=saved_search}
					<option value="{$saved_search.id}"{if $list.salesman_customer_id eq $saved_search.id} selected="selected"{/if}>{$saved_search.name}</option>
				{/foreach}
				{/if}
			</select>
		</div>
	</div>

	{include file='common/navigation.tpl'}

	<div class="box">
		<table class="header" width="100%">
			<tr>
				{if $list.avail eq 0}
				<th width="10"><input type='checkbox' class='select_all' class_to_select='subscribers_item' /></th>
				{/if}
				<th width="50%">{$lng.lbl_email}</th>
				<th width="50%">Subscribed</th>
			</tr>
			{if $subscribers}
				{foreach from=$subscribers item=subscriber}
					<tr{cycle values=', class="cycle"'}>
						{if $list.avail eq 0}
						<td>{if $subscriber.direct}<input type="checkbox" name="to_delete[{$subscriber.email|escape}]" class="subscribers_item" />{/if}</td>
						{/if}
						<td>{$subscriber.email}</td>
						<td>{if $subscriber.since_date}{$subscriber.since_date|date_format:$config.Appearance.date_format}{else}{$subscriber.membership}{/if}</td>
					</tr>
				{/foreach}
			{else}
				<tr>
					<td colspan="{if $list.avail eq 0}3{else}2{/if}" align="center">{$lng.txt_no_subscribers}</td>
				</tr>
			{/if}
		</table>
	</div>
	{include file='common/navigation.tpl'}

	{if $subscribers && $list.avail eq 0}
		{include file='buttons/button.tpl' button_title=$lng.lbl_export_selected href="javascript:cw_submit_form('subscribers_form', 'export');"}
		{include file='buttons/button.tpl' button_title=$lng.lbl_delete_selected href="javascript:cw_submit_form('subscribers_form', 'delete');"}
	{/if}

	{if $list.avail eq 0}
	<div class="box">
		{include file='common/subheader.tpl' title=$lng.lbl_add_to_maillist}
		<div class="input_field_1">
			<label>{$lng.lbl_email}</label>
			<input type="text" id="new_email" name="new_email" size="40" />
			{include file='buttons/button.tpl' button_title=$lng.lbl_add href="javascript:cw_submit_form('subscribers_form', 'add');"}
		</div>
	</div>
	{/if}

	<div class="buttons">{include file='buttons/button.tpl' button_title=$lng.lbl_save href="javascript:cw_submit_form('subscribers_form', 'update');"}</div>
</form>

<script type="text/javascript">
{literal}
function vr_change(val) {
	if (val == 1) {
		$('#saved_search_box').show();
	} else {
		$('#saved_search_box').hide();
	}
}
{/literal}
</script>