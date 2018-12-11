<table width="100%" style="max-width: 600px;">
<tr>
<td width="100%" style="vertical-align: top;">
<div class="order_shipping">



{if $order.type eq 'S'}
 


{if $info.shipping_insurance gt 0}
<div id="doc_shipment_insured_for">
    <label>{$lng.lbl_shipment_insured_for}:&nbsp;</label>
    {include file='common/currency.tpl' value=$info.total}
</div>
{/if}


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


    {if $order.type eq "I" || $order.type eq "R"}
<div id="doc_forwarder_assigned">
    <label>{$lng.lbl_forwarder_assigned}:&nbsp;</label>
    {if $info.carrier}{$info.carrier.carrier}{else}{$lng.lbl_none}{/if}
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
{if $info.aspect_id}
<div id="doc_aspect_id">
    <label>{$lng.lbl_products_aspects}:&nbsp;</label>
    {$info.aspect_id}
</div>
{/if}
{if $info.shipping_cause_id}
<div id="shipping_cause_id">
    <label>{$lng.lbl_shipping_cause}:&nbsp;</label>
    {$info.shipping_cause_id}
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



</div>
</td>
</tr>

<tr>

<td width="100%" style="vertical-align: top;">
<div class="order_shipping" style="text-align: right;">

<div id="doc_subtotal">
    <label>{$lng.lbl_transaction} {$lng.lbl_subtotal}:&nbsp;</label>
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
    <label>{$lng.lbl_transaction} {$lng.lbl_shipping_cost}:&nbsp;</label>
    {include file='common/currency.tpl' value=$info.display_shipping_cost}
</div>

{/if}
{if $info.coupon and $info.coupon_type eq "free_ship"}
<div id="doc_coupon_saving">
    <label>{$lng.lbl_coupon_saving}:&nbsp;</label>
    {include file='common/currency.tpl' value=$info.coupon_discount}
</div>
{/if}
<div id="dsm_total" style="background: none repeat scroll 0 0 #ccc; border-top: 2px solid #58595b; height: 25px; line-height: 22px;  padding-right: 3px; text-align: right;  width: 100%;">
    <label>{$lng.lbl_transaction} {$lng.lbl_total}:&nbsp;</label>
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

{if $info.customer_notes ne ""}
<div class="doc_customer_notes" id="doc_customer_notes">
    <div class="notes_title" id="dcn_notes_title">{$lng.lbl_customer_notes}</div>
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
*}
</div>
</td>
</tr>
</table>

<div style="text-align: center; margin: 20px 0; max-width: 600px;">{$lng.txt_thank_you_for_purchase}</div>
