~/public_html/cw/skins_magazineexchange/_autoload/_seller/addons/messaging_system/mail/new_message.tpl<br>
{$lng.eml_mail_header}
<b>You have received a new message from {$sender_name} at <a href="{$current_location}">{$config.Company.company_name}</a></b><br />
<b>Subject:</b>{$subject}<br />
<b>Body:</b>{$body|@nl2br}<br />
<a href="{$current_location}/index.php?target=message_box&mode=new&contact_id={$customer_id}&conversation_id={$current_conversation_id}">Link to reply</a><br />
