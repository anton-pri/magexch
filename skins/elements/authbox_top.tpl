{if $config.Security.use_https_login eq "Y"}
{assign var="form_url" value=$catalogs_secure.$app_area}
{else}
{assign var="form_url" value=$catalogs.$app_area}
{/if}
<form action="{$form_url}/index.php?target=login" method="post" name="logout_form">
{$user_account.email} {$lng.txt_logged_in}
<input type="hidden" name="action" value="logout" />
</form>
{include file='buttons/button.tpl' button_title=$lng.lbl_modify href='index.php?target=acc_manager'} | 
{include file='buttons/button.tpl' button_title=$lng.lbl_logoff href="javascript: cw_submit_form('logout_form');"} 
{*include file='buttons/button.tpl' button_title=$lng.lbl_delete href='index.php?target=register&mode=delete'*}
