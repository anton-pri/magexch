{include file="mail/mail_header.tpl"}
<p />{$lng.eml_gc_notification|substitute:"recipient":$giftcert.recipient}

<p />{$lng.eml_gc_copy_sent|substitute:"email":$giftcert.recipient_email}:

<p />=========================| start |=========================

<table cellpadding="15" cellspacing="0" width="100%"><tr><td bgcolor="#EEEEEE">
{include file="mail/giftcert/body.tpl"}
</td></tr></table>

<p />=========================| end |=========================

{include file="mail/signature.tpl"}
