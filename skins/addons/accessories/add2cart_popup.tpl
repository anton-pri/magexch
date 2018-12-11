<!-- this block added by accessories addon -->
{if $added_accessories}
<div class='recommended_products' id='added_accessories' comment='added_accessories'>
<h3>Also {$added_accessories|@count} accessories added to cart</h3>
    {include file='customer/products/products_gallery.tpl' products=$added_accessories usertype=''}
</div>
{/if}


{if $recommended_products}
<div class='recommended_products' id='recommended_products_a2c' comment='accessories'>
<h3>You may also like</h3>
    {include file='customer/products/products_gallery.tpl' products=$recommended_products}
</div>
{/if}
<!-- / this block added by accessories addon -->
