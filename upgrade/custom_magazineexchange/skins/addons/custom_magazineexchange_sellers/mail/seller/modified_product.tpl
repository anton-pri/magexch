{include file="mail/mail_header.tpl"}
<p />
<p />
Seller {$seller_info.firstname} {$seller_info.lastname} ({$seller_info.email}) has {if $is_new_product}added{else}updated{/if} the product:
<br>
<b><a href="{$catalogs.customer}/index.php?target=product&product_id={$product_id}">{$product_data.product}</a></b>
<p />
<p />
{include file="mail/signature.tpl"}