{if $product.free_shipping eq 'Y' && $product.rma_coupon ne 'Y'}
<span class="free_shipping_note">{$lng.lbl_and_free_shipping}</div>
{*
<img src="{$ImagesDir}/free_shipping.gif" width="31" height="18" alt="" />
{if $full}{$lng.lbl_free_shipping_product}{/if}
*}
{/if}
