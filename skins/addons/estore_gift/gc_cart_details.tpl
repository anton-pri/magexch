{if $cart.giftcerts ne ""}
{if $cart.products}
<tr>
<td colspan="{$colspan}">&nbsp;</td>
</tr>
<tr bgcolor="#EEEEEE">
<th align="left" colspan="{$colspan}">{$lng.lbl_gift_certificates}:</th>
</tr>
{/if}
{assign var="gc_summary_subtotal" value=0}
{section name=giftcert loop=$cart.giftcerts}
{if $cart.giftcerts[giftcert].deleted eq ""}
{assign var="have_giftcerts" value="Y"}
{math equation="x+y" x=$gc_summary_subtotal y=$cart.giftcerts[giftcert].amount assign="gc_summary_subtotal"}
<tr{if $bg eq ""}{assign var="bg" value="1"} bgcolor="#FFFFFF"{else}{assign var="bg" value=""} bgcolor="#EEEEEE"{/if}>
<td><a href="index.php?target=giftcert&gcindex={%giftcert.index%}" title="{$lng.lbl_gc_for|escape} {$cart.giftcerts[giftcert].recipient|escape}">GC #{math equation="x+1" x=%giftcert.index%}</a></td>
{if $cart.display_cart_products_tax_rates eq "Y"}
<td>&nbsp;</td>
{/if}
<td align="right" nowrap="nowrap">{include file='common/currency.tpl' value=$cart.giftcerts[giftcert].amount}</td>
<td align="center">1</td>
{if $cart.discount gt 0}
<td align="right" nowrap="nowrap">{include file='common/currency.tpl' value=0}</td>
{/if}
{if $addons.discount_coupons ne "" and $cart.coupon}
<td align="right" nowrap="nowrap">{include file='common/currency.tpl' value=0}</td>
{/if}
<td align="right" nowrap="nowrap">{include file='common/currency.tpl' value=$cart.giftcerts[giftcert].amount}</td>
</tr>
{/if}
{/section}
{if $have_giftcerts eq "Y"}
<tr class="TableHead">
<th align="left">{$lng.lbl_summary}:</th>
{if $cart.display_cart_products_tax_rates eq "Y"}
<td>&nbsp;</td>
{/if}
<td align="right" nowrap="nowrap"><b>{include file='common/currency.tpl' value=$gc_summary_subtotal}</b></td>
<td align="right">&nbsp;</td>
{if $cart.discount gt 0}
<td align="right" nowrap="nowrap">{include file='common/currency.tpl' value=0}</td>
{/if}
{if $addons.discount_coupons ne "" and $cart.coupon}
<td align="right" nowrap="nowrap">{include file='common/currency.tpl' value=0}</td>
{/if}
<td align="right" nowrap="nowrap">{include file="currency.tpl value=$gc_summary_subtotal}</td>
</tr>
{/if}
{/if}
