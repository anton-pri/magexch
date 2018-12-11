<div class="box">

{if $items_for_relations}

<form action="index.php?target={$current_target}&doc_id={$doc_id}" name="make_relations_form" method="post">
<input type="hidden" name="action" value="make_relations" />

<table class="header" width="100%">
<tr>
    <th width="1%"><input type='checkbox' class='select_all' class_to_select='relations_item' /></th>
    <th width="80%">{$lng.lbl_item}</th>
    <th>{$lng.lbl_amount}</th>
</tr>
{foreach from=$items_for_relations key=item_id item=ri}
<tr{cycle values=", class='cycle'"}>
    <td><input type="checkbox" name="relations[{$item_id}][create]" value="1" class="relations_item" /></td>
    <td>{$ri.productcode} {$ri.product}</td>
    <td><input type="text" name="relations[{$item_id}][amount]" value="{$ri.amount}" size="8" /></td>
</tr>
{/foreach}
</table>
<div class="input_field_1 doccc">
    <label>{$lng.lbl_relation}</label>
    {include file='main/select/doc_relation.tpl' type=$doc.type name='relation_doc_type'}
    {$lng.lbl_or}
    {include file='main/select/find_doc.tpl' name='relation_doc_id' form='make_related_doc_form'}
<div class="clear"></div>
</div>

{include file='buttons/button.tpl' href="javascript:cw_submit_form('make_relations_form');" button_title=$lng.lbl_create acl=$page_acl}
</form>

{if $relations}
<form action="index.php?target={$current_target}&doc_id={$doc_id}" name="relations_form" method="post">
<input type="hidden" name="action" value="delete_relations" />

{foreach from=$relations key=type item=docs}
{lng name="lbl_doc_info_`$type`" assign='title'}
{include file='common/subheader.tpl' title=$title}
<table class="header" width="100%">
<tr>
    <th width="1%">&nbsp;</th>
    <th width="10%">{$lng.lbl_doc_id}</th>
    <th>{$lng.lbl_item}</th>
    <th width="10%">{$lng.lbl_amount}</th>
</tr>
    {foreach from=$docs item=rel}
    {foreach from=$rel.items key=item_id item=ri}
<tr>
    <td><input type="checkbox" name="del_relations[{$rel.doc_id}][{$item_id}]" value="1" /></td>
    <td align="center">{$rel.display_doc_id}</td>
    <td>{$ri.productcode} {$ri.product}</td>
    <td align="center">{$ri.amount}</td>
</tr>
    {/foreach}
    {/foreach}
</tr>
</table>
{/foreach}
{include file='buttons/button.tpl' href="javascript:cw_submit_form('relations_form');" button_title=$lng.lbl_delete acl=$page_acl}
</form>
    {/if}
{else}
<center>{$lng.lbl_not_found}</center>
{/if}

</div>
