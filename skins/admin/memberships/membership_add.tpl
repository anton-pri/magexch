{capture name=section}

<form method="post" action="index.php?target=memberships" name="form{$type}">
<input type="hidden" name="action" value="update" />
<input type="hidden" name="add[area]" value="{$type}" />

<table class="table table-striped dataTable vertical-center" width="100%">
<thead>
<tr>
	<th >{$lng.lbl_membership}</th>
	<th class="text-center">{$lng.lbl_active}</th>
	<th class="text-center">{$lng.lbl_orderby}</th>
    {if $type eq 'A' || $type eq 'P'}
    <th class="text-center">{$lng.lbl_settings}</th>
    {/if}
</tr>
</thead>
<tr>
	<td ><input type="text" class="form-control" size="30" name="add[membership]" style="width: 100%" /></td>
	<td align="center"><input type="checkbox" name="add[active]" value="Y" checked="checked" /></td>
	<td align="center"><input type="text" size="5" name="add[orderby]" value="" class="form-control" /></td>
	{if $type eq 'A' || $type eq 'P'}
	<td class="narrow_select">
	<select name="add[flag]" style="width: 100%;" class="form-control">
		<option value="">{$lng.lbl_none}</option>
{if $type eq 'A' || ($type eq 'P' && $addons.Simple_Mode)}
		<option value="FS">{$lng.lbl_subtype_FS}</option>
{/if}
{if $type eq 'P' && !$addons.Simple_Mode}
		<option value="RP">{$lng.lbl_subtype_RP}</option>
{/if}
	</select>
	</td>
	{/if}
</tr>
</table>
    <div class="buttons">
    {include file='admin/buttons/button.tpl' href="javascript: cw_submit_form(document.form`$type`, 'add');" button_title=$lng.lbl_add_new style="btn-green push-20"}</div>

</form>
{/capture} 
{include file='admin/wrappers/block.tpl' content=$smarty.capture.section title=$lng.lbl_add_new}
