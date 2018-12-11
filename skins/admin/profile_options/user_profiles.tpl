{if $smarty.get.submode eq ''}
<div class="block transparent">

{jstabs}
default_tab={$js_tab|default:"profile_options"}
default_template=admin/profile_options/additional_sections.tpl

[addition_section]
title={$lng.lbl_addition_sections}

[addition_fields]
title={$lng.lbl_addition_fields}

{/jstabs}

{include file='main/select/edit_lng.tpl' script="index.php?target=`$current_target`&mode=fields&js_tab=`$js_tab`"}
{include file='admin/tabs/js_tabs.tpl' style="default" }
</div>
{elseif $smarty.get.submode eq 'add_section'}

<form action="index.php?target={$current_target}" method="post" name="sections_form">
<input type="hidden" name="action" value="update_sections" />
{*include file='common/page_title.tpl' title=$lng.lbl_additional_sections*}
{capture name=section2}
{capture name=block}

<div class="box">
<table class="table table-striped dataTable vertical-center" >
<tr>
    <th>{$lng.lbl_section}</th>
    <th>{$lng.lbl_pos}</th>
</tr>
<tr>
    <td><input type="text" name="update[0][name]" size="50" maxlength="100" class="form-control" /></td>
    <td><input type="text" size="5" name="update[0][orderby]" class="form-control"  /></td>
</tr>
</table>

      <div class="buttons">
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_add href="javascript:cw_submit_form('sections_form', 'update_sections');" acl='__2502' style="btn-green push-5-r push-20"}</div>


</div>

</form>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block }
{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section2 extra='width="100%"' title=$lng.lbl_additional_sections}



{elseif $smarty.get.submode eq 'add_field'}

<form action="index.php?target={$current_target}" method="post" name="fields_form">
<input type="hidden" name="action" value="update_fields" />
{capture name=section}
{capture name=block2}

<div class="box">

<table class="table table-striped dataTable vertical-center" id="additional_fields">
<tr>
    <th>{$lng.lbl_field_name}</th>
    <th>{$lng.lbl_section}</th>
    <th>{$lng.lbl_type}</th>
    <th>{$lng.lbl_pos}</th>
</tr>
<tr>
    <td><input type="text" name="update[0][field]" size="30" maxlength="100" class="form-control" /></td>
    <td>
    <select name="update[0][section_id]" class="form-control" >
    {foreach from=$profile_sections item=v key=k}
        <option value="{$v.section_id}">{$v.section_title}</option>
    {/foreach}
    </select>
    </td>
    <td>
    <select id="newfield_type" name="update[0][type]" class="form-control">
    {foreach from=$types item=v key=k}
        <option value="{$k}">{$v}</option>
    {/foreach}
    </select>
    </td>
    <td><input type="text" size="5" name="update[0][orderby]" class="form-control" /></td>
</tr>

<tr>

    <td colspan="5">{$lng.lbl_variants_for_selectbox}:</td>
</tr>
<tr>
    <td colspan="5"><input id="newfield_variants" type="text" size="60" name="update[0][variants]" class="form-control" /></td>
</tr>
</table>
<div class="buttons">
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_add href="javascript:cw_submit_form('fields_form', 'update_fields');" acl='__2502' style="btn-green push-5-r push-20"}
</div>
</div>

</form>

{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block2 }
{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_additional_fields}



{/if}
