{capture name=sublinks}
<ul>
<li><a href="{$current_location}/index.php?target=arrivals">{$lng.lbl_whats_new}</a></li>
<li><a href="{$current_location}/index.php?target=hot_deals">{$lng.lbl_hot_deals}</a></li>
<li><a href="{$current_location}/index.php?target=clearance">{$lng.lbl_clearance}</a></li>
{if $addons.bestsellers}
<li><a href="{$current_location}/index.php?target=top_sellers">{$lng.lbl_top_sellers}</a></li>
{/if}
{if $addons.estore_products_review}
<li><a href="{$current_location}/index.php?target=top_rated">{$lng.lbl_top_rated}</a></li>
{/if}
{if $addons.bestsellers}
<li><a href="{$current_location}/index.php?target=customer_wishes">{$lng.lbl_customer_top_wishes}</a></li>
{/if}
<li><a href="{$current_location}/index.php?target=super_deals">{$lng.lbl_super_deals}</a></li>
</ul>
{/capture}
{include file='common/menu.tpl' content=$smarty.capture.sublinks style='simple' title=$lng.special}
