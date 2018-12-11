{capture name=menu}
<ul>
<li><a href="index.php?target=help" class="Bullet">{$lng.lbl_help}</a></li>
{if $membership_faq}
<li><a href="javascript:window.open('index.php?target=faq_membership&amp;mode=membership&cat_id={$membership_faq.main_category.rubrik}', 'Member_FAQ', 'width=780,height=450,resizable=no,menubar=no,scrollbars=yes'); void(0);" class="Bullet">{$lng.lbl_faq}</a></li>{/if}
{if $addons.Salesman}
<li><a href="{$catalogs.salesman}" class="Bullet"><b>{$lng.lbl_login_of_sales_managers}</b></a></li>
{/if}
{if $addons.estore_gift and $wlid ne ""}
<li><a href="index.php?target=cart&amp;mode=friend_wl&amp;wlid={$wlid}" class="Bullet">{$lng.lbl_friends_wish_list}</a></li>
{/if}
<li><a href="{pages_url var='help' section='contactus'}" class="Bullet">{$lng.lbl_contact_us}</a></li>
<li><a href="{pages_url var='help' section='business'}" class="Bullet">{$lng.lbl_privacy_statement}</a></li>
<li><a href="{pages_url var='help' section='conditions'}" class="Bullet">{$lng.lbl_terms_n_conditions}</a></li>
</ul>
{/capture}
{include file='common/menu.tpl' title=$lng.lbl_resources content=$smarty.capture.menu style='grey'}
