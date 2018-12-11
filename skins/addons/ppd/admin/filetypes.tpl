{if $addons.ppd ne '' && $current_target eq 'filetypes'}

{if $smarty.get.mode neq 'add'}
{capture name=section}
{capture name=block}

<script type="text/javascript">
    {if $config.ppd.ppd_icons_dir ne ''}
    var ppd_dir = "{$config.ppd.ppd_icons_dir|escape}";
    {literal}
    var ppd_pattern=new RegExp("^/");
    if (ppd_pattern.test(ppd_dir) == false) {
        ppd_dir = '/' + ppd_dir;
    }
    {/literal}
    {/if}
</script>
{* moved to ppd/init.php include_once_src file='main/include_js.tpl' src='addons/ppd/js/popup_files.js' *}
<form action="index.php?target={$current_target}" method="post" name="update_types_form">

    {if $ppd_types}
        {include file='common/navigation.tpl'}
    {/if}

<div class="box">
    <input type="hidden" name="action" value="ppd_filetype_update" />


    <table class="table table-striped dataTable vertical-center">
    <thead>
        <tr valign="top">
            <th width="5%">{if $ppd_types}<input type='checkbox' class='select_all' class_to_select='update_types_item' />{else}&nbsp;{/if}</th>
            <th>{$lng.lbl_ppd_file_type}</th>
            <th>{$lng.lbl_ppd_file_exts}</th>
            <th>{$lng.lbl_ppd_file_icon}</th>
        </tr>
	</thead>
        {if $ppd_types}

        {foreach from=$ppd_types item=type}
        <tr{cycle values=', class="cycle"'}>
            <td align="center"><input type="checkbox" value="1" name="type_ids[{$type.type_id}]" class="update_types_item" /></td>
            <td>{if $type.fileicon_url ne ''}<img class="file-icon" src="{$type.fileicon_url|escape}" alt="{$type.type|escape}" />{/if}{$type.type|escape}</td>
            <td>{$type.extension|escape}</td>
            <td><input type="text" class="form-control" size="15" maxlength="255" name="ppd_types[{$type.type_id}][fileicon]" value="{$type.fileicon|escape:html}" />{if !$type.fileicon_exists} (<span class="file-not-exist">{$lng.lbl_ppd_file_not_exist}</span>){/if}</td>
        </tr>
        {/foreach}
        {if $navigation.total_pages gt 2}
        <tr>
            <td colspan="4">{include file='common/navigation.tpl'}</td>
        </tr>
{*
        <tr>
            <td colspan="4" class="recs-delimiter"><img src="{$ImagesDir}/spacer.gif" height="5px" class="Spc" alt="" /></td>
        </tr>
*}
{/if}
{*
        <tr>
            <td colspan="4" class="recs-delimiter"><img src="{$ImagesDir}/spacer.gif" height="5px" class="Spc" alt="" /></td>
        </tr>
*}
        {else}
        <tr>
            <td colspan="4" align="center">{$lng.txt_ppd_no_elements}</td>
        </tr>
        {/if}
    </table>

</div>

<div class="buttons">
                {include file='admin/buttons/button.tpl' href="javascript:cw_submit_form('update_types_form');" button_title=$lng.lbl_ppd_update_selected style="btn-green push-5-r push-20"}
                {include file='admin/buttons/button.tpl' href="javascript:cw_submit_form('update_types_form', 'ppd_filetype_delete');" button_title=$lng.lbl_ppd_delete_selected style="btn-danger push-5-r push-20"}
                {include file='admin/buttons/button.tpl' href="index.php?target=filetypes&mode=add" button_title=$lng.lbl_add_new  style="btn-green push-5-r push-20"}
</div>
</form>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block title=$lng.txt_ppd_filetype_top_text}
{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section title=$lng.lbl_filetypes}


{elseif $smarty.get.mode eq 'add'}

{capture name=sections2}
{capture name=block2}
<form action="index.php?target={$current_target}" method="post" name="update_types_form">
    <input type="hidden" name="action" value="ppd_filetype_update" />
<div class="box">

    <table class="table table-striped dataTable vertical-center">
{*        <tr>
            <td colspan="4">{include file="common/subheader.tpl" title=$lng.lbl_ppd_newtype}</td>
        </tr>
*}
		<thead>
        <tr>
            <th>{$lng.lbl_ppd_file_type}&nbsp;{$lng.lbl_ppd_field_required}</th>
            <th>{$lng.lbl_ppd_file_exts}&nbsp;{$lng.lbl_ppd_field_required}</th>
            <th colspan="2">{$lng.lbl_ppd_file_icon}</th>
        </tr>
		</thead>
        <tr valign="top">
            <td id="ftype_box_1"><input id="ftype_type_0" type="text" class="form-control" size="35" maxlength="50" name="new_types[0][type]" value="{$_new_types[0].type|escape}" /></td>
            <td id="ftype_box_2" align="center"><input id="ftype_extension_0" type="text" class="form-control" size="10" maxlength="10" name="new_types[0][extension]" value="{$_new_types[0].extension|escape}" /></td>
            <td id="ftype_box_3"><!--<input id="ftype_fileicon_0" type="text" size="15" maxlength="255" name="new_types[0][fileicon]" value="{$_new_types[0].fileicon|escape}" />-->
                <input id="ftype_name_file_0" type="hidden" name="new_types[0][fileicon]" value="{$_new_types[0].fileicon|escape}" />
				<div class="row">
                	<div class="col-xs-8"><input id="ftype_path_file_0" type="text" class="form-control" size="40" maxlength="255" name="new_types[0][path]" value="{$_new_types[0].path|escape}" readonly="readonly" /></div>
                	<div class="col-xs-4"><input id="file_0" type="button" class="btn btn-default" value="{$lng.lbl_browse_|strip_tags:false|escape}" onclick="javascript: ppd_popup_files(this, 'ftype_name_', 'ftype_path_', true);" /></div>
				</div>
            </td>
            <td id="ftype_add_button">{include file="main/multirow_add.tpl" mark="ftype" is_lined=true}</td>
        </tr>
    </table>
    {if $_new_types ne '' && $_new_types|@count gt 1}
    {assign var='ppd_id' value='ftype'}
    <script type="text/javascript">
        {foreach from=$_new_types item=_new_type name=new_ppd_types}
        {if !$smarty.foreach.new_ppd_types.first}
        add_inputset_preset('{$ppd_id}', document.getElementById('{$ppd_id}_add_button'), false,
        [
            {ldelim}regExp: /{$ppd_id}_type/, value: '{$_new_type.type|escape}'{rdelim},
            {ldelim}regExp: /{$ppd_id}_extension/, value: '{$_new_type.extension|escape}'{rdelim},
            {ldelim}regExp: /{$ppd_id}_path_file/, value: '{$_new_type.path|escape}'{rdelim},
            {ldelim}regExp: /{$ppd_id}_name_file/, value: '{$_new_type.fileicon|escape}'{rdelim},
        ]
        );
        {/if}
        {/foreach}
    </script>
    {/if}
</div>
    <div class="buttons">{include file='admin/buttons/button.tpl' href="javascript:cw_submit_form('update_types_form', 'ppd_filetype_add');" button_title=$lng.lbl_ppd_add style="btn-green push-20"}</div>
</form>
<div class="fields-note">{$lng.txt_ppd_fields_note|substitute:'symbol_required':"`$lng.lbl_ppd_field_required`"}</div>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block2}
{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.sections2 title=$lng.lbl_ppd_newtype}

{/if}

{/if}
