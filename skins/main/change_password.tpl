{capture name=section}
<form action="index.php?target=change_password" method="post">
<table>
<tr>
	<td colspan="3">{$lng.txt_chpass_msg}</td>
</tr>
<tr>
	<td colspan="3">&nbsp;</td>
</tr>
<tr>
	<td>{$lng.lbl_email}:</td>
	<td>&nbsp;</td>
        <td><b>{$user_account.email}</b></td>
</tr>
<tr>
	<td>{$lng.lbl_old_password}:</td>
	<td><font class="Star">*</font></td>
	<td><input type="password" size="30" name="old_password" maxlength="64" value="{$old_password}" /></td>
</tr>
<tr>
	<td>{$lng.lbl_new_password}:</td>
	<td><font class="Star">*</font></td>
	<td><input type="password" size="30" name="new_password" maxlength="64" value="{$new_password}" /></td>
</tr>
<tr>
	<td>{$lng.lbl_confirm_password}:</td>
	<td><font class="Star">*</font></td>
	<td><input type="password" size="30" name="confirm_password" value="{$confirm_password}" /></td>
</tr>
<tr>
	<td colspan="3">&nbsp;</td>
</tr>
<tr>
	<td colspan="3" align="center"><input type="submit" /></td>
</tr>
</table>
</form>
{/capture}
{include file="common/section.tpl" title=$lng.lbl_chpass content=$smarty.capture.section extra='width="100%"'}
