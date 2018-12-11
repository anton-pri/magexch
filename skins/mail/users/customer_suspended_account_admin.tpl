{include file='mail/mail_header.tpl'}
<br /><br />
{assign var=max_truncate value=$config.Email.max_truncate}
{math assign="max_space" equation="x+5" x=$max_truncate}
{assign var="max_space" value="%-"|cat:$max_space|cat:"s"}
{capture assign='full_user_page_link'}{$catalogs.admin}/{$user_page_link}{/capture}
{$lng.eml_customer_suspend_account_admin_notification|substitute:'user_page_link':$full_user_page_link}
<br />
{include file='mail/profile_data.tpl'}

{include file='mail/signature.tpl'}
