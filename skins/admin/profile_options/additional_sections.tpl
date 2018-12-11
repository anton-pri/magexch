{if $included_tab eq 'addition_fields'}
{* start *}

<form action="index.php?target={$current_target}" method="post" name="fields_form">
<input type="hidden" name="action" value="update_fields" />
{capture name=section}
{capture name=block}

<div class="box">

<table class="table table-striped dataTable vertical-center" id="additional_fields">
<tr>
	<th>{$lng.lbl_delete}</th>
	<th>{$lng.lbl_field_name}</th>
    <th>{$lng.lbl_title}</th>
	<th>{$lng.lbl_section}</th>
	<th>{$lng.lbl_type}</th>
	<th>{$lng.lbl_pos}</th>
</tr>

{if $additional_fields}
{foreach from=$additional_fields key=section_name item=section}
{if $section}
{assign var="current_section" value=$profile_sections.$section_name}
<tr>
    <td colspan="5">{include file='common/subheader.tpl' title=$current_section.section_title class="grey"}</td>
</tr>
    {foreach from=$section item=v}
<tr>
	<td align="center">{if !$v.is_protected}<input type="checkbox" name="update[{$v.field_id}][del]" value="Y" />{/if}</td>
	<td><input type="text" size="20" maxlength="64" name="update[{$v.field_id}][field]" value="{$v.field}" class="form-control" {if $v.is_protected}readonly="readonly"{/if} /></td>
    <td><input type="text" size="30" maxlength="64" name="update[{$v.field_id}][title]" value="{$v.title}" class="form-control" /></td>
    
	<td>

	<select name="update[{$v.field_id}][section_id]" class="form-control">
    {foreach from=$profile_sections item=s key=k}
        <option value="{$s.section_id}"{if $v.section_id eq $s.section_id} selected="selected"{/if}>{$s.section_title}</option>
    {/foreach}
	</select>
	</td>
	<td>
	<select name="update[{$v.field_id}][type]" class="form-control">
	{foreach from=$types item=t key=k}
		<option value="{$k}"{if $v.type eq $k} selected="selected"{elseif $v.is_protected} disabled="disabled"{/if}>{$t}</option>
	{/foreach}
	</select>
	</td>
	<td><input type="text" name="update[{$v.field_id}][orderby]" value="{$v.orderby}" size="5"  class="form-control" /></td>
</tr>
{if $v.type eq 'S' || $v.type eq 'M'}
<tr>
    <td align="center">&nbsp;</td>
    <td colspan="4"><b>{$lng.lbl_variants}</b></td>
</tr>
<tr>
    <td align="center">&nbsp;</td>
    <td colspan="4"><input id="var_{$v.field_id}" type="text" size="60" name="update[{$v.field_id}][variants]" value="{$v.variants_str}"  class="form-control" /></td>
</tr>
{/if}
    {/foreach}
{/if}
{/foreach}


{else}

<tr>
	<td colspan="5" align="center">{$lng.txt_no_additional_fields}</td>
</tr>

{/if}

</table>
<div class="buttons">
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_update href="javascript:cw_submit_form('fields_form', 'update_fields');" acl='__2502' style="btn-default btn-green push-5-r push-20"}
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_delete_selected href="javascript:cw_submit_form('fields_form', 'delete_fields');" acl='__2502' style="btn-default btn-danger push-5-r push-20"}
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_add_new href="index.php?target=user_profiles&mode=fields&submode=add_field" acl='__2502' style="btn-default btn-green push-5-r push-20"}
</div>

</div>

</form>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block }
{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_additional_fields}
{elseif $included_tab eq 'addition_section'}
{* start *}

<form action="index.php?target={$current_target}" method="post" name="sections_form">
<input type="hidden" name="action" value="update_sections" />
{capture name=section2}
{capture name=block2}

<div class="box">
<table class="table table-striped dataTable vertical-center" >
<tr>
    <th width="1%">{$lng.lbl_delete}</th>
    <th>{$lng.lbl_section}</th>
    <th>{$lng.lbl_pos}</th>
</tr>

{if $profile_sections}
{foreach from=$profile_sections item=v}
<tr{cycle name="root" values=", class='TableSubHead'"}>
    <td align="center">{if $v.is_default}<img src="{$ImagesDir}/admin/checked_admin.png" alt="" />{else}<input type="checkbox" name="update[{$v.section_id}][del]" value="Y" />{/if}</td>
    <td> {if $v.is_default}{$v.section_title}{else}<input type="text" size="50" maxlength="100" name="update[{$v.section_id}][name]" value="{$v.section_title}" class="form-control" />{/if}</td>
    <td><input type="text" name="update[{$v.section_id}][orderby]" value="{$v.orderby}" size="5"  class="form-control" /></td>
</tr>
{/foreach}
{/if}
</table>

      <div class="buttons">
		{include file='admin/buttons/button.tpl' button_title=$lng.lbl_update href="javascript:cw_submit_form('sections_form', 'update_sections');" acl='__2502' style="btn-default btn-green push-5-r push-20"}
		{include file='admin/buttons/button.tpl' button_title=$lng.lbl_delete_selected href="javascript:cw_submit_form('sections_form', 'delete_sections');" acl='__2502' style="btn-default btn-danger push-5-r push-20"}
		{include file='admin/buttons/button.tpl' button_title=$lng.lbl_add_new href="index.php?target=user_profiles&mode=fields&submode=add_section" acl='__2502' style="btn-default btn-green push-20"}</div>


</div>

</form>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block2}

{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section2 extra='width="100%"' title=$lng.lbl_additional_sections}
{/if}
