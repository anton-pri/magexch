{include file="mail/mail_header.tpl"}

<p />{$lng.eml_dear|substitute:"customer":$recipient_data.recipient_name},

<p />{$lng.eml_giftreg_confirmation_msg|substitute:"sender":"`$userinfo.title` `$userinfo.firstname` `$userinfo.lastname`"}

<hr size="1" noshade="noshade" />

<p />{$lng.lbl_event}: <b>{$event_data.title}</b>

<hr size="1" noshade="noshade" />

<p />{$lng.eml_giftreg_click_to_confirm}:  <a href="index.php?target=giftregs&cc={$confirmation_code}">{$http_customer_location}/giftregs.php?cc={$confirmation_code}</a>

<p />{$lng.eml_giftreg_click_to_decline}:  <a href="index.php?target=giftregs&cc={$decline_code}">{$http_customer_location}/giftregs.php?cc={$decline_code}</a>

<p />
{include file="mail/signature.tpl"}
