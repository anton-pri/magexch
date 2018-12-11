<form action="index.php?target=classes&mode=options&foption_id={$foption_id}" method="post" name="variants_form">
<input type="hidden" name="action" value="">

<table class="header">
<tr>
    <th>{$lng.lbl_del}</th>
    <th>{$lng.lbl_variant}</th>
</tr>
{if $variants}
{foreach from=$variants item=variant}
<tr>
    <td width="1%"><input type="checkbox" name="vids[{$variant.option_variant_id}]" value="Y" /></td>
    <td><input type="text" name="variants[{$variant.option_variant_id}]" value="{$variant.variant|escape}" size="20" /></td>
</tr>
{/foreach}
<tr>
    <td colspan="2">
        {include file='buttons/button.tpl' button_title=$lng.lbl_delete_selected_variants href="javascript: cw_submit_form(document.variants_form, 'delete');"}
        {include file='buttons/button.tpl' button_title=$lng.lbl_update href="javascript: cw_submit_form(document.variants_form, 'update');"}
    </td>
</tr>
{else}
<tr>
    <td colspan="2" align="center">{$lng.lbl_not_found}</td>
</tr>
{/if}
<tr>
    <td colspan="2">{include file="common/subheader.tpl" title=$lng.lbl_add_option_variant}</td>
</tr>
<tr>
    <td>&nbsp;</td>
    <td><input type="text" name="new_variant" value="" size="20" /></td>
</tr>
<tr>
    <td colspan="2">
        {include file='buttons/button.tpl' button_title=$lng.lbl_add href="javascript: cw_submit_form(document.variants_form, 'add');"}
    </td>
</tr>
</table>

</form>
