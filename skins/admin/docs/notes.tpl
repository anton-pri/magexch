<div class="form-horizontal">

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

<form action="index.php?target={$current_target}&doc_id={$doc_id}" method="post" name="status_form">
<input type="hidden" name="action" value="status_change" />

{if $usertype ne "C" and $usertype ne "B"}

  <div class="form-group">
    <label class="col-xs-12">{$lng.lbl_status}</label>
    <div class="col-xs-12 col-md-4">
    {if $current_target eq "docs_I"}
	{assign var='doc_status_tpl' value="doc_i_status"}
    {else}
	{assign var='doc_status_tpl' value="doc_status"}
    {/if}

    {if $usertype eq 'A' || ($usertype eq 'P' and $doc.type eq 'D')}
       {include file="main/select/`$doc_status_tpl`.tpl" status=$doc.status mode='select' name="status" extra="class='form-control'"}
    {else}
       {include file="main/select/`$doc_status_tpl`.tpl" status=$doc.status mode='static' extra="class='form-control'"}
    {/if}
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
{/if}
</div>

<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_customer_notes}</label>
    <div class="col-xs-12 col-md-6"><textarea class="form-control" name="customer_notes" cols="70" rows="12">{$doc.info.customer_notes|escape:quotes}</textarea></div>
</div>

<!-- cw@doc_process_note -->

<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_ship_date}</label>
    <div class="col-xs-12 col-md-4"><input type="text" name="ship_time" class="form-control" value="{$doc.info.ship_time}"{if $usertype eq 'C'} readonly="readonly"{/if} /></div>
</div>

<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_tracking_number}</label>
    <div class="col-xs-12 col-md-4"><input type="text" name="tracking" class="form-control" value="{$doc.info.tracking}"{if $usertype eq 'C'} readonly="readonly"{/if} /></div>
</div>

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

<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_doc_details}</label>
    <div class="col-xs-12 col-md-6"><textarea id="details_edit" class="form-control" name="details" cols="70" rows="12" readonly>{$doc.info.details|escape:quotes}</textarea></div>
</div>
{/if}

{if $usertype ne "C" and $usertype ne "B"}
<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_doc_notes}</label>
    <div class="col-xs-12 col-md-6"><textarea name="notes" class="form-control" cols="70" rows="12">{$doc.info.notes|escape:quotes}</textarea></div>
</div>
{/if}

<!-- cw@doc_process_other -->

<div class="form-group">
  <div class="col-xs-12">
  {if $usertype eq 'A' || $usertype eq 'P'}
    {include file='buttons/button.tpl' href="javascript: cw_submit_form('status_form');" button_title=$lng.lbl_apply_changes acl=$page_acl style="btn btn-minw btn-default btn-green"}
    {if $usertype eq 'A'}
      {$lng.txt_change_doc_status}
    {else}
      {$lng.txt_apply_changes}
    {/if}
  {/if}

  {if $usertype eq 'A' && $doc.is_egood && $addons.egoods}
    {include file='buttons/button.tpl' href="index.php?target=`$current_target`&amp;mode=prolong_ttl&amp;doc_id=`$doc_id`" button_title=$lng.lbl_regenerate_ttl style="btn btn-minw btn-default btn-green"}
    {$lng.txt_prolong_ttl}
  {/if}
  </div>
</div>

<!-- cw@doc_process_buttons -->
</form>

</div>
