{include file='common/page_title.tpl' title=$section_title}

<br />
{capture name=section}
<form action="index.php?target=change_mpassword" method="post">
{if $from_config ne ''}
<input type="hidden" name="from_config" value="{$from_config}" />
{/if}

<table>
<tr>
	<td colspan="3">{$lng.txt_change_mpassword}</td>
</tr>
<tr>
	<td colspan="3">&nbsp;</td>
</tr>
{if $config.mpassword ne ''}
<tr>
	<td>{$lng.lbl_old_merchant_password}:</td>
	<td><font class="Star">*</font></td>
	<td><input type="password" size="30" maxlength="64" name="old_password" value="{$old_password}" /></td>
</tr>
{/if}
<tr>
	<td>{if $config.mpassword eq ''}{$lng.lbl_merchant_password}{else}{$lng.lbl_new_merchant_password}{/if}:</td>
	<td><font class="Star">*</font></td>
	<td><input type="password" size="30" name="new_password" value="{$new_password}" /></td>
</tr>
<tr>
	<td>{$lng.lbl_confirm_merchant_password}:</td>
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
{include file="common/section.tpl" title=$section_title content=$smarty.capture.section extra='width="100%"'}
