<div class="usually_ship">
    <label>{$lng.lbl_ships_in|substitute:'delivery_time':$product.supplier.delivery_time}</label>
	<a onclick="return show_shipping_estimate_dialog('{$product.product_id}')" href="">{$lng.lbl_estimate_ship_note}</a>
</div>
