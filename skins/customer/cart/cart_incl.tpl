<p />
{if $products ne ""}
<form action="index.php?target=cart" method="post" name="cartform">
<table width="100%" border="0">
{if $config.Appearance.show_cart_summary eq 'Y'}
{foreach from=$warehouses_cart item=tmp_cart}
<tr><td colspan="2"><hr align="left" noshade size="1"></td></tr>
<tr><td colspan="2" class="CartTitle">{$tmp_cart.warehouse_title}</td></tr>
    {include file="customer/cart/content.tpl" products=$tmp_cart.products wcart=$tmp_cart}
{if $enought_count}
<tr><td colspan="2" id="cart_totals_{$tmp_cart.warehouse_customer_id}">{include file="customer/main/cart_totals.tpl" shipping=$tmp_cart.shipping small_format=true shipping_name="shipping_arr[`$tmp_cart.warehouse_customer_id`]" cart=$tmp_cart carrier_name="carrier_arr[`$tmp_cart.warehouse_customer_id`]" current_carrier=$tmp_cart.current_carrier use_ajax=true}</td></tr>
{else}
<tr><td colspan="2" id="cart_totals_{$tmp_cart.warehouse_customer_id}">{include file="customer/main/cart_totals.tpl" shipping=$tmp_cart.shipping shipping_name="shipping_arr[`$tmp_cart.warehouse_customer_id`]" cart=$tmp_cart carrier_name="carrier_arr[`$tmp_cart.warehouse_customer_id`]" use_ajax=true}</td></tr>
{/if}
{/foreach}
{if $enought_count}
<tr><td colspan="2"><hr align="left" noshade size="1"></td></tr>
<tr><td colspan="2" class="CartTitle">{$lng.lbl_grand_total}</td</tr>
<tr><td colspan="2" id="grand_total">{include file="customer/main/cart_totals.tpl" need_shipping=false use_ajax=true}</td></tr>
{/if}
{else}
    {include file="customer/main/cart_content.tpl"}
    <tr><td colspan="2" id="cart_totals">{include file="customer/main/cart_totals.tpl" need_shipping=true use_ajax=true}</td></tr>
{/if}
</table>
{if $addons.estore_gift}
{include file='addons/estore_gift/gc_cart.tpl' giftcerts_data=$cart.giftcerts}
{/if}

<br />
<br />
<table cellpadding="0" cellspacing="0" width="100%">
<tr>
<td>
<table cellspacing="0" cellpadding="0">
<tr>
    <td class="ButtonsRow">{include file="buttons/update.tpl" href="javascript: cw_submit_form(document.cartform)" style='top'}</td>
    <td class="ButtonsRow">{include file='buttons/button.tpl' button_title=$lng.lbl_clear_cart href="index.php?target=cart&amp;mode=clear_cart" style='top'}</td>
</tr>
</table>
</td>
<td align="right">
<input type="hidden" name="redirect" value="cart" />
{include file='buttons/button.tpl' button_title=$lng.lbl_checkout_ href="javascript: cw_submit_form(document.cartform, 'checkout')"}
</td>
</tr>
</table>
<input type="hidden" name="action" value="checkout" />
</form>
{else}
{$lng.txt_your_shopping_cart_is_empty}
{/if}
