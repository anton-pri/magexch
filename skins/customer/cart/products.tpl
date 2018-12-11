{foreach from=$products item=product}
{if !$product.hidden}
<tr><td class="PListImgBox">
<a href="{pages_url var="product" product_id=$product.product_id}">{if $product.is_pimage eq 'W' }{assign var="imageid" value=$product.variant_id}{else}{assign var="imageid" value=$product.product_id}{/if}{include file='common/thumbnail.tpl' image=$product.image}</a>
</td>
<td valign="top">
<font class="ProductCartTitle">{$product.product}</font>

<table cellpadding="0" cellspacing="0" width="100%"><tr><td>
{$product.descr}
</td></tr></table>
<br />
{include file='customer/products/free_shipping.tpl'}
<br />
{* kornev, TOFIX *}
{if $product.product_options ne ""}
<b>{$lng.lbl_selected_options}:</b><br />
{include file="addons/product_options/main/options/display.tpl" options=$product.product_options}
<br />
<br />
{/if}
{assign var="price" value=$product.display_price}
<div align="left">
<font class="ProductPriceConverting">{include file='common/currency.tpl' value=$price} x {if $addons.egoods and $product.distribution}1<input type="hidden"{else}<input type="text" size="3"{/if} name="productindexes[{$product.cartid}]" value="{$product.amount}" /> = </font><font class="ProductPrice">{math equation="price*amount" price=$price amount=$product.amount format="%.2f" assign=unformatted}{include file='common/currency.tpl' value=$unformatted}</font><font class="MarketPrice"> {include file='common/alter_currency_value.tpl' alter_currency_value=$unformatted}</font>
{if $config.Taxes.display_taxed_order_totals eq "Y" and $product.taxes}<br />{include file="customer/main/taxed_price.tpl" taxes=$product.taxes}
{/if}
<br />
<br />
<table cellspacing="0" cellpadding="0">
<tr>
    <td class="ButtonsRow">{include file="buttons/delete_item.tpl" href="index.php?target=cart&amp;mode=delete&amp;productindex=`$product.cartid`" style='top'}</td>
    <td class="ButtonsRow">
{* kornev, TOFIX *}
{if $product.product_options ne ''}
{include file="buttons/edit_product_options.tpl" id=$product.cartid style='top'}
{/if}
    </td>
</tr>
</table>
</div>
</td></tr>
<tr><td colspan="2"><hr size="1" noshade="noshade" /></td></tr>
{/if}
{/section}
