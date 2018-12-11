{if $wl_products or $wl_giftcerts}
{include file='addons/estore_gift/wishlist.tpl' wl_products=$wl_products source='giftreg' script="events" js_tab='wishlist'}

{include file='buttons/button.tpl' button_title=$lng.lbl_giftreg_send_wishlist href="index.php?target=giftreg_manage&mode=send&eventid=`$eventid`"}

{else}
<div class="dialog_title">
{$lng.txt_giftreg_wishlist_empty}<br />
{$lng.txt_giftreg_wishlist_empty_note}
</div>
{/if}
