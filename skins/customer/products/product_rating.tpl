    {if $addons.estore_products_review}
        <div class="bottom_rating">
            <div class="rating_stars"><span class="float-left">{$lng.lbl_rating}</span> {include file='addons/estore_products_review/product_rating.tpl' rating=$product.rating}</div>
            <div class="rating_text">{if $reviews|@count eq 0}{$lng.txt_no_reviews}. {$lng.lbl_write_rev}.{else}<a href="#write_rev" class="read_rev">{$lng.lbl_read_reviews} ({$reviews_navigation.total_items})</a>{*$lng.lbl_average_customer_rating|substitute:'rating':$product.rating|substitute:'total':$reviews_navigation.total_items*}{/if}</div>
            <div class="clear"></div>
        </div>
    {/if}