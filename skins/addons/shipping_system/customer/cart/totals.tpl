<div class="shipping_estimator_link">
{if $main ne 'checkout' && $current_section_dir ne 'checkout'}
{include file='addons/shipping_system/customer/cart/shipping.tpl' onchange="ajax_update_cart();"}
{/if}
</div>