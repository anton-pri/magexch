{include file='common/page_title.tpl' title=$lng.lbl_search_wishlists}

{$lng.txt_admin_wishlists}

<br /><br />

{if $wishlists eq ''}

{capture name=section}
<form name="searchform" action="index.php?target=wishlists" method="post">
<input type="hidden" name="action" value="search" />

<table cellpadding="1" cellspacing="5" width="100%">

<tr>
	<td height="10" class="FormButton" nowrap="nowrap">{$lng.lbl_customer}:</td>
	<td width="10" height="10">&nbsp;</td>
	<td height="10" width="100%"><input type="text" name="search_data[email]" value="{$search_data.email}" /></td>
</tr>
<tr>
	<td height="10" class="FormButton" nowrap="nowrap">{$lng.lbl_sku}:</td>
	<td width="10" height="10">&nbsp;</td>
	<td height="10" width="100%"><input type="text" name="search_data[sku]" value="{$search_data.sku}" /></td>
</tr>
<tr>
	<td height="10" class="FormButton" nowrap="nowrap">{$lng.lbl_product_id}:</td>
	<td width="10" height="10">&nbsp;</td>
	<td height="10" width="100%"><input type="text" name="search_data[product_id]" value="{$search_data.product_id}" /></td>
</tr>
<tr>
	<td height="10" class="FormButton" nowrap="nowrap">{$lng.lbl_product}:</td>
	<td width="10" height="10">&nbsp;</td>
	<td height="10" width="100%"><input type="text" name="search_data[product]" value="{$search_data.product}" size="40" /></td>
</tr>

<tr>
	<td colspan="3">&nbsp;</td>
</tr>
<tr>
	<td colspan="2">&nbsp;</td>
	<td><input type="submit" value="{$lng.lbl_search|strip_tags:false|escape}" /></td>
</tr>

</table>
</form>
{/capture}
{include file="common/section.tpl" title=$lng.lbl_search_wishlists content=$smarty.capture.section extra='width="100%"'}

<br />

{/if}

{if $mode eq "search"}
{include file="common/navigation_counter.tpl"}
{/if}

{if $mode eq "search" and $wishlists ne ""}

<br /><br />

{capture name=section}

<div align="right">{include file='buttons/button.tpl' button_title=$lng.lbl_search_again href="index.php?target=wishlists"}</div>

<br />

<table cellpadding="0" cellspacing="0" width="100%">
<tr><td>

{include file="common/navigation.tpl"}

<table cellpadding="1" cellspacing="2" width="100%">
<tr class="TableHead">
	<td>{$lng.lbl_customer}</td>
	<td>{$lng.lbl_items}</td>
</tr>
{foreach from=$wishlists item=v}
<tr{cycle name=c1 values=', class="TableSubHead"'}>
	<td>{if ($usertype eq "A" and $acc and $accl.1000) or ($usertype eq "P" and $addons.Simple_Mode ne "")}<a href="index.php?target=user_modify&user={$v.customer_id|escape:"url"}&amp;usertype={$v.usertype}">{$v.firstname}&nbsp;{$v.lastname}&nbsp;({$v.customer_id})</a>{else}{$v.firstname}&nbsp;{$v.lastname}&nbsp;({$v.customer_id}){/if}</td>
	<td><a href="index.php?target=wishlists&mode=wishlist&amp;customer={$v.customer_id}">{$lng.lbl_n_items_in_wishlist|substitute:"items":$v.products_count}</a></td>
</tr>
{/foreach}
</table>

{include file="common/navigation.tpl"}

</td></tr>

</table>
{/capture}
{include file="common/section.tpl" title=$lng.lbl_search_results content=$smarty.capture.section extra='width="100%"'}

{/if}
