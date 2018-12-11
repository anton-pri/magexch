{capture name=section}
<form action="index.php?target={$current_target}" method="post" name="discounts_form">
<input type="hidden" name="action" value="update" />
<table class="header" width="100%">
<tr>
    <th>{$lng.lbl_del}</th>
    <th>{$lng.lbl_user_C}</th>
    <th>{$lng.lbl_discount} (%)</th>
    <th>{$lng.lbl_discount_code}</th>
    <th>{$lng.lbl_status}</th>
    <th width="10%">{$lng.lbl_take_from_my_account}</th>
</tr>
{if $discounts}
{foreach from=$discounts item=dis}
<tr{cycle values=" class='cycle',"}>
    <td><input type="checkbox" name="data[{$dis.coupon}][del]" value="1" /></td>
    <td>{include file="main/select/salesman_users.tpl" name="data[`$dis.coupon`][customer_id]" value=$dis.customer_id disabled=1}</td>
    <td><input type="text" name="data[{$dis.coupon}][discount]" value="{$dis.discount|formatprice}" size="10" disabled/></td>
    <td>{$dis.coupon}</td>
    <td>{include file='main/select/salesman_discount_status.tpl' static=1 value=$dis.status}</td>
    <td align="center"><input type="checkbox" name="data[{$dis.coupon}][from_account]" value="1" {if $dis.from_account}checked{/if} disabled/></td>
</tr>
{/foreach}
<tr><td colspan="6">{include file='buttons/button.tpl' href="javascript: cw_submit_form('discounts_form');" button_title=$lng.lbl_delete}</td></tr>
{else}
<tr>
    <td colspan="6" align="center">{$lng.lbl_not_found}</td>
</tr>
{/if}

<tr><td colspan="6">{include file="common/subheader.tpl" title=$lng.lbl_add_new}</td></tr>
<tr>
    <td>&nbsp;</td>
    <td>
        {include file='main/select/salesman_users.tpl' name="new_discount[customer_id]" value=$new_discount.customer_id}
    </td>
    <td><input type="text" name="new_discount[discount]" value="{$new_discount.discount}" size="10"/></td>
    <td><input type="text" name="new_discount[coupon]" value="{$new_discount.coupon}" size="25"/></td>
    <td>&nbsp;</td>
    <td align="center"><input type="checkbox" name="new_discount[from_account]" value="1" {if $new_discount.from_account}checked{/if}/></td>
</tr>
</table>
{include file='buttons/button.tpl' href="javascript:cw_submit_form('discounts_form', 'add');" button_title=$lng.lbl_add}

</form>
{/capture} 
{include file='common/section.tpl' title=$lng.lbl_discounts content=$smarty.capture.section}
