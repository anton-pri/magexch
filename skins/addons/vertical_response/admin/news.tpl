{*include file='common/page_title.tpl' title=$lng.lbl_news_management*}
{capture name=section}
<div class="dialog_title">{$lng.txt_news_management_top_text}</div>

<form action="index.php?target={$current_target}" method="post" name="select_lists_form">
	<input type="hidden" name="action" value="update" />

	<div class="box">
		<table class="header" width="100%">
		<tr>
			<th width="15"><input type='checkbox' class='select_all' class_to_select='news_list_item' /></th>
			<th width="100%">{$lng.lbl_list_name}</th>
		</tr>
		{if $lists}
		{foreach from=$lists item=list}
		<tr{cycle values=', class="cycle"'}>
			<td>
				<input type="hidden" name="posted_data[{$list.list_id}][list_id]" value="{$list.list_id}" />
				<input type="checkbox" name="to_delete[{$list.list_id}]" value="1" class="news_list_item" />
			</td>
			<td>
				<a href="index.php?target={$current_target}&amp;list_id={$list.list_id}" title="">{$list.name}</a>
			</td>
		</tr>
		{/foreach}
		{else}
		<tr>
			<td colspan="2" align="center">{$lng.txt_no_newslists}</td>
		</tr>
		{/if}
		</table>
	</div>
	<div class="buttons">
		{if $lists}
		{include file='buttons/button.tpl' button_title=$lng.lbl_delete_selected href="javascript: cw_submit_form('select_lists_form', 'delete');" acl=__2600}
		{/if}
	</div>
</form>
{/capture}
{include file="common/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_news_management}
