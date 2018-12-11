<div class="product_qty">
    <label>{$lng.lbl_quantity}{if $product.min_amount gt 1}{$lng.txt_need_min_amount|substitute:"items":$product.min_amount}{/if}</label>
    {include file='customer/products/product_amount.tpl'}
</div>