{*include file='common/page_title.tpl' title=$lng.lbl_news_management*}
{capture name=section}
{capture name=block}

<div class="dialog_title">{$lng.txt_news_management_top_text}</div>

<form action="index.php?target={$current_target}" method="post" name="select_lists_form">
<input type="hidden" name="action" value="update" />

<div class="box">

<table class="table table-striped dataTable vertical-center" width="100%">
<thead>
<tr>
	<th width="5" align="center"><input type='checkbox' class='select_all' class_to_select='news_list_item' /></th>
	<th width="65%">{$lng.lbl_list_name}</th>
    <th class="text-center" width="15%">{$lng.lbl_homepage_subscribe}</th>
    <th class="text-center" width="15%">{$lng.lbl_show_as_news}</th>
	<th class="text-center" width="15%">{$lng.lbl_active}</th>
</tr>
</thead>
{if $lists}
{foreach from=$lists item=list}
<tr{cycle values=', class="cycle"'} >
	<td align="center">
    	<input type="hidden" name="posted_data[{$list.list_id}][list_id]" value="{$list.list_id}" />
	    <input type="checkbox" name="to_delete[{$list.list_id}]" value="1" class="news_list_item" />
	</td>
	<td>
        <a href="index.php?target={$current_target}&amp;list_id={$list.list_id}" title="">{$list.name}</a>
    </td>
    <td align="center"><input type="checkbox" name="posted_data[{$list.list_id}][subscribe]"{if $list.subscribe} checked="checked"{/if} value="1" /></td>
    <td align="center"><input type="checkbox" name="posted_data[{$list.list_id}][show_as_news]"{if $list.show_as_news} checked="checked"{/if} value="1" /></td>
	<td align="center"><input type="checkbox" name="posted_data[{$list.list_id}][avail]"{if $list.avail} checked="checked"{/if} {if $usertype eq 'B'}disabled{/if} value="1" /></td>
</tr>
{/foreach}
{else}
<tr>
	<td colspan="4" align="center">{$lng.txt_no_newslists}</td>
</tr>
{/if}
</table>

</div>
<div class="buttons">
{if $lists}
    {if $usertype ne 'B'}
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_update href="javascript: cw_submit_form('select_lists_form');" acl=__2600 style="btn-green push-20 push-5-r"}
    {/if}
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_delete_selected href="javascript: cw_submit_form('select_lists_form', 'delete');" acl=__2600 style="btn-danger push-20 push-5-r"}
{/if}
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_add_new href="index.php?target=news&list_id=" acl=__2600 style="btn-green push-20 push-5-r"}
</div>
</form>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block}

{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_news_management}
