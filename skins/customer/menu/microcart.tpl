<span id='microcart'>
{assign_session var='cart' assign='cart'}
{count assign='minicart_total_items_products' value=$cart.products}
{count assign='minicart_total_items_giftcerts' value=$cart.giftcerts}
{if $minicart_total_items_products+$minicart_total_items_giftcerts eq ""}<a href="{$current_location}/index.php?target=cart"><i class="icon-shopping-cart"></i><span class="no_products cart_qty">0</span></a>
{else}
<a href="{$current_location}/index.php?target=cart"><i class="icon-shopping-cart"></i> <span class="cart_qty">{$minicart_total_items_products+$minicart_total_items_giftcerts|default:0}</span>
 {*if $minicart_total_items_products+$minicart_total_items_giftcerts eq 1}{$lng.lbl_item}{else}{$lng.lbl_items}{/if*} </a>
{/if}
</span>
