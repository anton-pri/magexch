{include_once file='addons/shipping_system/customer/products/estimate_js.tpl'}
<li class="product_field{cycle values=", cycle"}">
    <div class="field-title"><label>{$lng.lbl_ships_in}</label>

    </div>

    <div>
            {if $product.free_shipping eq 'Y'}{$lng.lbl_free_shipping_note}<br />{/if}
          {*  <a href="javascript:open_shipping_estimate('{$product.product_id}')">{$lng.lbl_estimate_ship_note}</a>*}
    </div>

</li>
