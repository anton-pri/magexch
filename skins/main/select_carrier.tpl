<select name="{$name}"{if $disabled} disabled{/if}>
{foreach from=$carriers item=carrier}
<option value="{$carrier.carrier_id}"{if $carrier.carrier_id eq $value} selected="selected"{/if}>{$carrier.carrier}</option>
{/foreach}
</select>
