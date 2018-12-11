
<table cellpadding="5" cellspacing="1" width="100%">

<tr class="TableHead">
<td><b>{$lng.lbl_qty}</b></td>
<td><b>{$lng.lbl_sku}</b></td>
<td><b>{$lng.lbl_product}</b></td>
{if $cart.display_cart_products_tax_rates eq "Y"}
<td align="center"><b>{if $cart.product_tax_name ne ""}{$cart.product_tax_name}{else}{$lng.lbl_tax}{/if}</b></td>
{/if}
<td align="right"><b>{$lng.lbl_price}</b></td>
<td align="right"><b>{$lng.lbl_total}</b></td>
</tr>

{section name=prod_num loop=$products}
<tr {cycle values=", class='cycle'"}>
<td class="ProductPriceSmall">{if $config.Appearance.allow_update_quantity_in_cart eq "N" or ($addons.egoods and $products[prod_num].distribution)}{$products[prod_num].amount}{else}{if $link_qty eq"Y"}<a href="index.php?target=cart">{$products[prod_num].amount}</a>{else}<input type="text" size="3" name="productindexes[{$products[prod_num].cartid}]" value="{$products[prod_num].amount}" />{/if}{/if}</td>
<td>{$products[prod_num].productcode}</td>
<td>{$products[prod_num].product|truncate:30:"...":true}</td>
{if $cart.display_cart_products_tax_rates eq "Y"}
<td align="center">
{foreach from=$products[prod_num].taxes key=tax_name item=tax}
{if $cart.product_tax_name eq ""}<span style="white-space: nowrap;">{$tax.tax_display_name}:</span>{/if}
{if $tax.rate_type eq "%"}{$tax.rate_value}%{else}{include file='common/currency.tpl' value=$tax.rate_value}{/if}<br />
{/foreach}
</td>
{/if}
<td class="ProductPriceSmall" align="right">{include file='common/currency.tpl' value=$products[prod_num].display_price}</td>
{math equation="x*y" x=$products[prod_num].display_price y=$products[prod_num].amount assign="total"}
<td class="ProductPriceSmall" align="right">{include file='common/currency.tpl' value=$total}</td>
</tr>
{/section}

{if $addons.estore_gift}
{include file='addons/estore_gift/gc_checkout.tpl'}
{/if}

</table>
