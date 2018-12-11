{if !$customer_id}
{capture name=menu}
<div class="label">{$lng.lbl_b2b_customers_login}</div>
<a href="{$catalogs.customer}/index.php?target=acc_manager&usertype=R" class="Bullet">{$lng.lbl_reseller_login}</a>
<a href="{$catalogs.customer}/index.php?target=acc_manager&usertype=R" class="Bullet">{$lng.lbl_register_as_reseller}</a>
{/capture}
{include file='common/menu.tpl' title=$lng.lbl_dealers_and_distributers content=$smarty.capture.menu}
{/if}
