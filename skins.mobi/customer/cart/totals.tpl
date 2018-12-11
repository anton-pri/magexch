<div class="cart_totals">
{assign var="subtotal" value=$cart.subtotal}
{assign var="discounted_subtotal" value=$cart.discounted_subtotal}
{assign var="shipping_cost" value=$cart.display_shipping_cost}

<div class="input_field_1">
    <label>{$lng.lbl_subtotal}</label>
    {include file='common/currency.tpl' value=$cart.info.display_subtotal}
    {include file='common/alter_currency_value.tpl' alter_currency_value=$cart.info.display_subtotal}
</div>

{if $cart.info.discount gt 0}
<div class="input_field_1">
	<label>{$lng.lbl_discount}</label>
    {include file='common/currency.tpl' value=$cart.info.discount}
    {include file='common/alter_currency_value.tpl' alter_currency_value=$cart.info.discount}
</div>
{/if}

{if $cart.info.coupon_discount ne 0 and $cart.info.coupon_type ne "free_ship"}
<div class="input_field_1">
	<label>
        {$lng.lbl_discount_coupon} 
        <a href="index.php?target=cart&amp;action=unset_coupons" alt="{$lng.lbl_unset_coupon|escape}"><img src="{$ImagesDir}/clear.gif" width="11" height="11" border="0" valign="top" alt="{$lng.lbl_unset_coupon|escape}" /></a>
    </label>
    {include file='common/currency.tpl' value=$cart.info.coupon_discount}
    {include file='common/alter_currency_value.tpl' alter_currency_value=$cart.info.coupon_discount}
</div>
{/if}

{if $cart.info.display_discounted_subtotal ne $cart.info.display_subtotal}
<div class="input_field_1">
	<label>{$lng.lbl_discounted_subtotal}</label>
    {include file='common/currency.tpl' value=$cart.info.display_discounted_subtotal}
    {include file='common/alter_currency_value.tpl' alter_currency_value=$cart.info.display_discounted_subtotal}
</div>
{/if}

<div class="input_field_1">
	<label>{$lng.lbl_shipping_cost}
    {if $cart.info.coupon_discount ne 0 and $cart.info.coupon_type eq "free_ship"} ({$lng.lbl_discounted} <a href="index.php?target=cart&amp;mode=unset_coupons" alt="{$lng.lbl_unset_coupon|escape}"><img src="{$ImagesDir}/clear.gif" width="11" height="11" border="0" valign="top" alt="{$lng.lbl_unset_coupon|escape}" /></a>){/if}
    </label>
    {if $customer_id or $config.General.apply_default_country eq "Y" or $cart.info.display_shipping_cost gt 0}
        {include file='common/currency.tpl' value=$cart.info.display_shipping_cost}
        {include file='common/alter_currency_value.tpl' alter_currency_value=$cart.info.display_shipping_cost}
    {else}
        {$lng.txt_not_available_value}
    {/if}
</div>

{if $cart.info.shipping_insurance gt 0}
<div class="input_field_1">
	<label>{$lng.lbl_shipping_insurance}</label>
    {include file='common/currency.tpl' value=$cart.info.shipping_insurance}
    {include file='common/alter_currency_value.tpl' alter_currency_value=$cart.info.shipping_insurance}
</div>
{/if}

{if $cart.info.payment_surcharge gt 0}
<div class="input_field_1">
	<label>{if $cart.info.payment_surcharge gt 0}{$lng.lbl_payment_method_surcharge|escape:"hexentity"}{else}{$lng.lbl_payment_method_discount|escape:"hexentity"}{/if}</label>
    {include file='common/currency.tpl' value=$cart.info.payment_surcharge}
    {include file='common/alter_currency_value.tpl' alter_currency_value=$cart.info.payment_surcharge}
</div>
{/if}

{if $cart.info.taxes and $config.Taxes.display_taxed_order_totals ne "Y"}
{foreach key=tax_name item=tax from=$cart.info.taxes}
<div class="input_field_1">
	<label>{$tax.tax_display_name}{if $tax.rate_type eq "%"} {$tax.rate_value}%{/if}</label>
    {if $customer_id or $config.General.apply_default_country eq "Y"}{include file='common/currency.tpl' value=$tax.tax_cost}
    {include file='common/alter_currency_value.tpl' alter_currency_value=$tax.tax_cost}{else}{$lng.txt_not_available_value}{assign var="not_logged_message" value="1"}{/if}
</div>
{/foreach}
{/if}

{if $cart.info.applied_giftcerts}
<div class="input_field_1">
	<label>{$lng.lbl_giftcert_discount}</label>
    {include file='common/currency.tpl' value=$cart.info.giftcert_discount}
    {include file='common/alter_currency_value.tpl' alter_currency_value=$cart.info.giftcert_discount}
</div>
{/if}

{if !$small_format}
<div class="input_field_1">
    <label>{$lng.lbl_cart_total}</label>
    {include file='common/currency.tpl' value=$cart.info.total}
    {include file='common/alter_currency_value.tpl' alter_currency_value=$cart.info.total}
</div>

{if $cart.info.taxes and $config.Taxes.display_taxed_order_totals eq "Y"}
<div class="input_field_1">
    <label>{$lng.lbl_including}</label>
</div>

{foreach key=tax_name item=tax from=$cart.info.taxes}
<div class="input_field_1">
    <label>{$tax.tax_display_name}</label>
    {include file='common/currency.tpl' value=$tax.tax_cost}
    {include file='common/alter_currency_value.tpl' alter_currency_value=$tax.tax_cost}
</div>
{/foreach}

{elseif !$cart.info.taxes}
<div class="input_field_1">
    <label>{$lng.lbl_vat}</label>
    {include file='common/currency.tpl' value=0}
    {include file='common/alter_currency_value.tpl' alter_currency_value=0}
</div>
{/if}

{else}
    {include file="buttons/update.tpl" href="javascript: cw_submit_form('cartform')"}
{/if}

{if !$small_format}
{if $cart.info.applied_giftcerts}
<font class="FormButton">{$lng.lbl_applied_giftcerts}:</font>
<br />
{foreach from=$cart.info.applied_giftcerts item=gc}
{$gc.giftcert_id} <a href="index.php?target=cart&amp;mode=unset_gc&amp;gc_id={$gc.giftcert_id}{if $smarty.get.payment_id}&amp;payment_id={$smarty.get.payment_id}{/if}"><img src="{$ImagesDir}/clear.gif" width="11" height="11" border="0" valign="top" alt="{$lng.lbl_unset_gc|escape}" /></a> : <font class="ProductPriceSmall">{include file='common/currency.tpl' value=$gc.giftcert_cost}</font><br />
{/foreach}
{/if}

{if $not_logged_message eq "1"}{$lng.txt_order_total_msg}{/if}

{if !$no_form_fields}
<input type="hidden" name="payment_id" value="{$smarty.get.payment_id|escape:"html"}" />
{/if}
{/if}
</div>
<div class="clear"></div>
