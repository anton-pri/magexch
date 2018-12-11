{include file="mail/mail_header.tpl"}
<p />{$lng.eml_dear|substitute:"customer":$giftcert.recipient},

<p />{if $giftcert.purchaser ne ""}{assign var="purchaser" value=$giftcert.purchaser}{else}{assign var="purchaser" value=$giftcert.purchaser_email}{/if}{include file='common/currency.tpl' value=$giftcert.amount assign="amount"}{$lng.eml_gc_header|substitute:"purchaser":$purchaser:"amount":$amount}


<p />{$lng.lbl_message}:
<br />
{$giftcert.message}

<p />
<table border="1" cellpadding="20" cellspacing="0">
<tr><td>{$lng.lbl_gc_id}: {$giftcert.gc_id}</td></tr>
</table>

<p /><pre>{$lng.eml_gc_body}</pre>

{include file="mail/signature.tpl"}
