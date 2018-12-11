{*include file='common/page_title.tpl' title=$lng.lbl_feature_classes*}
{capture name=section}
{include file='common/navigation.tpl'}

{capture name=block}
<form action="index.php?target={$current_target}&mode=att" method="post" name="atts_classes_form">
<input type="hidden" name="action" value="update_class" />
<input type="hidden" name="page" value="{$page}" />

<table width="100%" class="table table-striped dataTable vertical-center">
<thead>
<tr>
    {if $attributes_classes}<th width="10"><input type='checkbox' class='select_all' class_to_select='attribute_class_item' /></th>{/if}
    <th width="10%" class="text-center">{$lng.lbl_att_class_is_default}</th>
    <th width="80%">{include file='common/sort.tpl' title=$lng.lbl_attribute_class field='name'}</th>
    <th width="10%">{include file='common/sort.tpl' title=$lng.lbl_orderby field='orderby'}</th>
    <th>&nbsp;</th>
</tr>
</thead>
{if $attributes_classes}
{foreach from=$attributes_classes item=v}
<tr{cycle values=", class='cycle'"}>
    <td align="center"><input type="checkbox" name="to_delete[{$v.attribute_class_id}]" class="attribute_class_item" /></td>
    <td align="center"><input type="radio" name="is_default" value="{$v.attribute_class_id}" {if $v.is_default} checked{/if} /></td>
    <td><a href="index.php?target={$current_target}&attribute_class_id={$v.attribute_class_id}">{$v.name}</a></b></td>
    <td align="center"><input type="text" class="form-control" name="posted_data[{$v.attribute_class_id}][orderby]" size="5" value="{$v.orderby}" /></td>
    <td><a href="index.php?target=attributes&attribute_class_id={$v.attribute_class_id}{if $page}&amp;page={$page}{/if}" class="btn btn-default btn-xs">{$lng.lbl_modify}</a></td>
</tr>
{/foreach}
{else}
<tr>
    <td colspan="6" align="center">{$lng.txt_no_attributes_classes}</td>
</tr>
{/if}
</table>
</form>

{include file='common/navigation.tpl'}

{if $attributes_classes}
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_update href="javascript:cw_submit_form('atts_classes_form')" acl='__1201' class="btn-green push-20 push-5-r"}
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_delete_selected onclick="javascript: cw_submit_form('atts_classes_form', 'delete_class');" acl='__1201' class="btn-danger push-20 push-5-r"}
{/if}
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_add_new href="index.php?target=attributes&attribute_class_id=" acl='__1201' class="btn-green push-20 push-5-r"}


{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block}

{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_feature_classes}
