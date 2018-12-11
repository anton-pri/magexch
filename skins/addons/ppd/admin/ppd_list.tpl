<script type="text/javascript">
    {if $config.ppd.ppd_product_files_dir}
    var ppd_dir = "{$config.ppd.ppd_product_files_dir|escape}";
    {literal}
    var ppd_pattern=new RegExp("^/");
    if (ppd_pattern.test(ppd_dir) == false) {
        ppd_dir = '/' + ppd_dir;
    }
    {/literal}
    {/if}
</script>
{* moved to ppd/init.php include_once_src file='main/include_js.tpl' src='addons/ppd/js/popup_files.js' *}
{capture name=section}
<div class="box">
<div class="dialog_title">{$lng.txt_ppd_top_text}</div>

<form action="index.php?target={$current_target}" method="post" name="update_files_form">
    <input type="hidden" name="mode" value="{$mode}" />
    <input type="hidden" name="action" value="ppd_update" />
    <input type="hidden" name="product_id" value="{$product.product_id}" />
    <input type="hidden" name="js_tab" value="ppd" />

    {if $ppd_files}
        {include file='common/navigation.tpl'}
    {/if}
    <table class="table table-striped dataTable vertical-center" width="100%">
    <thead>
        <tr valign="top">
            <th width="5%"><input type='checkbox' class='select_all' class_to_select='ppd_list_item' /></th>
            <th width="5%">{$lng.lbl_ppd_file_number}</th>
            <th width="15%">{$lng.lbl_ppd_file_type}</th>
            <th width="30%">{$lng.lbl_ppd_file_title}&nbsp;/&nbsp;{$lng.lbl_ppd_file_filename}</th>
            <th width="10%">{$lng.lbl_ppd_file_size}</th>
            <th width="10%">{$lng.lbl_ppd_file_owner_perms}<br />{$lng.lbl_ppd_file_perms_visy}&nbsp;/&nbsp;{$lng.lbl_ppd_file_perms_accy}</th>
            <th width="10%">{$lng.lbl_ppd_file_all_perms}<br />{$lng.lbl_ppd_file_perms_visy}&nbsp;/&nbsp;{$lng.lbl_ppd_file_perms_accy}</th>
            <th width="5%">{$lng.lbl_ppd_file_active}</th>
            <th width="10%">{$lng.lbl_ppd_downds_stats}<br />{$lng.lbl_ppd_month_stats}&nbsp;/&nbsp;{$lng.lbl_ppd_year_stats}</th>
        </tr>
	</thead>
        {if $ppd_files}

        {foreach from=$ppd_files item=file}
        <tr{cycle values=', class="cycle"'}>
            <td align="center"><input type="checkbox" value="Y" name="file_ids[{$file.file_id}]" class="ppd_list_item" /></td>
            <td align="center"><input class="form-control" type="text" size="6" maxlength="11" name="ppd_files[{$file.file_id}][number]" value="{$file.number|default:0}" /></td>
            <td>{if $file.fileicon}<img class="file-icon" src="{$file.fileicon|escape}" alt="{$file.type|escape}" />{/if}{if $file.type}{$file.type}{else}{$lng.lbl_ppd_unknown}{/if}</td>
            <td><div><a href="index.php?target={$current_target}&amp;mode={$mode}&amp;product_id={$product_id}&amp;action=ppd_details&amp;file_id={$file.file_id}&amp;js_tab=ppd" title="{$lng.lbl_ppd_modify}">{$file.title|escape}</a></div>
                {if $file.filename}<div>{$file.filename|escape}{if $file.is_deleted} (<span class="file-not-exist">{$lng.lbl_ppd_file_not_exist}</span>){/if}</div>{/if}</td>
            <td align="right">{$file.size|escape}</td>
            <td align="center"><input type="checkbox" value="4" name="ppd_files[{$file.file_id}][perms_owner][vis]"{if $file.perms_owner.vis eq 4} checked{/if} />&nbsp;/&nbsp;<input type="checkbox" value="1" name="ppd_files[{$file.file_id}][perms_owner][acc]"{if $file.perms_owner.acc eq 1} checked{/if} /></td>
            <td align="center"><input type="checkbox" value="4" name="ppd_files[{$file.file_id}][perms_all][vis]"{if $file.perms_all.vis eq 4} checked{/if} />&nbsp;/&nbsp;<input type="checkbox" value="1" name="ppd_files[{$file.file_id}][perms_all][acc]"{if $file.perms_all.acc eq 1} checked{/if} /></td>
            <td align="center"><input type="checkbox" value="1" name="ppd_files[{$file.file_id}][active]"{if $file.active eq 1} checked{/if} /></td>
            <td align="center">{$file.month_stats|default:0}&nbsp;/&nbsp;{$file.year_stats|default:0}</td>
        </tr>
        {/foreach}
        {if $navigation.total_pages gt 2}
        <tr>
            <td colspan="9">{include file='common/navigation.tpl'}</td>
        </tr>
		{/if}
        <tr>
            <td colspan="9">
                {include file='admin/buttons/button.tpl' href="javascript:cw_submit_form('update_files_form');" button_title=$lng.lbl_ppd_update_selected class="btn-green push-5-r"}
                {include file='admin/buttons/button.tpl' href="javascript:cw_submit_form('update_files_form', 'ppd_delete');" button_title=$lng.lbl_ppd_delete_selected class="btn-danger push-5-r"}
                {include file='admin/buttons/button.tpl' href="javascript:cw_submit_form('update_files_form', 'ppd_clean');" button_title=$lng.lbl_ppd_clean_files_records class="btn-danger"}
            </td>
        </tr>

        {else}
        <tr>
            <td colspan="9" align="center">{$lng.txt_ppd_no_elements}</td>
        </tr>
        {/if}
    </table>
</form>
</div>

{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_ppd_addon_title}

