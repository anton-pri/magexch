<div class="products_compact">
{if $products}
<table class="header" width="100%">
<tr>
    {*<th>&nbsp;</th>*}
    <th>{$lng.lbl_product_name}</th>
{if $config.Appearance.display_productcode_in_list eq 'Y'}
   {* <th width="60">{$lng.lbl_part}</th> *}
{/if}
   {* <th>Free </th> *}
    <th width="50">{$lng.lbl_price}</th>
   {* <th width="40">{$lng.lbl_stock}</th>*}
    <th width="40" >{$lng.lbl_add}</th>
</tr>
<tbody class="product_info">
{foreach from=$products item=product}
<tr{cycle values=", class='cycle'"}>
{*
    <td>
{if $config.Appearance.show_thumbnails ne 'N'} 
<a href="{pages_url var="product" product_id=$product.product_id cat=$cat page=$navigation_page featured=$featured}">{include file='common/thumbnail.tpl' image=$product.image_small}</a>
{/if}
    </td>
*}
    <td>
<a href="{pages_url var="product" product_id=$product.product_id cat=$cat page=$navigation_page featured=$featured}" class="product">{$product.product}</a>
{if $config.Appearance.display_productcode_in_list eq 'Y'}#{$product.productcode}{/if}<br />
{if $product.manufacturer}{$lng.lbl_brand}: {$product.manufacturer}{/if}
    </td>

{*
    <td><div class="product_code" align="center"></div></td>


    <td>{include file='customer/products/free_shipping.tpl'}</td>

*}

    <td align="center">
    {if $addons.subscriptions and $product.catalogprice}
{include file='addons/subscriptions/subscription_info_inlist.tpl'}
    {elseif $product.display_price gt 0}
<div class="price">
    {include file='common/currency.tpl' value=$product.display_price}
    {include file='common/alter_currency_value.tpl' alter_currency_value=$product.display_price}
</div>
    {else}
        <div class="price"><label>{$lng.lbl_enter_your_price}</label></div>
    {/if}
    </td>
{*
    <td align="center">{if $product.rma_coupon ne 'Y'}{$product.avail}{else}&nbsp;{/if}</td>
*}
    <td nowrap align="right" class="buy_now_cell">
{if $product.product_type eq 3}
{capture name=product_url}{pages_url var="product" product_id=$product.product_id cat=$cat page=$navigation_page}{/capture}
{include file='buttons/details.tpl' href=$smarty.capture.product_url}
{else}
{if $usertype eq "C" and $config.Appearance.buynow_button_enabled eq "Y"}
{include file='customer/main/buy_now.tpl' product=$product}
{/if}
{/if}
	</td>
</tr>
{/foreach}
</tbody>
</table>

{else}
{$lng.txt_no_products_found}
{/if}
</div>
