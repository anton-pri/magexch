<table cellspacing="0" cellpadding="0" width="100%" border="0">
{if $order.type eq 'S'}
<tr>
<td align="center"><font style="FONT-SIZE: 14px; FONT-WEIGHT: bold;" {if $is_pdf}size="4"{/if}>{$lng.lbl_products_shipped}</font></td>
</tr>
{elseif $order.type ne 'I'}
<tr>
<td align="center"><font style="FONT-SIZE: 14px; FONT-WEIGHT: bold;" {if $is_pdf}size="4"{/if}>{$lng.lbl_products_ordered}</font></td>
</tr>
{/if}
</table>

{if $is_pdf}<br/>{/if}

<table cellspacing="0" cellpadding="3" width="100%" {if $is_pdf}border="1"{else}style="border-collapse: collapse; border: 1px solid #DDDDDD;"{/if}>
<tr>
<th width="60" bgcolor="#cccccc" style="border: 1px solid #DDDDDD;" nowrap>{$lng.lbl_sku}</th>
<th bgcolor="#cccccc" style="border: 1px solid #DDDDDD;" {if $is_pdf}width="350"{/if}>{$lng.lbl_product}</th>
{if $order.extra.tax_info.display_cart_products_tax_rates eq "Y" and $_userinfo.tax_exempt ne "Y"}
<th nowrap="nowrap" width="60" bgcolor="#cccccc" align="center" style="border: 1px solid #DDDDDD;">{if $order.extra.tax_info.product_tax_name ne ""}{$order.extra.tax_info.product_tax_name}{else}{$lng.lbl_tax}{/if}</th>
{/if}
<th nowrap="nowrap" width="60" bgcolor="#cccccc" align="center" style="border: 1px solid #DDDDDD;">{$lng.lbl_price}</th>
<th nowrap="nowrap" width="60" bgcolor="#cccccc" align="center" style="border: 1px solid #DDDDDD;">{$lng.lbl_discount}</th>
<th width="60" bgcolor="#cccccc" align="center" style="border: 1px solid #DDDDDD;">{$lng.lbl_quantity}</th>
<th width="60" bgcolor="#cccccc" align="center" style="border: 1px solid #DDDDDD;">{$lng.lbl_total}<br /><img height="1" src="{$ImagesDir}/spacer.gif" width="50" border="0" alt="" /></th>
</tr>

{if $products}
{foreach from=$products item=product}
<tr>
<td align="center" style="border: 1px solid #DDDDDD;" nowrap>{$product.productcode|default:"&nbsp;"}</td>
<td style="border: 1px solid #DDDDDD;"><font style="FONT-SIZE: 11px">{$product.product}</font>

{if $product.destination_warehouse}
<div><b>{$lng.lbl_delive_to_warehouse}: {$product.destination_warehouse_title}</b></div>
{/if}

{* kornev, TOFIX *}
{if $product.product_options ne ''}
<table>
<tr>
<td valign="top"><b>{$lng.lbl_options}:</b></td> 
<td>{include file='addons/product_options/main/options/display.tpl' options=$product.product_options options_txt=$product.product_options_txt force_product_options_txt=$product.force_product_options_txt}</td>
</tr>
</table>
{/if}

{if $product.serial_numbers ne ''}
<table>
<tr>
<td valign="top"><b>{$lng.lbl_serial_numbers}:</b></td> 
    <td>
<table>
{foreach from=$product.serial_numbers item=sn}
<tr><td>{$sn.number}</td></tr>
{/foreach}
</table>
    </td>
</tr>
</table>
{/if}

{if $addons.egoods and $product.download_key and ($order.status eq "P" or $order.status eq "C")}
<br />
<a href="index.php?target=download&id={$product.download_key}" class="SmallNote" target="_blank">{$lng.lbl_download}</a>
{/if}
</td>
{if $order.extra.tax_info.display_cart_products_tax_rates eq "Y" and $_userinfo.tax_exempt ne "Y"}
<td align="center" style="border: 1px solid #DDDDDD;">
{foreach from=$product.extra_data.taxes key=tax_name item=tax}
{if $tax.tax_value gt 0}
{if $order.extra.tax_info.product_tax_name eq ""}{$tax.tax_display_name} {/if}
{if $tax.rate_type eq "%"}{$tax.rate_value}%{else}{include file='common/currency.tpl' value=$tax.rate_value}{/if}<br />
{/if}
{/foreach}
</td>
{/if}
<td align="center" nowrap="nowrap" style="border: 1px solid #DDDDDD;">{include file='common/currency.tpl' value=$product.display_price}&nbsp;&nbsp;</td>
<td align="center" nowrap="nowrap" style="border: 1px solid #DDDDDD;">
    {math equation="a-b" a=$product.extra_data.display.price b=$product.extra_data.display.discounted_price assign=discount}
    {include file='common/currency.tpl' value=$discount}
