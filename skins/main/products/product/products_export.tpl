
{/if}
{section name=prod_num loop=$products}
{assign var="cntr" value=1}{foreach key=key item=item from=$products[prod_num]}{if $key ne 'categories' && $key ne 'prices'}{$item}{if $key eq 'category' && $max_categories > 1}{foreach from=$products[prod_num].categories item=c}{$delimiter}{$c}{/foreach}{elseif $key eq 'price' && $max_prices > 1}{foreach from=$products[prod_num].prices item=p}{$delimiter}{$p|default:"0.00"}{/foreach}{/if}{if $cntr lt $total_columns}{$delimiter}{math assign="cntr" equation="x+1" x=$cntr}{/if}{/if}{/foreach}

{/section}
