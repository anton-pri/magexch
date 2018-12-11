{capture name=section}
{if $config.Security.use_https_login eq "Y"}
{assign var="form_url" value=$catalogs_secure.$app_area}
{else}
{assign var="form_url" value=$catalogs.$app_area}
{/if}
<form name="pass_form" id="pass_form" action="{$form_url}/index.php?target=change_password" method="post">
<ul class="change_password">
<li>{$lng.txt_chpass_msg}</li>
<li><label>{$lng.lbl_email}:</label><b>{$user_account.email}</b></li>
<li>
<label>{$lng.lbl_old_password}:<font class="Star">*</font></label><input type="password" size="30" name="old_password" value="{$old_password}" />
</li>
<li>
<label>{$lng.lbl_new_password}:<font class="Star">*</font></label><input type="password" size="30" name="new_password" value="{$new_password}" />
</li>
<li>
<label>{$lng.lbl_confirm_password}:<font class="Star">*</font></label><input type="password" size="30" name="confirm_password" value="{$confirm_password}" />
</li>
<li>
{include file="buttons/button.tpl" button_title=$lng.lbl_save href="javascript: cw_submit_form('pass_form');" style='button'}
</li>
</ul>
<input type='submit' style='display:none;' hidefocus="true" tabindex="-1" />
</form>

{/capture}
{include file="common/section.tpl" title=$lng.lbl_chpass content=$smarty.capture.section extra='width="100%"'}
