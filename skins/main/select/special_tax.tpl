<select name="{$name}">
    <option value="0">{$lng.lbl_no}</option>
{foreach from=$special_taxes item=special_tax}
    <option value="{$special_tax.tax_id}"{if $special_tax.tax_id eq $value} selected="selected"{/if}>{$tax.value}% {$special_tax.title}</option>
{/foreach}
</select>
