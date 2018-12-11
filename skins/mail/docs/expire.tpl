{include file='mail/mail_header.tpl'}

<p />{$lng.eml_doc_expired|substitute:"doc_id":$order.display_id}

{include file='main/docs/doc.tpl'}

{include file='mail/signature.tpl'}