</td>
<td align="center" style="border: 1px solid #DDDDDD;">{$product.amount}</td>
<td align="center" nowrap="nowrap" style="border: 1px solid #DDDDDD;">{math assign="total" equation="amount*price" amount=$product.amount price=$product.display_price}{include file='common/currency.tpl' value=$total}&nbsp;&nbsp;</td>
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

{if $order.type eq 'S'}
<table cellspacing="0" cellpadding="0" width="100%" border="0">
<tr>
    <td rowspan="2" valign="top"{if $is_pdf} width="550"{/if}>
        <br/>
        <table cellspacing="3">
            <tr>
                <td><b>{$lng.lbl_ship_date}:</b> {$order.ship_time}</td>    
            </tr>
            <tr>
                <td><b>{$lng.lbl_insured_shipment}:</b> {if $info.shipping_insurance gt 0}{$lng.lbl_yes|upper}{else}{$lng.lbl_no|upper}{/if}</td>
            </tr>
            <tr>
                <td><b>{$lng.lbl_shipment_weight}:</b> {$info.weight} {$config.General.weight_symbol|upper}</tD>
            </tr>
        </table>
    </td>
    <td {if $is_pdf} width="260"{/if} align="right">
        <br/>
        {if $info.shipping_insurance gt 0}
        <table cellspacing="3" {if $is_pdf}width="100%" align="right"{/if}>
            <tr><td><b>{$lng.lbl_shipment_insured_for}:</b></td></tr>
            <tr><td><font class="ProductPrice" {if $is_pdf}color="#bb0000"{/if}>
                {include file='common/currency.tpl' value=$info.total}
            </font></td>
        </table>
        {/if}
    </td>
</tr>
<tr>
    <td align="center">
        <br/>
        <b>{$lng.lbl_cash_on_delivery}:</b> {if $order.is_cod eq 'Y'}{$lng.lbl_yes|upper}{else}{$lng.lbl_no|upper}{/if}
    </td>
</tr>
<tr>
    <td colspan="3"><img height="4" src="{$ImagesDir}/spacer.gif" alt="" /></td>
</tr>
<tr>
    <td bgcolor="#000000" colspan="3"><img height="2" src="{$ImagesDir}/spacer_black.gif" alt="" /></td>
</tr>
<tr> 
    <td bgcolor="#cccccc" nowrap height="25"><b>{$lng.lbl_assigned_sales_manager}:</b>&nbsp;&nbsp;&nbsp;&nbsp;{$doc.salesman_info.title}</td>
    <td align="right" bgcolor="#cccccc" nowrap><font color="#bb0000"><b>{$lng.lbl_reference} {$lng.lbl_order_number} {$order.display_id} ({$order.main_order_info.date|date_format:$config.Appearance.date_format})</b>&nbsp;&nbsp;&nbsp;</font></td>
</tr>
</table>
<br/><br/>
<div align="right"><b>{$lng.lbl_forwarder_signature}:<u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u></b></div>
{else}
<table cellspacing="0" cellpadding="0" width="100%" border="0">
<tr>
    <td colspan="3"><img height="4" src="{$ImagesDir}/spacer.gif" alt="" /></td>
</tr>
<tr>
    <td {if $is_pdf}width="450"{else}width="60%"{/if}>
        <table cellspacing="3" border="0">
            <tr><td valign="top">{$lng.lbl_to_delivery_from_warehouse}:</td><td>{$order.warehouse_info.company}<br>{$order.warehouse_info.country_name}</td></tr>
        </table>
        <br/>
        <table cellspacing="3">
        {if $order.type eq "I" || $order.type eq "R"}
            <tr><td><b>{$lng.lbl_forwarder_assigned}:</b></td><td>{$order.forwarder}</td></tr>
        {/if}
            <tr><td><b>{$lng.lbl_shipment_weight}:</b></td><td>{$info.weight} {$config.General.weight_symbol|upper}</td></tr>
            <tr><td><b>{$lng.lbl_cash_on_delivery}:</b></td><td>{if $order.is_cod eq "Y"}{$lng.lbl_yes|upper}{else}{$lng.lbl_no|upper}{/if}</td></tr>
        </table>
    </td>
    <td align="right" colspan="2" valign="top" {if $is_pdf}width="300"{else}width="40%"{/if}>
<table cellspacing="0" cellpadding="0" width="100%" border="0">
<tr>
<td align="right" height="20" {if $is_pdf}width="230"{/if}><b>{$lng.lbl_subtotal}:</b>&nbsp;</td>
<td align="right" width="70">{include file='common/currency.tpl' value=$info.display_subtotal}&nbsp;&nbsp;&nbsp;</td>
</tr>

{if $order.discount gt 0}
<tr>
<td align="right" height="20"><b>{$lng.lbl_discount}:</b>&nbsp;</td>
<td align="right">{include file='common/currency.tpl' value=$info.discount}&nbsp;&nbsp;&nbsp;</td>
</tr>
{/if}

