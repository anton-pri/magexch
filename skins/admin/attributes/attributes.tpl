{*include file='common/page_title.tpl' title=$lng.lbl_features*}
{capture name=section}

{include file='main/select/edit_lng.tpl' script="index.php?target=`$current_target`&mode=att"}
{capture name=block}

{include file='admin/attributes/attributes_filter.tpl'}
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block extra='width="100%"'}

{capture name=block2}

{include file='common/navigation.tpl'}

<div class="box">
<form action="index.php?target={$current_target}&mode=att" method="post" name="atts_form">
<input type="hidden" name="action" value="update_att" />
<input type="hidden" name="page" value="{$page}" />

<table width="100%" class="table table-striped dataTable vertical-center">
<thead>
<tr>
    {if $attributes}<th width="5%" align="center"><input type='checkbox' class='select_all' class_to_select='attribute_item' /></th>{/if}
    <th width="25%">{include file='common/sort.tpl' title=$lng.lbl_attribute field='name'}</th>
    <th width="25%">{include file='common/sort.tpl' title=$lng.lbl_field field='field'}</th>
    <th width="15%">{include file='common/sort.tpl' title=$lng.lbl_addon field='addon'}</th>
    <th width="10%">{include file='common/sort.tpl' title=$lng.lbl_item_type field='item_type'}</th>
    <th width="10%">{include file='common/sort.tpl' title=$lng.lbl_orderby field='orderby'}</th>
    <th width="5%">{$lng.lbl_active}</th>
    <th>&nbsp;</th>
</tr>
</thead>
{if $attributes}
{foreach from=$attributes item=v}
<tr{cycle values=", class='cycle'"}>
    <td align="center">{if !($v.protection & $smarty.const.ATTR_PROTECTION_DELETE)}<input type="checkbox" name="to_delete[{$v.attribute_id}]" class="attribute_item" />{/if}</td>
    <td><a href="index.php?target={$current_target}&mode=att&attribute_id={$v.attribute_id}">{$v.name}</a></b></td>
    <td><a href="index.php?target={$current_target}&mode=att&attribute_id={$v.attribute_id}">{$v.field}</a></b></td>
    <td>{$v.addon}</td>
    <td align="center">{$v.item_type}</td>
    <td align="center"><input type="text" class="form-control" name="posted_data[{$v.attribute_id}][orderby]" size="5" value="{$v.orderby}" /></td>
    <td align="center">
        <input type="hidden" name="posted_data[{$v.attribute_id}][active]" value="0" />
        <input type="checkbox" name="posted_data[{$v.attribute_id}][active]" value="1"{if $v.active} checked="checked"{/if} />
    </td>
    <td><a href="index.php?target=attributes&mode=att&attribute_id={$v.attribute_id}{if $page}&amp;page={$page}{/if}" class="btn btn-xs btn-default">{$lng.lbl_modify}</a></td>
</tr>
{/foreach}
{else}
<tr>
    <td colspan="6" align="center">{$lng.txt_no_attributes}</td>
</tr>
{/if}
</table>
</form>
</div>

{include file='common/navigation.tpl'}

<div class="buttons">
{if $attributes}
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_update href="javascript:cw_submit_form('atts_form')" acl='__1201' style="btn-green push-5-r push-20"}
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_delete_selected onclick="javascript: cw_submit_form('atts_form', 'delete_att');" acl='__1201' style="btn-danger push-5-r push-20"}
{/if}
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_add_new href="index.php?target=attributes&mode=att&attribute_id=" acl='__1201' style="btn-green push-5-r push-20"}
</div>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block2 extra='width="100%"'}
{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_features}
