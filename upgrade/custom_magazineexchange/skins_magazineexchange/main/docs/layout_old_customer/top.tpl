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
{assign var='bank' value=$doc.bank_info}


<div id="doc_extras_info">
    {include file='main/docs/extras.tpl' extras=$info.extras}
</div>


{if $doc.info.cause_id}
<div id="cause_id">
    <label>{$lng.lbl_causale}</label>
    {$doc.cause_info.category}
</div>
{if $doc.cause_info.info.doc_required}
<div id="cause_invoice_id">
    <label>{$lng.lbl_cause_invoice_id}</label>
    {$doc.cause_info.doc.invoice_id}
</div>
<div id="cause_invoice_date">
    <label>{$lng.lbl_cause_invoice_date}</label>
    {$doc.cause_info.doc.invoice_date|date_format:$config.Appearance.date_format}
</div>
{/if}
{/if}
<table width="600" style="color: #000; vertical-align: top; border-top: 4px solid #000;" cellspacing="0" cellpadding="0">
<tr>
<td style="vertical-align: top;">

<div id="doc_image">
{include file='main/images/webmaster_image.tpl' image='logo_invoice'}
</div>

</td>
<td>
<strong class="invoice-title" style="font-size: 28px;font-weight: bold; text-transform: uppercase;">{$lng.lbl_invoice}</strong>
</td>
</tr>

<tr>
<td>
<div class="order_information">

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

</td>

<td>
<div class="doc_company" style="text-align: right;">
    <div id="dc_line_company">{$config.Company.company_name}</div>
    <div id="dc_line_address">{$config.Company.address},  {$config.Company.city}</div>
    <div id="dc_line_zipcode">{$config.Company.zipcode} {$config.Company.state_name}</div>
    <div id="dc_line_tel">{$lng.lbl_call_us}: {$config.Company.company_phone}</div>
    <div id="dc_line_tel">{$lng.lbl_phone_2_title}: {$lng.lbl_phone_2}</div>

    <div id="dc_line_email">{$lng.lbl_email}: {$config.Company.orders_department}</div>
</div>
</td>

</tr>
</table>
<hr class="invoice-line" style=" max-width: 600px; border-top: 2px solid #58595b; margin:10px 0;"/>
<table width="600" cellspacing="0" cellpadding="0" style="color: #000; vertical-align: top;">
<tr>
<td width="50%" class="order_adress" style="vertical-align: top; padding-top: 0; padding-bottom: 20px;">
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

<td width="50%" class="order_adress" style="vertical-align: top; padding-top: 0; padding-bottom: 20px;">
</td>
</tr>
<tr>
<td width="50%" class="order_adress" style="vertical-align: top; padding-top: 0; padding-bottom: 20px;">
<div>
    <div id="dba_address_title" class="adress_title" style="margin-right: 30px; border-bottom: 2px solid #58595b; margin-bottom: 4px; padding-bottom: 4px;"><b>
    {if $doc.type eq 'P' or $doc.type eq 'R' or $doc.type eq 'Q'}
    {$lng.lbl_supplier_billing_address}
    {else}
    {$lng.lbl_billing_address}
    {/if}</b>
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
{if $profile_fields.address.address.is_avail}
    <div id="dba_line_address">
        <label>{$lng.lbl_address}:&nbsp;</label>
        {$userinfo.main_address.address} {$userinfo.main_address.address_2}
    </div>
{/if}
{if $profile_fields.address.city.is_avail}
    <div id="dba_line_city">
        <label>{$lng.lbl_city}:&nbsp;</label>
        {$userinfo.main_address.city}&nbsp;
    </div>
{/if}
{if $profile_fields.address.county.is_avail && $config.General.use_counties eq 'Y'}
    <div id="dba_line_county">
        <label>{$lng.lbl_county}:&nbsp;</label>
        {$userinfo.main_address.countyname}&nbsp;
    </div>
{/if}
{if $profile_fields.address.state.is_avail}
    <div id="dba_line_state">
        <label>{$lng.lbl_state}:&nbsp;</label>
        {$userinfo.main_address.statename}&nbsp;
    </div>
{/if}
{if $profile_fields.address.country.is_avail}
    <div id="dba_line_country">
        <label>{$lng.lbl_country}:&nbsp;</label>
        {$userinfo.main_address.countryname}&nbsp;
    </div>
{/if}
{if $profile_fields.address.zipcode.is_avail}
    <div id="dba_line_zipcode">
        <label>{$lng.lbl_zipcode}:&nbsp;</label>
        {$userinfo.main_address.zipcode}&nbsp;
    </div>
{/if}
{if $profile_fields.address.email.is_avail}
    <div id="dba_line_email">
        <label>{$lng.lbl_email}:&nbsp;</label>
        {$userinfo.main_address.email}&nbsp;
    </div>
{/if}
{if $profile_fields.address.fax.is_avail}
    <div id="dba_line_fax">
        <label>{$lng.lbl_fax}:&nbsp;</label>
        {$userinfo.main_address.fax}&nbsp;
    </div>
{/if}
{if $profile_fields.address.phone.is_avail}
    <div id="dba_line_phone">
        <label>{$lng.lbl_phone}:&nbsp;</label>
        {$userinfo.main_address.phone}&nbsp;
    </div>
{/if}
</div>
</td>

