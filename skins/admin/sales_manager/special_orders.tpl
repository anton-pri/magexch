{if $salesman_orders}
<br/>
{capture name=section}
<table >
<tr class="TableHead">
    <td>{$lng.lbl_doc_id}</td>
    <td>{$lng.lbl_salesman}</td>
    <td>{$lng.lbl_status}</td>
</tr>
{foreach from=$salesman_orders item=order}
<tr>
    <td><a href="index.php?target=salesman_order&doc_id={$order.id}">#{$order.id}</a></td>
    <td>{$order.salesman}</td>
    <td>{if $order.status eq 0}{$lng.lbl_pending}{elseif $order.status eq 1}{$lng.lbl_approved}{else}{$lng.lbl_purchased}{/if}</td>
</tr>
{/foreach}
</table>
{/capture}
{include file="common/section.tpl" title=$lng.lbl_salesman_orders content=$smarty.capture.section extra='width="100%"'}
{/if}
