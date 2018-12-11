
 {cms service_code="process_order"} 
<div class="block-content block-content-narrow push-50">

<div style="background-color: #FCCB05;
    padding: 0px 20px 20px;
    max-width: 100%;
    overflow-x: visible;
 border: 8px solid #FCCB05;">




 <div class="adminnotes">{$lng.lbl_notes_process_order}</div>


<div style="background-color: white; padding: 20px 20px 1px; max-width: 100%; height:500px; overflow-x: visible;">



<div style="margin-left:15%; float:left;">




{* Print Documents Box *} 
<div style="width:300px; margin-left: auto; margin-right: auto;" class="block block-themed animated fadeIn">

{if $usertype eq 'A'} 
<div class="block-header bg-green"><h3 style="text-align: center;" class="block-title">Order Documents</h3></div>
{else}
<div class="block-header bg-green"><h3 style="text-align: center;" class="block-title">{$lng.lbl_print_documents}</h3></div>
{/if}

<div style="width:300px; padding-top:20px; text-align:center;" class="jasellerblock-content block-content-full block-content-narrow">

<a class="btn btn-minw btn-default btn-green push-20 " href="index.php?target={$current_target}&doc_id={$doc_id}&mode=print" target=_blank>{$lng.lbl_print_invoice}</a>
<br>
<a class="btn btn-minw btn-default btn-green push-20 " href="index.php?target={$current_target}&doc_id={$doc_id}&mode=print_label" target=_blank>{$lng.lbl_print_label}</a>

</div></div>


{* Required for Tracking info. text box functionality *}
<form action="index.php?target={$current_target}&doc_id={$doc_id}" method="post" name="status_form">
<input type="hidden" name="action" value="status_change" />


{* Required for Drop-Down status box functionality *}
 {if $usertype ne "C" and $usertype ne "B"} 
    <div class="col-xs-12 col-md-4">
    {if $current_target eq "docs_I"}
	{assign var='doc_status_tpl' value="doc_i_status"}
    {else}
	{assign var='doc_status_tpl' value="doc_status"}
    {/if}

 {/if}
 </div>


{* If user is Master Administrator, display drop-down of all status options *}
{if $usertype eq 'A'} 

<div class="block-header bg-green"><h3 style="text-align: center;" class="block-title">Change Status & Seller Tracking info.</h3></div>

<div style="width:400px; height:190px; text-align:center;" class="jasellerblock-content block-content-full block-content-narrow">


<br><br>

 <div style="float:left;">

       {include file="main/select/`$doc_status_tpl`.tpl" status=$doc.status mode='select' name="status" extra="class='form-control'"}</div>



<div style"float:right;">    {include file='buttons/button.tpl' href="javascript: cw_submit_form('status_form');" button_title=$lng.lbl_apply_changes acl=$page_acl style="btn btn-minw btn-default btn-green"}
 </div><br><br>
     {* {$lng.txt_change_doc_status} *}
<div style="float:left;" class="notes_title" id="dcn_notes_title"><strong>Seller Tracking info.:</strong></div>
    <div style"float:right;">   {$doc.info.notes}</div>
<div style="clear: both;"></div> 









{else}

{* If user is Seller, display Confirm Despatch Box *} 

<div style="width:400px; margin-left: auto; margin-right: auto;" class="block block-themed animated fadeIn">

<div class="block-header bg-green"><h3 style="text-align: center;" class="block-title">{$lng.lbl_confirm_despatch}</h3></div>

<div style="width:400px; height:190px; text-align:center;" class="jasellerblock-content block-content-full block-content-narrow">

<div style="float:left; padding-top:10px; color:black;">Service: {$info.shipping_label}</div><div style="float:right; padding-top:10px; color:black;"> Weight: {$info.weight|formatprice}{$lng.lbl_process_order_weight}<br><br></div>
<div style="clear: both;"></div> 

{if $doc.status eq 'P'}
<a class="btn btn-minw btn-default btn-green push-20" href="#"><h8 style="font-size:9pt; font-weight:300;">Posting Date: {$smarty.now|date_format:"%d-%m-%y"}</h8><br><h3>Confirm Fulll Despatch</h3></a>

