{include file="mail/mail_header.tpl"}

<p />{$lng.eml_dear|substitute:"customer":"`$userinfo.title` `$userinfo.firstname` `$userinfo.lastname`"},

<p />{$lng.eml_signin_salesman_notification}

<p />{$lng.lbl_profile_details}:

{include file="mail/profile_data.tpl" show_pwd="Y"}

{include file="mail/signature.tpl"}

