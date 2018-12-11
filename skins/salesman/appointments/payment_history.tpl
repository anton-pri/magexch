<div class="dialog_title">{$lng.txt_payment_history_note}</div>
 
{capture name=section}
<form method="post" action="index.php?target=payment_history" name="search_form">
<input type="hidden" name="action" value="go" />
<div class="input_field_1">
	<label>{$lng.lbl_date}</label>
    {include file='main/select/date.tpl' name='posted_data[start_date]' value=$search_prefilled.start_date} -
    {include file='main/select/date.tpl' name='posted_data[end_date]' value=$search_prefilled.end_date}
</div>
{include file="buttons/search.tpl" href="javascript: cw_submit_form(document.search_form)"}
</form>
{/capture}
{include file='common/section.tpl' content=$smarty.capture.section title=$lng.lbl_search}

{if $smarty.get.mode}

{include file="common/navigation.tpl"}
{capture name=section}
{if !$payments}
{$lng.lbl_no_records_found}<br />
{else}
<table cellpadding="2" cellspacing="1">
<tr class="TableHead">
	<td><b>{$lng.lbl_date}</b></td>
	<td><b>{$lng.lbl_amount}</b></td>
</tr>
{section name=pi loop=$payments}
<tr>
	<td>{$payments[pi].add_date|date_format:$config.Appearance.datetime_format}</td>
	<td>{include file='common/currency.tpl' value=$payments[pi].commissions}</td>
</tr>
{/section}
</table>
{/if}
<br />
<b>{$lng.lbl_paid_total}: {include file='common/currency.tpl' value=$paid_total}</b>
<br />
{/capture}
{include file="common/section.tpl" content=$smarty.capture.section title=$lng.lbl_payment_history}
{/if}
