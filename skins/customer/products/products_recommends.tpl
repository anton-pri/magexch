<div class="products_gallery">
{foreach from=$products item=product name='product'}
    <div class="product_info">
<a href="{pages_url var='product' product_id=$product.product_id cat=$cat page=$navigation_page}" class="thumbnail">
    {include file='common/product_image.tpl' image=$product.image_thumb product_id=$product.product_id}
</a>

{if $product.manufacturer}<div class="manufacturer">{$product.manufacturer}</div>{/if}
<a href="{pages_url var="product" product_id=$product.product_id cat=$cat page=$navigation_page}" class="product">{$product.product}</a>

<div class="price">
    {include file='common/currency.tpl' value=$product.display_price}
    {include file='common/alter_currency_value.tpl' alter_currency_value=$product.display_price}
</div>

{include file='customer/main/buy_now_list.tpl' product=$product with_amount=true}
    </div>
{/foreach}
</div>
<div class="clear"></div>
