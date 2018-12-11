{capture name=section}
<table width="100%" border="0">
    <tr>
        <td><a href="index.php?target={$current_target}&amp;mode={$mode}&amp;product_id={$product_id}&amp;js_tab=ppd" title="{$lng.lbl_ppd_files_list}">{$lng.lbl_ppd_files_list}</a> :: {$file_data.title|escape}</td>
    </tr>
    <tr>
        <td><img src="{$ImagesDir}/spacer.gif" height="10px" class="Spc" alt="" /></td>
    </tr>
</table>

<form action="index.php?target={$current_target}" method="post" name="ppd_details">
    <input type="hidden" name="mode" value="{$mode}" />
    <input type="hidden" name="action" value="ppd_modify" />
    <input type="hidden" name="product_id" value="{$product.product_id}" />
    <input type="hidden" name="file_id" value="{$file_data.file_id}" />

<div class="box">
    {*include file='common/subheader.tpl' title=$lng.lbl_ppd_details*}

    <div class="input_field_1">
        <label class='multilan'>
            {$lng.lbl_ppd_file_title} 
        </label>
        <input type="text" size="32" maxlength="32" name="file_data[title]" value="{$file_data.title|default:$lng.lbl_ppd_unknown|escape}"{if $read_only} disabled{/if} />
    </div>

    <div class="input_field_0">
        <label>
            {$lng.lbl_ppd_file_number}
        </label>
        <input type="text" size="4" maxlength="4" name="file_data[number]" value="{$file_data.number|default:0|escape}"{if $read_only} disabled{/if} />
    </div>

    <div class="input_field_0">
        <label>
            {$lng.lbl_ppd_file_type}
        </label>
        {if $file_data.fileicon}<img class="file-icon" src="{$file_data.fileicon}" alt="{$file_data.type}" />&nbsp;{/if}{if $file_data.type}{$file_data.type}{else}{$lng.lbl_ppd_unknown}{/if}
        {if $types}&nbsp;&nbsp;{$lng.lbl_ppd_new_filetype}
        <select name="file_data[type_id]" size="1">
            <option value="" selected>&nbsp;</option>
        {foreach from=$types item=type}
        <option class="filetype-icon-back" value="{$type.type_id}"{if $type.fileicon} style="background-image: url({$type.fileicon})"{/if}>{$type.type|escape}</option>
        {/foreach}</select>&nbsp;&nbsp;<a href="index.php?target=filetypes" title="{$lng.lbl_ppd_manage_filetypes}">{$lng.lbl_ppd_manage_filetypes}</a>{/if}
    </div>

    <div class="input_field_0">
        <label>
            {$lng.lbl_ppd_file_path}
        </label>
        {$file_data.filename}{if $file_data.size}{$file_data.size}{/if}
    </div>

    <div class="input_field_0">
        <label>
            {$lng.lbl_ppd_file_owner_perms}
        </label>
        <div style="margin: 2px 0;display:inline-block;">{$lng.lbl_ppd_file_perms_visy}&nbsp;<input type="checkbox" value="4" name="file_data[perms_owner][vis]"{if $file_data.perms_owner.vis eq 4} checked{/if} />&nbsp;/&nbsp;{$lng.lbl_ppd_file_perms_accy}&nbsp;<input type="checkbox" value="1" name="file_data[perms_owner][acc]"{if $file_data.perms_owner.acc eq 1} checked{/if} /></div>
    </div>

    <div class="input_field_0">
        <label>
            {$lng.lbl_ppd_file_all_perms}
        </label>
        <div style="margin: 2px 0;display:inline-block;">{$lng.lbl_ppd_file_perms_visy}&nbsp;<input type="checkbox" value="4" id="perms_all_visy" name="file_data[perms_all][vis]"{if $file_data.perms_all.vis eq 4} checked{/if} />&nbsp;/&nbsp;{$lng.lbl_ppd_file_perms_accy}&nbsp;<input type="checkbox" value="1" id="perms_all_accy" name="file_data[perms_all][acc]"{if $file_data.perms_all.acc eq 1} checked{/if} /></div>
    </div>

    <div class="input_field_0">
        <label>
            {$lng.lbl_ppd_file_active}
        </label>
        <label><input type="checkbox" name="file_data[active]" value="Y"{if $file_data.active eq 1} checked{/if} /></label>
    </div>
</div>
<br>
{include file='admin/buttons/button.tpl' href="javascript:cw_submit_form('ppd_details');" button_title=$lng.lbl_ppd_button_save acl=$page_acl style="btn-green push-20"}
</form>
{/capture}
{include file='admin/wrappers/block.tpl' title=$lng.lbl_ppd_details content=$smarty.capture.section}
