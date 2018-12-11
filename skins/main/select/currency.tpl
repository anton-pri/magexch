{tunnel func='cw_currency_get_list' load='currency' assign='currencies'}
<select name="{$name}"{if $onchange} onchange="{$onchange}"{/if}{if $disabled} disabled{/if}>
{foreach from=$currencies item=currency}
<option value="{$currency.code}"{if $currency.code eq $value} selected="selected"{/if}>{$currency.code} {$currency.name}</option>
{/foreach}
</select>
