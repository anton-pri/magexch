<div id="footer_container">
<div id="footer">


{if ($usertype eq "C" or $usertype eq "B") && !$home_style}
<div class="bottom_links">
{include file='customer/elements/bottom_links.tpl'}
</div>
{/if}

<div class="logged">
    {if $customer_id}
      {$lng.lbl_logged_as} <a href="index.php?target=acc_manager&usertype=C">{$user_account.firstname} {$user_account.lastname|default:$user_account.email}</a> 
  
    {else}   
      <a href="index.php?target=help&section=login_customer">{$lng.lbl_login}</a>
    {/if}

{*    <div class="full_version"><a href="#">{$lng.lbl_full_version}</a></div>*}
</div>

<div class="bottom_line">{include file='elements/copyright.tpl'}</div>

<div class="clear"></div>


{if $addons.now_online && $home_style ne 'popup' && $users_online}
{include file='addons/now_online/menu_users_online.tpl'}
{/if}
</div>
</div>
