{include file='mail/mail_header.tpl'}
<br /><br />
{assign var=max_truncate value=$config.Email.max_truncate}
{math assign="max_space" equation="x+5" x=$max_truncate}
{assign var="max_space" value="%-"|cat:$max_space|cat:"s"}

{if $userinfo.main_address.firstname}
	{assign var=customer_name value="`$userinfo.main_address.title` `$userinfo.main_address.firstname` `$userinfo.main_address.lastname`"}
{else}
	{assign var=customer_name value=$lng.lbl_customer}
{/if}

{$lng.eml_dear|substitute:"customer":$customer_name},
{$lng.eml_signin_notification}
<br />
{include file='mail/profile_data.tpl'}

{include file='mail/signature.tpl'}
