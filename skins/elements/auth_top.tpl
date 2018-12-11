{if $config.Security.use_https_login eq "Y"}
    {assign var="form_url" value=$catalogs_secure.$app_area}
{else}
    {assign var="form_url" value=$catalogs.$app_area}
{/if}
{if !$customer_id}
<form action="{$form_url}/index.php?target=acc_manager" method="post" name="auth_form_top">
<input type="hidden" name="action" value="login_customer" />

<div class="input_field_1">
<label for="auth_username">{$lng.lbl_email}</label>
<input type="email" name="email" id="auth_username" size="16" value="{#default_login#|default:$email}" />
</div>

<div class="input_field_1">
<label for="auth_password">{$lng.lbl_password}</label>
<input type="password" name="password" size="16" maxlength="64" value="{#default_password#}" />
</div>

{include file='buttons/button.tpl' button_title=$lng.lbl_log_in href="javascript: cw_submit_form('auth_form_top');" style='top'}
{include file='buttons/create_profile_menu.tpl' style='top'}
<a href="{pages_url var='help' section='password'}" id="forgot_password">{$lng.lbl_forgot_password}</a>
</form>

{else}
<form action="{$form_url}/index.php?target=acc_manager" method="post" name="logout_form_top">
<input type="hidden" name="action" value="logout" />
{$user_account.email} {$lng.txt_logged_in}
{include file='buttons/button.tpl' button_title=$lng.lbl_modify_profile href="index.php?target=acc_manager" style='top'}
{include file='buttons/button.tpl' button_title=$lng.lbl_logoff href="javascript: cw_submit_form('logout_form_top');" style='top'}
</form>

{/if}
