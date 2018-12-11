{if $is_title}
<th align="middle">{$lng.lbl_seller_payment}</th>
{else}
<td align="middle">{include file='common/currency.tpl' value=$order.owed}</td>
{/if}
