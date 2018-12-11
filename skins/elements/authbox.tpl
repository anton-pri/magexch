{capture name=menu}
{if $config.Security.use_https_login eq "Y"}
{assign var="form_url" value=$catalogs_secure.$app_area}
{else}
{assign var="form_url" value=$catalogs.$app_area}
{/if}
<form action="{$form_url}/index.php?target=acc_manager" method="post" name="logout_form">
<div class="customer_email"><b>{$user_account.email} </b><br />{$lng.txt_logged_in}</div>
<input type="hidden" name="action" value="logout" />

<div class="auth_buttons logged">
<ul>
<li>{include file='buttons/button.tpl' button_title=$lng.lbl_logoff href="javascript: cw_submit_form('logout_form');"} </li>
<li>{include file='buttons/button.tpl' button_title=$lng.lbl_modify href='index.php?target=acc_manager' }</li>
<li>{include file='buttons/button.tpl' button_title=$lng.lbl_delete href='index.php?target=acc_manager&mode=delete'}</li>
</ul>
</div>
</form>
{/capture}
{include file='common/menu.tpl' title=$lng.lbl_authentication content=$smarty.capture.menu}
