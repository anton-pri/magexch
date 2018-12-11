{capture name="main_section"}
<script type="text/javascript">
var txt_are_you_sure = "{$lng.txt_are_you_sure|escape:javascript}";
</script>

{if $smarty.get.mode neq 'add'}

<form method="post" action="index.php?target={$current_target}" name="languages_form">
<input type="hidden" name="action" value="" />
{capture name=section}

<table class="table table-striped dataTable vertical-center" width="100%">
<thead>
<tr>
    <th class="text-center"><input type='checkbox' class='select_all' class_to_select='languages_item' /></th>
    <th>{$lng.lbl_language}</th>
    <th class="text-center">{$lng.lbl_avail}</th>
    <th class="text-center">{$lng.lbl_r2l_text_direction}</th>
    <th class="text-center">{$lng.lbl_charset}</th>
    <th>&nbsp;</th>
    <th>&nbsp;</th>
</tr>
</thead>
{if $languages}
{foreach from=$languages item=l}
<tr>
    <td align="center"><input type="checkbox" name="del[{$l.code}][delete]" value="1" class="languages_item"/></td>
    <td>{$l.language}</td>
    <td align="center"><input type="checkbox" name="upd[{$l.code}][enable]" value="1"{if $l.enable} checked{/if}/></td>
    <td align="center"><input type="checkbox" name="upd[{$l.code}][text_direction]" value="1"{if $l.text_direction} checked{/if}/></td>
    <td align="center"><input class="form-control" type="text" name="upd[{$l.code}][charset]" value="{$l.charset}" /></td>
    <td align="center">
        {include file='admin/buttons/button.tpl' href="index.php?target=languages&language=`$l.code`" button_title=$lng.lbl_modify style="btn-green"}
    </td>
    <td align="center">
        {*include file='main/select/delimiter.tpl'*}
        {include file='admin/buttons/button.tpl' href="index.php?target=languages&action=export&language=`$l.code`" button_title=$lng.lbl_export view="top_"  style="btn-green"}
    </td>
</tr>
{/foreach}
{/if}
</table>

<div class="buttons">
{include file='admin/buttons/button.tpl' href="javascript: cw_submit_form('languages_form', 'update_languages');" button_title=$lng.lbl_update style="btn-green push-20 push-5-r"}
{include file='admin/buttons/button.tpl' href="javascript: if (confirm(txt_are_you_sure)) cw_submit_form(document.languages_form, 'del_lang');" button_title=$lng.lbl_delete style="btn-danger push-20 push-5-r"}
{include file='admin/buttons/button.tpl' href="index.php?target=languages&mode=add" button_title=$lng.lbl_add_new style="btn-green push-20 push-5-r"}
</div>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.section title=$lng.lbl_edit_language}

</form>




{capture name=section}
<form method="post" action="index.php?target=languages" class="form-horizontal">

<div class="form-group">
	<label class="col-xs-12">{$lng.lbl_default_customer_language}:</label>
	<div class="col-xs-12">
	<select name="new_customer_language" class="form-control">
		<option value="">{$lng.lbl_select_one}</option>
		{foreach from=$languages item=l}
		{if $l.disabled ne 'Y' || $config.default_customer_language eq $l.code}
		<option value="{$l.code}"{if $config.default_customer_language eq $l.code} selected="selected"{/if}>{$l.language}</option>
		{/if}
		{/foreach}
	</select>
	</div>
</div>
<div class="form-group">
	<label class="col-xs-12">{$lng.lbl_default_admin_language}:</label>
	<div class="col-xs-12">
	<select name="new_admin_language" class="form-control">
		<option value="">{$lng.lbl_select_one}</option>
		{foreach from=$languages item=l}
		{if $l.disabled ne 'Y' || $config.default_admin_language eq $l.code}
		<option value="{$l.code}"{if $config.default_admin_language eq $l.code} selected="selected"{/if}>{$l.language}</option>
		{/if}
		{/foreach}
	</select>
	</div>
</div>
<div class="buttons">
	<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" class="btn btn-green push-20" />
</div>
<input type="hidden" name="action" value="change_defaults" />
<input type="hidden" name="language" value="{$smarty.get.language|escape:'html'}" />
</form>
{/capture}
{include file='admin/wrappers/block.tpl' content=$smarty.capture.section title=$lng.lbl_default_languages}

{else}

{capture name=section}
<form method="post" action="index.php?target=languages" enctype="multipart/form-data" name="newlanguageform">
<input type="hidden" name="action" value="add_lang" />
<div class="form-group">
	<label>{$lng.lbl_choose_language}:</label>
	<div >
	<select name="new_language" class="form-control">
		<option value="">{$lng.lbl_select_one}</option>
		{foreach from=$new_languages item=l}
		<option value="{$l.code}">{$l.language}</option>
		{/foreach}
	</select>
	</div>
</div>

<div class="form-group">
       	<label>{$lng.lbl_csv_delimiter}:</label>
		<div>{include file="main/select/delimiter.tpl"}</div>
</div>

<div class="form-group">
		<script type="text/javascript">
		filesrc='1';
		</script>
		<label>{$lng.txt_source_import_file}:</label>

		<table cellpadding="0" cellspacing="0" width="70%">
		<tr>
			<td style="padding:0 0 5px;width: 20px;"><input type="radio" id="source_server" name="source" value="server"{if $import_data eq '' || $import_data.source eq 'server'} checked="checked"{/if} onclick="javascript: $('#box2').hide();$('#box1').show();" /></td>
			<td style="padding:0 0 5px;"><label for="source_server">{$lng.lbl_server}</label></td>
		</tr>
		<tr>
			<td style="padding:0 0 5px;width: 20px;"><input type="radio" id="source_upload" name="source" value="upload"{if $import_data.source eq 'upload'} checked="checked"{/if} onclick="javascript: $('#box1').hide();$('#box2').show();" /></td>
			<td style="padding:0 0 5px;"><label for="source_upload">{$lng.lbl_home_computer}</label></td>
		</tr>
		</table>
</div>
<div class="form-group" id="box1" {if $import_data ne '' && $import_data.source ne 'server'} style="display: none;"{/if}>
	<label>{$lng.txt_csv_file_is_located_on_the_server}:</label>

	<input type="text" size="60" name="localfile" value="{$localfile}" /> 
	<br />
	{$lng.txt_csv_file_is_located_on_the_server_expl|substitute:"my_files_location":$my_files_location}
</div>

<div class="form-group" id="box2"{if $import_data eq '' || $import_data.source ne 'upload'} style="display: none;"{/if}>
	<label>{$lng.lbl_csv_file_for_upload}:</label>
       <br /><input type="file" size="60" name="import_file" />
	{if $upload_max_filesize}
	<br /><font class="Star">{$lng.lbl_warning}!</font> {$lng.txt_max_file_size_that_can_be_uploaded}: {$upload_max_filesize}b.
	{/if}

</div>	

<p style="padding:7px;" />
{$lng.txt_import_language_note}
<div class="buttons">
<input type="submit" value="{$lng.lbl_add_update_language|strip_tags:false|escape}"  class="btn btn-green push-20" />
</div>
</form>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.section title=$lng.lbl_add_new_language}
{/if}

{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.main_section title=$lng.lbl_languages}
