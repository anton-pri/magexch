<table width="100%" style="margin-top: 12px;">
<tr>
<td width="30%" style="vertical-align: top;">

<div class="order_shipping" id="order_shipping_info">

<div class="adress_title">
<b>{$lng.lbl_shipping}</b>
</div>

<div id='tracking'>
{include file='addons/shipping_system/doc_tracking.tpl'}
</div>

{if $order.type eq 'S'}
<div id="doc_ship_date">
    <label>{$lng.lbl_ship_date}:&nbsp;</label> 
    {$order.ship_time}
</div>    
<div id="doc_shipping_insurance">
    <label>{$lng.lbl_insured_shipment}:&nbsp;</label>
    {if $info.shipping_insurance gt 0}{$lng.lbl_yes|upper}{else}{$lng.lbl_no|upper}{/if}
</div>
<div id="doc_shipment_weight">
    <label>{$lng.lbl_shipment_weight}:&nbsp;</label>
    {$info.weight|formatprice} {$config.General.weight_symbol|upper}
</div>
{if $info.shipping_insurance gt 0}
<div id="doc_shipment_insured_for">
    <label>{$lng.lbl_shipment_insured_for}:&nbsp;</label>
    {include file='common/currency.tpl' value=$info.total}
</div>
{/if}
<div class="doc_cash_on_delivery">
    <label>{$lng.lbl_cash_on_delivery}:&nbsp;</label>
    {if $info.cod_type_id}{$lng.lbl_yes|upper} ({$info.cod_type_label}){else}{$lng.lbl_no|upper}{/if}
</div>

<div class="doc_sales_manager" id="dsm_title_bottom">
    <label>{$lng.lbl_assigned_sales_manager}:&nbsp;</label>
    {$info.salesman_customer_id|user_title:'B'}
</div>
<div class="doc_total" id="dsm_total_red">
    {$lng.lbl_reference} {$lng.lbl_order_number} 
    {if $order.related_docs.O}
        {foreach from=$order.related_docs.O item=rel_doc}
        {$rel_doc.display_id} ({$rel_doc.date|date_format:$config.Appearance.date_format})&nbsp;
        {/foreach}
    {/if}
</div>

<div id="doc_forwarder_signature"><label>{$lng.lbl_forwarder_signature}:&nbsp;</label></div>

{else}

{*
<span id="doc_delivery_from_warehouse">
    <label>{$lng.lbl_to_delivery_from_warehouse}:&nbsp;</label>
    {$order.warehouse_info.company}<br />{$order.warehouse_info.country_name}
</span>
*}
    {if $order.type eq "I" || $order.type eq "R"}
<div id="doc_forwarder_assigned">
    <label>{$lng.lbl_forwarder_assigned}:&nbsp;</label>
    {if $info.carrier}{$info.carrier.carrier}{else}{$lng.lbl_none}{/if}
</div>
    {/if}
<div id="doc_shipment_weight">
    <label>{$lng.lbl_shipment_weight}:&nbsp;</label>
    <label>{$info.weight|formatprice} {$config.General.weight_symbol|upper}</label>
</div>
<div id="doc_cash_on_delivery">
    <label>{$lng.lbl_cash_on_delivery}:&nbsp;</label>
    {if $info.cod_type_id}{$lng.lbl_yes|upper} ({$info.cod_type_label}){else}{$lng.lbl_no|upper}{/if}
</div>



{if $info.applied_taxes}
<div id="doc_tax">
    {foreach key=tax_name item=tax from=$info.applied_taxes}
    <label>{$tax.tax_display_name}{if $tax.rate_type eq "%"} {$tax.rate_value}%{/if}:</label>
    {include file='common/currency.tpl' value=$tax.tax_cost}
    {/foreach}
</div>
{else}
<div id="doc_tax">
    <label>{$lng.lbl_vat}:&nbsp;</label>
    {include file='common/currency.tpl' value=0}
</div>
{/if}

{if $info.shipment_paid}
<div id="doc_lbl_shipment_paid">
    <label>{$lng.lbl_shipment_paid}:&nbsp;</label>
    {include file='main/select/shipping_paid.tpl' flat=1 value=$info.shipment_paid}
</div>
{/if}
{if $info.box_number}
<div id="doc_box_number">
    <label>{$lng.lbl_box_number}:&nbsp;</label>
    {$info.box_number}
</div>
{/if}
{if $info.pickup_date}
<div id="doc_pickup_date">
    <label>{$lng.lbl_pickup_date}:&nbsp;</label>
    {$info.pickup_date|date_format:$config.Appearance.date_format}
