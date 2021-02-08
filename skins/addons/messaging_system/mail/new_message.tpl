<b>You have received a new message from {$sender_name} at <a href="{$current_location}">{$config.Company.company_name}</a></b><br /><br />
<b>Subject:</b> {$subject}<br />
<b>Body:</b> {$body|@nl2br}<br />
{if $is_recipient_seller}
    <a href="{$current_location}/seller/index.php?target=message_box&mode=new&contact_id={$customer_id}&conversation_id={$current_conversation_id}">Link to reply</a>
{else}
    <a href="{$current_location}/index.php?target=message_box&mode=new&contact_id={$customer_id}&conversation_id={$current_conversation_id}">Link to reply</a>
{/if}
<br />
