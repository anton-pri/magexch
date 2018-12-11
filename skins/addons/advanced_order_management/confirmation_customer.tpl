{capture name=section}
<form action="index.php?target={$current_target}" method="post" name="confirmation_form">
<input type="hidden" name="mode" value="edit" />
<input type="hidden" name="action" value="save" />
<input type="hidden" name="doc_id" value="{$doc_id}" />
<input type="hidden" name="confirmed" value="Y" />

{$lng.txt_aom_confirm_update_order}<br/>

{include file="buttons/yes.tpl" href="javascript:cw_submit_form(document.confirmation_form);"}
{include file="buttons/no.tpl" href="index.php?target=`$current_target`&doc_id=`$doc_id`&mode=edit&js_tab=preview"}

</form>
{/capture}
{include file='common/section.tpl' content=$smarty.capture.section title=$lng.lbl_confirmation}
