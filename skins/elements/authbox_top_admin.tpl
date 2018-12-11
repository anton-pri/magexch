{if $config.Security.use_https_login eq "Y"}
{assign var="form_url" value=$catalogs_secure.$app_area}
{else}
{assign var="form_url" value=$catalogs.$app_area}
{/if}

<ul class="dropdown-menu dropdown-menu-right">
<li class="dropdown-header">Profile</li>
<li><a href="{if $current_area eq 'A'}index.php?target=user_A&mode=modify&user={$customer_id}{else}index.php?target=acc_manager{/if}">{$lng.txt_logged_in_admin} {$user_account.email}</a></li>
<li>
<a href="javascript: cw_submit_form('logout_form');">
<i class="si si-logout pull-right"></i>
{$lng.lbl_logoff}
</a>
<form action="{$form_url}/index.php?target=login" method="post" name="logout_form">
<input type="hidden" name="action" value="logout" />
</form>
</li>
</ul>
<div class="font-s13 margin_top_5">


{*include file='buttons/button.tpl' button_title=$lng.lbl_modify href='index.php?target=register&mode=update'}
{include file='buttons/button.tpl' button_title=$lng.lbl_delete href='index.php?target=register&mode=delete'*}
