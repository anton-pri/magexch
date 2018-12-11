{count assign='minicart_total_items_products' value=$cart.products}
{count assign='minicart_total_items_giftcerts' value=$cart.giftcerts}

{if $minicart_total_items_products+$minicart_total_items_giftcerts > 0}
<div class="items">
    <label>{$lng.lbl_cart_items}:</label>
    {$minicart_total_items_products+$minicart_total_items_giftcerts|default:0}
</div>
<div class="total">
    <label>{$lng.lbl_total}:</label>
    {if $cart}{include file='common/currency.tpl' value=$cart.info.total|default:0}{else}{include file='common/currency.tpl' value=0}{/if}
</div>
{else}
<div class="empty_cart"> 
{$lng.lbl_cart_is_empty}
</div>
{/if}
