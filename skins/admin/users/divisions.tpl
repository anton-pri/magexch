<form action="index.php?target={$current_target}" method="post" name="divisions_form">
<input type="hidden" name="action" value="update" />
<input type="hidden" name="division_id" id="division_id" value="" />

<table class="header" width="100%">
<tr>
{if $accl.__03}
    <th width="1%">&nbsp;</th>
{/if}
    <th>{$lng.lbl_title}</th>
    <th>{$lng.lbl_address}</th>
{* kornev, disabled for now *}
{*if $accl.__03}
    <th>{$lng.lbl_reset}</th>
{/if*}
{if $addons.pos}
    <th>{$lng.lbl_backorder}</th>
{/if}
</tr>
{foreach from=$divisions item=division}
<tr{cycle values=", class='cycle'"}>
{if $accl.__03}
    <td align="center"><input type="checkbox" name="del[{$division.division_id}]" value="1"></td>
{/if}
    <td>
        <input type="text" name="divisions[{$division.division_id}][title]" value="{$division.title|escape}" size="50" />
        <input type="hidden" name="divisions[{$division.division_id}][address_id]" value="{$division.address_id}" />
    </td>
{*if $accl.__03}
    <td>{include file='main/visiblebox_link.tpl' mark="division_address_`$division.division_id`"}</td>
{/if*}
    <td>{include file='main/visiblebox_link.tpl' mark="division_reset_`$division.division_id`"}</td>
{if $addons.EStore || $addons.pos}
    <td>{include file='main/select/backorder.tpl' name="divisions[`$division.division_id`][backorder][]" value=$division.backorder}</td>
{/if}
</tr>
<tr id="division_address_{$division.division_id}" style="display:none">
    <td colspan="3">{include file='main/users/sections/address.tpl' included=1 name_prefix="divisions[`$division.division_id`][address]" address=$division.address}</td>
</tr>
{*
<tr id="division_reset_{$division.division_id}" style="display:none">
    <td colspan="3">
<div class="input_field_1">
    <label>{$lng.lbl_category}</label>
    {include file='main/select/category.tpl' name="reset[`$division.division_id`][category_id]" value='' is_please_select=1}
</div>
{include file='buttons/button.tpl' button_title=$lng.lbl_reset_amount href="javascript: document.getElementById('division_id').value = '`$division.division_id`'; cw_submit_form('divisions_form', 'reset_amount');" acl='__03'}
{include file='buttons/button.tpl' button_title=$lng.lbl_reset_all_amount href="javascript: document.getElementById('division_id').value = '`$division.division_id`'; cw_submit_form('divisions_form', 'reset_all_amount');" acl='__03'}
    </td>
</tr>
*}
{foreachelse}
<tr>
    <td align="center" colspan="13">{$lng.lbl_not_found}</td>
</tr>
{/foreach}
{if $accl.__03}
<tr>
    <td colspan="3">{include file='common/subheader.tpl' title=$lng.lbl_add_new}</td>
</tr>
<tr>
    <td>&nbsp;</td>
    <td><input type="text" name="divisions[0][title]" value="" size="50"></td>
</tr>
{/if}
</table>
</form>

{if $divisions}
{include file='buttons/button.tpl' button_title=$lng.lbl_delete href="javascript:cw_submit_form('divisions_form', 'delete');" acl='__03'}
{/if}
{include file='buttons/button.tpl' button_title=$lng.lbl_add_update href="javascript:cw_submit_form('divisions_form');" acl='__03'}
