
{count assign='items_products' value=$cart.products}
<!-- cw@aa2c_left [ -->
<div class="poput_left">
  <h2>
    <i class="icon-ok"></i>
    {$lng.lbl_successfully_added}
  </h2>
  <div class="image">
{include file='common/product_image.tpl' image=$product.image_thumb product_id=$product.product_id id="cart_product_thumbnail_`$product.productindex`"}
  </div>
  <div class='product'>{$product.product}</div>
  <div class='qty'><b>{$lng.lbl_quantity}</b> {$product.added_amount}</div> 
  <div class='prices'>
    <b>{$lng.lbl_total}</b>
    {include file='common/currency.tpl' value=$cart.products[$product.productindex].display_price}
    {if $config.Taxes.display_taxed_order_totals eq "Y" and $product.taxes}
      {include file="customer/main/taxed_price.tpl" taxes=$product.taxes}
    {/if}
  </div>
</div>
<!-- cw@aa2c_left ] -->

<!-- cw@aa2c_right [ -->
<div class="poput_right">
  <h3>{$lng.lbl_there_are_x_items_in_cart|substitute:"items":$items_products}.</h3>
  <table>
    <tr><td align='left'><b>{$lng.lbl_subtotal}</b></td><td align='right'> <span>{include file='common/currency.tpl' value=$cart.info.display_subtotal}</span></td></tr>
  </table>
  <div class="buttons">
    <div class="continue">{include file='buttons/button.tpl' style='button' href="javascript: hm('add2cart_popup');" button_title=$lng.lbl_continue_shopping}</div>
<div class="continue">
    {include file='buttons/button.tpl' style='button' href="`$current_location`/index.php?target=cart" button_title=$lng.lbl_proceed_to_cart class='continue right_align'}
</div>
    {include file='buttons/button.tpl' style='button' href="`$current_location`/index.php?target=cart&mode=checkout" button_title=$lng.lbl_proceed_to_checkout}
  </div>

</div>
<!-- cw@aa2c_right ] -->
