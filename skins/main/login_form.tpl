{if $config.Security.use_https_login eq "Y" and $usertype eq "C"}
{assign var="form_url" value=$catalogs_secure.$app_area}
{else}
{assign var="form_url" value=$catalogs.$app_area}
{/if}
<form action="{$form_url}/index.php?target=login" method="post" name="errorform">
<table>
<tr>
	<td height="10" width="78" class="FormButton">{$lng.lbl_email}</td>
	<td width="10" height="10"><font class="Star">*</font></td>
	<td width="282" height="10"><input type="text" name="email" size="30" value="{#default_login#|default:$email}" /></td>
</tr>
<tr>
	<td height="10" width="78" class="FormButton">{$lng.lbl_password}</td>
	<td width="10" height="10"><font class="Star">*</font></td>
	<td width="282" height="10"><input type="password" name="password" size="30" maxlength="64" value="{#default_password#}" />
{if $addons.Simple_Mode ne "" and $usertype ne "C" and $usertype ne "B"}
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
	<td width="282" height="10" class="ErrorMessage">{ if $main eq "login_incorrect"}{$lng.err_invalid_login}{/if}</td>
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
{include file="buttons/submit.tpl" href="javascript:cw_submit_form('errorform')" style='btn'}
</td>
</tr>

</table>

</form>

<div align="right">
    {capture name="page_url"}{pages_url var='help' section='password'}{/capture}
    {include file='buttons/button.tpl' href=$smarty.capture.page_url button_title=$lng.lbl_recover_password}
</div>
{if $usertype eq "C" && !$is_flc}
<br />{$lng.txt_new_account_msg}
{elseif $usertype eq "P"}
<br />{$lng.txt_create_account_msg}
{/if}
