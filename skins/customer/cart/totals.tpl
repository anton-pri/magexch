<table class="cart_totals">
{assign var="subtotal" value=$cart.subtotal}
{assign var="discounted_subtotal" value=$cart.discounted_subtotal}
{assign var="shipping_cost" value=$cart.info.display_shipping_cost}
<!-- cw@subtotal [ -->
<tr>
    <td>{$lng.lbl_subtotal}</td>
    <td class="total_right">
      {include file='common/currency.tpl' value=$cart.info.display_subtotal}
      {include file='common/alter_currency_value.tpl' alter_currency_value=$cart.info.display_subtotal}
    </td>
</tr>
<!-- cw@subtotal ] -->

<!-- cw@discount [ -->
{if $cart.info.discount gt 0}
<tr>
    <td>{$lng.lbl_discount}</td>
    <td class="total_right">
      {include file='common/currency.tpl' value=$cart.info.discount}
      {include file='common/alter_currency_value.tpl' alter_currency_value=$cart.info.discount}
    </td>
</tr>
{/if}

{if $cart.info.coupon_discount ne 0 and $cart.info.coupon_type ne "free_ship"}
<tr>
    <td>
        {$lng.lbl_discount_coupon} 
        <a href="index.php?target=cart&amp;action=unset_coupons" alt="{$lng.lbl_unset_coupon|escape}"><img src="{$ImagesDir}/clear.gif" width="11" height="11" border="0" valign="top" alt="{$lng.lbl_unset_coupon|escape}" /></a>
    </td>
    <td class="total_right">
      {include file='common/currency.tpl' value=$cart.info.coupon_discount}
      {include file='common/alter_currency_value.tpl' alter_currency_value=$cart.info.coupon_discount}
    </td>
</tr>
{/if}

{if $cart.info.discount &&  $cart.info.display_discounted_subtotal ne $cart.info.display_subtotal}
<tr>
    <td>{$lng.lbl_discounted_subtotal}</td>
    <td class="total_right">
      {include file='common/currency.tpl' value=$cart.info.display_discounted_subtotal}
      {include file='common/alter_currency_value.tpl' alter_currency_value=$cart.info.display_discounted_subtotal}
    </td>
</tr>
{/if}
<!-- cw@discount ] -->

<!-- cw@shipping [ -->
{if $addons.shipping_system}
<tr>
    <td>{$lng.lbl_shipping_cost}
    {if $cart.info.coupon_discount ne 0 and $cart.info.coupon_type eq "free_ship"} ({$lng.lbl_discounted} <a href="index.php?target=cart&amp;mode=unset_coupons" alt="{$lng.lbl_unset_coupon|escape}"><img src="{$ImagesDir}/clear.gif" width="11" height="11" border="0" valign="top" alt="{$lng.lbl_unset_coupon|escape}" /></a>){/if}
    </td>
    <td class="total_right">
    {if $customer_id or $config.General.apply_default_country eq "Y" or $shipping_cost gt 0}
        {include file='common/currency.tpl' value=$shipping_cost}
        {include file='common/alter_currency_value.tpl' alter_currency_value=$shipping_cost}
    {else}
        {$lng.txt_not_available_value}
    {/if}
    </td>
</tr>
{/if}

{if $cart.info.shipping_insurance gt 0}
<tr>
    <td>{$lng.lbl_shipping_insurance}</td>
    <td class="total_right">
      {include file='common/currency.tpl' value=$cart.info.shipping_insurance}
      {include file='common/alter_currency_value.tpl' alter_currency_value=$cart.info.shipping_insurance}
    </td>
</tr>
{/if}
<!-- cw@shipping ] -->

{if $cart.info.payment_surcharge gt 0}
<tr>
    <td>{if $cart.info.payment_surcharge gt 0}{$lng.lbl_payment_method_surcharge|escape:"hexentity"}{else}{$lng.lbl_payment_method_discount|escape:"hexentity"}{/if}</td>
    <td class="total_right">
      {include file='common/currency.tpl' value=$cart.info.payment_surcharge}
      {include file='common/alter_currency_value.tpl' alter_currency_value=$cart.info.payment_surcharge}
    </td>
