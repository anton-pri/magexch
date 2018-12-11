{tunnel func='cw_web_get_layout_elements' load='web' layout="docs_`$doc.type[0]`" assign='elements'}
{if $elements}
{capture name=styles}
{foreach from=$elements item=element}
{if $element.id}
#{$element.id} {ldelim}
    position: absolute;
    top: {$element.y}px;
    left: {$element.x}px;
{if $element.display}
    display: {$element.display};
{/if}
{rdelim}
{/if}
{/foreach}
{/capture}
    {if $smarty.capture.styles}
<style type="text/css" media="all">
{$smarty.capture.styles}
</style>
    {/if}
{/if}

{assign var='info' value=$doc.info}
{assign var='userinfo' value=$doc.userinfo}
{assign var='warehouse' value=$doc.warehouse}
{assign var='profile_fields' value=$doc.userinfo.profile_fields}
{assign var='products' value=$doc.products}
{assign var='company' value=$doc.company_info}
{if $current_area eq 'A'}
    <div class="admin_order_id">
        {$lng.lbl_order_info}. {$lng.lbl_id}:&nbsp; #{$doc.display_id}
    </div>
{/if}
<!-- cw@order_title [ -->
{if $current_area ne 'A'}

<div id="doc_title">
<h1>
    {lng name="lbl_doc_info_`$doc.type`"}
</h1>
</div>

{/if}
<!-- cw@order_title ] -->

<table width="100%" style="color: #000; vertical-align: top;" cellspacing="0" cellpadding="0">
<!-- cw@order_logo [ -->
<tr>
<td colspan="2">
<div id="doc_image">
{include file='main/images/webmaster_image.tpl' image='logo_invoice'}
</div>
</td>
</tr>
<!-- cw@order_logo ] -->

<tr>
<td width="50%" style="vertical-align: top;">
<div>

<!-- cw@order_info [ -->

<div class="order_information">
    <div class="adress_title">
        <b>{$lng.lbl_information}</b>
    </div>
{*
    <div id="doc_id">
        <label>{$lng.lbl_doc_id}&nbsp;</label>
        {$doc.doc_id}
    </div>
*}
    <div id="dig_line_date">
        <label>{$lng.lbl_date}:&nbsp;</label>
        {$doc.date|date_format:$config.Appearance.datetime_format}
    </div>
{if $doc.type eq 'S' || $doc.type eq 'R'}
    <div id="dig_line_doc_number">
        <label>{$lng.lbl_shipment_document_number}:&nbsp;</label>
        {$doc.display_id}
    </div>
    <div id="dig_line_doc_status">
        <label>{$lng.lbl_payment_status}:&nbsp;</label>
        {if $doc.status eq "P" or $doc.status eq "C"}{$lng.lbl_processed}{else}{$lng.lbl_pending}{/if}
    </div>
    <div id="dig_line_doc_forwarder">
        <label>{$lng.lbl_forwarder_assigned}:&nbsp;</label>
        {$info.carrier.carrier}
    </div>
{elseif $doc.type eq 'I'}
    <div id="dig_line_doc_number">
        <label>{$lng.lbl_invoce_number}:&nbsp;</label>
        {$doc.display_id}
    </div>
    <div id="dig_line_doc_status">
        <label>{$lng.lbl_invoice_status}:&nbsp;</label>
         {include file="main/select/doc_i_status.tpl" status=$doc.status mode="static"}
    </div>
{else}
    <div id="dig_line_doc_number">
        <label>{$lng.lbl_order_id}:&nbsp;</label>
        #{$doc.display_id}
    </div>
    <div id="dig_line_doc_status">
        <label>{$lng.lbl_order_status}:&nbsp;</label>
        {include file="main/select/doc_status.tpl" status=$doc.status mode="static"}
    </div>
{/if}
    <div id="dig_line_payment">
        <label>{$lng.lbl_payment_method}:&nbsp;</label>
        {$info.payment_label}
        {if $doc.quotes}
            {foreach from=$doc.quotes item=quote}
        <br/>{$quote.exp_date|date_format:$config.Appearance.date_format} {include file='common/currency.tpl' value=$quote.paid} / {include file='common/currency.tpl' value=$quote.total}
            {/foreach}
        {/if}
    </div>
    <div id="dig_line_delivery">
        <label>{$lng.lbl_delivery}:&nbsp;</label>
        {$info.shipping_label|trademark|default:$lng.txt_not_available}
    </div>

</div>
<!-- cw@order_info ] -->

</div>
</td>

<td id="doc_bank" width="50%" style="vertical-align: top; padding: 0 0 20px;">
    &nbsp;
</td>
</tr>

