{strip}
<div class="users_online">
{if $users_online}
<b>{$lng.lbl_users_online}:</b>&nbsp;
{foreach from=$users_online item=v name="_users"}
{$v.count}&nbsp;
{if $v.usertype eq 'C' && $v.is_registered eq 'Y'}
{$lng.lbl_registered_customer_s} 
{elseif $v.usertype eq 'C' && $v.is_registered eq ''}
{$lng.lbl_unregistered_customer_s} 
{else}
{lng name="lbl_user_type_`$v.usertype`"}
{/if}
    {if not $smarty.foreach._users.last}, {/if}
{/foreach}
{/if}
</div>
{/strip}
