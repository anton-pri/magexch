<form action="index.php?target={$current_target}&user={$user}" method="post" name="taxes_form">
<input type="hidden" name="mode" value="{$mode}" />
<input type="hidden" name="action" value="update_taxes" />

<table class="header" width="100%">
<tr>
    <th width="1%">&nbsp;</th>
    <th>{$lng.lbl_value}</th>
    <th>{$lng.lbl_title}</th>
    <th>{$lng.lbl_orderby}</th>
</tr>
{if $taxes}
{foreach from=$taxes item=tax}
<tr valign="top">
    <td align="center"><input type="checkbox" name="del[{$tax.tax_id}]" value="1"></td>
    <td><input type="text" name="update_tax[{$tax.tax_id}][value]" value="{$tax.value}" size="4"/></td>
    <td><input type="text" name="update_tax[{$tax.tax_id}][title]" value="{$tax.title}" size="15"></td>
    <td><input type="text" name="update_tax[{$tax.tax_id}][orderby]" value="{$tax.orderby}" size="5"/></td>
</tr>
{/foreach}
{else}
<tr>
    <td align="center" colspan="13">{$lng.lbl_not_found}</td>
</tr>
{/if}
<tr valign="top">
    <td align="center">&nbsp;</td>
    <td><input type="text" name="update_tax[0][value]" value="" size="4" /></td>
    <td><input type="text" name="update_tax[0][title]" value="" size="15" /></td>
    <td><input type="text" name="update_tax[0][orderby]" value="" size="5" /></td>
</tr>
</table>
</form>

{include file='buttons/button.tpl' button_title=$lng.lbl_update href="javascript:cw_submit_form('taxes_form');"}
{include file='buttons/button.tpl' button_title=$lng.lbl_delete href="javascript:cw_submit_form('taxes_form', 'delete_taxes');"}

{if $js_update}
<script language="javascript">
window.parent.ajax_update_taxes_list();
</script>
{/if}
