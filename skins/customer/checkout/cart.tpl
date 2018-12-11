<table width="100%" border="0">
{foreach from=$products item=product name='products'}
{if $product.hidden eq ""}
<tr>
    <td width="80">
        <a href="{pages_url var="product" product_id=$product.product_id}">{if $product.is_pimage eq 'W' }{assign var="imageid" value=$product.variantid}{else}{assign var="imageid" value=$product.productid}{/if}{include file="product_thumbnail.tpl" productid=$imageid image_x="70" product=$product.product tmbn_url=$product.pimage_url type=$product.is_pimage class="NoBorder" alt=$product.product|escape main='one_step_checkout'}</a>
    </td>
    <td valign="top" align="left">
        <a href="{pages_url var="product" product_id=$product.product_id}">{$product.product}</a><br/><br/>
{* kornev, TOFIX *}
    {if $product.product_options ne ""}
        {include file='addons/product_options/main/options/display.tpl' options=$product.product_options}
        <br />
    {/if}
    {assign var="price" value=$product.display_price}
    <div align="left">
            <font class="ProductPriceConverting">{include file="common/currency.tpl" value=$price} x {$product.amount} = </font><font class="ProductPrice">{math equation="price*amount" price=$price amount=$product.amount format="%.2f" assign=unformatted}{include file="common/currency.tpl" value=$unformatted}</font><font class="MarketPrice"> {include file="common/alter_currency_value.tpl" alter_currency_value=$unformatted}</font>
            {if $config.Taxes.display_taxed_order_totals eq "Y" and $product.taxes}<br />{include file="customer/main/taxed_price.tpl" taxes=$product.taxes}{/if}

        {if $product.onsale.onsaleid}
            <img src="{$ImagesDir}/sale_tag.gif" alt="{$lng.lbl_os_onsale}" width="28" height="20" align="top"><span style="color:#FF0000; font-weight:bold;">{$lng.lbl_os_onsale}</span>
        {/if}

    </div>
    </td>
</tr>
<tr><td colspan="2"><hr size="1" noshade="noshade" /></td></tr>
{/if}
{/foreach}
</table>
