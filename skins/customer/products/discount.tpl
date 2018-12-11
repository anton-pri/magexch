{assign var='discount' value=0}
{if $products[product].list_price gt 0 and $products[product].price lt $products[product].list_price_net}
{math equation="100-(price/lprice)*100" price=$products[product].price lprice=$products[product].list_price_net assign='discount' format="%3.0f"}
{/if}
{if $discount gt 0}{if $config.General.alter_currency_symbol},{/if} {if $view eq 'compact'}<br/><nobr>{$lng.lbl_you_save}</nobr><br/>{else}{$lng.lbl_you_save} {/if}<font class="Discount"><b>{$discount}%</b></font>{/if}