</div>
{/if}

{if $info.payment_surcharge ne 0}
<div id="doc_payment_surcharge">
    <label>{if $info.payment_surcharge gt 0}{$lng.lbl_payment_method_surcharge}{else}{$lng.lbl_payment_method_discount}{/if}:&nbsp;</label>
    {include file='common/currency.tpl' value=$info.payment_surcharge}
</div>
{/if}
{if $info.giftcert_discount gt 0}
<div id="doc_giftcert_discount">
    <label>{$lng.lbl_giftcert_discount}:&nbsp;</label>
    {include file='common/currency.tpl' value=$info.giftcert_discount}
</div>
{/if}

<div id="dsm_title_bottom">
    <label>{$lng.lbl_assigned_sales_manager}:&nbsp;</label>
    {$info.salesman_customer_id|user_title:'B'}
</div>

</div>

</td>


<td width="70%" style="vertical-align: top;">

<!-- cw@order_totals [ -->

<div class="order_shipping right">

<div class="adress_title">
<b>{$lng.lbl_totals}</b>
</div>

<div id="doc_subtotal">
    <label>{$lng.lbl_subtotal}:&nbsp;</label>
    {include file='common/currency.tpl' value=$info.display_subtotal}
</div>
    {if $info.discount gt 0}
<div id="doc_discount">
    <label>{$lng.lbl_discount}:&nbsp;</label>
    {include file='common/currency.tpl' value=$info.discount}
</div>
    {/if}
    {if $info.coupon and $info.coupon_type ne "free_ship"}
<div id="doc_coupon_saving">
    <label>{$lng.lbl_coupon_saving}:&nbsp;</label>
    {include file='common/currency.tpl' value=$info.coupon_discount}
</div>
    {/if}
    {if $info.discounted_subtotal ne $info.subtotal}
<div id="doc_discounted_subtotal">
    <label>{$lng.lbl_discounted_subtotal}:&nbsp;</label>
    {include file='common/currency.tpl' value=$info.display_discounted_subtotal}
</div>
    {/if}
    {if $config.Shipping.disable_shipping ne 'Y'}
<div id="doc_shipping_cost">
    <label>{$lng.lbl_shipping_cost}:&nbsp;</label>
    {include file='common/currency.tpl' value=$info.display_shipping_cost}
</div>
    {if $info.shipping_insurance}
<div id="doc_shipping_insurance">
    <label>{$lng.lbl_shipping_insurance}:&nbsp;</label>
    {include file='common/currency.tpl' value=$info.shipping_insurance}
</div>
    {/if}
{/if}
{if $info.coupon and $info.coupon_type eq "free_ship"}
<div id="doc_coupon_saving">
    <label>{$lng.lbl_coupon_saving}:&nbsp;</label>
    {include file='common/currency.tpl' value=$info.coupon_discount}
</div>
{/if}
<div id="dsm_total">
    <label>{$lng.lbl_total}:&nbsp;</label>
    {include file='common/currency.tpl' value=$info.total}
</div>

{if $info.tax_exempt_text}
<div id="doc_tax_exemption_applied">{$lng.txt_tax_exemption_applied}</div>
<div id="doc_tax_exempt_text">{$info.tax_exempt_text}</div>
{/if}
{/if}

{if $info.applied_giftcerts}
<div id="doc_applied_giftcerts_label">{$lng.lbl_applied_giftcerts}</div>
{foreach from=$info.applied_giftcerts item=gc}
<div id="doc_applied_giftcerts_{$gc.giftcert_id}" style="padding-left: 5px">
<label>{$gc.giftcert_id}:&nbsp;</label>
{include file='common/currency.tpl' value=$gc.giftcert_cost}
</div>
{/foreach}
{/if}


</div>
<!-- cw@order_totals ] -->

{if $info.customer_notes ne ""}
<div class="doc_customer_notes" id="doc_customer_notes">
    <div class="notes_title" id="dcn_notes_title"><label>{$lng.lbl_customer_notes}</label></div>
    {$info.customer_notes}
</div>
{/if}


{*
<div class="order_sign">

<div class="doc_signature" id="doc_recipient_signature">
    {$lng.lbl_recipient_signature}
</div>
<div class="doc_signature" id="doc_driver_signature">
    {$lng.lbl_driver_signature}
</div>
<div class="doc_signature" id="doc_shipping_company_signature">
    {$lng.lbl_shipping_company_signature}
</div>
</div>
*}
</td>
</tr>
</table>
