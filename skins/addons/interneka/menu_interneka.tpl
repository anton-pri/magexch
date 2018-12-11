{capture name=menu}
<a href="http://interneka.com/index.php?target=AffiliateSignup&WID={$interneka_id6}">{$lng.lbl_interneka_click_to_register}</a><br/>
{/capture}
{include file='common/menu.tpl' title=$lng.lbl_interneka_affiliates content=$smarty.capture.menu}
