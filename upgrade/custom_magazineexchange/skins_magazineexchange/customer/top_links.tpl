<div class="top_links">
  {*include file='customer/menu/register.tpl'*}
  <a href="index.php?target=cart&mode=checkout" style="background-image: none;">{$lng.lbl_checkout}</a>
  {include file='customer/menu/microcart.tpl'}
  {include file='customer/menu/login.tpl'} 
  {*<a href="{$current_location}/index.php?target=help&section=login_customer">{$lng.lbl_login}</a>*}
  <span class="hello">{$lng.lbl_hello} <b>{if !$customer_id}{$lng.lbl_guest}{else}{$user_address.current_address.firstname|truncate:16:"...":true}{*$user_address.current_address.lastname*}{/if}</b></span>
  <div class="clear"></div>
</div>
