<form action="index.php?target={$current_target}" method="post" name="payment_categories_form">
<input type="hidden" name="mode" value="{$mode}" />
<input type="hidden" name="action" value="update" />

<table class="header" width="100%">
<tr>
    <th width="1%">&nbsp;</th>
    <th>{$lng.lbl_title}</th>
</tr>
{foreach from=$payment_categories item=cat}
<tr{cycle values=', class="cycle"'}>
    <td align="center"><input type="checkbox" name="del[{$cat.payment_category_id}]" value="1"></td>
    <td><input type="text" name="payment_categories[{$cat.payment_category_id}][title]" value="{$cat.title|escape}" size="50"></td>
</tr>
{foreachelse}
<tr>
    <td align="center" colspan="13">{$lng.lbl_not_found}</td>
</tr>
{/foreach}
<tr>
    <td colspan="3">{include file='common/subheader.tpl' title=$lng.lbl_add_new}</td>
</tr>
<tr>
    <td>&nbsp;</td>
    <td><input type="text" name="payment_categories[0][title]" value="" size="50"></td>
</tr>
</table>
</form>

{include file='buttons/button.tpl' button_title=$lng.lbl_add_update href="javascript:cw_submit_form('payment_categories_form');" acl='__2503'}
{if $payment_categories}
{include file='buttons/button.tpl' button_title=$lng.lbl_delete href="javascript:cw_submit_form('payment_categories_form', 'delete');" acl='__2503'}
{/if}
