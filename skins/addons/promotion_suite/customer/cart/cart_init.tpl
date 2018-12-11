<script>
$(document).ready(function(){ldelim}

    var offered_product;
    var new_price;
    {foreach from=$products item=product}
    {if $product.promotion_suite}
        offered_product = $('.cart_content .product_info[cartid={$product.cartid}]');
        offered_product.addClass('ps-offered-product');
        new_price = $(offered_product).find('#cart_item_price_{$product.cartid}');
        old_price = $(new_price).clone();
        old_price.html({$product.promotion_suite.saved_taxed_price});
        new_price.before('<s>'+old_price.html()+'</s>&nbsp;');
        
    {/if}
    {/foreach}

{rdelim});
</script>
