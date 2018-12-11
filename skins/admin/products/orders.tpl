{include file='common/navigation.tpl'}

<table width="100%" class="header">
<tr>
    <th width="10%">{$lng.lbl_order}</th>
    <th width="10%">{$lng.lbl_date}</th>
    <th width="10%">{$lng.lbl_total}</th>
</tr>
{if $orders}
{foreach from=$orders item=order}
<tr{cycle values=', class="cycle"'}>
    <td align="center"><a href="index.php?target=docs_{$order.type}&doc_id={$order.doc_id}" target=_blank>{$order.display_id}</a></td>
    <td>{$order.date|date_format:$config.Appearance.datetime_format}</td>
    <td align="center">{include file='common/currency.tpl' value=$order.total}</td>
</tr>
{/foreach}
{else}
<tr>
    <td align="center" colspan="13">{$lng.lbl_not_found}</td>
</tr>
{/if}
</table>

{include file='common/navigation.tpl'}
