{if $is_label}{include file='common/subheader.tpl' title=$lng.lbl_shipping}{/if}
{if $config.Appearance.show_cart_summary eq 'Y'}
{foreach from=$warehouses_cart item=tmp_cart}
    {include file='addons/shipping_system/customer/cart/shipping.tpl' is_radio=1 onclick="cw_submit_form_ajax('cart_form', 'cw_one_step_checkout_payment');"  shipping_name="shipping_arr[`$tmp_cart.warehouse_customer_id`]" carrier_name="carrier_arr[`$tmp_cart.warehouse`]" shipping=$tmp_cart.shipping cart=$tmp_cart}
{/foreach}
{else}
    {include file='addons/shipping_system/customer/cart/shipping.tpl' is_radio=1 onclick="cw_submit_form_ajax('cart_form', 'cw_one_step_checkout_payment');"}
{/if}
