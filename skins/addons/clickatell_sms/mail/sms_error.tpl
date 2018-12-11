{include file="mail/mail_header.tpl"}

<p /><b>{$lng.eml_clickatell_error|default:'Clickatell SMS send error'}</b>

<p /><b>ERROR #{$result.errorCode}:</b>&nbsp;{$result.error}<br />
<p /><b>Error Description:</b>&nbsp;{$result.errorDescription}<br />
<p /><b>{$lng.lbl_phone|default:'Phone'}:</b> {$phone}<br /><br />
<p /><b>{$lng.lbl_sms_content|default:'SMS Content'}:</b> {$content}<br /><br />


{include file="mail/signature.tpl"}
