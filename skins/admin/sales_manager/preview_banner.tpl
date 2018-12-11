<table cellpadding="0" cellspacing="0" class="PopupContainer">
<tr>
	<td class="PopupTitle">{$lng.lbl_preview_banner}</td>
</tr>
<tr>
	<td height="1"><img src="{$ImagesDir}/spacer.gif" class="Spc" alt="" /></td>
</tr>
<tr>
	<td class="PopupBG" height="1"><img src="{$ImagesDir}/spacer.gif" class="Spc" alt="" /></td>
</tr>

<tr>
	<td height="380" valign="top">

<table width="100%" cellpadding="15" cellspacing="0">
<tr>
	<td valign="middle" align="center" height="350">

<table cellspacing="1" cellpadding="0" bgcolor="#000000">
<tr bgcolor="#ffffff">
	<td>{$banner}</td>
</tr>
</table>

	</td>
</tr>
</table>

	</td>
</tr>
</table>
<script language="javascript">
if (window.opener && window.opener.document.getElementById('banner_body')) document.previewform.preview.value = window.opener.document.getElementById('banner_body').value; cw_submit_form(document.previewform);"
</script>
