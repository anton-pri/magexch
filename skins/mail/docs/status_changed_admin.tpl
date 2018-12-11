{tunnel func='cw_doc_get_order_status_email' status_code=$order.status area_name='admin' email_part='message' mode='R' assign='order_status_body'}
{if $order_status_body ne ''}
{eval var=$order_status_body}
{else}
{include file="mail/docs/admin.tpl"}
{/if}

