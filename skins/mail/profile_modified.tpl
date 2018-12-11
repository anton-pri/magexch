{include file="mail/mail_header.tpl"}

<p />{$lng.eml_dear|substitute:"customer":"`$userinfo.title` `$userinfo.firstname` `$userinfo.lastname`"},

<p />{$lng.txt_profile_modified}

<p />{$lng.lbl_your_profile}:

{include file="mail/profile_data.tpl"}

{include file="mail/signature.tpl"}
