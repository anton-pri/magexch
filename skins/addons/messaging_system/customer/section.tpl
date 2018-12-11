{if $section_name eq 'message_box' && $customer_id}
    {capture name=menu}
    <ul>
        <li><a href="index.php?target=message_box&amp;mode=new">{$lng.lbl_new_message}</a></li>
        <li><a href="index.php?target=message_box&amp;mode=incoming">{$lng.lbl_avail_type_incoming}</a> {if $messages_counter.new gt 0} <b>{$messages_counter.new}</b>{/if}({$messages_counter.incoming})</li>
        <li><a href="index.php?target=message_box&amp;mode=sent">{$lng.lbl_sent}</a> ({$messages_counter.sent})</li>
        <li><a href="index.php?target=message_box&amp;mode=archive">{$lng.lbl_archive}</a> ({$messages_counter.archive})</li>
    </ul>
    {/capture}
    {include file='common/menu.tpl' title=$lng.lbl_message_box content=$smarty.capture.menu} 
{/if}