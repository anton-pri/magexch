{capture name=section}
{if $config.Security.use_https_login eq "Y"}
{assign var="form_url" value=$catalogs_secure.$app_area}
{else}
{assign var="form_url" value=$catalogs.$app_area}
{/if}
<form name="pass_form" id="pass_form" action="{$form_url}/index.php?target=change_password" method="post">
<table>
<tr><td colspan="3">{$lng.txt_chpass_msg}</td></tr>
<tr><td colspan="3">&nbsp;</td></tr>
<tr>
<td>{$lng.lbl_email}:</td><td>&nbsp;</td><td><b>{$user_account.email}</b></td>
</tr>
<tr>
<td>{$lng.lbl_old_password}:</td><td><font class="Star">*</font></td><td><input type="password" size="30" name="old_password" value="{$old_password}" /></td>
</tr>
<tr>
<td>{$lng.lbl_new_password}:</td><td><font class="Star">*</font></td><td><input type="password" size="30" name="new_password" value="{$new_password}" /></td>
</tr>
<tr>
<td>{$lng.lbl_confirm_password}:</td><td><font class="Star">*</font></td><td><input type="password" size="30" name="confirm_password" value="{$confirm_password}" /></td>
</tr>
<tr><td colspan="3">&nbsp;</td></tr>
<tr>
<td colspan="3" align="center">{include file="buttons/button.tpl" button_title=$lng.lbl_save href="javascript: cw_submit_form('pass_form');" style='button'}</td>
</tr>
</table>
<input type='submit' style='display:none;' hidefocus="true" tabindex="-1" />
</form>

{/capture}
{include file="common/section.tpl" title=$lng.lbl_chpass content=$smarty.capture.section extra='width="100%"'}
