{include file='common/navigation.tpl'}

<table width="100%" class="header">
<tr>
    <th>{$lng.lbl_order}</th>
    <th>{$lng.lbl_date}</th>
    <th>{$lng.lbl_customer}</th>
    <th>{$lng.lbl_quantity}</th>
    <th>{$lng.lbl_price}</th>
</tr>
{if $products}
{foreach from=$products item=product}
<tr{cycle values=', class="cycle"'}>
    <td align="center"><a href="index.php?target=docs_O&doc_id={$product.doc_id}" target=_blank>{$product.display_id}</a></td>
    <td>{$product.date|date_format:$config.Appearance.datetime_format}</td>
    <td>{$product.customer_id|user_title:$product.usertype:$product.doc_id}</td>
    <td align="center">{$product.amount}</td>
    <td align="center">{include file='common/currency.tpl' value=$product.price}</td>
</tr>
{/foreach}
{else}
<tr>
    <td align="center" colspan="13">{$lng.lbl_not_found}</td>
</tr>
{/if}
</table>

{include file='common/navigation.tpl'}
