{if $menu_accessories}
{capture name=menu}

<div class="side_line">
{foreach from=$menu_accessories item=product}
<div class="product_info">
    <a href="{pages_url var="product" product_id=$product.product_id}" class="image">{include file='common/thumbnail.tpl' image=$product.image_small}</a>
    <a href="{pages_url var="product" product_id=$product.product_id}" class="product">{$product.product}</a>
    <div class="price">{include file='common/currency.tpl' value=$product.display_price}</div>
</div>
<div class="clear"></div>
{/foreach}
</div>

{/capture}
{if $main eq 'welcome'}
{include file='common/menu.tpl' title=$lng.lbl_accessories content=$smarty.capture.menu style='accessories'}
{else}
{include file='common/menu.tpl' title=$lng.lbl_accessories_not_home content=$smarty.capture.menu style='accessories'}
{/if}
{/if}
