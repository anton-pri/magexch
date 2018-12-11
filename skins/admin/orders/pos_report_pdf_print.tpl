{assign var='sum_profit' value=0}
{assign var='sum_price' value=0}
{assign var='sum_gsold' value=0}
{assign var='sum_purchased' value=0}
<table class="header" width="100%">
<tr>
{foreach from=$search_prefilled.elements item=item}
    <th>{lng name=$elements.$item}</th>
{/foreach}
</tr>
{foreach from=$pos_items item=item}
<tr{cycle values=", class='cycle'"}>
{foreach from=$search_prefilled.elements item=key}
    <td>
{if $key eq 'profit'}
      {math equation='a+b' a=$item.$key b=$sum_profit assign='sum_profit'}
{elseif $key eq 'price'}
      {math equation='a+b' a=$item.$key b=$sum_price assign='sum_price'}
{elseif $key eq 'gsold'}
      {math equation='a+b' a=$item.$key b=$sum_gsold assign='sum_gsold'}
{elseif $key eq 'purchased'}
      {math equation='a+b' a=$item.$key b=$sum_purchased assign='sum_purchased'}
{/if}
{if $key eq 'profit'}
        {include file='common/currency.tpl' value=$item.$key display_sign=1}
{elseif $key eq 'price' || $key eq 'gsold' || $key eq 'purchased'}
        {include file='common/currency.tpl' value=$item.$key}
{else}
        {$item.$key}
{/if}
    </td>
{/foreach}
</tr>
{/foreach}
<tr>
{foreach from=$search_prefilled.elements item=key}
    <th>
{if $key eq 'profit'}
        {include file='common/currency.tpl' value=$sum_profit display_sign=1}
{elseif $key eq 'price'}
        {include file='common/currency.tpl' value=$sum_price}
{elseif $key eq 'gsold'}
        {include file='common/currency.tpl' value=$sum_gsold}
{elseif $key eq 'purchased'}
        {include file='common/currency.tpl' value=$sum_purchased}
{else}
&nbsp;
{/if}
    </th>
{/foreach}
</tr>
</table>
