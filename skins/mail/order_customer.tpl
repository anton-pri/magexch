{include file='mail/mail_header.tpl'}

<p />{$lng.eml_dear|substitute:"customer":"`$order.title` `$order.firstname` `$order.lastname`"},

<p />{$lng.eml_thankyou_for_order}

<p /><b>{$lng.lbl_invoice}:</b>

<p />
{include file='main/docs/doc_layout.tpl'}

{include file="mail/signature.tpl"}
