{if $usertype eq "A" or $usertype eq "P"}
{capture name=section}

<div align="right">
{include file='buttons/button.tpl' button_title=$lng.lbl_import_trackingid_help href="index.php?target=window&action=IMP','IMP_HELP','width=600,height=460,toolbar=no,status=no,scrollbars=yes,resizable=no,menubar=no,location=no,direction=no');"}
</div>

<br />

{$lng.txt_import_trackingid}

<br />
<br />

<form name="importform" action="index.php?target=process_order" method="post" enctype="multipart/form-data">

<input type="hidden" name="action" value="tracking_data" />

<table cellpadding="0" cellspacing="3">
<tr>
	<td width="15%"><b>{$lng.lbl_import_csv}:</b>&nbsp;&nbsp;</td>
	<td width="85%"><input type="file" name="userfile" /></td>
</tr>

<tr>
	<td colspan="2"><br /><input type="submit" value="{$lng.lbl_import}" /></td>
</tr>

</table>


</form>

{/capture}
{include file="common/section.tpl" title=$lng.lbl_import_trackingid_file content=$smarty.capture.section extra='width="100%"'}
{/if}
