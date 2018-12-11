<select name="{$name}"{if $onchange} onchange="{$onchange}"{/if}{if $multiple} multiple="{$multiple}"{/if} style="min-width:70px;">
{foreach from=$all_languages key=code item=language}
<option value="{$code}"{if (!$multiple && ($code eq $value || (!$value and $shop_language eq $code))) || (is_array($value) && $multiple && in_array($code, $value))} selected="selected"{/if}>{$language.language}</option>
{/foreach}
</select>
