    <span class="ProductDef">{$lng.lbl_details_of_magazine}:</span>
    <ul>
      <li>{$lng.lbl_number_of_pages}<span style="width: 20px;height: 15px;display:inline-block;float:right;"></span><span class="right">{if ($product.weight ne "0.00" || $variants ne '')}{$magexch_product_NUMBER_PAGES}   {/if}</span></li>

      <li>{$lng.lbl_shipping_weight_kg}<span style="width: 20px;height: 15px;display:inline-block;float:right;"></span><span class="right">{if ($product.weight ne "0.00" || $variants ne '')}{$product.weight|formatprice}   {/if}</span></li>
      <li>{$lng.lbl_shipping_cost}<span class="right question">{cms service_code="shipping_cost_popup" preload_popup="Y"}</span></li>
    </ul>
    <span class="ProductDef">{$lng.lbl_contents_listing}:</span> {$lng.lbl_see_below}
    <div class="wish_wrapper"><a href="/cw/wanted-list-enquiry-form.html?magazineWanted={$product.product}" class="ProductBlue">{$lng.lbl_add_to_wl}</a><span class="right question">{cms service_code="wanted_popup" preload_popup="Y"}</span></div>
    <div class="sell_wrapper"><a href="/cw/seller/index.php" class="ProductBlue">{$lng.lbl_sell_this_item}</a><span class="right question">{cms service_code="sell_popup" preload_popup="Y"}</span></div>
