{include file="mail/mail_header.tpl"}

<p />{$lng.eml_subscribed}

<p />{$lng.eml_unsubscribe_information}
<br />
<a href="{$catalogs.customer}/index.php?target=unsubscribe&email={$email|escape}">{$catalogs.customer}/mail/unsubscribe.php?email={$email|escape}</a>

{include file="mail/signature.tpl"}
