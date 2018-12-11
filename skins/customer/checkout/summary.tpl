<div id="opc_totals" class="cart-totals" style="position: relative;">
{assign var="subtotal" value=$cart.subtotal}
{assign var="discounted_subtotal" value=$cart.discounted_subtotal}
{* $cart.info.display_shipping_cost - shipping with taxes
    $cart.info.shipping_cost - shipping without taxes *}
{assign var="shipping_cost" value=$cart.info.display_shipping_cost}
{count assign='total_items_products' value=$cart.products}
{count assign='total_items_giftcerts' value=$cart.giftcerts}

    <table cellspacing="2" summary="Total" class="totals">
        <tbody>
<!-- cw@total_items_list [ -->
            <tr>
                <td class="total-name"><a title="Your cart" id="cart-contents-link" class="dotted toggle-link" href="javascript:cw_submit_form_ajax('show_cart_form', 'cw_one_step_checkout_cart')">{$total_items_products+$total_items_giftcerts|default:0} item(s):</a></td>
                <td class="total-value"><span class="currency">
                        {include file='common/currency.tpl' value=$cart.info.display_subtotal}
    {include file='common/alter_currency_value.tpl' alter_currency_value=$cart.info.display_subtotal}
                    </span></td>
            </tr>
<!-- cw@total_items_list ] -->

            {if $cart.info.discount gt 0}
            <tr>
                <td class="total-name"><label>{$lng.lbl_discount}</label>:</td>
                <td class="total-value"><span class="currency">
                        {include file='common/currency.tpl' value=$cart.info.discount}
    {include file='common/alter_currency_value.tpl' alter_currency_value=$cart.info.discount}</span></td>
            </tr>
            {/if}

            {if $cart.info.coupon_discount ne 0 and $cart.info.coupon_type ne "free_ship"}
            <tr>
                <td class="total-name"><label>{$lng.lbl_discount_coupon}
                        <a href="index.php?target=cart&amp;action=unset_coupons&amp;mode=checkout" 
                           onclick="cw_submit_get_ajax('index.php?target=cart&amp;action=unset_coupons&amp;mode=checkout','cw_one_step_checkout_payment'); return false;" alt="{$lng.lbl_unset_coupon|escape}"><img src="{$ImagesDir}/clear.gif" width="11" height="11" border="0" valign="top" alt="{$lng.lbl_unset_coupon|escape}" /></a>
                    </label>:</td>
                <td class="total-value"><span class="currency">{include file='common/currency.tpl' value=$cart.info.coupon_discount}
    {include file='common/alter_currency_value.tpl' alter_currency_value=$cart.info.coupon_discount}
                    </span></td>
            </tr>
            {/if}

            {if $cart.info.discount && $cart.info.display_discounted_subtotal ne $cart.info.display_subtotal}
            <tr>
                <td class="total-name">
                    <label>{$lng.lbl_discounted_subtotal}:</label>
                </td>
                <td class="total-value">
                    <span class="currency">
    {include file='common/currency.tpl' value=$cart.info.display_discounted_subtotal}
    {include file='common/alter_currency_value.tpl' alter_currency_value=$cart.info.display_discounted_subtotal}
                    </span>
                </td>
            </tr>
            {/if}


            <tr>
                <td class="total-name">
                    <label>{$lng.lbl_shipping_cost}
                    {if $cart.info.coupon_discount ne 0 and $cart.info.coupon_type eq "free_ship"} ({$lng.lbl_discounted} <a href="index.php?target=cart&amp;mode=unset_coupons"
                                              onclick="cw_submit_get_ajax('index.php?target=cart&amp;action=unset_coupons&amp;mode=checkout','cw_one_step_checkout_payment'); return false;" alt="{$lng.lbl_unset_coupon|escape}"><img src="{$ImagesDir}/clear.gif" width="11" height="11" border="0" valign="top" alt="{$lng.lbl_unset_coupon|escape}" /></a>){/if}
                        :</label>
                </td>
                <td class="total-value">
                    <span class="currency">
    {if $customer_id or $config.General.apply_default_country eq "Y" or $shipping_cost gt 0}
        {include file='common/currency.tpl' value=$shipping_cost}
        {include file='common/alter_currency_value.tpl' alter_currency_value=$shipping_cost}
    {else}
        {$lng.txt_not_available_value}
    {/if}
                    </span>
                </td>
            </tr>

            {if $cart.info.shipping_insurance gt 0}
            <tr>
                <td class="total-name">
                    <label>{$lng.lbl_shipping_insurance}:</label>
                </td>
                <td class="total-value">
                    <span class="currency">
    {include file='common/currency.tpl' value=$cart.info.shipping_insurance}
    {include file='common/alter_currency_value.tpl' alter_currency_value=$cart.info.shipping_insurance}
                    </span>
                </td>
            </tr>
            {/if}

            {if $cart.info.payment_surcharge gt 0}
            <tr>
                <td class="total-name">
                    <label>{if $cart.info.payment_surcharge gt 0}{$lng.lbl_payment_method_surcharge|escape:"hexentity"}{else}{$lng.lbl_payment_method_discount|escape:"hexentity"}{/if}:</label>
                </td>
                <td class="total-value">
                    <span class="currency">
    {include file='common/currency.tpl' value=$cart.info.payment_surcharge}
    {include file='common/alter_currency_value.tpl' alter_currency_value=$cart.info.payment_surcharge}
                    </span>
                </td>
            </tr>
            {/if}

            {if $cart.info.taxes and $config.Taxes.display_taxed_order_totals ne "Y"}
            {foreach key=tax_name item=tax from=$cart.info.taxes}
            <tr>
                <td class="total-name">
                    <label>{$tax.tax_display_name}{if $tax.rate_type eq "%"} {$tax.rate_value}%{/if}:</label>
                </td>
                <td class="total-value">
                    <span class="currency">
    {if $customer_id or $config.General.apply_default_country eq "Y"}{include file='common/currency.tpl' value=$tax.tax_cost}
    {include file='common/alter_currency_value.tpl' alter_currency_value=$tax.tax_cost}{else}{$lng.txt_not_available_value}{assign var="not_logged_message" value="1"}{/if}

                    </span>
                </td>
            </tr>
            {/foreach}
            {/if}

            {if $cart.info.applied_giftcerts}
            <tr>
                <td class="total-name">
                	<a title="{$lng.lbl_giftcert_discount}" class="dotted toggle-link" href="javascript: void(0);" onclick="var el=$('#giftcert_discount_list');el.css('display')=='none'?el.css('display','block'):el.css('display','none');">{$lng.lbl_giftcert_discount}:</a>
                	<div id="giftcert_discount_list" style="display: none;padding-top: 3px;">
	                	{foreach from=$cart.info.applied_giftcerts item=gc}
	                		{$gc.giftcert_id} : {include file='common/currency.tpl' value=$gc.giftcert_cost}&nbsp;<a href="javascript: cw_submit_post_ajax('index.php?target=cart','mode=checkout&action=unset_gc&gc_id={$gc.giftcert_id}', 'cw_one_step_checkout_payment');void(0);"><img src="{$ImagesDir}/clear.gif" width="11" height="11" border="0" valign="top" alt="{$lng.lbl_unset_gc|escape}" /></a><br>
	                	{/foreach}
                	</div>
                </td>
                <td class="total-value">
                    <span class="currency">
    					{include file='common/currency.tpl' value=$cart.info.giftcert_discount}
    					{include file='common/alter_currency_value.tpl' alter_currency_value=$cart.info.giftcert_discount}
                    </span>
                </td>
            </tr>
            {/if}

            {if $cart.info.taxes and $config.Taxes.display_taxed_order_totals eq "Y"}
            <tr>
                <td colspan="2" class="total-name">
                    <label>{$lng.lbl_including}:</label>
                </td>
            </tr>
                {foreach key=tax_name item=tax from=$cart.info.taxes}
                <tr>
                    <td class="total-name">
                        <label>{$tax.tax_display_name}</label>
                    </td>
                    <td class="total-value">
                        <span class="currency">
                            {include file='common/currency.tpl' value=$tax.tax_cost}
                            {include file='common/alter_currency_value.tpl' alter_currency_value=$tax.tax_cost}
                        </span>
                    </td>
                </tr>
                {/foreach}
            {/if}

            {**
            <tr>
                <td class="total-name">
                    <label>:</label>
                </td>
                <td class="total-value">
                    <span class="currency">

                    </span>
                </td>
            </tr>
            **}


            <tr class="total">
                <td class="total-name">{$lng.lbl_cart_total}:</td>
                <td class="total-value nowrap">
                    <span class="currency">    {include file='common/currency.tpl' value=$cart.info.total}
                    {include file='common/alter_currency_value.tpl' alter_currency_value=$cart.info.total}</span>
                </td>
            </tr>
        </tbody>

</table>

</div>
