{assign var='objects' value=$addon_main_objects}

<script type="text/javascript">
  var txt_delete_selected_objects = "{$lng.txt_delete_selected_objects|escape:javascript|strip_tags}";
</script>

{capture name='section'}
{capture name='block'}

<form action="index.php?target=addon_main_target" method="post" name="update_objects_form">
<input type="hidden" name="mode" value="" />
<input type="hidden" name="action" value="" />

<div class="box">

<table width="100%" cellpadding="2" cellspacing="1" class="table table-striped dataTable vertical-center">
<thead>
  <tr>
    <th align="center">{if $objects}<input type='checkbox' class='select_all' class_to_select='objects_list_item' />{else}&nbsp;{/if}</th>
    <th width="10%">
        Name
    </th>
    <th>
        Property1
    </th>
    <th>
        Property2
    </th>
    <th>
        Property3
    </th>
  </tr>
</thead>
  {if $objects}
    {foreach from=$objects item="object"}
      {assign var="edit_link" value="index.php?target=addon_main_target&amp;mode=details&amp;id=`$object.id`"}
      <tr class="{cycle values=',cycle'}" valign='top'>
        <td align="center">
          <input type="checkbox" name="objects[{$object.id}][select]" class="objects_list_item" />
        </td>
        <td>
          <a href="{$edit_object_link}">{$object.name|truncate:100}</a>
        </td>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
      </tr>
    {/foreach}
  {else}
    <tr>
      <td colspan="10">{$lng.lbl_no_records_found}</td>
    </tr>
  {/if}
</table>

</div>

<div id="sticky_content" class="buttons">
{if $objects}
  {include file="admin/buttons/button.tpl" href="javascript: cw_submit_form('update_objects_form', 'update');" button_title=$lng.lbl_update acl=$page_acl style="btn-green push-20 push-5-r"}
  {include file="admin/buttons/button.tpl" href="javascript: if (confirm(txt_delete_selected_objects)) cw_submit_form('update_objects_form', 'delete');" button_title=$lng.lbl_delete_selected|escape acl=$page_acl style="btn-danger push-20 push-5-r"}
{/if}
  {include file="admin/buttons/button.tpl" href="index.php?target=addon_main_target&mode=add" button_title=$lng.lbl_add_new style="btn-green push-20 push-5-r"}
</div>

</form>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block}
{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_items}
