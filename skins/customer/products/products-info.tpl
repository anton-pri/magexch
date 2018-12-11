{*if $product.manufacturer}<div class="manufacturer">{$product.manufacturer}</div>{/if*}
<h5 class="name"><a href="{if $current_area eq 'B'}index.php?target=product&amp;product_id={$product.product_id}{else}{pages_url var='product' product_id=$product.product_id cat=$cat page=$navigation_page featured=$featured}{/if}" class="product">{$product.product}</a></h5>

{if $config.Appearance.display_productcode_in_list eq "Y" and $product.productcode ne ""}
    <div class="product_code">{$lng.lbl_sku}: <span id="product_code_{$product.product_id}">{$product.productcode}</span></div>
{/if}
<p>{$product.descr|default:$product.fulldescr|truncate:200|strip_tags}</p>

        {if $product.avail gt 0}
          <div class="in_stock">{$lng.lbl_in_stock}</div>
        {else}
          <div class="out_of_stock">{$lng.lbl_out_of_stock}</div>
        {/if}
{*
{include file='addons/estore_products_review/product_rating.tpl' rating=$product.rating}


        {if $product.display_price gt 0}
        <div class="price">
            {if $product.list_price gt "0"}<div class="list_price"><label>{$lng.lbl_list_price}:</label> <span>{include file='common/currency.tpl' value=$product.list_price}</span></div>{/if}

            <div class="our_price">
                {include file='customer/products/our_price.tpl'}
            {include file='addons/shipping_system/customer/products/free-shipping.tpl'}
            </div>

        </div>
        {else}
            <div class="price"><label>{$lng.lbl_enter_your_price}</label></div>
        {/if}



{if $config.Appearance.show_views_on_product_page eq "Y"}
    <div><span style="color: #808080;">{$lng.lbl_number_of_views}:</span> {$product.views_stats}</div>
{/if}

*}