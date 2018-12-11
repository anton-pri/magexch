{*include file='common/page_title.tpl' title=$lng.lbl_import_data*}
{capture name=section}
{capture name=block}

<p>{$lng.txt_import_data_text}</p>

<div class="Error" style="color:#4e0202;">{$err_msg}</div>

<form action="index.php?target={$current_target}&mode=impdata" method="post" name="upload_form" enctype="multipart/form-data">
<input type="hidden" name="action" value="upload">
<div class="form-horizontal">
<div class="form-group">
	<label class="col-xs-12">{$lng.lbl_upl_csv_local}</label>

	<div class="col-xs-12"><input name="csvfile" type="file" /></div>
</div>

<div class="form-group">
	<div class="col-xs-12">{$lng.lbl_upl_max_warning|substitute:upload_max_filesize:$max_upl_size}</div>
</div>

</div>

{include file='admin/buttons/button.tpl' href="javascript: cw_submit_form('upload_form');" button_title=$lng.lbl_upload acl='__1300' style="btn-green push-20 push-5-r"}
</form>


{if $files}
<form action="index.php?target={$current_target}&mode=impdata" method="post" name="import_form" enctype="multipart/form-data">
<input id="act1" type="hidden" name="action" value="import">
<div class="form-horizontal">
<div class="form-group">
	<label class="col-xs-12">{$lng.lbl_imp_csv_upl_before}</label>
</div>
{assign var="i" value=0}
{section name=ind loop=$files}
<div class="form-group">
<label class="col-xs-12">
	<input type="checkbox" name="filenames[{$i++}]" value="{$files[ind]}" />&nbsp;
	{$files[ind]}
</label>
</div>
{/section}
</div>
<div class="buttons">
{include file='admin/buttons/button.tpl' href="javascript:cw_submit_form('import_form');" button_title=$lng.lbl_import acl='__1300' style="btn-green push-20 push-5-r"}
{include file='admin/buttons/button.tpl' href="javascript:document.getElementById('act1').value='delete'; cw_submit_form('import_form');" button_title=$lng.lbl_delete_selected acl='__1300' style="btn-danger push-20 push-5-r"}
</div>
</form>
{/if}

{if $files2}
<form action="index.php?target={$current_target}&mode=impdata" method="post" name="import_form2" enctype="multipart/form-data">
<input id="act2" type="hidden" name="action" value="import2">
<div class="form-horizontal">
<div class="form-group">
	<label class="col-xs-12">{$lng.lbl_imp_csv_fr_serv}</label>
</div>
{assign var="i" value=0}
{section name=ind loop=$files2}
<div class="form-group">
<label class="col-xs-12">
	<input type="checkbox" name="filenames[{$i++}]" value="{$files2[ind]}" />&nbsp;
	{$files2[ind]}
</label>
</div>
{/section}
</div>
<div class="buttons">
{include file='admin/buttons/button.tpl' href="javascript:cw_submit_form('import_form2');" button_title=$lng.lbl_import acl='__1300' style="btn-green push-20 push-5-r"}
{include file='admin/buttons/button.tpl' href="javascript:document.getElementById('act2').value='delete2'; cw_submit_form('import_form2');" button_title=$lng.lbl_delete_selected acl='__1300' style="btn-danger push-20 push-5-r"}
</div>
</form>
{/if}
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block}

{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section title=$lng.lbl_import_data}
