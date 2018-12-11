{capture name=section}
{$lng.txt_newsletter_subscription_msg}:<br />
<b>{$email|replace:"\\":""}</b>
<p />
{$lng.txt_unsubscribe_information} <a href="{$catalogs.customer}/index.php?target=news&mode=unsubscribe&email={$email|replace:"\\":""}"><font class="FormButton">{$lng.lbl_this_url}</font></a>.
{/capture}
{ include file="common/section.tpl" title=$lng.txt_thankyou_for_subscription content=$smarty.capture.section extra='width="100%"'}
