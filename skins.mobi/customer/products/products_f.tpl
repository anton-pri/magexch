{*$products|@debug_print_var*}
{capture name=section}
{assign var='buy_now_postfix' value='featured'}
<div class="featured_products">
<ul id="product_slide">
<li>
{foreach from=$products item=product name='product'}
    <div class="product_info{if $config.special_sections.featured_products && $smarty.foreach.product.iteration%$config.special_sections.featured_products == 0} last{/if}">
<div class="white_bg">
{*xcm_thumb 
src_url=$image.tmbn_url 
width=190 
height=50
assign_url="image.tmbn_url" 
assign_x="image.image_x" 
assign_y="image.image_y"
*}
{xcm_thumb src_url=$product.image_thumb assign_url="product.image_thumb" assign_x="products.image_x" assign_y="products.image_y" width=100 height=100 keep_file_h2w="Y"}

<a href="{pages_url var='product' product_id=$product.product_id cat=$cat page=$navigation_page}" class="image">{include file='common/thumbnail.tpl' image=$product.image_thumb  height=$products.image_y width=$products.image_y}</a>

{if $product.manufacturer}<div class="manufacturer">{$product.manufacturer}</div>{/if}
<a href="{pages_url var='product' product_id=$product.product_id cat=$cat page=$navigation_page}" class="product">{$product.product|truncate:30}</a>
<div class="descr">{$product.descr|truncate:80:"...":true}</div>

{*
<div class="price">
    {include file='common/currency.tpl' value=$product.display_price}
    {include file='common/alter_currency_value.tpl' alter_currency_value=$product.display_price}
</div>

{if $product.product_type ne 10 && $usertype eq 'C' and $config.Appearance.buynow_button_enabled eq 'Y'}
{include file='customer/main/buy_now_list.tpl' product=$product with_amount=true}
{/if}

*}
</div>
    </div>

{if $smarty.foreach.product.iteration is div by 4}{if $smarty.foreach.product.iteration ne $smarty.foreach.product.total}</li><li>{/if}{/if}
{/foreach}
</li>
</ul>
<div class="clear"></div>
</div>
{/capture}
{include file='common/section.tpl' content=$smarty.capture.section title=$lng.lbl_best_sellers style='featured_products'}