{if $order.coupon and $order.coupon_type ne "free_ship"}
<tr>
<td align="right" height="20"><b>{$lng.lbl_coupon_saving}:</b>&nbsp;</td>
<td align="right">{include file='common/currency.tpl' value=$info.coupon_discount}&nbsp;&nbsp;&nbsp;</td>
</tr>
{/if}

{if $order.discounted_subtotal ne $order.subtotal}
<tr>
<td align="right" height="20" nowrap><b>{$lng.lbl_discounted_subtotal}:</b>&nbsp;</td>
<td align="right" nowrap>{include file='common/currency.tpl' value=$info.display_discounted_subtotal}&nbsp;&nbsp;&nbsp;</td>
</tr>
{/if}

{if $config.Shipping.disable_shipping ne 'Y'}
<tr>
<td align="right" height="20" nowrap><b>{$lng.lbl_shipping_cost}:</b>&nbsp;</td>
<td align="right">{include file='common/currency.tpl' value=$info.display_shipping_cost}&nbsp;&nbsp;&nbsp;</td>
</tr>

{if $order.shipping_insurance}
<tr>
<td align="right" height="20" nowrap><b>{$lng.lbl_shipping_insurance}:</b>&nbsp;</td>
<td align="right">{include file='common/currency.tpl' value=$order.shipping_insurance}&nbsp;&nbsp;&nbsp;</td>
</tr>
{/if}

{/if}

{if $order.coupon and $order.coupon_type eq "free_ship"}
<tr>
<td align="right" height="20"><b>{$lng.lbl_coupon_saving}:</b>&nbsp;</td>
<td align="right">{include file='common/currency.tpl' value=$info.coupon_discount}&nbsp;&nbsp;&nbsp;</td>
</tr>
{/if}

{*if $order.applied_taxes and $order.extra.tax_info.display_taxed_order_totals ne "Y"*}
{foreach key=tax_name item=tax from=$info.applied_taxes}
<tr>
<td align="right" height="20" nowrap><b>{$tax.tax_display_name}{if $tax.rate_type eq "%"} {$tax.rate_value}%{/if}:</b>&nbsp;</td>
<td align="right">{include file='common/currency.tpl' value=$tax.tax_cost}&nbsp;&nbsp;&nbsp;</td>
</tr>
{/foreach}
{*/if*}

{if !$info.applied_taxes}
<tr>
<td align="right" height="20"><b>{$lng.lbl_vat}:</b>&nbsp;</td>
<td align="right">{include file='common/currency.tpl' value=0}&nbsp;&nbsp;&nbsp;</td>
</tr>
{/if}

{if $info.payment_surcharge ne 0}
<tr>
<td align="right" height="20" nowrap><b>{if $info.payment_surcharge gt 0}{$lng.lbl_payment_method_surcharge}{else}{$lng.lbl_payment_method_discount}{/if}:</b>&nbsp;</td>
<td align="right">{include file='common/currency.tpl' value=$info.payment_surcharge}&nbsp;&nbsp;&nbsp;</td>
</tr>
{/if}


{if $info.giftcert_discount gt 0}
<tr>
<td align="right" height="20"><b>{$lng.lbl_giftcert_discount}:</b>&nbsp;</td>
<td align="right">{include file='common/currency.tpl' value=$info.giftcert_discount}&nbsp;&nbsp;&nbsp;</td>
</tr>
{/if}

    </table>
</td>
</tr>

<tr>
<td bgcolor="#000000" colspan="3"><img height="2" src="{$ImagesDir}/spacer_black.gif" alt="" /></td>
</tr>
<tr> 
<td bgcolor="#cccccc" nowrap height="20"><b>{$lng.lbl_assigned_sales_manager}:</b>&nbsp;&nbsp;&nbsp;&nbsp;{$doc.salesman_info.title}</td>
<td align="right" bgcolor="#cccccc" height="25" {if !$is_pdf}width="100%"{/if}><b>{$lng.lbl_total}:</b>&nbsp;</td>
<td align="right" bgcolor="#cccccc" width="70" style="width:70px"><div style="width:70px"><b>{include file='common/currency.tpl' value=$info.total}</b>&nbsp;&nbsp;&nbsp;</div></td>
</tr>

{if $_userinfo.tax_exempt ne "Y"}
{else}

<tr>
<td align="right" colspan="2" width="100%" height="20">{$lng.txt_tax_exemption_applied}</td>
</tr>
<tr>
<td align="right" colspan="2" width="100%" height="20">{$_userinfo.tax_exempt_text}</td>
</tr>
{/if}

</table>
{/if}

{if $order.info.applied_giftcerts}
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

{foreach from=$order.info.applied_giftcerts item=gc}
<tr>
<td align="center">{$gc.giftcert_id}</td>
<td align="right" nowrap="nowrap">{include file='common/currency.tpl' value=$gc.giftcert_cost}&nbsp;&nbsp;&nbsp;</td>
</tr>
{/foreach}

</table>
{/if}
