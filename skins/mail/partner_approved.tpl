{include file="mail/mail_header.tpl"}

{$lng.eml_dear|substitute:"customer":"`$userinfo.title` `$userinfo.firstname` `$userinfo.lastname`"},<br />
<br />
{$lng.eml_salesman_approved}<br />
<br />
{$lng.lbl_profile_details}:<br />
{include file="mail/profile_data.tpl" userinfo=$userinfo}
<br />

{include file="mail/signature.tpl"}
