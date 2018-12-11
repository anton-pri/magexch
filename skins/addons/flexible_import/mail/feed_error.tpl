{include file="mail/mail_header.tpl"}

<p>The error was detected in feed file while running the import profile "{$import_profile.name}"<br>
<p>
{if $added_columns ne ''}New columns detected in parsed file: {$added_columns}<br>{/if}
{if $missing_columns ne ''}Following columns are not found in parsed file: {$missing_columns}<br>{/if}


{include file="mail/signature.tpl"}
