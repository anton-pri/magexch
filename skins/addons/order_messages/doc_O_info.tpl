<script type="text/javascript">
{literal}
  $(document).ready(function(){
    MessageForm = $("#newMessageForm");
    MessageForm.validate();

/*
    var elm = "<div id='om_thread_dialog' title='Topic messages'></div>";
    $('body').append(elm);

    $("#om_thread_dialog").dialog({
        autoOpen: false,
        modal   : true,
        height  : 460,
        width   : 740-36
    });

    $('a[rel="om_link_threadpopup"]').click(
      function () {
        //$('#om_thread_dialog').dialog('option', 'title', $(this).attr('title'));
        $('#om_thread_dialog').html('<iframe id="threadpopup_modal_iframe" width="100%" height="95%" marginWidth="0" marginHeight="0" frameBorder="0" scrolling="auto" />').dialog("open");
        $("#threadpopup_modal_iframe").attr("src", $(this).attr('href'));
        return false;
      }
    );
*/
    $('a[rel="om_link_threadpopup"]').click(
      function () {
        if ($('#om_thread_dialog').length==0)
          $('body').append('<div id="om_thread_dialog" style="overflow:hidden"></div>');

        var hash = $(this).attr('href');
        if (hash != $('#om_thread_dialog').data('hash')) {
        // Load iframe into dialog
          $('#om_thread_dialog').html("<iframe frameborder='no' width='800' height='490' src='"+hash+"'></iframe>");
        }
        $('#om_thread_dialog').data('hash', hash);
        // Show dialog
        sm('om_thread_dialog', 830, 530, false, 'Topic messages');
        return false;
      }
    );
  });
{/literal}
</script>

<div class="box">
<br />
<h2>{$lng.lbl_om_communication_topics}</h2>

{if $doc_messages_threads ne ''}
{foreach from=$doc_messages_threads item=thread}
<div class="input_field_0">
<table cellpadding="3">
<tr>
<td width="5%" align="center">{if $thread.messages_unread}{$lng.lbl_om_unread_messages|substitute:'unread_messages':$thread.messages_unread}{else}{$lng.lbl_om_no_new_messages}{/if}</td>
<td width="55%" align="left"><a rel="om_link_threadpopup" href="index.php?target=thread_messages&thread_id={$thread.thread_id}" title="{$lng.lbl_om_initial_message}">{$thread.start_message.subject|truncate:'50':'...'}</a>&nbsp;
({if $thread.start_message.recepient.usertype eq 'C'}to customer{elseif $thread.start_message.recepient.usertype eq 'S'}to supplier{else}to admin{/if})<br /><nobr>
<strong><a target="_blank" href="index.php?target=user_{$thread.start_message.sender.usertype}&mode=modify&user={$thread.start_message.sender.customer_id}">{if $thread.start_message.sender.firstname ne '' || $thread.start_message.sender.lastname ne ''}{$thread.start_message.sender.firstname}&nbsp;{$thread.start_message.sender.lastname}&nbsp;({$thread.start_message.sender.email}){else}{$thread.start_message.sender.email|default:$config.order_messages.default_recepient_admin_email}{/if}</a></strong>, {$thread.start_message.date|date_format:$config.Appearance.datetime_format}</nobr></td> 
<td width="10%" align="center" valign="top">total: {$thread.messages_count}</td>
<td width="30%" align="left">
recent: {if !$thread.last_message.read_status && $thread.last_message.recepient_id eq $customer_id}new{/if} <a rel="om_link_threadpopup" href="index.php?target=thread_messages&thread_id={$thread.thread_id}">{tunnel func='cw_order_messages_all_decodes' via='cw_call' param1=$thread.last_message.body assign='last_message_body'}{$last_message_body|truncate:'20':'...'|default:'<i>(empty message)</i>'}</a><br /><nobr><strong><a target="_blank" href="index.php?target=user_{$thread.last_message.sender.usertype}&mode=modify&user={$thread.last_message.sender.customer_id}">{if $thread.last_message.sender.firstname ne '' || $thread.last_message.sender.lastname ne ''}{$thread.last_message.sender.firstname}&nbsp;{$thread.last_message.sender.lastname}&nbsp;({$thread.last_message.sender.email}){else}{$thread.last_message.sender.email|default:$config.order_messages.default_recepient_admin_email}{/if}</a></strong>,&nbsp;{$thread.last_message.date|date_format:$config.Appearance.datetime_format}</nobr>
</td></tr>
</table>

</div>
{/foreach}
{else}
<label>{$lng.lbl_no_messages|default:'No messages found'}</label>
{/if}

</div>

<div class="box">
<br />
{assign var='current_target' value='docs_O'}
<h2>{$lng.lbl_om_start_new_topic}</h2>

<form action="index.php?target={$current_target}&doc_id={$doc_id}" method="post" name="messages_form" id="newMessageForm">
<input type="hidden" name="mode" value="order_messages" />
<input type="hidden" name="action" value="new_thread" />

<div class="input_field_1 right">
    <label class="required">{$lng.lbl_contact_person|default:'Contact person'}:</label>
{assign var='userinfo' value=$doc.userinfo}
{capture assign="userinfo_name"}
{if $profile_fields.address.firstname.is_avail}{$userinfo.main_address.firstname}&nbsp;{/if}
{if $profile_fields.address.lastname.is_avail}{$userinfo.main_address.lastname}&nbsp;{/if}
{/capture}

    <select name="new_thread[recepient_id]" class="required">
    <option value="">{$lng.lbl_none}</option>
    <optgroup label="{$lng.lbl_customers}">
    <option value="{$userinfo.customer_id}">{$userinfo_name}&nbsp;({$userinfo.email})</option>
    </optgroup>
    {if $contact_suppliers ne ''}
      <optgroup label="{$lng.lbl_suppliers}">
      {foreach from=$contact_suppliers item=supplier}
      <option value="{$supplier.customer_id}">{$supplier.firstname}&nbsp;{$supplier.lastname}&nbsp;({$supplier.email})</option>
      {/foreach}
      </optgroup>
    {/if} 
    </select>
</div>

<div class="input_field_1 right">
    <label >Send standard notification:</label>
    <select name="new_thread[standard_email]">
    <option value="">No</option>
    <option value="Y">Yes</option>
    </select>
</div>

<div class="input_field_1 right">
    <label>{$lng.lbl_subject}:</label>
    <input type="text" class="short" name="new_thread[subject]" style="width:70%" />
</div>

<div class="input_field_1 right">
    <label>{$lng.lbl_message}:</label>
    <textarea name="new_thread[body]" style="width:70%" rows="4"></textarea>
</div>

<div class="input_field_1 right">
    <label>{$lng.lbl_post_on_behalf_of_contacted_user|default:'Post on behalf of contacted user'}:</label>
    <input type="checkbox" name="new_thread[on_behalf]" />
</div>

{if $usertype eq 'A' || $usertype eq 'P'}
    {include file="buttons/button.tpl" href="javascript: cw_submit_form('newMessageForm', 'new_thread');" button_title=$lng.lbl_new_message|escape acl=$page_acl}
{/if}

</form>

</div>