{* Confirm Despatch Box cont. - Order Notes if Not Despatched *}  
<div style="float:left; margin-left:48px;"><textarea style="background-color:#FAFFBD; width:200px; resize: none; overflow: hidden; padding: 1px 12px; height:24px;" name="notes" class="form-control" placeholder="Tracking Info. (opptional)">{$doc.info.notes|escape:quotes}</textarea><a class="ProductBlue" style="font-weight:600;"></div><div style="margin-right:48px; padding-top:2px; float:right;"><a href="javascript: cw_submit_form('status_form');">Update</a></div>

</div>

{else}

<h3 style="color:black;">Order Has Been Despatched</h3>

{* NOTE TO CARTWORKS: LINE BELOW TO BE MADE DYNAMIC SO THAT DATE ORDER WAS MARKED AS DESPATCHED IS DISPLAYED *}
<h8 style="font-size:9pt; font-weight:300; color:red;">Date Posted: <b>Do This!</b></h8>

<br><br>

{* Confirm Despatch Box cont. - Order Notes if Despatched *}  
<div style="float:left; margin-left:48px;"><textarea style="background-color:#FAFFBD; width:200px; resize: none; overflow: hidden; padding: 1px 12px; height:24px;" name="notes" class="form-control" placeholder="Tracking Info. (opttional)">{$doc.info.notes|escape:quotes}</textarea><a class="ProductBlue" style="font-weight:600;"></div><div style="margin-right:48px; padding-top:2px; float:right;"><a href="javascript: cw_submit_form('status_form');">Update</a></div>

</div>

{/if}
{/if}


</div></div>







<div style="float:right;"><a href="#"><img src="{$AltImagesDir}/admin/Need_Help_Selling_Guide.gif" width="123" height="183"></a></div>






<div style="margin-left:15%; float:left;">





<script type="text/javascript">
<!--
var details_mode = false;
var details_fields_labels = new Object();
{foreach from=$doc_details_fields_labels key=dfield item=dlabel}
details_fields_labels["{$dfield|escape:javascript}"] = "{$dlabel|escape:javascript}";
{/foreach}
-->
</script>
{*include_once_src file='main/include_js.tpl' src='main/history_doc.js'*}















<div>


<!-- Print invoice link [ -->

{* <li>
            <a class="button" href="index.php?target={$current_target}&doc_id={$doc_id}&mode=print" target=_blank><i class="si si-doc"></i> {$lng.lbl_html_format}</a>
            <!-- PDF converter does not work reliable
             <a href="index.php?target={$current_target}&doc_id={$doc_id}&mode=print_pdf" target=_blank>{$lng.lbl_pdf_format}</a>
            -->
            
      </li> *}

<!-- Print invoice link ] -->
</div>

<!-- cw@doc_process_status -->

{if $quotes}
<!-- cw@doc_process_quotes [ -->
<table class="header_bordered">
<tr>
    <th>{$lng.lbl_total}</th>
    <th>{$lng.lbl_paid}</th>
    <th>{$lng.lbl_expiration_date}</th>
    <th>{$lng.lbl_expiration_notification_before}</th>
    <th>{$lng.lbl_expiration_notification_after}</th>
    <th>{$lng.lbl_status}</th>
</tr>
{foreach from=$quotes item=quote}
<tr>
    <td>{include file='common/currency.tpl' value=$quote.total}</td>
    <td>
        <input type="text" name="quote[{$quote.doc_quote_id}][paid]" value="{$quote.paid|formatprice}" size="10" /></td>
    <td>
    {if $quote.exp_date}
    {$quote.exp_date|date_format:$config.Appearance.datetime_format}
    {else}
    {include file='main/select/date.tpl' name="quote[`$quote.doc_quote_id`][exp_date]" value=$quote.exp_date}
    {/if}
    </td>
    <td>{include file='main/select/date.tpl' name="quote[`$quote.doc_quote_id`][exp_mail_before]" value=$quote.exp_mail_before}</td>
    <td>{include file='main/select/date.tpl' name="quote[`$quote.doc_quote_id`][exp_mail_after]" value=$quote.exp_mail_after}</td>
    <td>{include file='main/select/doc_status.tpl' status=$quote.status mode='select' name="quote[`$quote.doc_quote_id`][status]"}</td>

    <!-- cw@doc_process_quote -->

</tr>
{/foreach}
</table>
<!-- cw@doc_process_quotes ] -->

