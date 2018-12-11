<span class="doc_products_title" id="doc_products_title">
{if $order.type eq 'S'}
{$lng.lbl_products_shipped}
{elseif $order.type ne 'I'}
{$lng.lbl_products_ordered}
{/if}
</span>

<div style="width:100%">
<table cellspacing="1" cellpadding="3" class="doc_products" id="doc_products">
<tr>
    <th width="60">{$lng.lbl_sku}</th>
    <th>{$lng.lbl_product}</th>
{if $order.extra.tax_info.display_cart_products_tax_rates eq "Y" and $_userinfo.tax_exempt ne "Y"}
    <th width="60">{if $order.extra.tax_info.product_tax_name ne ""}{$order.extra.tax_info.product_tax_name}{else}{$lng.lbl_tax}{/if}</th>
{/if}
    <th width="60">{$lng.lbl_quantity}</th>
{if !$is_printing}
    <th width="60">{$lng.lbl_price}</th>
    <th width="60">{$lng.lbl_discount}</th>
    <th width="60">{$lng.lbl_total}</th>
{/if}
</tr>

{if $products}
{foreach from=$products item=product}
<tr>
    <td align="center">{$product.productcode|default:"&nbsp;"}</td>
    <td>{$product.product}
{if $product.destination_warehouse}
        <div><b>{$lng.lbl_delive_to_warehouse}: {$product.destination_warehouse_title}</b></div>
{/if}

{* kornev, TOFIX *}
{if $product.product_options ne ''}
        <div><b>{$lng.lbl_options}:</b></div> 
        {include file='addons/product_options/main/options/display.tpl' options=$product.product_options options_txt=$product.product_options_txt force_product_options_txt=$product.force_product_options_txt}
{/if}

{if $product.serial_numbers ne ''}
        <div><b>{$lng.lbl_serial_numbers}:</b></div> 
    {foreach from=$product.serial_numbers item=sn}
        {$sn.number}<br/>
    {/foreach}
{/if}

{if $addons.egoods and $product.download_key and ($order.status eq "P" or $order.status eq "C")}
        <br />
        <a href="index.php?target=download&id={$product.download_key}" class="SmallNote" target="_blank">{$lng.lbl_download}</a>
{/if}
    </td>
{if $order.extra.tax_info.display_cart_products_tax_rates eq "Y" and $_userinfo.tax_exempt ne "Y"}
    <td align="center">
    {foreach from=$product.extra_data.taxes key=tax_name item=tax}
    {if $tax.tax_value gt 0}
        {if $order.extra.tax_info.product_tax_name eq ""}{$tax.tax_display_name} {/if}
        {if $tax.rate_type eq "%"}{$tax.rate_value}%{else}{include file='common/currency.tpl' value=$tax.rate_value}{/if}<br />
    {/if}
    {/foreach}
    </td>
{/if}
    <td align="center">{$product.amount}</td>
{if !$is_printing}
    <td align="center">{include file='common/currency.tpl' value=$product.display_net_price}</td>
    <td align="center">
        {math assign="total" equation="price-net_price" amount=$product.amount price=$product.price net_price=$product.net_price}
        {include file='common/currency.tpl' value=$total}
    </td>
    <td align="center" nowrap="nowrap" >
        {math assign="total" equation="amount*price" amount=$product.amount price=$product.price}
        {include file='common/currency.tpl' value=$total}
    </td>
{/if}
</tr>
{/foreach}
{/if}

