{select_country assign="countries"}
<select name="{$name}"{if $is_disabled} disabled{/if} {if $multiple}multiple="multiple" size='{$multiple}'{/if}>
{foreach from=$countries item=country}
<option value="{$country.country_code}"{if ($value eq $country.country_code) or (is_array($value) and $value[$country.country_code])}
selected="selected"{elseif $country.country_code eq $config.General.default_country and $value eq ""} selected="selected"{/if}>{$country.country|amp}</option>
{/foreach}
</select>
