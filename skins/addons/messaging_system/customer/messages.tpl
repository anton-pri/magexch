{include file='common/navigation_counter.tpl'}

{if $messages_list}
    <div class="common_navigation top">
    {include file='common/navigation.tpl'}
    </div>
    <form action="index.php?target={$current_target}" method="post" name="messages_list_form">
        <input type="hidden" name="action" value="" />
        <input type="hidden" name="mode" value="{$mode}" />
        <input type="hidden" name="message_id" value="" />

        {assign var="pagestr" value="`$navigation.script`&page=`$navigation.page`&items_per_page=`$navigation.objects_per_page`"}

        <table class="header message_box_messages" width="100%" cellspacing="0">
            <thead>
              <tr class='sort_order'>
                <th><input type='checkbox' class='select_all' class_to_select='messages_list_item' /></th>
                <th>{if $sort_field eq "sending_date"}{include file="buttons/sort_pointer.tpl" dir=$sort_direction}&nbsp;{/if}<a href="{$pagestr|amp}&amp;sort_field=sending_date&amp;sort_direction={$sort_direction}">{$lng.lbl_sending_date}</a></th>
                <th>{if $sort_field eq "firstname"}{include file="buttons/sort_pointer.tpl" dir=$sort_direction}&nbsp;{/if}<a href="{$pagestr|amp}&amp;sort_field=firstname&amp;sort_direction={$sort_direction}">{if $mode eq "sent"}{$lng.lbl_recipient}{else}{$lng.lbl_sender}{/if}</a></th>
                <th>{if $sort_field eq "subject"}{include file="buttons/sort_pointer.tpl" dir=$sort_direction}&nbsp;{/if}<a href="{$pagestr|amp}&amp;sort_field=subject&amp;sort_direction={$sort_direction}">{$lng.lbl_subject}</a></th>
                <th>{if $sort_field eq "read_status"}{include file="buttons/sort_pointer.tpl" dir=$sort_direction}&nbsp;{/if}<a href="{$pagestr|amp}&amp;sort_field=read_status&amp;sort_direction={$sort_direction}">{$lng.lbl_read_status}</a></th>
              </tr>
            </thead> 
            <tbody> 
            {foreach from=$messages_list item=message name=messages}
                <tr{if $message.read_status eq 0} style="font-weight: bold;" class="message_unread"{/if}>
                    <td width="20" height="20" class="m_border_left m_border_top{if $smarty.foreach.messages.last} m_border_bottom{/if}" align="center">
                        <input type="checkbox" name="m_item[{$message.message_id}]" class="messages_list_item" />
                    </td>
                    <td width="120" onclick="show_message({$message.message_id});" class="m_cursor m_border_top{if $smarty.foreach.messages.last} m_border_bottom{/if}">{$message.sending_date}</td>
                    <td width="160" onclick="show_message({$message.message_id});" class="m_cursor m_border_top{if $smarty.foreach.messages.last} m_border_bottom{/if}">{$message.sender_name}</td>
                    <td onclick="show_message({$message.message_id});" class="m_cursor m_border_top{if $smarty.foreach.messages.last} m_border_bottom{/if}">{$message.subject}</td>
                    <td width="80" onclick="show_message({$message.message_id});" class="m_cursor m_border_top m_border_right{if $smarty.foreach.messages.last} m_border_bottom{/if}">{if $message.read_status eq 1}{$lng.lbl_read}{else}{$lng.lbl_do_not_read}{/if}</td>
                </tr>
            {/foreach}
            </tbody>
        </table>
<br>
        {if $mode ne "archive"}
            {include file='buttons/button.tpl' button_title=$lng.lbl_mark_messages_read href="javascript: void(0);" onclick="document.forms.messages_list_form.action.value='mark_read'; submitFormAjax('messages_list_form');" style="button"}
            {include file='buttons/button.tpl' button_title=$lng.lbl_mark_messages_unread href="javascript: void(0);" onclick="document.forms.messages_list_form.action.value='mark_unread'; submitFormAjax('messages_list_form');" style="button"}
            {include file='buttons/button.tpl' button_title=$lng.lbl_send_to_archive href="javascript: void(0);" onclick="document.forms.messages_list_form.action.value='archive'; submitFormAjax('messages_list_form');" style="button"}
        {/if}
        {include file='buttons/button.tpl' button_title=$lng.lbl_delete_selected href="javascript: void(0);" onclick="document.forms.messages_list_form.action.value='delete'; submitFormAjax('messages_list_form');" style="button" class="danger"}
        {include file='buttons/button.tpl' button_title=$lng.lbl_new_message href='index.php?target=message_box&mode=new' style="button"}

    </form>
    <div class="common_navigation bottom">
    {include file='common/navigation.tpl'}
    </div>
{else}
	<span class='no_messages'>{$lng.txt_no_messages}</span>
<br />
       {include file='buttons/button.tpl' button_title=$lng.lbl_new_message href='index.php?target=message_box&mode=new' style="button"}
{/if}

<script type="text/javascript">
    <!--
    var current_target = '{$current_target}';
    {literal}
    function show_message(id) {
        document.location = "index.php?target=" + current_target + "&action=show&message_id=" + id;
    }

    $(document).ready(function() {
        $('a.page, a.page_arrow').bind('click',aAJAXClickHandler);
        $('tr.sort_order a').bind('click',aAJAXClickHandler);
        $('div.navigation_pages select').removeAttr('onchange');
        $('div.navigation_pages select').bind('change',function(){
            var url = $(this).attr('href')+this.value;
            $(this).attr('href',url);
            aAJAXClickHandler.apply(this);
        });

        if ($('input:checkbox.select_all').length) {
            $('input:checkbox.select_all').on('click', change_all_checkboxes);
        }
    });
    {/literal}
    -->
</script>