{if $giftcerts ne ''}
{foreach from=$giftcerts item=gc}
<tr>
	<td>&nbsp;</td>
	<td nowrap="nowrap">
{$lng.lbl_gift_certificate}: {$gc.gc_id}<br />
<div style="padding-left: 10px; white-space: nowrap;">
{if $gc.send_via eq "P"}
{$lng.lbl_gc_send_via_postal_mail}<br />
{$lng.lbl_mail_address}: {$gc.recipient_firstname} {$gc.recipient_lastname}<br />
{$gc.recipient_address}, {$gc.recipient_city},<br />
{if $gc.recipient_countyname ne ''}{$gc.recipient_countyname} {/if}{$gc.recipient_state} {$gc.recipient_country}, {$gc.recipient_zipcode}<br />
{$lng.lbl_phone}: {$gc.recipient_phone}
{else}
{$lng.lbl_recipient_email}: {$gc.recipient_email}
{/if}
</div>
	</td>
{if $order.extra.tax_info.display_cart_products_tax_rates eq "Y" and $_userinfo.tax_exempt ne "Y"}
	<td align="center">&nbsp;-&nbsp;</td>
{/if}
	<td align="right" nowrap="nowrap">{include file='common/currency.tpl' value=$gc.amount}&nbsp;&nbsp;</td>
	<td align="center">1</td>
	<td align="right" nowrap="nowrap">{include file='common/currency.tpl' value=$gc.amount}&nbsp;&nbsp;</td>
</tr>
{/foreach}
{/if}
</table>
</div>

<div style="position: relative">
{if $order.type eq 'S'}
<span id="doc_ship_date">
    <label>{$lng.lbl_ship_date}:</label> 
    {$order.ship_time}
</span>    
<span id="doc_ship_date">
    <label>{$lng.lbl_insured_shipment}:</label>
    {if $info.shipping_insurance gt 0}{$lng.lbl_yes|upper}{else}{$lng.lbl_no|upper}{/if}
</span>
<span id="doc_ship_date">
    <label>{$lng.lbl_shipment_weight}:</label>
    {$info.weight} {$config.General.weight_symbol|upper}
</span>
{if $info.shipping_insurance gt 0}
<span id="doc_shipment_insured_for">
    <label>{$lng.lbl_shipment_insured_for}:</label>
    {include file='common/currency.tpl' value=$info.total}
</span>
{/if}
<span class="doc_cash_on_delivery">
    <label>{$lng.lbl_cash_on_delivery}:</label>
    {if $info.cod_type_id}{$lng.lbl_yes|upper} ({$info.cod_type_label}){else}{$lng.lbl_no|upper}{/if}
</span>

<div id="doc_sales_manager">
    <span  class="dsm_title" id="dsm_title">
        <label>{$lng.lbl_assigned_sales_manager}:</label>
        {$info.salesman_customer_id|user_title:'B'}
    </span>
    <span id="dsm_total_red">
    {$lng.lbl_reference} {$lng.lbl_order_number} 
    {if $order.related_docs.O}
        {foreach from=$order.related_docs.O item=rel_doc}
        {$rel_doc.display_id} ({$rel_doc.date|date_format:$config.Appearance.date_format})&nbsp;
        {/foreach}
    {/if}
    </span>
</span>

<span id="doc_forwarder_signature"><label>{$lng.lbl_forwarder_signature}:</label><span>

{else}

<span id="doc_delivery_from_warehouse">
    <label>{$lng.lbl_to_delivery_from_warehouse}:</label>
    {$order.warehouse_info.company}<br />{$order.warehouse_info.country_name}
</span>
    {if $order.type eq "I" || $order.type eq "R"}
<span id="doc_forwarder_assigned">
    <label>{$lng.lbl_forwarder_assigned}:</label>
    {if $info.carrier}{$info.carrier.carrier}{else}{$lng.lbl_none}{/if}
</span>
    {/if}
<span id="doic_shipment_weight">
    <label>{$lng.lbl_shipment_weight}:</label>
    <label>{$info.weight} {$config.General.weight_symbol|upper}</label>
</span>
<span id="doc_cash_on_delivery">
    <label>{$lng.lbl_cash_on_delivery}:</label>
    {if $info.cod_type_id}{$lng.lbl_yes|upper} ({$info.cod_type_label}){else}{$lng.lbl_no|upper}{/if}
</span>

<span id="doc_subtotal">
    <label>{$lng.lbl_subtotal}:</label>
    {include file='common/currency.tpl' value=$info.display_subtotal}
</span>
    {if $info.discount gt 0}
<span id="doc_discount">
    <label>{$lng.lbl_discount}:</label>
    {include file='common/currency.tpl' value=$info.discount}
