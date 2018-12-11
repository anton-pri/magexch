{capture name=section}
<div class="box">
<form action="index.php?target={$current_target}" method="post" name="add_file_form">
    <input type="hidden" name="mode" value="{$mode}" />
    <input type="hidden" name="action" value="ppd_add" />
    <input type="hidden" name="product_id" value="{$product.product_id}" />
    <input type="hidden" name="js_tab" value="ppd" />
    <table class="table table-striped dataTable vertical-center">
        <tr>
            {*<th>&nbsp;</th>*}

            <th>{$lng.lbl_ppd_file_number}</th>
            <th style="width: 400px;">{$lng.lbl_ppd_file_title}&nbsp;{$lng.lbl_ppd_field_required}&nbsp;/&nbsp;{$lng.lbl_ppd_file_path}&nbsp;{$lng.lbl_ppd_field_required}</th>
            <th>{$lng.lbl_ppd_file_owner_perms}<br />{$lng.lbl_ppd_file_perms_visy}&nbsp;/&nbsp;{$lng.lbl_ppd_file_perms_accy}</th>
            <th>{$lng.lbl_ppd_file_all_perms}<br />{$lng.lbl_ppd_file_perms_visy}&nbsp;/&nbsp;{$lng.lbl_ppd_file_perms_accy}</th>
            <th>{$lng.lbl_ppd_file_active}</th>
            <th>{$lng.lbl_add}</th>
        </tr>

        <tr valign="top">
            {*<td id="ppd_box_0">&nbsp;</td>*}
            <td id="ppd_box_1"><input type="text" class="form-control" size="4" maxlength="11" name="new_files[0][number]" value="{$_new_files[0].number}" /></td>
            <td id="ppd_box_2">
            	<div class="col-xs-5"><input type="text" class="form-control" size="18" maxlength="255" name="new_files[0][title]" value="{$_new_files[0].title}" /></div>
                <div class="col-xs-4"><input id="name_file_0" type="hidden" name="new_files[0][filename]" value="{$_new_files[0].filename|escape}" />
                <input id="path_file_0" type="text" class="form-control" size="22" maxlength="255" name="new_files[0][path]" value="{$_new_files[0].path|escape}" readonly="readonly" /></div>
                <div class="col-xs-3"><input id="file_0" type="button" class="btn btn-default" value="{$lng.lbl_browse_|strip_tags:false|escape}" onclick="javascript: ppd_popup_files(this, 'name_', 'path_', false);" /></div>
            </td>
            <td id="ppd_box_3" align="center"><input type="checkbox" value="4" name="new_files[0][perms_owner][vis]"{if $_new_files[0].perms_owner.vis eq 4} checked{/if} />&nbsp;/&nbsp;<input type="checkbox" value="1" name="new_files[0][perms_owner][acc]"{if $_new_files[0].perms_owner.acc eq 1} checked{/if} /></td>
            <td id="ppd_box_4" align="center"><input type="checkbox" value="4" name="new_files[0][perms_all][vis]"{if $_new_files[0].perms_all.vis eq 4} checked{/if} />&nbsp;/&nbsp;<input type="checkbox" value="1" name="new_files[0][perms_all][acc]"{if $_new_files[0].perms_all.acc eq 1} checked{/if} /></td>
            <td id="ppd_box_5" align="center"><input type="checkbox" value="1" name="new_files[0][active]"{if $_new_files[0].active eq 1} checked{/if} /></td>
            <td>{include file="main/multirow_add.tpl" mark="ppd" is_lined=true}</td>
        </tr>
        {if $_new_files && $_new_files|@count > 1}
        {counter start=1 print=false assign="file_counter"}
        {foreach from=$_new_files item=_new_file name=new_ppd_files}
        {if !$smarty.foreach.new_ppd_files.first}
        {math equation="10000+x" x=$file_counter assign="file_number"}
        <tr class="TableSubHead" valign="top">
            {*<td>&nbsp;</td>*}
            <td><input type="text" class="form-control" size="6" maxlength="11" name="new_files[{$file_number}][number]" value="{$_new_file.number}" /></td>
            <td><div class="col-xs-5"><input type="text" class="form-control" size="20" maxlength="255" name="new_files[{$file_number}][title]" value="{$_new_file.title}" /></div>
                <div class="col-xs-4"><input id="name_file_{$file_number}" type="hidden" name="new_files[{$file_number}][filename]" value="{$_new_file.filename}" />
                <input id="path_file_{$file_number}" type="text" class="form-control" size="15" maxlength="255" name="new_files[{$file_number}][path]" value="{$_new_file.path}" readonly="readonly" /></div>
                <div class="col-xs-3"><input id="file_{$file_number}" type="button" class="btn btn-default" value="{$lng.lbl_browse_|strip_tags:false|escape}" onclick="javascript: ppd_popup_files(this);" /></div>
            </td>
            <td align="center"><input type="checkbox" value="4" name="new_files[{$file_number}][perms_owner][vis]"{if $_new_file.perms_owner.vis eq 4} checked{/if} />&nbsp;/&nbsp;<input type="checkbox" value="1" name="new_files[{$file_number}][perms_owner][acc]"{if $_new_file.perms_owner.acc eq 1} checked{/if} /></td>
            <td align="center"><input type="checkbox" value="4" name="new_files[{$file_number}][perms_all][vis]"{if $_new_file.perms_all.vis eq 4} checked{/if} />&nbsp;/&nbsp;<input type="checkbox" value="1" name="new_files[{$file_number}][perms_all][acc]"{if $_new_file.perms_all.acc eq 1} checked{/if} /></td>
            <td align="center"><input type="checkbox" value="1" name="new_files[{$file_number}][active]"{if $_new_file.active eq 1} checked{/if} /></td>
            <td nowrap="nowrap"><a href="javascript: void(0);" onclick="javascript: remove_inputset_new(this);"><img src="{$ImagesDir}/minus.gif" alt="Remove row"></a></td>
        </tr>
        {counter}
        {/if}
        {/foreach}
        {/if}

    </table>
<div class="buttons">
    {include file='admin/buttons/button.tpl' href="javascript:cw_submit_form('add_file_form', 'ppd_add');" button_title=$lng.lbl_ppd_add style="btn-green push-20"}
</div>
</form>
<div class="fields-note">{$lng.txt_ppd_fields_note|substitute:'symbol_required':"`$lng.lbl_ppd_field_required`"}</div>

</div>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_ppd_newfile}

