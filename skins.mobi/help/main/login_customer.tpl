{if $config.Security.use_https_login eq "Y"}
{assign var="form_url" value=$catalogs_secure.$app_area}
{else}
{assign var="form_url" value=$catalogs.$app_area}
{/if}
<form action="{$form_url}/index.php?target={if $current_area eq 'C'}acc_manager{else}login{/if}" method="post" name="auth_form">
<input type="hidden" name="action" value="login" />
<div class="input_field_easy_0">
    <label>{$lng.lbl_email}</label><br/>
    <input type="text" name="email" size="25" value="{#default_login#|default:$email}" />
</div>
<div class="input_field_easy_0">
    <label>{$lng.lbl_password}</label><br/>
    <input type="password" name="password" size="25" maxlength="64" value="{#default_password#}" onkeypress="javascript: return submitEnter(event);"/><br />
</div>
<div class="auth_buttons">{include file='buttons/login_menu.tpl'}
{if $usertype eq "C" or ($usertype eq "B" and $config.Salesman.salesman_register eq "Y")}
{include file='buttons/create_profile_menu.tpl'}
{/if}
</div>
{if !$customer_id}
<div class="password_recovery"><a href="{pages_url var='help' section='password'}">{$lng.lbl_recover_password}</a></div>
{/if}
</form>

