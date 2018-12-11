
        <li class="product_field{cycle values=", cycle"}">
            <div class="field-title">{$lng.lbl_sku}:</div>
            <div id="product_sku">{$product.productcode}</div>
        </li>

{if ($product.weight ne "0.00" || $variants ne '') && $product.rma_coupon ne 'Y'}
        <li class="product_field{cycle values=", cycle"}">
            <div class="field-title">{$lng.lbl_weight}:</div>
            <div id="product_weight">{$product.weight|formatprice} {$config.General.weight_symbol}</div>
        </li>
{/if}

