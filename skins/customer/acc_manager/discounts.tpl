<table class="header">
<tr>
    <th>{$lng.lbl_discount} (%)</th>
    <th>{$lng.lbl_orderby}</th>
</tr>
{if $discounts}
{foreach from=$discounts item=discount}
<tr valign="top">
    <td><input type="text" value="{$discount.discount|formatprice}" size="15"></td>
    <td><input type="text" value="{$discount.orderby}" size="5"></td>
</tr>
{/foreach}
{else}
<tr>
    <td align="center" colspan="3">{$lng.lbl_not_found}</td>
</tr>
{/if}
</table>
