<script type="text/javascript">
    {if $userinfo.main_address.address_id eq $userinfo.current_address.address_id}
        {assign var='is_same' value='1'}
    {else}
        {assign var='is_same' value='0'}
    {/if}
    var is_same = '{$is_same}';
    var checkout_step = {if $customer_id && $userinfo.main_address.firstname neq '' && ($userinfo.current_address.firstname neq '' || $is_same)}2{else}1{/if};
    var stepVisibility = new Array();
    stepVisibility[1] = [];
    stepVisibility[2] = ['onestep_place', 'onestep_payment_methods', 'onestep_shipping_methods'];

    {literal}
    $(document).ready(function(){
        $('.opc-visibility-section').hide();
        if (stepVisibility[checkout_step] && stepVisibility[checkout_step].length > 0) {
            for (var sec in stepVisibility[checkout_step]) {
                var a = '#'+stepVisibility[checkout_step][sec]+'.opc-visibility-section';
                $(a).show();
            }
        }
            $('.shipping > .width50 > div').equalHeights();

    });
    {/literal}
</script>

<form action="index.php?target=cart&mode=checkout" method="post" name="show_cart_form" >
<input type="hidden" name="action" value="show_cart" />
</form>

<form action="index.php?target=cart" method="post" name="cart_form">
<input type="hidden" name="mode" value="checkout" />
<input type="hidden" name="action" value="update" />

<div class="opc-section">
<div class="osc-section" id="section-shipping-method">
  <div class="grey_opc shipping">

    <div class="width50 float-right">
    <div id='onestep_shipping_methods' class="methods checkout_block opc-visibility-section">
      <!-- cw@shipping_title [ --><h2>{$lng.lbl_shipping_method}</h2><!-- cw@shipping_title ] -->

      {* kornev, TOFIX think how to fix *}
      {include file="customer/checkout/shipping_methods.tpl" is_label=false}
    </div>
    </div>
 
    <div class="width50 float-left">
    <div class="summary checkout_block">
      <h2>{$lng.lbl_cart_totals}</h2>

      {include file="customer/checkout/summary.tpl" is_label=false}
    </div>
    </div>

  </div>

</div>
</div>


<div id='onestep_payment_methods' class="opc-section opc-visibility-section">
<div class="osc-section payment_methods">

<div class="checkout_block">
  <!-- cw@payment_title [ --><h2>{$lng.lbl_payment_method}</h2><!-- cw@payment_title ] -->
  <div class="grey_opc payment">
  <div id="section-osc-left">
  {if $payment_methods}
    <table cellpadding='5' cellspacing='0' class="payment_list">
  {foreach from=$payment_methods item=payment name=payment}
        <tr valign='top' {if $smarty.foreach.payment.iteration is div by 2}class="white"{/if}>
            <td style="width:15px; padding-left: 35px; padding-right: 20px;">
            <input type="radio" id='pm{$payment.payment_id}' name="payment_id" value="{$payment.payment_id}"{if $payment.payment_id eq $cart.info.payment_id} checked="checked"{/if} onclick="javascript: cw_submit_form_ajax('cart_form', 'cw_one_step_checkout_payment')" />
            </td>
            <td class="title">
            <label for="pm{$payment.payment_id}">{$payment.title}</label>
            </td>
            <td>
            {$payment.descr}
            </td>
  </tr>
  {/foreach}
  </table>
  {else}
    {$lng.lng_no_payment_methods}
  {/if}
</div>

</div>

<div class="one_payment">
  <div id="onestep_payment">
    <div class="float-left">{include file='customer/checkout/payment.tpl'}</div>
  </div>
</div>

</div>
</div>
</div>
</form>


<div class="osc-section">

    {if $customer_id}
        {$lng.lbl_cart_total_note_reg}
    {else}
        {$lng.lbl_cart_total_note_anon}
    {/if}

    {if $addons.discount_coupons && $cart.info.coupon eq ''}
    <div class="coupon checkout_block" style="min-height: 10px;">
        <h2>{$lng.lbl_redeem_discount_coupon}</h2>
        <form action="index.php?target=cart" name="coupon_form" method="post">
        <input type="hidden" name="mode" value="checkout" />
        <input type="hidden" name="action" value="add_coupon" />
        <div>
             <p>{$lng.txt_add_coupon_header}</p>
          <div class="coupon_form">
            <label>
                <span style="float: left;">{$lng.lbl_coupon_code}:</span>
                <input type="text" size="20" name="coupon" />
            </label>
            <a class="image-button" href="javascript: cw_submit_form_ajax('coupon_form','cw_one_step_checkout_payment');void(0);"></a>
            {*include file="buttons/submit.tpl" href="javascript: cw_submit_form_ajax('coupon_form','cw_one_step_checkout_payment');"*}
          </div>
        </div>

        </form>
        <div class="clear"></div>
    </div>
    {/if}

</div>
