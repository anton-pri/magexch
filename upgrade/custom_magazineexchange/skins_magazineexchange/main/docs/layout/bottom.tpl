<div class="row">

  <div class="col-md-6">

    <p class="h2 font-w400 push-5">
      {$lng.lbl_shipping}:
    </p>

<!--    <div id='tracking'>
      {include file='addons/shipping_system/doc_tracking.tpl'}
    </div> -->

    {if $order.type eq 'S'}
    <div id="doc_ship_date">
      <strong>{$lng.lbl_ship_date}:&nbsp;</strong> 
      {$order.ship_time}
    </div>    
      <!--   <div id="doc_shipping_insurance">
      <strong>{$lng.lbl_insured_shipment}:&nbsp;</strong>
       {if $info.shipping_insurance gt 0}{$lng.lbl_yes|upper}{else}{$lng.lbl_no|upper}{/if}
    </div> -->
    <div id="doc_shipment_weight">
      <strong>{$lng.lbl_shipment_weight}:&nbsp;</strong>
      {$info.weight|formatprice} {$config.General.weight_symbol|upper}
    </div>
    {if $info.shipping_insurance gt 0}
    <div id="doc_shipment_insured_for">
      <strong>{$lng.lbl_shipment_insured_for}:&nbsp;</strong>
      {include file='common/currency.tpl' value=$info.total}
    </div>
    {/if}
{* <div class="doc_cash_on_delivery">
      <strong>{$lng.lbl_cash_on_delivery}:&nbsp;</strong>
      {if $info.cod_type_id}{$lng.lbl_yes|upper} ({$info.cod_type_label}){else}{$lng.lbl_no|upper}{/if}
    </div> *}

    <div class="doc_sales_manager" id="dsm_title_bottom">
      <strong>{$lng.lbl_assigned_sales_manager}:&nbsp;</strong>
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

    <div id="doc_forwarder_signature"><strong>{$lng.lbl_forwarder_signature}:&nbsp;</strong></div>

    {else}

