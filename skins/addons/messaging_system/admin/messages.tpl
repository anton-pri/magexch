{capture name=block}

<div class="row">
	<div class="col-xs-6 left-align">{include file='common/navigation_counter.tpl'}</div>
    <div class="col-xs-6">{include file='common/navigation.tpl'}</div>
</div>
    <form action="index.php?target={$current_target}" method="post" name="messages_list_form">
        <input type="hidden" name="action" value="" />
        <input type="hidden" name="mode" value="{$mode}" />
        <input type="hidden" name="message_id" value="" />

        {assign var="pagestr" value="`$navigation.script`&page=`$navigation.page`&items_per_page=`$navigation.objects_per_page`"}

        <div class="box">
        <table class="table table-striped dataTable vertical-center">
        <thead>
            <tr class='sort_order'>
                <th><input type='checkbox' class='select_all' class_to_select='messages_list_item' /></th>
                <th>{if $sort_field eq "sending_date"}{include file="buttons/sort_pointer.tpl" dir=$sort_direction}&nbsp;{/if}<a href="{$pagestr|amp}&amp;sort_field=sending_date&amp;sort_direction={$sort_direction}">{$lng.lbl_sending_date}</a></th>
                <th>{if $sort_field eq "firstname"}{include file="buttons/sort_pointer.tpl" dir=$sort_direction}&nbsp;{/if}<a href="{$pagestr|amp}&amp;sort_field=firstname&amp;sort_direction={$sort_direction}">{if $mode eq "sent"}{$lng.lbl_recipient}{else}{$lng.lbl_sender}{/if}</a></th>
                <th>{if $sort_field eq "subject"}{include file="buttons/sort_pointer.tpl" dir=$sort_direction}&nbsp;{/if}<a href="{$pagestr|amp}&amp;sort_field=subject&amp;sort_direction={$sort_direction}">{$lng.lbl_subject}</a></th>
                <th>{if $sort_field eq "read_status"}{include file="buttons/sort_pointer.tpl" dir=$sort_direction}&nbsp;{/if}<a href="{$pagestr|amp}&amp;sort_field=read_status&amp;sort_direction={$sort_direction}">{$lng.lbl_read_status}</a></th>
                {if $mode eq "sent"}
                <th>{$lng.lbl_recipient_status}</th>
                {/if}
            </tr>
		</thead>
        {if $messages_list}
            {foreach from=$messages_list item=message}
                <tr{if $message.read_status eq 0} style="font-weight: bold;cursor: pointer;"{else} style="cursor: pointer;"{/if}{cycle values=', class="cycle"'}>
                    <td width="20" height="20" align="center" style="cursor: default !important;">
                        <input type="checkbox" name="m_item[{$message.message_id}]" class="messages_list_item" />
                    </td>
                    <td width="120" onclick="show_message({$message.message_id});">{$message.sending_date}</td>
                    <td width="160" onclick="show_message({$message.message_id});">{$message.sender_name}</td>
                    <td onclick="show_message({$message.message_id});">{$message.subject}</td>
                    <td width="80" onclick="show_message({$message.message_id});">{if $message.read_status eq 1}{$lng.lbl_read}{else}{$lng.lbl_do_not_read}{/if}</td>
                    {if $mode eq "sent"}
                        <td width="100" onclick="show_message({$message.message_id});">{if $message.status eq 1}{$lng.lbl_read}{elseif $message.status eq 2}{$lng.lbl_aom_deleted}{else}{$lng.lbl_do_not_read}{/if}</td>
                    {/if}
                </tr>
            {/foreach}
        {else}
            <tr><td colspan='5' align='center'>No messages</td></tr>
        {/if}
        </table>
        </div>
<div class="row">
    <div class="col-xs-12">{include file='common/navigation.tpl'}</div>
</div>
        <div class="buttons">
        {if $messages_list}
        {if $mode ne "archive"}
            {include file='admin/buttons/button.tpl' button_title=$lng.lbl_mark_messages_read href="javascript: void(0);" onclick="document.forms.messages_list_form.action.value='mark_read'; submitFormAjax('messages_list_form');" style="btn-green push-5-r push-20"}
            {include file='admin/buttons/button.tpl' button_title=$lng.lbl_mark_messages_unread href="javascript: void(0);" onclick="document.forms.messages_list_form.action.value='mark_unread'; submitFormAjax('messages_list_form');" style="btn-green push-5-r push-20"}
            {include file='admin/buttons/button.tpl' button_title=$lng.lbl_send_to_archive href="javascript: void(0);" onclick="document.forms.messages_list_form.action.value='archive'; submitFormAjax('messages_list_form');" style="btn-green push-5-r push-20"}
        {/if}
        	{include file='admin/buttons/button.tpl' button_title=$lng.lbl_delete_selected href="javascript: void(0);" onclick="document.forms.messages_list_form.action.value='delete'; submitFormAjax('messages_list_form');" style="btn-danger push-5-r push-20"}
        {/if}
            {include file='admin/buttons/button.tpl' button_title=$lng.lbl_new_message href='index.php?target=message_box&mode=new' style="btn-green push-5-r push-20"}
        </div>
    </form>


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
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block}
