{include file='mail/mail_header.tpl'}
<br><br>
{tunnel func='cw_doc_get_order_status_email' status_code=$order.status area_name='customer' email_part='message' assign='order_status_subj'}{if $order_status_subj ne ''}{eval var=$order_status_subj}{else}{$lng.eml_order_notification|substitute:"doc_id":$order.display_id}{/if}

{include file='main/docs/doc_layout.tpl'}

{include file='mail/signature.tpl'}
