<div class="dialog_title">
{$lng.txt_payment_upload_note}<br />
{$lng.txt_payment_upload_example}</div>

{capture name=section}
<form method="post" action="index.php?target=payment_upload" enctype="multipart/form-data" name="payment_upload_form">
<input type="hidden" name="action" value="upload" />

<div class="input_field_1">
	<label>{$lng.lbl_csv_delimiter}</label>
	{include file='main/select/delimiter.tpl'}
</div>
<div class="input_field_1">
	<label>{$lng.lbl_csv_file}</label>
	<input type="file" name="userfile" />
</div>
{include file='buttons/button.tpl' button_title=$lng.lbl_save href="javascript: cw_submit_form('payment_upload_form')" acl='__1105'}

</form>
{/capture}
{include file='common/section.tpl' content=$smarty.capture.section title=$lng.lbl_payment_upload}
