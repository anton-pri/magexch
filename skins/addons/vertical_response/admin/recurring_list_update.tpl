{capture name=section}
<form action="index.php?target={$current_target}" method="post" name="recurring_list_update_form">
	<input type="hidden" name="action" value="" />
	<div class="box">
		<table width="100%" class="header">
			<tr>
				{if $recurring_list}
					<th width="5%" align="center"><input type='checkbox' class='select_all' class_to_select='recurring_list_update_item' /></th>
				{/if}
				<th width="35%">{$lng.lbl_email_list}</th>
				<th width="50%">{$lng.lbl_saved_search}</th>
				<th width="10%">{$lng.lbl_enabled}</th>
			</tr>
			{if $recurring_list}
				{foreach from=$recurring_list item=v}
					<tr{cycle values=", class='cycle'"}>
						<td align="center"><input type="checkbox" name="to_delete[{$v.id}]" class="recurring_list_update_item" /></td>
						<td>{$v.list_name}</td>
						<td>{$v.saved_search|stripslashes}</td>
						<td align="center"><input type="checkbox" name="active[{$v.id}]" {if $v.active} checked="checked"{/if} /></td>
					</tr>
				{/foreach}
			{else}
				<tr>
					<td colspan="4" align="center">{$lng.txt_no_avail_items}</td>
				</tr>
			{/if}
		</table>
	</div>
</form>

{if $recurring_list}
	{include file='buttons/button.tpl' button_title=$lng.lbl_delete_selected onclick="javascript: cw_submit_form('recurring_list_update_form', 'delete');"}
	{include file='buttons/button.tpl' button_title=$lng.lbl_update onclick="javascript: cw_submit_form('recurring_list_update_form', 'update');"}
{/if}

<div class="clear"></div>

<form action="index.php?target={$current_target}" method="post" name="recurring_list_add_form">
	<input type="hidden" name="action" value="add" />
	<input type="hidden" id="email_list_name" name="vr_data[email_list_name]" value="" />
	<div class="box">
		<table width="100%" class="header">
			<tr>
				<td colspan="2">{include file="common/subheader.tpl" title=$lng.lbl_new_profile}</td>
			</tr>
			<tr>
				<th width="35%"><label class="required">{$lng.lbl_email_list}</label></th>
				<th width="50%"><label class="required">{$lng.lbl_saved_search}</label></th>
			</tr>
			<tr>
				<td>
					<select name="vr_data[email_list]" onchange="$('#email_list_name').val($('option:selected', this).text());">
						<option value="0">{$lng.lbl_select}</option>
						{if $email_lists}
						{foreach from=$email_lists item=v}
							<option value="{$v.list_id}">{$v.name}</option>
						{/foreach}
						{/if}
					</select>
				</td>
				<td>
					<select name="vr_data[saved_search]">
						<option value="0">{$lng.lbl_select}</option>
						{if $saved_searches}
							{foreach from=$saved_searches item=v}
								<option value="{$v.id}">{$v.name|stripslashes}</option>
							{/foreach}
						{/if}
					</select>
				</td>
			</tr>
		</table>
	</div>
</form>

<div class="buttons">{include file='buttons/button.tpl' button_title=$lng.lbl_add_new onclick="javascript: cw_submit_form('recurring_list_add_form');"}</div>

{/capture}
{include file="common/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_recurring_list_update_full}
