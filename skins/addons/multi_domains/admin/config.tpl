{capture name=section}

{capture name=block}


<form action="index.php?target={$current_target}&mode=config&domain_id={$domain_id}" method="post" name="config_frm">
<input type="hidden" name="action" value="update" />
<table class="table table-striped dataTable vertical-center" width="100%">
<tr>
    <th>{$lng.lbl_del}</th>
    <th>{$lng.lbl_settings}</th>
    <th>{$lng.lbl_value}</th>
    <th>{$lng.lbl_main_setting}</th>
</tr>
{if $domain_config}
{foreach from=$domain_config item=set}
<tr>
    <td><input type="checkbox" name="del[{$set.config_category_id}][{$set.name}]" value="Y" /></td>
    <td>{$set.title}:</td>
    <td>{include file='main/settings/setting.tpl' name="posted_data[`$set.config_category_id`][`$set.name`]" value=$set.domain_value type=$set.type variants=$set.variants auto_submit=$set.auto_submit}</td>
    <td>{include file='main/settings/setting.tpl' name="posted_data[`$set.config_category_id`][`$set.name`]" value=$set.value type=$set.type variants=$set.variants auto_submit=$set.auto_submit is_disabled=1}</td>
</tr>
{/foreach}
{else}
<tr>
    <td colspan="4" align="center">{$lng.lbl_none}</td>
</tr>
{/if}
<tr>
    <td colspan="4">{include file='common/subheader.tpl' title=$lng.lbl_add_new}</td>
</tr>
<tr>
    <td colspan="4" class="domains_wide">
    <select name="new_option">
    <option value=""></option>
{foreach from=$categories item=it}
    <optgroup label="{$it.title|escape}">
    {select_config category=$it.category assign="settings"}
    {foreach from=$settings item=opt}
    <option value="{$it.category}:{$opt.name}">{$opt.title|strip_tags|truncate}</option>
    {/foreach}
    </optgroup>
{/foreach}
    </select>
    </td>
</tr>
</table>
<br />
<div class="buttons">
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_update_delete href="javascript: cw_submit_form('config_frm');"  style="btn-danger push-5-r push-20"}
</div>
</form>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block}

{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_edit_config}
