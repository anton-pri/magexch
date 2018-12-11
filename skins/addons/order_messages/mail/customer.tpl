{include file='mail/mail_header.tpl'}
<br><br>

{$message.body|replace:"\n\r":"<br>"}

{include file='addons/order_messages/doc_layout_post.tpl'}

{include file='mail/signature.tpl'}
