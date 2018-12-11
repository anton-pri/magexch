<form method="post" name="new_message_form" action="index.php?target={$current_target}">
    <input type="hidden" name="action" value="">
    <input type="hidden" name="mode" value="new">
    <input type="hidden" name="contact_id" value="{if $message.type eq 1}{$message.sender_id}{else}{$message.recipient_id}{/if}">
    <input type="hidden" name="subject" value="{$message.subject}">
    <input type="hidden" name="conversation_id" value="{$message.conversation_id}">
    <input type="hidden" name="message_id" value="{$message.message_id}">

    <table width="100%" class="new_message_table">
        <tr class='message-head'>
            <td width="100px"><b>{$lng.lbl_sender}</b></td>
            <td><b>{$message.sender_name}</b></td>
        </tr>
        <tr class='message-head'>
            <td><label class="required"><b>{$lng.lbl_recipient}</b></label></td>
            <td>{$message.recipient_name}</td>
        </tr>
        <tr class='message-head'>
            <td><label class="required"><b>{$lng.lbl_sending_date}</b></label></td>
            <td>{$message.sending_date}</td>
        </tr>
        <tr class='message-head'>
            <td valign="top"><b>{$lng.lbl_body}</b></td>
            <td>{$message.body}</td>
        </tr>
        <tr class='message-commands'>
            <td align="center" colspan="2">
                {include file='buttons/button.tpl' button_title=$lng.lbl_reply_to href="javascript: void(0);" onclick="document.forms.new_message_form.submit();" style="button"}
                {if $message.is_archive ne 1}
                    {if $message.read_status eq 0}
                        {include file='buttons/button.tpl' button_title=$lng.lbl_mark_messages_read href="javascript: void(0);" onclick="cw_submit_form('new_message_form', 'mark_read');" style="button"}
                    {else}
                        {include file='buttons/button.tpl' button_title=$lng.lbl_mark_messages_unread href="javascript: void(0);" onclick="cw_submit_form('new_message_form', 'mark_unread');" style="button"}
                    {/if}
                    {include file='buttons/button.tpl' button_title=$lng.lbl_send_to_archive href="javascript: void(0);" onclick="cw_submit_form('new_message_form', 'archive');" style="button"}
                {/if}
                {include file='buttons/button.tpl' button_title=$lng.lbl_delete href="javascript: void(0);" onclick="cw_submit_form('new_message_form', 'delete');" style="button"}
            </td>
        </tr>
        {if $conversation_messages}
        <tr class="message-conversation-title">
            <td colspan="2"><b>{$lng.lbl_also_in_this_conversation}</b></td>
        </tr>
        <tr class="message-conversation">
            <td colspan="2">
            {foreach from=$conversation_messages item=conversation_message}
                <div id="conversation_message_{$conversation_message.message_id}" class="m_conversation_message{if $conversation_message.message_id ne $message.message_id}{if $conversation_message.type eq 1} m_incomig{else} m_sent{/if}{/if}" onclick="show_full_mmessage({$conversation_message.message_id});">
                    <span class='author'><b>{$conversation_message.user_name}:</b></span> <span class='subject'>{$conversation_message.subject}</span>
                    <span class='body'><p>{$conversation_message.body}</p></span>
                    <span class='sending-date'{*style="font-size: 8px;"*}>{$conversation_message.sending_date}</span>
                </div>
            {/foreach}
            </td>
        </tr>
        {/if}
    </table>
</form>

<script type="text/javascript">
{literal}
function show_full_mmessage(id) {
    var el = $('#conversation_message_' + id);

    if (el.css('height') == "30px") {
        el.css('height', '100%');
        el.css('cursor', 'default');
    }
}
{/literal}
</script>