</span>
    {/if}
    {if $info.coupon and $info.coupon_type ne "free_ship"}
<span id="doc_coupon_saving">
    <label>{$lng.lbl_coupon_saving}:</label>
    {include file='common/currency.tpl' value=$info.coupon_discount}
</span>
    {/if}
    {if $info.discounted_subtotal ne $info.subtotal}
<span id="doc_discounted_subtotal">
    <label>{$lng.lbl_discounted_subtotal}:</label>
    {include file='common/currency.tpl' value=$info.display_discounted_subtotal}
</span>
    {/if}
    {if $config.Shipping.disable_shipping ne 'Y'}
<span id="doc_shipping_cost">
    <label>{$lng.lbl_shipping_cost}:</label>
    {include file='common/currency.tpl' value=$info.display_shipping_cost}
</span>
    {if $info.shipping_insurance}
<span id="doc_shipping_insurance">
    <label>{$lng.lbl_shipping_insurance}:</label>
    {include file='common/currency.tpl' value=$info.shipping_insurance}
</span>
    {/if}
{/if}
{if $info.coupon and $info.coupon_type eq "free_ship"}
<span id="doc_coupon_saving">
    <label>{$lng.lbl_coupon_saving}:</label>
    {include file='common/currency.tpl' value=$info.coupon_discount}
</span>
{/if}
{if $info.applied_taxes}
<span id="doc_tax">
    {foreach key=tax_name item=tax from=$info.applied_taxes}
    <label>{$tax.tax_display_name}{if $tax.rate_type eq "%"} {$tax.rate_value}%{/if}:</label>
    {include file='common/currency.tpl' value=$tax.tax_cost}
    {/foreach}
</span>
{/if}
{if !$info.applied_taxes}
<span id="doc_tax">
    <label>{$lng.lbl_vat}:</label>
    {include file='common/currency.tpl' value=0}
</span>
{/if}
{if $info.payment_surcharge ne 0}
<span id="doc_payment_surcharge">
    <label>{if $info.payment_surcharge gt 0}{$lng.lbl_payment_method_surcharge}{else}{$lng.lbl_payment_method_discount}{/if}:</label>
    {include file='common/currency.tpl' value=$info.payment_surcharge}
</span>
{/if}
{if $info.giftcert_discount gt 0}
<span id="doc_giftcert_discount">
    <label>{$lng.lbl_giftcert_discount}:</label>
    {include file='common/currency.tpl' value=$info.giftcert_discount}
</span>
{/if}

<span id="dsm_title">
    <label>{$lng.lbl_assigned_sales_manager}:</label>
    {$info.salesman_customer_id|user_title:'B'}
</span>
<span id="dsm_total">
    <label>{$lng.lbl_total}:</label>
    {include file='common/currency.tpl' value=$info.total}
</span>

{if $info.tax_exempt_text}
<span id="doc_tax_exemption_applied">{$lng.txt_tax_exemption_applied}</div>
<span id="doc_tax_exempt_text">{$info.tax_exempt_text}</span>
{/if}
{/if}

{if $info.applied_giftcerts}
<br />
<table cellspacing="0" cellpadding="0" width="100%" border="0">
<tr>
	<td align="center"><font style="FONT-SIZE: 14px; FONT-WEIGHT: bold;">{$lng.lbl_applied_giftcerts}</font></td>
</tr>
</table>

<table cellspacing="1" cellpadding="0" width="100%" border="0">

<tr>
<th width="60" bgcolor="#cccccc">{$lng.lbl_giftcert_ID}</th>
<th bgcolor="#cccccc">{$lng.lbl_giftcert_cost}</th>
</tr>

{foreach from=$info.applied_giftcerts item=gc}
<tr>
<td align="center">{$gc.giftcert_id}</td>
<td align="right" nowrap="nowrap">{include file='common/currency.tpl' value=$gc.giftcert_cost}&nbsp;&nbsp;&nbsp;</td>
</tr>
{/foreach}

</table>
{/if}

</div>
