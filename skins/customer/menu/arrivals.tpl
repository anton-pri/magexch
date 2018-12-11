{if $menu_arrivals}
{capture name=menu}

<div class="accessories">
{foreach from=$menu_arrivals item=product}
<div class="product_info">
    <a href="{pages_url var='product' product_id=$product.product_id}" class="image">{include file='common/thumbnail.tpl' image=$product.image_small}</a>
    <a href="{pages_url var='product' product_id=$product.product_id}" class="product">{$product.product}</a>
    <div class="price">{include file='common/currency.tpl' value=$product.display_price}</div>
</div>
{/foreach}
</div>
{/capture}
{if !$current_category}
{include file='common/menu.tpl' title=$lng.lbl_new_arrivals content=$smarty.capture.menu }
{else}
{include file='common/menu.tpl' title="`$lng.lbl_new_in` `$current_category.category`" content=$smarty.capture.menu }
{/if}
{/if}
