<form action="index.php?target={$current_target}&mode=events&event_id={$event_id}" method="post" name="mail_list_form">
<input type="hidden" name="action" value="maillist" />

{include file='common/subheader.tpl' title=$lng.lbl_giftreg_recipients_list}
{if $mailing_list}
<table class="header" width="100%">
<tr>
	<th>&nbsp;</th>
	<th>{$lng.lbl_recipient_name}</th>
	<th>{$lng.lbl_recipient_email}</th>
	<th>{$lng.lbl_status}</th>
</tr>
{foreach from=$mailing_list item=ml}
<tr{cycle values=", class=cycle"}>
	<td align="center"><input type="checkbox" name="del[{$ml.reg_id}]" value="Y" /></td>
	<td><input type="text" name="recipient_details[{$ml.reg_id}][recipient_name]" value="{$ml.recipient_name}" /></td>
	<td><input type="email" name="recipient_details[{$ml.reg_id}][recipient_email]" value="{$ml.recipient_email}" /></td>
	<td>
        {if $ml.status eq 'P'}{$lng.lbl_pending}{elseif $ml.status eq 'S'}{$lng.lbl_sent}{elseif $ml.status eq 'Y'}{$lng.lbl_confirmed}{else}{$lng.lbl_declined}{/if}
        {if $ml.status ne 'P'}{$ml.date|date_format:$config.Appearance.datetime_format}{/if}
    </td>
</tr>
{/foreach}
</table>

{include file="buttons/update.tpl" href="javascript:cw_submit_form('mail_list_form')"}
{include file='buttons/button.tpl' button_title=$lng.lbl_send_confirmation_request href="javascript:cw_submit_form('mail_list_form', 'send_conf')"}
{include file="buttons/delete.tpl" href="javascript:cw_submit_form('mail_list_form', 'maillist_delete')"}

{else}
<div class="send_all">
<center>{$lng.txt_no_recipients}</center>
</div>
{/if}

{if !$recipients_limit_reached}
{include file='common/subheader.tpl' title=$lng.lbl_add_new_recipient}</td>

<div class="input_field_1">
	<label>{$lng.lbl_recipient_name}</label>
    <input type="text" name="recipient_details[0][recipient_name]" value="" />
</div>
<div class="input_field_1">
	<label>{$lng.lbl_recipient_email}</label>
	<input type="email" name="recipient_details[0][recipient_email]" value="" />
</div>
{else}
<center>{$lng.txt_giftreg_max_allowed_recipients_msg}</center>
{/if}

{include file='buttons/update.tpl' href="javascript:cw_submit_form('mail_list_form')"}

</form>
