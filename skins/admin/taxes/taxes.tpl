{*include file='common/page_title.tpl' title=$lng.lbl_taxes*}
{capture name=section}
{capture name=block}

<div class="dialog_title">{$lng.txt_taxes_general_note}</div>

<form action="index.php?target=taxes" method="post" name="taxes_form">
<input type="hidden" name="action" value="update" />

<div class="box">
<table class="table table-striped dataTable vertical-center" width="100%">
<thead>
<tr>
	{if $taxes}<th><input type='checkbox' class='select_all' class_to_select='taxes_item' /></th>{/if}
	<th width="30%">{$lng.lbl_tax_name}</th>
	<th width="30%" class="text-center">{$lng.lbl_tax_apply_to}</th>
	<th width="20%" class="text-center">{$lng.lbl_tax_priority}</th>
	<th width="20%" class="text-center">{$lng.lbl_enabled}</th>
</tr>
</thead>
{if $taxes}
{section name=tax loop=$taxes}
<tr {cycle values=", class='cycle'"}>
	<td><input type="checkbox" name="to_delete[{$taxes[tax].tax_id}]" class="taxes_item" /></td>
	<td class="FormButton"><a href="index.php?target=taxes&tax_id={$taxes[tax].tax_id}">{$taxes[tax].tax_name}</a></td>
	<td align="center"><a href="index.php?target=taxes&tax_id={$taxes[tax].tax_id}">{$taxes[tax].formula}</a></td>
	<td align="center"><input type="text" class="form-control" size="5" name="posted_data[{$taxes[tax].tax_id}][tax_priority]" value="{$taxes[tax].priority}" /></td>
	<td align="center">{include file='admin/select/yes_no.tpl' name="posted_data[`$taxes[tax].tax_id`][active]" value=$taxes[tax].active}</td>
</tr>

{/section}
{else}
<tr>
    <td colspan="{$colspan}" align="center">{$lng.txt_no_taxes_defined}</td>
</tr>
{/if}
</table>

</div>
<div class="buttons">
{if $taxes}
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_update href="javascript: cw_submit_form('taxes_form');" style="btn-green push-20 push-5-r"}
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_delete_selected href="javascript: cw_submit_form('taxes_form', 'delete');" style="btn-danger push-20 push-5-r"}
{/if}
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_add_new href="index.php?target=taxes&mode=add" style="btn-green push-20 push-5-r"}
</div>
</form>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block}
{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_taxes local_config='Taxes'}

