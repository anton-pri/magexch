{include file="mail/mail_header.tpl"}

<p />{$lng.eml_dear|substitute:"customer":"`$userinfo.title` `$userinfo.firstname` `$userinfo.lastname`"},

<p />{$lng.eml_profile_deleted|substitute:"company":$config.Company.company_name}

{include file="mail/signature.tpl"}

