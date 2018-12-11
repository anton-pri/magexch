{include file="mail/mail_header.tpl"}

{$lng.eml_dear|substitute:"customer":"`$userinfo.title` `$userinfo.firstname` `$userinfo.lastname`"},<br />
<br />
{$lng.eml_salesman_declined}<br />
<br />
{if $reason ne ""}
<b>{$lng.eml_reason}:</b><br />
{$reason}<br />
<br />
{/if}


{include file="mail/signature.tpl"}
