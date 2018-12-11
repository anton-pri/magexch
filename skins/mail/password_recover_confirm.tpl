{include file="mail/mail_header.tpl"}

<p />{$lng.eml_dear_customer},
<p />{$lng.eml_password_reset_link}
<p />

<a href="{if $user_info.usertype eq 'A'}{$catalogs.admin}{elseif $user_info.usertype eq 'V'}{$catalogs.seller}/{elseif $user_info.usertype eq 'C'}{$catalogs.customer}/{/if}{$reset_url}">
{if $user_info.usertype eq 'A'}{$catalogs.admin}/{elseif $user_info.usertype eq 'C'}{$catalogs.customer}/{/if}{$reset_url}
</a>

{include file="mail/signature.tpl"}

