{if $customer_id}
{capture name=menu}
<ul>
{if $addons.estore_gift}
<li><a href="index.php?target=gifts&amp;mode=friends">{$lng.lbl_friends_wish_list}</a></li>
<li><a href="index.php?target=gifts">{$lng.lbl_wish_list}</a></li>
<li><a href="index.php?target=gifts&amp;mode=events">{$lng.lbl_gift_registry}</a></li>
{/if}
{if $customer_id}
<li><a href="index.php?target=acc_manager">{$lng.lbl_modify_profile}</a></li>
{/if}
</ul>
{/capture}
{include file='common/menu.tpl' title=$lng.lbl_my_account content=$smarty.capture.menu}
{/if}
