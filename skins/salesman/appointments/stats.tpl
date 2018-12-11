<div class="dialog_titl">{$lng.txt_summary_stats_note}</div>

<div class="input_field_1">
    <label>{$lng.lbl_total_sales}</label>
	{$stats_info.total_sales}
</div>
<div class="input_field_1">
	<label>{$lng.lbl_total_unapproved_sales}</label>
	{$stats_info.unapproved_sales}
</div>
<div class="input_field_1">
	<label>{$lng.lbl_pending_sale_commissions}</label>
	{include file='common/currency.tpl' value=$stats_info.pending_commissions}
</div>
<div class="input_field_1">
	<label>{$lng.lbl_approved_sale_commissions}</label>
	{include file='common/currency.tpl' value=$stats_info.approved_commissions}
</div>
<div class="input_field_1">
	<label>{$lng.lbl_paid_sales_commissions}</label>
	{include file='common/currency.tpl' value=$stats_info.paid_commissions}
</div>
