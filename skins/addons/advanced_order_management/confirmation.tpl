{if $confirm_deletion eq "Y"}
{include file="addons/advanced_order_management/confirm_deletion.tpl"}
{else}
<form action="index.php?target={$current_target}" method="post" name="confirmation_form">
<input type="hidden" name="mode" value="edit" />
<input type="hidden" name="action" value="save" />
<input type="hidden" name="doc_id" value="{$doc_id}" />
<input type="hidden" name="confirmed" value="Y" />

{$lng.txt_aom_confirm_update_order}
<br /><br />

<input type="checkbox" id="notify_customer" name="notify_customer" value="Y" />
<label for="notify_customer">{$lng.lbl_aom_notify_customer}</label>
<br/>

{include file="buttons/yes.tpl" href="javascript:cw_submit_form(document.confirmation_form);"}
{include file="buttons/no.tpl" href="index.php?target=`$current_target`&doc_id=`$doc_id`&mode=edit&js_tab=preview"}

</form>
{/if}
