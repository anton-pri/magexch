{capture name=section}
{if $last_orders}
<table class="header" width="100%">
<tr>
    <th>{$lng.lbl_customer}</th>
    <th>{$lng.lbl_customer_type}</th>
    <th>{$lng.lbl_order_status}</th>
    <th>{$lng.lbl_order_total}</th>
    <th>{$lng.lbl_commission}</th>
</tr>
{foreach from=$last_orders item=order}
<tr>
    <td>{$order.userinfo.customer_id|user_title:$order.userinfo.usertype:$order.userinfo.doc_id}</td>
    <td>{lng name="lbl_user_`$order.userinfo.usertype`"}</td>
    <td><a href="index.php?target=docs_{$order.type}&doc_id={$order.doc_id}">{include file="main/select/doc_status.tpl" status=$order.status mode="static"}</a></td>
    <td>{include file='common/currency.tpl' value=$order.info.total}</td>
    <td>{include file='common/currency.tpl' value=$order.info.salesman_comission|default:0}</td>
</tr>
{/foreach}
</table>
{else}
<center>{$lng.lbl_not_found}</center>
{/if}
{/capture}
{include file='common/section.tpl' title=$lng.lbl_last_orders content=$smarty.capture.section}