</tr>
{/if}

<!-- cw@tax [ -->
{if $cart.info.taxes and $config.Taxes.display_taxed_order_totals ne "Y"}
{foreach key=tax_name item=tax from=$cart.info.taxes}
<tr>
    <td>{$tax.tax_display_name}{if $tax.rate_type eq "%"} {$tax.rate_value}%{/if}</td>
    <td class="total_right">
      {if $customer_id or $config.General.apply_default_country eq "Y"}{include file='common/currency.tpl' value=$tax.tax_cost}
      {include file='common/alter_currency_value.tpl' alter_currency_value=$tax.tax_cost}{else}{$lng.txt_not_available_value}{assign var="not_logged_message" value="1"}{/if}
    </td>
</tr>
{/foreach}
{/if}
<!-- cw@tax ] -->

<!-- cw@gift [ -->

{if $cart.info.applied_giftcerts}
<tr>
    <td>{$lng.lbl_giftcert_discount}</td>
    <td class="total_right">
      {include file='common/currency.tpl' value=$cart.info.giftcert_discount}
      {include file='common/alter_currency_value.tpl' alter_currency_value=$cart.info.giftcert_discount}
    </td>
</tr>
{/if}
<!-- cw@gift ] -->

<!-- cw@total [ -->
{if !$small_format}
<tr class="total_sum">
    <td>{$lng.lbl_cart_total}</td>
    <td class="total_value total_right">
      {include file='common/currency.tpl' value=$cart.info.total}
      {include file='common/alter_currency_value.tpl' alter_currency_value=$cart.info.total}
    </td>
</tr>
<!-- cw@total ] -->

<!-- cw@vat [ -->

{if $cart.info.taxes and $config.Taxes.display_taxed_order_totals eq "Y"}
<tr>
    <td colspan="2" class="total_right vat">{$lng.lbl_including}:</td>
</tr>

{foreach key=tax_name item=tax from=$cart.info.taxes}
<tr>
    <td>{$tax.tax_display_name}</td>
    <td class="total_right">
      {include file='common/currency.tpl' value=$tax.tax_cost}
      {include file='common/alter_currency_value.tpl' alter_currency_value=$tax.tax_cost}
    </td>
</tr>
{/foreach}

{elseif !$cart.info.taxes}
<tr>
    <td>{$lng.lbl_vat}</td>
    <td class="total_right">
      {include file='common/currency.tpl' value=0}
      {include file='common/alter_currency_value.tpl' alter_currency_value=0}
    </td>
</tr>
{/if}
<!-- cw@vat ] -->

{else}
<tr>
    <td colspan="2" class="total_right">
    {include file="buttons/update.tpl" href="javascript: cw_submit_form('cartform')"}
    </td>
</tr>
{/if}

{if !$small_format}
{if $cart.info.applied_giftcerts}
<tr>
  <td><font class="FormButton">{$lng.lbl_applied_giftcerts}:</font></td>
  <td class="total_right">
    {foreach from=$cart.info.applied_giftcerts item=gc}
    {$gc.giftcert_id} <a href="index.php?target=cart&amp;mode=unset_gc&amp;gc_id={$gc.giftcert_id}{if $smarty.get.payment_id}&amp;payment_id={$smarty.get.payment_id}{/if}"><img src="{$ImagesDir}/clear.gif" width="11" height="11" border="0" valign="top" alt="{$lng.lbl_unset_gc|escape}" /></a> : <font class="ProductPriceSmall">{include file='common/currency.tpl' value=$gc.giftcert_cost}</font><br />
    {/foreach}
  </td>
</table>
{/if}

{if $not_logged_message eq "1"}{$lng.txt_order_total_msg}{/if}

{if !$no_form_fields}
<input type="hidden" name="payment_id" value="{$smarty.get.payment_id|escape:"html"}" />
{/if}
{/if}
</table>
