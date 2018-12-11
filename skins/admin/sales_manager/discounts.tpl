<form action="index.php?target=discounts" method="post" name="discounts_frm">
<input type="hidden" name="action" value="update" />
<table class="header" width="100%">
<tr>
    <th>{$lng.lbl_del}</th>
    <th>{$lng.lbl_salesman}</th>
    <th>{$lng.lbl_customer}</th>
    <th>{$lng.lbl_discount} (%)</th>
    <th>{$lng.lbl_discount_code}</th>
    <th>{$lng.lbl_status}</th>
    <th>{$lng.lbl_take_from_my_account}</th>
</tr>
{if $discounts}
{foreach from=$discounts item=dis}
<tr{cycle values=', class="cycle"'}>
    <td>
        <input type="checkbox" name="data[{$dis.coupon}][del]" value="1" />
        <input type="hidden" name="data[{$dis.coupon}][salesman_customer_id]" value="{$dis.salesman_customer_id}" />
    </td>
    <td>{$dis.salesman_customer_id|user_title:'B'}</td>
    <td>{$dis.customer_id|user_title}</td>
    <td><input type="text" name="data[{$dis.coupon}][discount]" value="{$dis.discount}" size="10" disabled/></td>
    <td>{$dis.coupon}</td>
    <td>{include file="main/select/salesman_discount_status.tpl" name="data[`$dis.coupon`][status]" value=$dis.status}</td>
    <td><input type="checkbox" name="data[{$dis.coupon}][from_account]" value="1" {if $dis.from_account}checked{/if} disabled/></td>
</tr>
{/foreach}
<tr><td colspan="6">{include file='buttons/button.tpl' href="javascript: cw_submit_form('discounts_frm');" button_title=$lng.lbl_update_delete acl='__1111'}</td></tr>
{else}
<tr>
    <td colspan="6" align="center">{$lng.lbl_not_found}</td>
</tr>
{/if}
</table>

</form>
