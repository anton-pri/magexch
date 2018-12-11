{*include file='common/page_title.tpl' title=$lng.lbl_import_xcart*}
{capture name=section}
{capture name=block}

<div class="Error" style="color:#4e0202;">{$err_msg}</div>
<p>{$lng.txt_imp_from_xc}</p>
<form action="index.php?target={$current_target}&mode=xcart" method="post" name="import_form" enctype="multipart/form-data">
<input type="hidden" name="action" value="import">

<div class="form-horizontal">

<div class="form-group"> 
    <label class="col-xs-12">{$lng.txt_path_to_xc}</label>
    <div class="col-xs-12">
    	<input type="text" name="path" value="{$path}" class="form-control""/>
    </div>
</div>
<div class="checkbox">
	<label class="push-10 ">
		<input type="checkbox" name="agree" value="1" />
		I agree, that all previous data will be erased during this import, including products, categories and users, that are not admin-role users 
	</label>
</div>

</div>

<div class="buttons">{include file='admin/buttons/button.tpl' href="javascript: cw_submit_form('import_form');" button_title=$lng.lbl_import acl='__1300' style="btn-green push-20"}</div>
</form>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block}
{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section title=$lng.lbl_import_xcart}
