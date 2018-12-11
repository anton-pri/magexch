<form action="index.php?target={$current_target}" method="post" name="confirmation_form">
<input type="hidden" name="action" value="edit" />
<input type="hidden" name="action" value="delete" />
<input type="hidden" name="doc_id" value="{$doc_id}" />
<input type="hidden" name="confirmed" value="Y" />

{$lng.txt_aom_confirm_deletion_order}
<br /><br />
{include file='buttons/yes.tpl' href="javascript: cw_submit_form('confirmation_form');"}
{include file='buttons/no.tpl' href="index.php?target=`$current_target`&doc_id=`$doc_id`&mode=edit&show=preview"}

</form>