<!-- cw@order_address [ -->
<tr>
<td width="50%" class="order_adress" style="vertical-align: top; padding-top: 12px; padding-bottom: 12px;">
<div id="dba_address_title" class="adress_title">
<b> {$lng.lbl_customer_info} </b>
</div>
{if $profile_fields.address.firstname.is_avail && (!$profile_fields.basic.company.is_avail || !$userinfo.company)}
    <div id="dba_line_firstname">
        <label>{$lng.lbl_firstname}:&nbsp;</label>
        {$userinfo.main_address.firstname}&nbsp;
    </div>
{/if}
{if $profile_fields.address.lastname.is_avail  && (!$profile_fields.basic.company.is_avail || !$userinfo.company)}
    <div id="dba_line_lastname">
        <label>{$lng.lbl_lastname}:&nbsp;</label>
        {$userinfo.main_address.lastname}&nbsp;
    </div>
{/if}
    <div id="dba_line_email">
        <label>{$lng.lbl_email}:&nbsp;</label>
        {$userinfo.email}&nbsp;
    </div>
    <div id="dba_line_company">
        <label>{$lng.lbl_company}:&nbsp;</label>
        {$userinfo.company}&nbsp;
    </div>
</td>

<td width="50%" class="order_adress" style="vertical-align: top; padding-top: 12px; padding-bottom: 12px;">
</td>
</tr>
<tr>
<tr>
<td width="50%" class="order_adress" style="vertical-align: top; padding-top: 12px; padding-bottom: 12px;">
<div>
    <div id="dba_address_title" class="adress_title"><b>
    {if $doc.type eq 'P' or $doc.type eq 'R' or $doc.type eq 'Q'}
    {$lng.lbl_supplier_billing_address}
    {else}
    {$lng.lbl_billing_address}
    {/if}</b>
    </div>

    {include file='main/users/address_label.tpl' address=$userinfo.main_address}

</div>
</td>

<td width="50%" class="order_adress" style="vertical-align: top; padding-top: 12px; padding-bottom: 12px;">
<div >
{if $doc.type eq 'P' or $doc.type eq 'R' or $doc.type eq 'Q'}
    <div id="dsa_address_title" class="adress_title"><b>{$lng.lbl_warehouse_shipping_address}</b></div>
    <div id="dsa_warehouse_title">
        <label>{$lng.lbl_warehouse}:&nbsp;</label>
        {$warehouse.title}
    </div>
    {assign_ext var="userinfo[current_address]" value=$warehouse.main_address}
{else}
    <div id="dsa_address_title"  class="adress_title"><b>{$lng.lbl_shipping_address}</b></div>
{/if}

    {include file='main/users/address_label.tpl' address=$userinfo.current_address}

</div>
</td>
</tr>
<!-- cw@order_address ] -->

<tr>
<td width="50%" class="order_adress" style="vertical-align: top; padding-top: 10px;">

{if $config.Email.show_cc_info eq "Y" and $usertype_layout eq "A"}
<div class="doc_payment_details" id="doc_payment_details">
    <span class="payment_title" id="dpd_payment_title">{$lng.lbl_order_payment_details}</span>
    {$doc.details|replace:"\n":"<br />"}
</div>
{/if}
<div class="clear"></div>
{*
<div id="dsm_title_top">
    <label>{$lng.lbl_assigned_sales_manager}:&nbsp;</label>
    {$info.salesman_customer_id|user_title:'B'}
</div>
*}

</td>


<td width="50%" class="order_adress" style="vertical-align: top; padding-top: 10px;">

{if $doc.type eq 'I' || $is_credit eq "Y"}
<div class="doc_reference" id="doc_reference">
    {$lng.lbl_reference}:
    {if $doc.related_docs.O}
        {$lng.lbl_order_number}
        {foreach from=$doc.related_docs.O item=rel_doc}
        {$rel_doc.display_id} ({$rel_doc.date|date_format:$config.Appearance.date_format})&nbsp;
        {/foreach}
    {/if}
    {if $doc.related_docs.S}
        -&nbsp;{$lng.lbl_shipment_document_number}
        {foreach from=$doc.related_docs.S item=rel_doc}
        {$rel_doc.display_id} ({$rel_doc.date|date_format:$config.Appearance.date_format})&nbsp;
        {/foreach}
    {/if}
</div>
{/if}
</td>
</tr>
</table>



<div class="doc_products_title" id="doc_products_title" style="font-weight: bold; font-size: 14px; padding-top: 10px; text-align: center;">
{if $order.type eq 'S'}
{$lng.lbl_products_shipped}
{elseif $order.type ne 'I'}
{$lng.lbl_products_ordered}
{/if}
</div>
