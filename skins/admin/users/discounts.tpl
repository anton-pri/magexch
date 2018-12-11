<form action="index.php?target={$current_target}&user={$user}" method="post" name="discounts_form">
<input type="hidden" name="mode" value="{$mode}" />
<input type="hidden" name="action" value="update" />

<table class="header">
<tr>
    <th width="1%">&nbsp;</th>
    <th>{$lng.lbl_discount} (%)</th>
    <th>{$lng.lbl_orderby}</th>
</tr>
{if $discounts}
{foreach from=$discounts item=discount}
<tr valign="top">
    <td align="center"><input type="checkbox" name="del[{$discount.discount_id}]" value="1"></td>
    <td><input type="text" name="update_discount[{$discount.discount_id}][discount]" value="{$discount.discount|formatprice}" size="15"></td>
    <td><input type="text" name="update_discount[{$discount.discount_id}][orderby]" value="{$discount.orderby}" size="5"></td>
</tr>
{/foreach}
{else}
<tr>
    <td align="center" colspan="3">{$lng.lbl_not_found}</td>
</tr>
{/if}
<tr valign="top">
    <td align="center">&nbsp;</td>
    <td><input type="text" name="update_discount[0][discount]" value="" size="15" /></td>
    <td><input type="text" name="update_discount[0][orderby]" value="" size="5" /></td>
</tr>
</table>

</form>

{include file='buttons/button.tpl' button_title=$lng.lbl_update href="javascript:cw_submit_form('discounts_form', 'update_discount');"}
{include file='buttons/button.tpl' button_title=$lng.lbl_delete href="javascript:cw_submit_form('discounts_form', 'delete_discount');"}