{*
<span id="doc_delivery_from_warehouse">
    <strong>{$lng.lbl_to_delivery_from_warehouse}:&nbsp;</strong>
    {$order.warehouse_info.company}<br />{$order.warehouse_info.country_name}
</span>
*}
      {if $order.type eq "I" || $order.type eq "R"}
      <div id="doc_forwarder_assigned">
        <strong>{$lng.lbl_forwarder_assigned}:&nbsp;</strong>
        {if $info.carrier}{$info.carrier.carrier}{else}{$lng.lbl_none}{/if}
      </div>
      {/if}

      <div id="dig_line_delivery">
        <strong>{$lng.lbl_delivery}:&nbsp;</strong>
        {$info.shipping_label|trademark|default:$lng.txt_not_available}
      </div>


      <div id="doc_shipment_weight">
        <strong>{$lng.lbl_shipment_weight}:&nbsp;</strong>
        {$info.weight|formatprice}{$config.General.weight_symbol|lower}
      </div>
  <!--  <div>
        <strong>{$lng.lbl_cash_on_delivery}:&nbsp;</strong>
    {include file='main/docs/extras.tpl' extras=$order.extras order=$order orders_list="Y"}</div>



      <div id="doc_cash_on_delivery">
        <strong>{$lng.lbl_cash_on_delivery}:&nbsp;</strong>
       {if $info.cod_type_id}{$lng.lbl_yes|upper} ({$info.cod_type_label}){else}{$lng.lbl_no|upper}{/if}
      </div>



      {if $info.applied_taxes}
      <div id="doc_tax">
        {foreach key=tax_name item=tax from=$info.applied_taxes}
          <strong>{$tax.tax_display_name}{if $tax.rate_type eq "%"} {$tax.rate_value}%{/if}:</strong>
          {include file='common/currency.tpl' value=$tax.tax_cost}
        {/foreach}
      </div>
      {else}
      <div id="doc_tax">
        <strong>{$lng.lbl_vat}:&nbsp;</strong>
        {include file='common/currency.tpl' value=0}
      </div>
      {/if} -->

      {if $info.shipment_paid}
      <div id="doc_lbl_shipment_paid">
        <strong>{$lng.lbl_shipment_paid}:&nbsp;</strong>
        {include file='main/select/shipping_paid.tpl' flat=1 value=$info.shipment_paid}
      </div>
      {/if}
      {if $info.box_number}
      <div id="doc_box_number">
        <strong>{$lng.lbl_box_number}:&nbsp;</strong>
        {$info.box_number}
      </div>
      {/if}
      {if $info.pickup_date}
      <div id="doc_pickup_date">
        <strong>{$lng.lbl_pickup_date}:&nbsp;</strong>
        {$info.pickup_date|date_format:$config.Appearance.date_format}
      </div>
      {/if}
      {if $info.aspect_id}
      <div id="doc_aspect_id">
        <strong>{$lng.lbl_products_aspects}:&nbsp;</strong>
        {$info.aspect_id}
      </div>
      {/if}
      {if $info.shipping_cause_id}
      <div id="shipping_cause_id">
       <strong>{$lng.lbl_shipping_cause}:&nbsp;</strong>
       {$info.shipping_cause_id}
      </div>
      {/if}

      {if $info.payment_surcharge ne 0}
      <div id="doc_payment_surcharge">
        <strong>{if $info.payment_surcharge gt 0}{$lng.lbl_payment_method_surcharge}{else}{$lng.lbl_payment_method_discount}{/if}:&nbsp;</strong>
        {include file='common/currency.tpl' value=$info.payment_surcharge}
      </div>
      {/if}
      {if $info.giftcert_discount gt 0}
      <div id="doc_giftcert_discount">
        <strong>{$lng.lbl_giftcert_discount}:&nbsp;</strong>
        {include file='common/currency.tpl' value=$info.giftcert_discount}
      </div>
      {/if}

     <!-- <div id="dsm_title_bottom">
        <strong>{$lng.lbl_assigned_sales_manager}:&nbsp;</strong>
        {$info.salesman_customer_id|user_title:'B'}
      </div> -->

  </div>



  <!-- cw@order_totals [ -->

  <div class="col-md-6 ja-col-sm-12 text-right">

    <p class="h2 font-w400 push-5">{$lng.lbl_totals}:</p>

    <div id="doc_subtotal">
      <strong>{$lng.lbl_subtotal}:&nbsp;</strong>
      {include file='common/currency.tpl' value=$info.display_subtotal}
    </div>
    {if $info.discount gt 0}
    <div id="doc_discount">
      <strong>{$lng.lbl_discount}:&nbsp;</strong>
      {include file='common/currency.tpl' value=$info.discount}
    </div>
    {/if}
    {if $info.coupon and $info.coupon_type ne "free_ship"}
    <div id="doc_coupon_saving">
      <strong>{$lng.lbl_coupon_saving}:&nbsp;</strong>
      {include file='common/currency.tpl' value=$info.coupon_discount}
    </div>
    {/if}
    {if $info.discounted_subtotal ne $info.subtotal}
    <div id="doc_discounted_subtotal">
      <strong>{$lng.lbl_discounted_subtotal}:&nbsp;</strong>
      {include file='common/currency.tpl' value=$info.display_discounted_subtotal}
    </div>
    {/if}
    {if $config.Shipping.disable_shipping ne 'Y'}
    <div id="doc_shipping_cost">
      <strong>{$lng.lbl_shipping_cost}:&nbsp;</strong>
      {include file='common/currency.tpl' value=$info.display_shipping_cost}
    </div>
     <!-- {if $info.shipping_insurance}
      <div id="doc_shipping_insurance">
        <strong>{$lng.lbl_shipping_insurance}:&nbsp;</strong>
        {include file='common/currency.tpl' value=$info.shipping_insurance}
      </div>
      {/if} -->
    {/if}
    {if $info.coupon and $info.coupon_type eq "free_ship"}
    <div id="doc_coupon_saving">
      <strong>{$lng.lbl_coupon_saving}:&nbsp;</strong>
      {include file='common/currency.tpl' value=$info.coupon_discount}
    </div>
    {/if}
    <div id="dsm_total">
      <strong>{$lng.lbl_total}:&nbsp;</strong>
      {include file='common/currency.tpl' value=$info.total}
    </div>
{if $current_area ne 'C'}
    <div>
      <strong>{$lng.lbl_seller_payment}:&nbsp;</strong>
      {tunnel func='cw\custom_magazineexchange_sellers\mag_order_owed' via='cw_call' param1=$doc.doc_id assign='seller_owed'}
      {include file='common/currency.tpl' value=$seller_owed|default:0} 
    </div>
{/if}

    {if $info.tax_exempt_text}
    <div id="doc_tax_exemption_applied">{$lng.txt_tax_exemption_applied}</div>
    <div id="doc_tax_exempt_text">{$info.tax_exempt_text}</div>
    {/if}
  {/if}

  {if $info.applied_giftcerts}
    <div id="doc_applied_giftcerts_label">{$lng.lbl_applied_giftcerts}</div>
    {foreach from=$info.applied_giftcerts item=gc}
      <div id="doc_applied_giftcerts_{$gc.giftcert_id}" style="padding-left: 5px">
        <strong>{$gc.giftcert_id}:&nbsp;</strong>
        {include file='common/currency.tpl' value=$gc.giftcert_cost}
      </div>
    {/foreach}
  {/if}


  </div>
<!-- cw@order_totals ] -->
  <div class="col-xs-12">
<!--    {if $info.customer_notes ne ""}
    <div class="doc_customer_notes" id="doc_customer_notes">
      <div class="notes_title" id="dcn_notes_title"><strong>{$lng.lbl_customer_notes}</strong></div>
      {$info.customer_notes}
    </div>
    {/if}  -->

  </div>  
</div> {* row *}
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
