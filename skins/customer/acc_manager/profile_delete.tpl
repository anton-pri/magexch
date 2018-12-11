<div class="dialog_title">{$lng.txt_delete_users_top_text}</div>
<p>{$lng.txt_delete_users_top_note}</p>

{$lng.txt_operation_not_reverted_warning}

<br /><br />

<form action="index.php?target={$current_target}" method="post" name="processform">
<input type="hidden" name="mode" value="process" />
<input type="hidden" name="action" value="delete" />
<input type="hidden" name="confirmed" value="Y" />

{$lng.txt_are_you_sure_to_proceed}
{include file='buttons/button.tpl' button_title=$lng.lbl_yes href="javascript:cw_submit_form(document.processform)"}&nbsp;
{include file='buttons/button.tpl' button_title=$lng.lbl_no href="index.php?target=`$current_target`&mode=search"}
</form>
