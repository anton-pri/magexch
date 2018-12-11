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
{if $current_area eq 'A'}
    <div class="h1 text-center push-30-t push-30 hidden-print">
        {$lng.lbl_order_info}. {$lng.lbl_id}:&nbsp; #{$doc.display_id}
    </div>
    <hr class="hidden-print" />
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
<div class="row items-push-2x">
  <div class="col-xs-4 col-sm-4 col-lg-4">

<!-- cw@order_logo [ -->
    <address>
    <div id="doc_image">
      {include file='main/images/webmaster_image.tpl' image='logo_invoice'}
    </div>
    </address>

<!-- cw@order_logo ] -->


{*
<div id="doc_bank" style="vertical-align: top; padding: 0 0 20px;">

<div>
<div class="adress_title"><b>
{if $doc.type eq 'S'}
    {$lng.lbl_warehouse_assigned_to_ship}
{elseif $doc.type eq 'I' || $doc.type eq 'D' || $doc.type eq 'O' || $doc.type eq 'G' || $doc.type eq 'C'}
    {$lng.lbl_company_bank_details}
{/if}
</b></div>

    <div>
<div class="doc_company">
    <div id="dc_line_company">{$company.company_name}</div>
    <div id="dc_line_address">{$company.address}</div>
    <div id="dc_line_zipcode">{$company.zipcode} {$company.state_name} {$company.city}</div>
    <div id="dc_line_vat"><b>{$lng.lbl_vat_uppercase}</b> {$company.vat_number}</div>
    <div id="dc_line_tel"><b>{$lng.lbl_tel}</b> {$company.company_phone}</div>
    <div id="dc_line_fax"><b>{$lng.lbl_fax}:</b> {$company.company_fax}</div>
    <div id="dc_line_email"><b>{$lng.lbl_email}:</b> {$company.company_email}</div>
</div>


{if $doc.type eq 'S' || $doc.type eq 'R'}
<div class="doc_warehouse">
    <div id="dw_line_company">{$warehouse.company}</div>
    <div id="dw_line_address">{$warehouse.address}</div>
    <div id="dw_line_zipcode">{$warehouse.zipcode} {$warehouse.state_name} {$warehouse.city}</div>
    <div id="dw_line_tel"><label>{$lng.lbl_tel}:</label> {$warehouse.phone}</div>
    <div id="dw_line_fax"><label>{$lng.lbl_fax}:</label> {$warehouse.fax}</div>
    <div id="dw_line_email"><label>{$lng.lbl_email}:</label> {$warehouse.email}</div>
    <div id="dw_line_email"><label>{$lng.lbl_contact_person}:</label> {$warehouse.firstname} {$warehouse.lastname}</div>
</div>
{else}
{if $main ne "document"}
<div class="doc_bank_info">
    <div id="db_line_bank">{$bank.bank_name}</div>
    <div id="db_line_abi"><label>{$lng.lbl_abi_code}:&nbsp;</label> {$bank.abi}</div>
    <div id="db_line_cab"><label>{$lng.lbl_cab_code}:&nbsp;</label> {$bank.cab}</div>
    <div id="db_line_account"><label>{$lng.lbl_account_number}:&nbsp;</label> {$bank.number}</div>
    <div id="db_line_cin"><label>{$lng.lbl_cin_code}:&nbsp;</label> {$bank.cin}</div>
    <div id="db_line_iban"><label>{$lng.lbl_iban_code}:&nbsp;</label> {$bank.iban}</div>
    <div id="db_line_swift"><label>{$lng.lbl_swift_code}:&nbsp;</label> {$bank.swift}</div>
    <div id="db_line_country"><label>{$lng.lbl_country}:&nbsp;</label> {$bank.country_name}</div>
</div>
{/if}
{/if}

{if $profile_fields.basic.company.is_avail}
    <div id="dui_line_company">
        <label>{$lng.lbl_company}:&nbsp;</label>
        {$userinfo.company}
    </div>
{/if}
{if $profile_fields.basic.tax_number.is_avail}
    <div id="dui_line_tax">
        <label>{if $userinfo.usertype eq 'R'}{$lng.lbl_tax_number_reseller}{else}{$lng.lbl_tax_number}{/if}:&nbsp;</label>
        {$userinfo.tax_number}
    </div>
{/if}
{if $profile_fields.basic.ssn.is_avail}
    <div id="dui_line_ssn">
        <label>{$lng.lbl_ssn}:&nbsp;</label>
        {$userinfo.ssn}
    </div>
{/if}
{if $profile_fields.contact_list.email.is_avail}
    <div id="dui_line_email">
        <label>{$lng.lbl_email}:&nbsp;</label>
        {$userinfo.email}
    </div>
{/if}
    </div>
</div>

</div>
*}
</div>
<div class="col-xs-4 col-sm-4 col-lg-4 text-right">
    <div id="dc_line_company">{$config.Company.company_name}</div>
    <div id="dc_line_address">{$config.Company.address},  {$config.Company.city}</div>
    <div id="dc_line_zipcode">{$config.Company.zipcode} {$config.Company.state_name}</div>
    <div id="dc_line_tel">{$lng.lbl_toll_free}: {$config.Company.company_phone}</div>
    <div id="dc_line_tel">{$lng.lbl_phone_2_title}: {$lng.lbl_phone_2}</div>

    <div id="dc_line_email">{$lng.lbl_email}: {$config.Company.orders_department}</div>
