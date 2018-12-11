<section>
<div class="message-box-container">
{if $mode eq 'sent'}

    {capture name='message_box'}
        <!-- <h2>{$lng.lbl_sent}</h2> -->
        <div id="contents_messages_list" blockUI="contents_messages_list">
            {include file="addons/messaging_system/customer/messages.tpl"}
        </div>
    {/capture}
    {include file='common/section.tpl' is_dialog=1 content=$smarty.capture.message_box title=$lng.lbl_messages_sent style='message_box'}

{elseif $mode eq 'archive'}

    {capture name='message_box'}
        <!-- <h2>{$lng.lbl_archive}</h2> -->
        <div id="contents_messages_list" blockUI="contents_messages_list">
            {include file="addons/messaging_system/customer/messages.tpl"}
        </div>
    {/capture}
    {include file='common/section.tpl' is_dialog=1 content=$smarty.capture.message_box title=$lng.lbl_messages_archived style='message_box'}

{elseif $mode eq 'new'}
    <div class="message-new-container">
    {capture name='message_box'}
        <div id="contents_message_new">
        <!-- <h2>{$lng.lbl_new_message}</h2> -->
        {include file="addons/messaging_system/customer/new_message.tpl"}
        </div>
    {/capture}
    {include file='common/section.tpl' is_dialog=1 content=$smarty.capture.message_box title=$lng.lbl_new_message style='message_box'}    
    </div>
{elseif $mode eq 'show'}

    {capture name='message_box'}
        <div id="contents_message_text">
        <h2>{$message.subject}</h2>
        {include file="addons/messaging_system/customer/show_message.tpl"}
        </div>
    {/capture}
    {include file='common/section.tpl' is_dialog=1 content=$smarty.capture.message_box title=$lng.lbl_message style='message_box'}

{else}

    {capture name='message_box'}
        <!-- <h2>{$lng.lbl_avail_type_incoming}</h2> -->
        <div id="contents_messages_list" blockUI="contents_messages_list">
            {include file="addons/messaging_system/customer/messages.tpl"}
        </div>
        {/capture}
    {include file='common/section.tpl' is_dialog=1 content=$smarty.capture.message_box title=$lng.lbl_messages_received style='message_box'}

{/if}
</div>
</section>
