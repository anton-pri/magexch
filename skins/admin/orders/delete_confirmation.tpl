<form action="index.php?target={$current_target}" method="post" name="processform">
<input type="hidden" name="action" value="{$mode}" />
<input type="hidden" name="confirmed" value="Y" />

{if $mode eq 'delete_all'}
{$lng.txt_delete_N_orders_message|substitute:"count":$orders_count}
{else}

{foreach from=$docs item=doc}
 #{$doc.display_doc_id} {$doc.date|date_format:$config.Appearance.date_format}<br/>
{/foreach}

{/if}

{$lng.txt_operation_not_reverted_warning}
<br/><br/>

{$lng.txt_are_you_sure_to_proceed}
{include file='buttons/yes.tpl' href="javascript: cw_submit_form('processform')"}
{include file="buttons/no.tpl" href="index.php?target=`$target`&mode=search"}

</form>
