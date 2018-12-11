{capture name=section}

{if $ge_id}<b>* {$lng.lbl_note}:</b> {$lng.txt_edit_product_group}{/if}
<table class="header" width="100%">
<tr>
	{if $ge_id}<th width="15">&nbsp;</th>{/if}
	<th width="15"><img src="{$ImagesDir}/spacer.gif" width="15" height="1" border="0" /></th>
	<th width="25%">{$lng.lbl_quantity}</th>
	<th width="25%">{$lng.lbl_price_per_item} ({$config.General.currency_symbol})</th>
	<th width="50%">{$lng.lbl_membership}</th>
</tr>
<tr>
{if $ge_id}<td>&nbsp;</td>{/if}
	<td>&nbsp;</td>
	<td><b>1</b></td>
	<td><b>{$product.price|formatprice}</b></td>
	<td><b>{$lng.lbl_all}</b></td>
</tr>

{foreach from=$products_prices item=v}
{if $v.membershipid > 0 || $v.quantity > 1}
<tr{cycle values=' class="cycle",'}>
	<td>{$v.quantity}</td>
	<td>{include file='common/currency.tpl' value=$v.price}</td>
	<td><b>
        {if $v.membershipid}
{foreach from=$memberships item=m}
{if $v.membershipid eq $m.membershipid}{$m.membership}{/if}
{/foreach}
        {else}{$lng.lbl_all}{/if}
        </b>
	</td>
</tr>
{/if}
{/foreach}
</table>

<form action="index.php?target={$current_target}" method="post">
<input type="hidden" name="action" value="send_prices" />
<input type="hidden" name="product_id" value="{$product.product_id}">
{include file='main/textarea.tpl' name='prices_descr' cols=45 rows=8 data=''}
<input type="submit" value="{$lng.lbl_send}">
</form>
{/capture}
{include file='common/section.tpl' title=$lng.lbl_wholesale_prices content=$smarty.capture.section}
