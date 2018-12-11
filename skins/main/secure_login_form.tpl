{$lng.txt_secure_login_form}
<p />
{capture name=section}

<form action="{$catalog_secure.$app_area}/index.php?target=login" method="post" name=secureform>

<table border="0">
<tr>
	<td height="10" width="78" class="FormButton">{$lng.lbl_email}</td>
	<td width="10" height="10"><font class="Star">*</font></td>
	<td width="282" height="10"><input type="text" name="email" size="30" /></td>
</tr>
<tr>
	<td height="10" width="78" class="FormButton">{$lng.lbl_password}</td>
	<td width="10" height="10"><font class="Star">*</font></td>
	<td width="282" height="10">
<input type="password" name="password" size="30" maxlength="64" />
{if $addons.Simple_Mode ne "" and $usertype ne "C"}
<input type="hidden" name="usertype" value="P" />
{else}
<input type="hidden" name="usertype" value="{$usertype}" />
{/if}
<input type="hidden" name="redirect" value="{$redirect}" />
<input type="hidden" name="action" value="login" />
	</td>
</tr>
<tr>
	<td height="10" width="78" class="FormButton"></td>
	<td width="10" height="10">&nbsp;</td>
	<td width="282" height="10" class="ErrorMessage"></td>
</tr>
{if $addons.image_verification and $show_antibot.on_login eq 'Y' and $login_antibot_on}
{include file="addons/image_verification/spambot_arrest.tpl" mode="advanced" id=$antibot_sections.on_login}
{/if}
<tr>
	<td height="10" width="78" class="FormButton"></td>
	<td width="10" height="10">&nbsp;</td>
	<td width="282" height="10" class="ErrorMessage">{if $antibot_err}{$lng.msg_err_antibot}{/if}</td>
</tr>

<tr>
	<td height="10" width="78" class="FormButton"></td>
	<td width="10" height="10" class="FormButton">&nbsp;</td>
	<td width="282" height="10">
{include file="buttons/submit.tpl" href="javascript:cw_submit_form(document.secureform)"}
	</td>
</tr>

</table>
</form>

<p />
{/capture}
{include file="common/section.tpl" title=$lng.lbl_secure_login_form content=$smarty.capture.section extra='width="100%"'}
