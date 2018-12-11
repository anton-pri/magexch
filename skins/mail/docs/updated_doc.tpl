{include file='mail/mail_header.tpl'}
<br><br>
{$lng.eml_update_order_notification|substitute:"doc_id":$order.display_id}

{include file='main/docs/doc_layout.tpl'}

{include file='mail/signature.tpl'}