</div>
<div class="col-xs-4 col-sm-4 col-lg-4 text-right">
  <p class="h2 font-w400 push-5">{$lng.lbl_customer}</p>
  <address>
  {if $profile_fields.address.firstname.is_avail && (!$profile_fields.basic.company.is_avail || !$userinfo.company)}
    <div id="dba_line_firstname">
        <strong>{$lng.lbl_firstname}:&nbsp;</strong>
        {$userinfo.main_address.firstname}
    </div>
  {/if}
  {if $profile_fields.address.lastname.is_avail  && (!$profile_fields.basic.company.is_avail || !$userinfo.company)}
    <div id="dba_line_lastname">
        <strong>{$lng.lbl_lastname}:&nbsp;</strong>
        {$userinfo.main_address.lastname}
    </div>
  {/if}
    <div id="dba_line_email">
        <strong>{$lng.lbl_email}:&nbsp;</strong>
        {$userinfo.email}
    </div>
  {if $userinfo.company} 
    <div id="dba_line_company">
        <strong>{$lng.lbl_company}:&nbsp;</strong>
        {$userinfo.company}
    </div>
  {/if}
  </address>
</div>
</div> {* row *}
  <!-- cw@order_address [ -->

  <div class="row items-push-2x">


<!-- cw@order_info [ -->
  <div class="col-xs-4 col-sm-4 col-lg-4">
      <p class="h2 font-w400 push-5">{$lng.lbl_information}</p>

{*
      <div id="doc_id">
        <label>{$lng.lbl_doc_id}&nbsp;</label>
        {$doc.doc_id}
      </div>
*}
      <div id="dig_line_date">
        <strong>{$lng.lbl_date}:&nbsp;</strong>
        {$doc.date|date_format:$config.Appearance.datetime_format}
      </div>
      {if $doc.type eq 'S' || $doc.type eq 'R'}
      <div id="dig_line_doc_number">
        <strong>{$lng.lbl_shipment_document_number}:&nbsp;</strong>
        {$doc.display_id}
      </div>
      <div id="dig_line_doc_status">
        <strong>{$lng.lbl_payment_status}:&nbsp;</strong>
        {if $doc.status eq "P" or $doc.status eq "C"}{$lng.lbl_processed}{else}{$lng.lbl_pending}{/if}
      </div>
      <div id="dig_line_doc_forwarder">
        <strong>{$lng.lbl_forwarder_assigned}:&nbsp;</strong>
        {$info.carrier.carrier}
      </div>
      {elseif $doc.type eq 'I'}
      <div id="dig_line_doc_number">
        <strong>{$lng.lbl_invoce_number}:&nbsp;</strong>
        {$doc.display_id}
      </div>
      <div id="dig_line_doc_status">
        <strong>{$lng.lbl_invoice_status}:&nbsp;</strong>
         {include file="main/select/doc_i_status.tpl" status=$doc.status mode="static"}
      </div>
      {else}
      <div id="dig_line_doc_number">
        <strong>{$lng.lbl_order_id}:&nbsp;</strong>
        #{$doc.display_id}
      </div>
      <div id="dig_line_doc_status">
        <strong>{$lng.lbl_order_status}:&nbsp;</strong>
        {include file="main/select/doc_status.tpl" status=$doc.status mode="static"}
      </div>
      {/if}
      <div id="dig_line_payment">
        <strong>{$lng.lbl_payment_method}:&nbsp;</strong>
        {$info.payment_label}
        {if $doc.quotes}
            {foreach from=$doc.quotes item=quote}
        <br/>{$quote.exp_date|date_format:$config.Appearance.date_format} {include file='common/currency.tpl' value=$quote.paid} / {include file='common/currency.tpl' value=$quote.total}
            {/foreach}
        {/if}
      </div>
      <div id="dig_line_delivery">
        <strong>{$lng.lbl_delivery}:&nbsp;</strong>
        {$info.shipping_label|trademark|default:$lng.txt_not_available}
      </div>

<!-- cw@order_info ] -->

  </div>

  <div class="col-xs-4 col-sm-4 col-lg-4 text-right">
    <p class="h2 font-w400 push-5">
    {if $doc.type eq 'P' or $doc.type eq 'R' or $doc.type eq 'Q'}
    {$lng.lbl_supplier_billing_address}
    {else}
    {$lng.lbl_billing_address}
    {/if}
    </p>
    <address>{include file='admin/users/address_label.tpl' address=$userinfo.main_address}</address>
  </div>
  <div class="col-xs-4 col-sm-4 col-lg-4 text-right">
    {if $doc.type eq 'P' or $doc.type eq 'R' or $doc.type eq 'Q'}
      <p id="dsa_address_title" class="h2 font-w400 push-5">{$lng.lbl_warehouse_shipping_address}</p>
      <div id="dsa_warehouse_title">
        <strong>{$lng.lbl_warehouse}:&nbsp;</strong>
        {$warehouse.title}
      </div>
      {assign_ext var="userinfo[current_address]" value=$warehouse.main_address}
    {else}
      <p id="dsa_address_title" class="h2 font-w400 push-5">{$lng.lbl_shipping_address}</p>
    {/if}

    <address>{include file='admin/users/address_label.tpl' address=$userinfo.current_address}</address>
  </div>

  </div>


<!-- cw@order_address ] -->
<table>
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

{*
<div class="doc_products_title" id="doc_products_title" style="font-weight: bold; font-size: 14px; padding-top: 10px; text-align: center;">
{if $order.type eq 'S'}
{$lng.lbl_products_shipped}
{elseif $order.type ne 'I'}
{$lng.lbl_products_ordered}
{/if}
</div>
*}
