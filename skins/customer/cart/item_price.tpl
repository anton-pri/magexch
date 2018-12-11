{assign var="price" value=$product.display_price}
<div align="left">
<font class="ProductPriceConverting">
<span id="cart_item_price_{$product.cartid}">{include file='common/currency.tpl' value=$price}</span> x {if $addons.egoods and $product.distribution || $from_quote}{$product.amount}<input type="hidden"{else}<input type="number" size="3"{/if} name="productindexes[{$product.cartid}]" value="{$product.amount}" id="productindexes_{$product.cartid}" {if $use_ajax} onChange="javascript: ajax_update_cart();"{/if} > = 
</font>
<font class="ProductPrice">
    {math equation="price*amount" price=$price amount=$product.amount format="%.2f" assign=unformatted}
    <span id="cart_item_total_{$product.cartid}">{include file='common/currency.tpl' value=$unformatted}</span>
</font>
<font class="MarketPrice"> 
    <span id="cart_item_alter_{$product.cartid}">{include file='common/alter_currency_value.tpl' alter_currency_value=$unformatted}</span>
</font>
{if $config.Taxes.display_taxed_order_totals eq "Y" and $product.taxes}<span id="cart_item_taxes_{$product.cartid}">{include file="customer/main/taxed_price.tpl" taxes=$product.taxes}</span>
{/if}
</div>

