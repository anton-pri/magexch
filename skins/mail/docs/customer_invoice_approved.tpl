{include file='mail/mail_header.tpl'}
<br><br>
{$lng.eml_order_notification|substitute:"doc_id":$order.display_id}

<div style="padding: 15px 0">
	<a style="font-size:16px;font-weight:bold" href="index.php?target=docs_I&mode=quote&doc_id={$doc.doc_id}">{$lng.lbl_complete_order}</a> - <span style="font-size:10px">{$lng.txt_link_to_cart_from_qoute}</span>
</div>

{include file='main/docs/doc_layout.tpl'}

{include file='mail/signature.tpl'}
