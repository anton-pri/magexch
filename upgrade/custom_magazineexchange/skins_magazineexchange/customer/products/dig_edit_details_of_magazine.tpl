    <span class="ProductDef">{$lng.lbl_details_of_magazine}:</span>
    <ul>
      <li>{$lng.lbl_number_of_pages}<span style="width: 20px;height: 15px;display:inline-block;float:right;"></span><span class="right">{if ($product.weight ne "0.00" || $variants ne '')}{$magexch_product_NUMBER_PAGES}   {/if}</span></li>
      <li>{$lng.lbl_shipping_weight_kg}<span style="width: 20px;height: 15px;display:inline-block;float:right;"></span><span class="right">0</span></li>
      <li>{$lng.lbl_shipping_cost} <span class="right question">{cms service_code="feature_not_activated" preload_popup="Y"}</span></li>
    </ul>
    <span class="ProductDef">{$lng.lbl_digital_editions_feedback}:</span>
      <ul>
        <li class="adb_text">{cms service_code='product_banner'}</li>
      </ul>
    <div class="sell_wrapper" style="margin-top: 3px"><a href="/cw/seller/index.php" class="ProductBlue">{$lng.lbl_sell_this_item}</a><span class="right question">{cms service_code="sell_popup" preload_popup="Y"}</span></div>
