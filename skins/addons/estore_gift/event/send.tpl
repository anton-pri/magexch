{if $event_data.allow_to_send}

{if $event_data.sent_date gt 0}
{assign var="s_date" value=$event_data.sent_date|date_format:"%A, %B %e, %Y"}
{assign var="s_time" value=$event_data.sent_date|date_format:"%T"}
<div class="dialog_title">{$lng.txt_giftreg_already_sent_notification_msg|substitute:"s_date":$s_date:"s_time":$s_time}</div>
{/if}

<form action="index.php?target={$current_target}&mode=events&event_id={$event_id}" method="post" name="notify_form">
<input type="hidden" name="action" value="send" />

<div class="input_field_1">
    <label>{$lng.lbl_subject}</label>
    <input type="text" name="mail_data[subj]" value="{$mail_data.subj}" />
</div>

<div class="input_field_1">
    <label>{$lng.lbl_message}</label>
</div>
{include file='main/textarea.tpl' name='mail_data[message]' data=$mail_data.message init_mode='exact'}<br/>

{include file='buttons/submit.tpl' href="javascript:cw_submit_form('notify_form');"}
</form>
{else}
<div class="dialog_title">{$lng.err_giftreg_no_recipients_msg}</div>
{/if}
