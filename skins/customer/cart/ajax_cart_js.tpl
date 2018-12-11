{*strip*}
{ldelim}
"expired":"{$expired}",
"cart_items":{ldelim}
{foreach from=$products item=product name="cart_items_subtotal"}
    {assign var="price" value=$product.display_price}
    {math equation="price*amount" price=$price amount=$product.amount format="%.2f" assign=unformatted}
    "{$product.cartid}":{ldelim}
        "price":"{capture name=tmp}{include file='common/currency.tpl' value=$price}{/capture}{$smarty.capture.tmp|escape:"json"}",
        "total":"{capture name=tmp}{include file='common/currency.tpl' value=$unformatted}{/capture}{$smarty.capture.tmp|escape:"json"}",
        "alter":"{capture name=tmp}{include file='common/alter_currency_value.tpl' alter_currency_value=$unformatted}{/capture}{$smarty.capture.tmp|escape:"json"}",
        "taxes":"{capture name=tmp}{include file="customer/main/taxed_price.tpl" taxes=$product.taxes}{/capture}{$smarty.capture.tmp|escape:"json"}",
{*        "special":"{capture name=tmp}{/capture}{$smarty.capture.tmp|escape:"json"}", *}
        "amount":"{$product.amount}"
    {rdelim}{if !$smarty.foreach.cart_items_subtotal.last},{/if}
{/foreach}
{rdelim}

{if $config.Appearance.show_cart_summary eq 'Y'}
,"cart_totals_arr":{ldelim}
    {foreach from=$warehouses_cart item=tmp_cart name="cart_totals_arr"}
        {if $enought_count}
{capture name=tmp}{include file="customer/cart/totals.tpl" shipping=$tmp_cart.shipping small_format=true shipping_name="shipping_arr[`$tmp_cart.warehouse_customer_id`]" cart=$tmp_cart carrier_name="carrier_arr[`$tmp_cart.warehouse_customer_id`]" current_carrier=$tmp_cart.current_carrier use_ajax=true cart_warehouse=$tmp_cart.warehouse}{/capture}
        {else}
{capture name=tmp}{include file="customer/cart/totals.tpl" shipping=$tmp_cart.shipping shipping_name="shipping_arr[`$tmp_cart.warehouse_customer_id`]" cart=$tmp_cart carrier_name="carrier_arr[`$tmp_cart.warehouse_customer_id`]" use_ajax=true cart_warehouse=$tmp_cart.warehouse_customer_id}{/capture}
        {/if}
    "{$tmp_cart.warehouse_customer_id}":"{$smarty.capture.tmp|escape:"json"}"{if !$smarty.foreach.cart_totals_arr.last},{/if}
    {/foreach}
{rdelim}
    {if $enought_count}
{capture name=tmp}{include file="customer/cart/totals.tpl" need_shipping=false use_ajax=true}{/capture}
,"grand_total":"{$smarty.capture.tmp|escape:"json"}"
    {/if}
{else}
{capture name=tmp}{include file="customer/cart/totals.tpl" need_shipping=true use_ajax=true}{/capture}
,"cart_totals":"{$smarty.capture.tmp|escape:"json"}"
{/if}
{rdelim}
{*/strip*}
