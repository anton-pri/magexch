{include file="mail/mail_header.tpl"}

<p />{$lng.eml_signin_admin_notification}

<p />{$lng.lbl_profile_details}:

{include file="mail/profile_data.tpl" show_pwd="Y"}

{include file="mail/signature.tpl"}
