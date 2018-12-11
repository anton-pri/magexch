{count assign='items_products' value=$cart.products}
<!-- cw@aa2c_left [ -->
<div class="poput_left">
<table class="add_to_cart_table">
  <tr>
    <th>{$lng.lbl_item}</th>
    <th>{$lng.lbl_price}</th>
    <th>{$lng.lbl_quantity}</th>
    <th>{$lng.lbl_subtotal}</th>

  </tr>
  <tr>

    <td><div class='product'>{$product.product}</div> </td>
    <td>
    <div class='prices'>
        {include file='common/currency.tpl' value=$cart.products[$product.productindex].display_price}
        {if $config.Taxes.display_taxed_order_totals eq "Y" and $product.taxes}
          {include file="customer/main/taxed_price.tpl" taxes=$product.taxes}
        {/if}
      </div>
    </td>
    <td><div class='qty'>{$product.added_amount}</div>  </td>

    <td style="text-align: right;">
        {math equation="x * y" x=$cart.products[$product.productindex].display_price y=$product.added_amount assign=prod_subtotal}
        {include file='common/currency.tpl' value=$prod_subtotal}

    </td>
  </tr>
</table>
</div>
<!-- cw@aa2c_left ] -->

<!-- cw@aa2c_right [ -->
<div class="poput_right">
  <div class="aa2c_total"><b>{$lng.lbl_total}:</b> <span>{include file='common/currency.tpl' value=$cart.info.display_subtotal}*</span></div>
  <div class="aa2c_comment">{$lng.lbl_aatc_comment}</div>

  <div class="buttons">
    <div class="continue">{include file='buttons/button.tpl' style='button' href="javascript: hm('add2cart_popup');" button_title=$lng.lbl_continue_shopping}</div>
    <div class="checkout">{include file='buttons/button.tpl' style='button' href='index.php?target=cart&mode=checkout' button_title=$lng.lbl_proceed_to_checkout}</div>
  </div>

</div>
<!-- cw@aa2c_right ] -->
<script>
var added_seller_item_id = '{$cart.products[$product.productindex].seller.seller_item_id}';
{literal}
$('#already_in_basket_'+added_seller_item_id).show();
$('#add2basket_'+added_seller_item_id).hide();
{/literal}
</script>
