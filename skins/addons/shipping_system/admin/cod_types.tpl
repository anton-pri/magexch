{*include file='common/page_title.tpl' title=$lng.lbl_cod_types*}
{capture name=section}
{capture name=block}

<p>{$lng.lbl_cod_explanation}</p>

<div class="box">

<form action="index.php?target={$current_target}" method="post" name="cod_types_form">
<input type="hidden" name="action" value="update" />
<input type="hidden" name="iframe" value="{$iframe}">

<table class="table table-striped dataTable vertical-center">
<thead>
<tr>
    <th width="1%">{$lng.lbl_delete}</th>
    <th>{$lng.lbl_cod_type}</th>
    <th>{$lng.lbl_title}</th>
    <th>{$lng.lbl_orderby}</th>
</tr>
</thead>
{if $cod_types}
{foreach from=$cod_types item=cod_type}
<tr valign="top">
    <td align="center"><input type="checkbox" name="del[{$cod_type.cod_type_id}]" value="1"></td>
    <td>{include file="main/select/leaving_cod_type.tpl" name="update_types[`$cod_type.cod_type_id`][leaving_type]" value=$cod_type.leaving_type}</td>
    <td><input type="text" class="form-control" name="update_types[{$cod_type.cod_type_id}][title]" value="{$cod_type.title}" size="25"></td>
    <td align="center"><input type="text" class="form-control" name="update_types[{$cod_type.cod_type_id}][orderby]" value="{$cod_type.orderby}" size="5"/></td>
</tr>
{/foreach}
{else}
<tr>
    <td align="center" colspan="13">{$lng.lbl_not_found}</td>
</tr>
{/if}

<tr valign="top" >
    <td colspan="4">{include file='common/subheader.tpl' title=$lng.lbl_add_new}</td>

</tr>

<tr valign="top">
    <td align="center">&nbsp;</td>
    <td>{include file="main/select/leaving_cod_type.tpl" name="update_types[0][leaving_type]"}</td>
    <td><input type="text" class="form-control" name="update_types[0][title]" value="" size="25" /></td>
    <td align="center"><input type="text" class="form-control" name="update_types[0][orderby]" value="" size="5" /></td>
</tr>
</table>
</form>

</div>

<div class="buttons">
    {include file='admin/buttons/button.tpl' button_title=$lng.lbl_update href="javascript:cw_submit_form(document.cod_types_form, 'update');" js_to_href='Y' style="btn-green push-20"}
    {include file='admin/buttons/button.tpl' button_title=$lng.lbl_delete href="javascript:cw_submit_form(document.cod_types_form, 'delete');" js_to_href='Y' style="btn-danger push-20"}
</div>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block}
{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_cod_types}
{if $js_update}
<script language="javascript">
window.parent.ajax_update_cod_types_list();
</script>
{/if}
