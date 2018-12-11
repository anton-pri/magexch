{if $events_list}
<div class="wl_tpls">
<form name='event' method='GET'>
    <input type="hidden" name="target" value="gifts" />
    <input type="hidden" name="mode" value="wishlist" />
    Show products from wishlist
    <select name='event_id' onchange='document.event.submit();'>
        <option value='0' {if $event_id==0} selected{/if}>-- Default --</option>
        {foreach from=$events_list item=event}
        <option value='{$event.event_id}' {if $event_id==$event.event_id} selected{/if}>{$event.title}</option>
        {/foreach}
    </select>
</form>
</div>
{/if}

{if $wl_products}
<div class="wishlist_products">
    {foreach from=$wl_products item=product}
    <div class="product_info">

<div style="overflow: hidden;">

<div class="left wishlist_img" >
<a href="{pages_url var="product" product_id=$product.product_id quantity=$product.amount}"  class="thumbnail">{include file='common/product_image.tpl' image=$product.image_thumb product_id=$product.product_id}</a>
</div>
<div class="left wishlist_content">
<a href="{pages_url var="product" product_id=$product.product_id cat=$cat page=$navigation_page}" class="product">{$product.product}</a>
<div class="descr">{$product.descr|truncate:150:"...":true}</div>
{* kornev, TOFIX *}
{if $product.product_options}
    <b>{$lng.lbl_selected_options}:</b><br />
    {include file='addons/product_options/main/options/display.tpl' options=$product.product_options}
    {if $product.product_options ne "" && $giftregistry eq "" && $source ne "giftreg"}
    {include file='buttons/edit_product_options.tpl' mode="wishlist" id=$product.wishlist_id|cat:"&eventid="|cat:$event_id}
    {/if}
{/if}

<div class="wishlist_counter">
<form action="index.php?target={$current_target}&mode=wishlist" method="post" name="update{$product.wishlist_id}_form">
<input type="hidden" name="wlitem" value="{$product.wishlist_id}" />
<input type="hidden" name="action" value="update" />
<input type="hidden" name="event_id" value="{$event_id}" />
<input type="hidden" name="js_tab" value="{$js_tab}" />
<input type="hidden" name="script" value="{$script|default:'wishlist'}" />



{include file='common/currency.tpl' value=$product.display_price} x
{if $allow_edit && ($product.distribution eq '' || !$addons.egoods)}
<input type="number" size="3" name="quantity" value="{$product.amount}" />
{else}
<input type="hidden" size="3" name="quantity" value="{$product.amount}" /> {$product.amount}
{/if}
= {math equation="price*amount" price=$product.display_price amount=$product.amount format="%.2f" assign=unformatted}
{include file='common/currency.tpl' value=$unformatted}



{if $product.taxes}
{include file="customer/main/taxed_price.tpl" taxes=$product.taxes}
{/if}


{if $events_list}
<div class='wl_item_event'>
    Wishlist
    <select name='eventid'>
        <option value='0' {if $event_id==0} selected{/if}>-- Default --</option>
        {foreach from=$events_list item=event}
        <option value='{$event.event_id}' {if $event_id==$event.event_id} selected{/if}>{$event.title}</option>
        {/foreach}
    </select>
</div>
{else}
<input type="hidden" name="eventid" value="{$event_id}" />
{/if}

</form>
</div>
    {if $product.amount_purchased gt 0}
        {if $product.amount_purchased ge $product.amount_requested}
    &nbsp;({$lng.txt_all_items_already_purchased})
        {else}
&nbsp;({$lng.txt_items_already_purchased|substitute:"items":$product.amount_purchased})
        {/if}
    {/if}
    {* <div> added to cart </div> *}
</div>

</div>

{*
{capture name=product_url}{pages_url var="product" product_id=$product.product_id cat=$product.category_id}{/capture}
<div class="left">{include file='buttons/details.tpl' href=$smarty.capture.product_url}</div>
*}

{if $allow_edit}
<div class="left" style="margin: 5px 2px;">{include file='buttons/delete_item.tpl' href="index.php?target=`$current_target`&mode=wishlist&action=delete&wlitem=`$product.wishlist_id`&event_id=`$event_id`"}</div>

{include file='buttons/update.tpl' href="javascript: cw_submit_form('update`$product.wishlist_id`_form');"}
{/if}
{if !$from_quote}
	{include file='buttons/add_to_cart.tpl' href="javascript: cw_submit_form('update`$product.wishlist_id`_form','add2cart');" style='btn'}
{/if}

    </div>
    {/foreach}
</div>
{elseif !$wl_giftcerts}
<div class="wl_not_found"><center>{$lng.lbl_not_found}</center></div>
{/if}

{include file='addons/estore_gift/gc_cart.tpl' giftcerts_data=$wl_giftcerts}

{if $wl_products && $allow_edit}
<div class="send_all">
<form method="post" action="index.php?target={$current_target}&mode=wishlist" name="sendall_form">
<input type="hidden" name="action" value="entire_list" />
	<div class="float-left">{$lng.lbl_send_entire_wishlist}: <input type="email" size="18" name="friend_email" />&nbsp;</div>
	{include file='buttons/button.tpl' href="javascript:cw_submit_form('sendall_form')" button_title=$lng.lbl_send style='btn'}
</form>
</div>
{include file='buttons/button.tpl' button_title=$lng.lbl_wl_clear href="index.php?target=`$current_target`&mode=wishlist&action=wlclear&event_id=`$event_id`" style='btn'}
{/if}
