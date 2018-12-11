{include file="mail/mail_header.tpl"}

<p />{if $current_area eq 'A'}{$lng.eml_dear_user}{else}{$lng.eml_dear_customer}{/if},
<p />{$lng.eml_password_reset_link}
<p />
<a href="{if $user_info.usertype eq 'A'}{$catalogs.admin}{elseif $user_info.usertype eq 'V'}{$catalogs.seller}/{elseif $user_info.usertype eq 'C'}{$catalogs.customer}{/if}{$reset_url}">{$reset_url}</a>

{include file="mail/signature.tpl"}

