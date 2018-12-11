<div class="products_compact" id="product_list">
{if $products}
<table class="header" width="100%">
<tr>
    <th>&nbsp;</th>
    <th style="text-align:left;">{$lng.lbl_product_name}</th>

{if $config.Appearance.display_productcode_in_list eq 'Y'}

    <th width="60">{$lng.lbl_part_number}</th>

{/if}

    <th width="50">{$lng.lbl_price}</th>
    <th width="40">{$lng.lbl_stock}</th>
    <th width="40" >{$lng.lbl_add}</th>
</tr>
<tbody class="product_info">
{foreach from=$products item=product}
<tr{*cycle values=", class='cycle'"*} class="item">
    <td>
<div class="image">
{if $config.Appearance.show_thumbnails ne 'N'} 
<a href="{pages_url var="product" product_id=$product.product_id cat=$cat page=$navigation_page featured=$featured}">{include file='common/product_image.tpl' image=$product.image_thumb product_id=$product.product_id html_height=$config.Appearance.products_images_thumb_height no_img_id='Y'}
</a>
{/if}
</div>
    </td>
    <td>
<a href="{pages_url var="product" product_id=$product.product_id cat=$cat page=$navigation_page featured=$featured}" class="product">{$product.product}</a>

{if $product.manufacturer}{$lng.lbl_brand}: {$product.manufacturer}{/if}
    </td>

{if $config.Appearance.display_productcode_in_list eq 'Y'}
    <td><div class="product_code" align="center">{$product.productcode}</div></td>
{/if}
    <td align="center">
    {if $product.display_price gt 0}
<div class="price">
    {include file='common/currency.tpl' value=$product.display_price}
    {include file='common/alter_currency_value.tpl' alter_currency_value=$product.display_price}
</div>
    {else}
        <div class="price"><label>{$lng.lbl_enter_your_price}</label></div>
    {/if}
    </td>
    <td align="center">{$product.avail}</td>
<td nowrap align="center">
<div class="compact_add">
{if $usertype eq "C" and $config.Appearance.buynow_button_enabled eq "Y"}
{include file='customer/main/buy_now.tpl' product=$product}
{/if}
</div>
	</td>
</tr>
{/foreach}
</tbody>
</table>

{else}
{$lng.txt_no_products_found}
{/if}
</div>
