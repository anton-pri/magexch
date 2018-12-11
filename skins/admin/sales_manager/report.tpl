<div class="dialog_title">{$lng.txt_salesman_accounts_note}</div>
<div class="dialog_title">{$lng.txt_salesman_accounts_comment}</div>

<a href="index.php?target=salesman_report">{$lng.lbl_all_accounts}</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="index.php?target=salesman_report&use_limit=Y">{$lng.lbl_accounts_ready_to_be_paid}</a><br />
<br />
{if $result ne ''}
<form action="index.php?target=salesman_report" method="post" name="report_form">
<input type="hidden" name="action" value="paid" />

<table class="header" width="100%">
<tr>
	<th rowspan="2">{$lng.lbl_salesman}</th>
    <th colspan="4" align="center">{$lng.lbl_commissions}</th>
{if $is_paid eq 'Y'}
    <th rowspan="2" align="center">{$lng.lbl_ready_to_be_paid}</th>
{/if}
</tr>
<tr>
    <th>{$lng.lbl_paid}</th>
    <th>{$lng.lbl_approved}</th>
    <th>{$lng.lbl_pending}</th>
	<th align="center">{$lng.lbl_min_limit}</th>
</tr>
{foreach from=$result item=v}
<tr>
	<td>{$v.firstname} {$v.lastname}</td>
	<td align="right" nowrap="nowrap">{include file='common/currency.tpl' value=$v.sum_paid}</td>
	<td align="right" nowrap="nowrap">{include file='common/currency.tpl' value=$v.sum_nopaid}</td>
    <td align="right" nowrap="nowrap">{include file='common/currency.tpl' value=$v.sum}</td>
    <td align="right" nowrap="nowrap">{include file='common/currency.tpl' value=$v.min_paid}</td>
{if $is_paid eq 'Y'}
	<td align="center">{if $v.is_paid eq 'Y'}<input type="checkbox" name="paid[{$v.customer_id}]" value="Y" />{/if}</td>
{/if}
</tr>
{/foreach}
</table>
{if $is_paid eq 'Y'}
{include file='buttons/button.tpl' button_title=$lng.lbl_paid href="javascript: cw_submit_form('report_form')"}
{/if}
</form>

<br />

<form action="index.php?target=salesman_report" method="post" name="export_form">
<input type="hidden" name="action" value="export" />

{include file="common/subheader.tpl" title=$lng.lbl_export_salesman_account}
<div class="input_field_1">
	<label>{$lng.lbl_csv_delimiter}</label>
	{include file='main/select/delimiter.tpl'}
</div>
{include file='buttons/button.tpl' button_title=$lng.lbl_export href="javascript: cw_submit_form('export_form')"}
</form>
{/if}
