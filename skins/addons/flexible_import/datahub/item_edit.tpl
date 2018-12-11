{capture name=section}
{capture name=block1}

{if $data2edit ne ''}
<form name="datahub_item_edit"  method="post" action="index.php?target=datahub_item_edit">
<input type="hidden" name="action" value="delete" />
<input type="hidden" name="table_name" value="{$table_name}">
<input type="hidden" name="key_field" value="{$key_field}">
<input type="hidden" name="key_value" value="{$key_value}">

<div class="box">
{foreach from=$data2edit item=entry2edit}
<table class="table table-striped">
<tr><th>Parameter</th><th>Value</th></tr>
{foreach from=$entry2edit item=i key=k}
<tr class='{cycle values="cycle,"}'><td>{$k}</td><td>{$i}</td></tr>
{/foreach}
</table>
{/foreach}
</div>


<br/>
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_delete href="javascript:if (confirm('Current entry will be deleted!')) cw_submit_form('datahub_item_edit', 'delete');" style='btn-danger push-15-r'} <p />

</form>
{else}
<p>The entry with {$key_field}={$key_value} does not exist in {$table_name}</p>
<br/>
{/if}

{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block1 title="`$table_name`: `$key_field`=`$key_value`" inline_style_content="padding-top:0px;"}

{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_delete_hub_entry|default:'Delete hub table entry'}

