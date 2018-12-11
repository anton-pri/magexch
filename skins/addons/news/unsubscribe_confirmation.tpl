{capture name=section}
{$lng.txt_unsubscribed_msg}<br />
{$lng.lbl_email}: <b>{$email|replace:"\\":""}</b>
{/capture}
{ include file="common/section.tpl" title=$lng.txt_thankyou_for_unsubscription content=$smarty.capture.section extra='width="100%"'}
