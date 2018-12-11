{capture name=section}
<table cellpadding="0" cellspacing="2">
<tr>
    <td nowrap="nowrap"><b>{$lng.lbl_pending_sale_commissions}:</b></td>
    <td>&nbsp;</td>
    <td>{include file='common/currency.tpl' value=$stats_info.pending_commissions}</td>
</tr>
<tr>
    <td nowrap="nowrap"><b>{$lng.lbl_approved_sale_commissions}:</b></td>
    <td>&nbsp;</td>
    <td>{include file='common/currency.tpl' value=$stats_info.approved_commissions}</td>
</tr>
<tr>
    <td nowrap="nowrap"><b>{$lng.lbl_paid_sales_commissions}:</b></td>
    <td>&nbsp;</td>
    <td>{include file='common/currency.tpl' value=$stats_info.paid_commissions}</td>
</tr>
</table>
{/capture}
{include file="common/section.tpl" content=$smarty.capture.section title=$lng.lbl_salesman_commission extra='width="100%"'}