</div>








{* Customer Notes *}
{*
<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_customer_notes}</label>
    <div class="col-xs-12 col-md-6"><textarea class="form-control" name="customer_notes" cols="70" rows="12">{$doc.info.customer_notes|escape:quotes}</textarea></div>
</div>
*}




<!-- cw@doc_process_note -->

{*
<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_ship_date}</label>
    <div class="col-xs-12 col-md-4"><input type="text" name="ship_time" class="form-control" value="{$doc.info.ship_time}"{if $usertype eq 'C'} readonly="readonly"{/if} /></div>
</div> 

<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_tracking_number}</label>
    <div class="col-xs-12 col-md-4"><input type="text" name="tracking" class="form-control" value="{$doc.info.tracking}"{if $usertype eq 'C'} readonly="readonly"{/if} /></div>
</div>
*}





<!-- cw@doc_process_shipping -->

{if $doc.extra.ip}
<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_ip_address}</label>
    <div class="col-xs-12 col-md-4">
    {$doc.extra.ip}{if $doc.extra.proxy_ip ne ''} ({$doc.extra.proxy_ip}){/if}
    {if $addons.stop_list}
        {if $doc.blocked eq 'Y'}
<font class="field_error">{$lng.lbl_ip_address_blocked}</font>
        {else}
{include file='buttons/button.tpl' href="index.php?target=doc&amp;mode=block_ip&amp;doc_id=`$doc_id`" button_title=$lng.lbl_block_ip_address}
        {/if}
    {/if}
    </div>
{/if}





{* Payment Information *} {*
<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_doc_details}</label>
    <div class="col-xs-12 col-md-6"><textarea id="details_edit" class="form-control" name="details" cols="70" rows="12" readonly>{$doc.info.details|escape:quotes}</textarea></div>
</div> *}
{/if}







<!-- cw@doc_process_other -->






<div class="form-group">
  <div class="col-xs-12">


  {if $usertype eq 'A' && $doc.is_egood && $addons.egoods}
    {include file='buttons/button.tpl' href="index.php?target=`$current_target`&amp;mode=prolong_ttl&amp;doc_id=`$doc_id`" button_title=$lng.lbl_regenerate_ttl style="btn btn-minw btn-default btn-green"}
    {$lng.txt_prolong_ttl}
  {/if}
  </div>
</div>

<!-- cw@doc_process_buttons -->
</form>

</div></div></div>

<br><br>


<div style="background-color: #F8740A;
    padding: 0px 20px 20px;
    max-width: 100%;
    overflow-x: visible;
 border: 8px solid #F8740A;">




 <div class="adminnotes">{$lng.lbl_notes_process_order_2}</div>

<div style="background-color: white; padding: 20px 20px 1px; max-width: 100%; height:1200px; overflow-x: visible;">





<div style="float:left;">
<div style="width:450px; margin-left: 75px; margin-right: auto;" class="block block-themed animated fadeIn">
<iframe allowtransparency="true" src="http://form.jotformpro.com/form/62737334950965?orderNumber={$doc.display_id}" frameborder="0" style="width:450px; height:352px; border:none;" scrolling="no">
</iframe>
<hr style="border: 0; height: 1px; background: #333; background-image: linear-gradient(to right, #ccc, #333, #ccc);">
</div>
<div style="width:450px; margin-left: 75px; margin-right: auto;" class="block block-themed animated fadeIn">
<iframe allowtransparency="true" src="http://form.jotformpro.com/form/62778022935967?orderNumber={$doc.display_id}" frameborder="0" style="width:450px; height:327px; border:none;" scrolling="no">
</iframe>
<hr style="border: 0; height: 1px; background: #333; background-image: linear-gradient(to right, #ccc, #333, #ccc);">
</div>
<div style="width:350px; margin-left: 125px; margin-right: auto;" class="block block-themed animated fadeIn">
<iframe allowtransparency="true" src="http://form.jotformpro.com/form/62804053507956?orderNumber={$doc.display_id}" frameborder="0" style="width:350px; height:384px; border:none;" scrolling="no">
</iframe>
</div>


</div>


<div style="float:right; vertical-align: top;"><a href="#"><img src="{$AltImagesDir}/admin/Cant_complete_order.gif" width="123" height="315"></a></div>






</div></div>


