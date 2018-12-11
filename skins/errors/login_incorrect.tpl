{$lng.txt_login_incorrect}
<p />
{if $current_area eq 'C'}
{include file='customer/acc_manager/acc_manager.tpl'}
{else}
{include file='main/auth/login_form.tpl'}
{/if}
