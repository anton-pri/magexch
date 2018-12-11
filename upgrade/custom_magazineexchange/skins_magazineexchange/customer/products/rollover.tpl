{tunnel func='magexch_get_attribute_value' via='cw_call' param1='P' param2=$product.product_id param3='magexch_product_rollover_image' assign='magexch_product_rollover_image'}


        {if $magexch_product_rollover_image}
        <div class="rollover-image"><a href="{pages_url var="product" product_id=$product.product_id cat=$cat page=$navigation_page}"><img src="{$magexch_product_rollover_image}" alt=""></a></div>
        {/if}