<td width="50%" class="order_adress" style="vertical-align: top; padding-top: 0; padding-bottom: 20px;">
<div >
{if $doc.type eq 'P' or $doc.type eq 'R' or $doc.type eq 'Q'}
    <div id="dsa_address_title" class="adress_title" style="margin-left: 30px; border-bottom: 2px solid #58595b; margin-bottom: 4px; padding-bottom: 4px;"><b>{$lng.lbl_warehouse_shipping_address}</b></div>
    <div id="dsa_warehouse_title">
        <label>{$lng.lbl_warehouse}:&nbsp;</label>
        {$warehouse.title}
    </div>
    {assign_ext var="userinfo[current_address]" value=$warehouse.main_address}
{else}
    <div id="dsa_address_title"  class="adress_title" style=" border-bottom: 2px solid #58595b; margin-bottom: 4px; padding-bottom: 4px;"><b>{$lng.lbl_shipping_address}</b></div>
{/if}

    {if $profile_fields.address.firstname.is_avail && $userinfo.current_address.firstname}
    <div id="dsa_line_firstname">
        <label>{$lng.lbl_firstname}:&nbsp;</label>
        {$userinfo.current_address.firstname}&nbsp;
    </div>
    {/if}
    {if $profile_fields.address.lastname.is_avail && $userinfo.current_address.lastname}
    <div id="dsa_line_lastname">
        <label>{$lng.lbl_lastname}:&nbsp;</label>
        {$userinfo.current_address.lastname}&nbsp;
    </div>
    {/if}
    {if $profile_fields.address.address.is_avail}
    <div id="dsa_line_address">
        <label>{$lng.lbl_address}:&nbsp;</label>
        {$userinfo.current_address.address} {$userinfo.current_address.address_2}
    </div>
    {/if}
    {if $profile_fields.address.city.is_avail}
    <div id="dsa_line_city">
        <label>{$lng.lbl_city}:&nbsp;</label>
        {$userinfo.current_address.city}
    </div>
    {/if}
    {if $profile_fields.address.county.is_avail && $config.General.use_counties eq 'Y'}
    <div id="dsa_line_county">
        <label>{$lng.lbl_county}:&nbsp;</label>
        {$userinfo.current_address.countyname}
    </div>
    {/if}
    {if $profile_fields.address.state.is_avail}
    <div id="dsa_line_state">
        <label>{$lng.lbl_state}:&nbsp;</label>
        {$userinfo.current_address.statename}
    </div>
    {/if}
    {if $profile_fields.address.country.is_avail}
    <div id="dsa_line_country">
        <label>{$lng.lbl_country}:&nbsp;</label>
        {$userinfo.current_address.countryname}
    </div>
    {/if}
    {if $profile_fields.address.zipcode.is_avail}
    <div id="dsa_line_zipcode">
        <label>{$lng.lbl_zipcode}:&nbsp;</label>
        {$userinfo.current_address.zipcode}
    </div>
    {/if}
    {if $profile_fields.address.email.is_avail}
    <div id="dsa_line_email">
        <label>{$lng.lbl_email}:&nbsp;</label>
        {$userinfo.current_address.email}
    </div>
    {/if}
    {if $profile_fields.address.fax.is_avail}
    <div id="dsa_line_fax">
        <label>{$lng.lbl_fax}:&nbsp;</label>
        {$userinfo.current_address.fax}
    </div>
    {/if}
    {if $profile_fields.address.phone.is_avail}
    <div id="dsa_line_phone">
        <label>{$lng.lbl_phone}:&nbsp;</label>
        {$userinfo.current_address.phone}
    </div>
    {/if}
</div>
</td>
</tr>
<tr>
<td width="50%" class="order_adress" style="vertical-align: top; padding-top: 20px;">

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


<td width="50%" class="order_adress" style="vertical-align: top; padding-top: 20px;">

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



<div class="doc_products_title" id="doc_products_title" style="font-weight: bold; font-size: 14px; padding-top: 10px; text-align: center; max-width: 600px;">
{if $order.type eq 'S'}
{$lng.lbl_products_shipped}
{elseif $order.type ne 'I'}
{$lng.lbl_items_purchased_me|substitute:'seller_name':$seller_info.fullname}
{/if}
</div>
