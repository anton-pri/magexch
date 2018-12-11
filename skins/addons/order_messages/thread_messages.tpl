<script type="text/javascript">
{literal}
  $(document).ready(function(){
    MessageForm = $("#newMessageForm");
    MessageForm.validate();
  });
{/literal}
</script>


<div class="box">
<h2>&nbsp;&nbsp;&nbsp;&nbsp;{$start_message.subject}</h2>
{assign var="current_target" value="thread_messages"}
{foreach from=$thread_messages item=msg}
{assign var='msg_sender_id' value=$msg.sender_id}
<div class="input_field_0">
<table width="100%">
<tr>
<td width="80%" valign="top" align="left">
<nobr><strong><u><a target="_blank" href="index.php?target=user_{$messages_users.$msg_sender_id.usertype}&mode=modify&user={$msg_sender_id}">{if $messages_users.$msg_sender_id.firstname ne '' || $messages_users.$msg_sender_id.lastname ne ''}{$messages_users.$msg_sender_id.firstname}&nbsp;{$messages_users.$msg_sender_id.lastname}&nbsp;({$messages_users.$msg_sender_id.email}){else}{$messages_users.$msg_sender_id.email}{/if}</a></u></strong></nobr>:<br />
<b>{$msg.subject}</b></td>
<td width="20%" valign="top" align="right" style="padding-right:15px;"> 
<a href="index.php?target=thread_messages&thread_id={$thread_id}#msg{$msg.message_id}" id="msg{$msg.message_id}">#{$doc_id}/{$msg.message_id}<a><br />
<nobr>{$msg.date|date_format:$config.Appearance.datetime_format}</nobr>
</td>
</tr><tr><td colspan="2" style="padding-right:15px;">
<div style="border: solid 1px gray; background: #F0F0F0; padding: 8px">{tunnel func='cw_order_messages_all_decodes' via='cw_call' param1=$msg.body}</div>
</td></tr>
</table>
</div>
{/foreach}
<h2>&nbsp;&nbsp;&nbsp;{$lng.lbl_om_new_message|default:'New message'}</h2>
<form action="index.php?target={$current_target}&doc_id={$doc_id}" method="post" name="messages_form" id="newMessageForm">
<input type="hidden" name="mode" value="thread_messages" />
<input type="hidden" name="action" value="add_message" />
<input type="hidden" name="thread_id" value="{$thread_id}" />

<div class="input_field_1 right">
    <label>{$lng.lbl_subject}:</label>
    <input type="text" class="short" name="new_message[subject]" style="width:70%" />
</div>

<div class="input_field_1 right">
    <label>{$lng.lbl_message}:</label>
    <textarea name="new_message[body]" style="width:70%" rows="4"></textarea>
</div>

<div class="input_field_1 right">
    <label>{$lng.lbl_post_on_behalf_of_contacted_user|default:'Post on behalf of contacted user'}:</label>
    <input type="checkbox" name="new_message[on_behalf]" />
</div>

&nbsp;&nbsp;&nbsp;{include file="buttons/button.tpl" href="javascript: cw_submit_form('newMessageForm', 'add_message');" button_title=$lng.lbl_new_message|escape acl=$page_acl}

</form>
</div>
