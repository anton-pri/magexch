{include_once file='js/cart_update.tpl'}

<form action="index.php?target={$current_target}" method="post" name="cartform" id="cartform">
<input type="hidden" name="action" value="place_order" />

 {include file='customer/cart/contents.tpl' use_ajax=false}
<div id="cart_totals">{include file="customer/cart/totals.tpl" need_shipping=true use_ajax=false}</div>

<center><input type="submit" value="{$lng.lbl_paypal_express_confirm}" /></center>
</form>
