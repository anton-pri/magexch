{include file='mail/mail_header.tpl'}
<br /><br />
{assign var=max_truncate value=$config.Email.max_truncate}
{math assign="max_space" equation="x+5" x=$max_truncate}
{assign var="max_space" value="%-"|cat:$max_space|cat:"s"}

{$lng.eml_signin_admin_notification}
<br />
{include file='mail/profile_data.tpl'}

{include file='mail/signature.tpl'}
