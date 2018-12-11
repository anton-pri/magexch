
{if !$is_ajax}<div class="dialog_title">{$lng.txt_delete_users_top_text}</div>{/if}

{$lng.txt_delete_users_top_note}

<ul>
{foreach from=$users item=user}
<li>
#{$user.customer_id} {$user.email}: {$user.main_address.firstname} {$user.main_address.lastname}
</li>
{/foreach}
</ul>

{$lng.txt_operation_not_reverted_warning}

<br /><br />

<form action="index.php?target={$current_target}" method="post" name="processform">
<input type="hidden" name="mode" value="process" />
<input type="hidden" name="action" value="delete" />
<input type="hidden" name="confirmed" value="Y" />

<div class="buttons">
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_confirm href="javascript:cw_submit_form(processform)" style="btn btn-green"}
&nbsp;&nbsp;
{if $is_ajax}
	{include file='admin/buttons/button.tpl' button_title=$lng.lbl_cancel onclick="javascript: hm('delete_confirm');" style="btn btn-danger"}
{else}
	{include file='admin/buttons/button.tpl' button_title=$lng.lbl_cancel href="index.php?target=`$current_target`&mode=search" style="btn btn-danger"}
{/if}
</div>
</form>
