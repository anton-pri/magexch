<form action="index.php?target={$current_target}" method="post" name="subscribers_form" enctype="multipart/form-data">
<input type="hidden" name="mode" value="subscribers" />
<input type="hidden" name="action" value="" />
<input type="hidden" name="list_id" value="{$list_id}" />

{include file='common/navigation.tpl'}

<div class="form-horizontal">
<table class="table table-striped dataTable vertical-center" width="100%">
<thead>
<tr>
    <th width="10"><input type='checkbox' class='select_all' class_to_select='subscribers_item' /></th>
    <th width="50%">{$lng.lbl_email}</th>
    <th width="50%">Subscribed</th>
</tr>
</thead>
{if $subscribers}
{foreach from=$subscribers item=subscriber}
<tr{cycle values=', class="cycle"'}>
    <td>{if $subscriber.direct}<input type="checkbox" name="to_delete[{$subscriber.email|escape}]" class="subscribers_item" />{/if}</td>
    <td>{$subscriber.email}</td>
    <td>{if $subscriber.since_date}{$subscriber.since_date|date_format:$config.Appearance.date_format}{else}{$subscriber.membership}{/if}</td>
</tr>
{/foreach}
{else}
<tr>
    <td colspan="3" align="center">{$lng.txt_no_subscribers}</td>
</tr>
{/if}
</table>

</div>
{include file='common/navigation.tpl'}

{if $subscribers}
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_export_selected href="javascript:cw_submit_form('subscribers_form', 'export');" style="btn-green push-20 push-5-r"}
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_delete_selected href="javascript:cw_submit_form('subscribers_form', 'delete');" style="btn-danger push-20 push-5-r"}
{/if}

<div class="form-horizontal">
{include file='common/subheader.tpl' title=$lng.lbl_add_to_maillist}
<div class="form-group form-inline">
    <label class="col-xs-12">{$lng.lbl_email}</label>
    <div class="col-xs-12">
    	<div class="form-group">
    		<input type="text" class="form-control" id="new_email" name="new_email" size="40" />
    	</div>
    	<div class="form-group">
    		{include file='admin/buttons/button.tpl' button_title=$lng.lbl_add href="javascript:cw_submit_form('subscribers_form', 'add');" style="btn-green"}
		</div>
    </div>
</div>
</div>

<div class="form-horizontal">
{include file='common/subheader.tpl' title=$lng.lbl_news_list_subscribers_import}
<div class="form-group form-inline">
    <label class="col-xs-12">{$lng.lbl_file_for_upload}</label>
    <div class="col-xs-12">
    	<div class="form-group">
    		<input type="file" class="form-control" size="32" name="userfile" />
    	</div>
    	<div class="form-group">
    		{include file='admin/buttons/button.tpl' button_title=$lng.lbl_import href="javascript:cw_submit_form('subscribers_form', 'import');" style="btn-green"}
		</div>
    </div>
</div>

</div>

</form>
