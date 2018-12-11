<form action="index.php?target={$current_target}" method="post" name="messagesform">
<input type="hidden" name="list_id" value="{$list_id}" />
<input type="hidden" name="mode" value="messages" />
<input type="hidden" name="action" value="" />
<input type="hidden" name="messageid" value="" />
<div class="box">
<table class="table table-striped dataTable vertical-center" width="100%">
<thead>
<tr>
	<th width="10"><input type='checkbox' class='select_all' class_to_select='message_item' /></th>
	<th width="*">{$lng.lbl_subject}</th>
	<th width="12%" class="text-center">{$lng.lbl_created}</th>
	<th width="12%" class="text-center">{$lng.lbl_updated}</th>
	<th width="12%" class="text-center">{$lng.lbl_status}</th>
	<th width="10%" class="text-center">{$lng.lbl_send_message}</th>
</tr>
</thead>
{if $messages}
{foreach from=$messages item=message}
<tr{cycle values=', class="cycle"'}>
	<td><input type="checkbox" name="to_delete[{$message.news_id}]" class="message_item" /></td>
	<td><a href="index.php?target={$current_target}&messageid={$message.news_id}&amp;list_id={$list_id}&amp;js_tab=message">{$message.subject}</a></td>
	<td align="center">{$message.created_date|date_format:$config.Appearance.datetime_format}</td>
	<td align="center">{$message.updated_date|date_format:$config.Appearance.datetime_format}</td>
    <td align="center">
{if $message.status eq "N"}{$lng.lbl_queued}{elseif $message.status eq "A"}{$lng.lbl_sent_to_admin}{else}{$lng.lbl_sent}<br /><font class="SmallText">[{$message.send_date|date_format:$config.Appearance.datetime_format}]</font>{/if}
	</td>
	<td align="center"><input type="button" value=" {if $message.status eq "N" or $message.status eq "A"}{$lng.lbl_send|strip_tags:false|escape}{else}{$lng.lbl_resend|strip_tags:false|escape}{/if} " onclick="javascript: document.messagesform.messageid.value='{$message.news_id}'; document.messagesform.action.value='send'; document.messagesform.submit();" /></td>
</tr>
{/foreach}
{else}
<tr>
	<td colspan="5" align="center">{$lng.txt_no_messages}</td>
</tr>
{/if}
</table>
</div>
<div class="buttons">
{if $messages}
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_delete href="javascript:cw_submit_form('messagesform','delete');" style="btn-green push-20 push-5-r"}
{/if}
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_add_new href="index.php?target=`$current_target`&list_id=`$list_id`&js_tab=add_message" style="btn-green push-20 push-5-r"}
</div>
</form>

