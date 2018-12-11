<table class="header">
<tr>
    <th>{$lng.lbl_warehouse}</th>
    <th>{$lng.lbl_total}</th>
</tr>
{foreach from=$warehouses item=warehouse}
<tr>
    <td>{$warehouse.title}</td>
    <td>{include file='common/currency.tpl' value=$warehouse.wa}</td>
</tr>
{/foreach}
</table>
