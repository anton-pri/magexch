<div class="cart-container">


{include_once file='js/cart_update.tpl'}
{if $products}
{*if $cart ne ''}
    {$lng.txt_cart_header}
    {if $addons.estore_gift}{$lng.txt_cart_note}{/if}
{/if*}
{else}
{/if}
{capture name=dialog}
{*capture name=section*}

{if !$products}
<p class="empty">{$lng.txt_your_shopping_cart_is_empty}</p>
<div class="clear"></div>
{/if}
{*/capture*}
{*include file='common/section.tpl' title=$lng.lbl_items_in_cart content=$smarty.capture.section style='cart'*}

{if $products}
<form action="index.php?target={$current_target}" method="post" name="cartform" id="cartform">
<input type="hidden" name="action" value="update" />

{if $config.Appearance.show_cart_summary eq 'Y'}
{foreach from=$warehouses_cart item=tmp_cart}
<div class="cart_content">
{*
    <div class="warehouse">{$tmp_cart.warehouse_customer_id|user_title:'W'}</div>
*}
    {include file='customer/cart/content.tpl' products=$tmp_cart.products cart=tmp_cart use_ajax=false}

    <div id="cart_totals_{$tmp_cart.warehouse}" class="margin20">{include file="customer/cart/totals.tpl" shipping=$tmp_cart.shipping shipping_name="shipping_arr[`$tmp_cart.warehouse_customer_id`]" cart=$tmp_cart carrier_name="carrier_arr[`$tmp_cart.warehouse`]" use_ajax=false cart_warehouse=$tmp_cart.warehouse}</div>
</div>
{/foreach}

{if $enought_count}
<hr align="left" noshade size="1">
<div class="total">{$lng.lbl_grand_total}</div>
<div id="grand_total">{include file="customer/cart/totals.tpl" need_shipping=false use_ajax=false}</div>
{/if}
{else}
<div class="cart_content">
    {include file='customer/cart/content.tpl' use_ajax=false}
    <div id="cart_totals">{include file="customer/cart/totals.tpl" need_shipping=true use_ajax=false}</div>
</div>
{/if}

{if $products}

{include file='customer/cart/buttons.tpl'}

<div class="cart_butt">
{if !$from_quote}
{include file="buttons/update.tpl" href="javascript: cw_submit_form('cartform')"}
{/if}
{include file='buttons/button.tpl' button_title=$lng.lbl_clear_cart href="index.php?target=`$current_target`&action=clear_cart" style='button'}
<div class="clear"></div>
</div>
<div class="checkout_button">{include file='buttons/button.tpl' button_title=$lng.lbl_checkout style='btn' href="javascript: cw_submit_form('cartform', 'checkout')"}</div>

{/if}
</form>
{/if}
{/capture}
{include file='common/section.tpl' is_dialog=1 title=$lng.lbl_cart content=$smarty.capture.dialog }



{if $cart.coupon_discount eq 0 and $products}

    {if $addons.recommended_products}
{capture name=prod}
{include file='addons/recommended_products/recommends.tpl'}
{/capture}
{include file='common/section.tpl' is_dialog=1 content=$smarty.capture.prod}
    {/if}


<div class="coupon">
    {if $addons.discount_coupons && $cart.info.coupon eq ''}

{include file='addons/discount_coupons/add_coupon.tpl'}

    {/if}
</div>

{/if}


</div>
