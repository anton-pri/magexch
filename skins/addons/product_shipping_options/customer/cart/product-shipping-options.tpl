{tunnel func='cw_product_shipping_get_options' via='cw_call' assign='product_shipping_options_data' param1=$product.product_id}
{assign var='shipping_values' value=$product_shipping_options_data.shipping_values}
{if $shipping_values|@count gt 1}
<select name="product_shipping_options[{$product.cartid}]">
{foreach from=$shipping_values item=shv}
<option value="{$shv.shipping_id}" {if $product.product_shipping_option eq $shv.shipping_id}selected="selected"{/if}>{$shv.shipping.shipping} - {include file='common/currency.tpl' value=$shv.price}</option>
{/foreach}
</select>
{else}
{$lng.lbl_free_shipping}
{/if}
