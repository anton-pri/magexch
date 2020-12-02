{* countries list *}
<select name="{$name}" class="form-control" id="{$name|id}"{if $disabled} disabled{/if} onchange="cw_address_init(this.value, state, '{$name}'); cw_checkout_save_addresses();">
<option value="">{$lng.lbl_country}</option>
{foreach from=$countries item=country}
<option value="{$country.country_code}" {if $default eq $country.country_code || (!$default and $country.country_code eq $config.General.default_country)} selected="selected"{/if}>{$country.country}{if $show_code}&nbsp;({$country.country_code}){/if}</option>
{/foreach}
</select>
