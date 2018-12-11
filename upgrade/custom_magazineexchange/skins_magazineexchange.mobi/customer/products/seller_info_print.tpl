
<div class="sell_table incomplete">
{if $product.avail eq 0}
  <div class="seller_block">
      <strong>{$lng.lbl_currently_no_sellers}</strong>
  </div>
{else}
  <div class="seller_block">
    <ul>
      <li><b>{$lng.lbl_seller_info}:</b> 
      <span class="SellerName"><a title="All Magazines" href="index.php?cat={$config.custom_magazineexchange.magexch_default_root_category}&vendorid={$seller_info.customer_id}" style="color:blue">{$seller_info.name}</a></span>
      </li>
      <li><b>{$lng.lbl_seller_descr}:</b> New stock direct from publisher</li>
      <li><b>{$lng.lbl_condition}:</b> New</li>
      <li><b>{$lng.lbl_price}:</b> <span style="WHITE-SPACE: nowrap">{include file='common/currency.tpl' value=$product.display_price plain_text_message=true}</span></li>
    </ul>
    <div class="seller_add_to_cart">
            <div class="hidden_qty">{include file='customer/products/product-amount.tpl'}</div>

            {include file='buttons/add_to_cart.tpl' button_title=$lng.lbl_add_to_basket style='btn' href="javascript: if(FormValidation()) cw_submit_form('order_form');"}
    </div>
  </div>
  <div class="seller_block">
    <ul>
      <li><b>{$lng.lbl_seller_info}:</b> 
      <span class="SellerName"><a title="All Magazines" href="index.php?cat={$config.custom_magazineexchange.magexch_default_root_category}&vendorid={$seller_info.customer_id}" style="color:blue">{$seller_info.name}</a></span>
      </li>
      <li><b>{$lng.lbl_seller_descr}:</b> New stock direct from publisher</li>
      <li><b>{$lng.lbl_condition}:</b> New</li>
      <li><b>{$lng.lbl_price}:</b> <span style="WHITE-SPACE: nowrap">{include file='common/currency.tpl' value=$product.display_price plain_text_message=true}</span></li>
    </ul>
    <div class="seller_add_to_cart">
            <div class="hidden_qty">{include file='customer/products/product-amount.tpl'}</div>

            {include file='buttons/add_to_cart.tpl' button_title=$lng.lbl_add_to_basket style='btn' href="javascript: if(FormValidation()) cw_submit_form('order_form');"}
    </div>
  </div>
{/if}	
</div>
