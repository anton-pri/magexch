{capture name=section}
{if $salesman_orders}
<table >
<tr class="TableHead">
    <td>{$lng.lbl_doc_id}</td>
    <td>{$lng.lbl_customer}</td>
    <td>{$lng.lbl_status}</td>
</tr>
{foreach from=$salesman_orders item=order}
<tr>
    <td><a href="index.php?target=salesman_order&doc_id={$order.id}&orders=1">#{$order.id}</a></td>
    <td>{$order.customer}</td>
    <td>{if $order.status eq 0}{$lng.lbl_pending}{elseif $order.status eq 1}{$lng.lbl_approved}{else}{$lng.lbl_purchased}{/if}</td>
</tr>
{/foreach}
</table>
{else}
<center>{$lng.lbl_not_found}</center>
{/if}
{/capture}
{include file="common/section.tpl" title=$lng.lbl_salesman_orders content=$smarty.capture.section extra='width="100%"'}
