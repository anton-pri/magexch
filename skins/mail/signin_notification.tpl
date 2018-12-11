{include file="mail/mail_header.tpl"}

<p />{$lng.eml_dear|substitute:"customer":"`$userinfo.title` `$userinfo.firstname` `$userinfo.lastname`"},

<p />{$lng.eml_signin_notification}

<p />{$lng.lbl_your_profile}:

{include file="mail/profile_data.tpl" show_pwd="Y"}

{include file="mail/signature.tpl"}

