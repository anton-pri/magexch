{capture name=menu}
<ul>
<li class="contact_us"><a href="{pages_url var='help' section='contactus'}" class="Bullet">{$lng.lbl_contact_us}</a></li>
</ul>
{/capture}
{include file='common/menu.tpl' title=$lng.lbl_online_support content=$smarty.capture.menu style='support'}
