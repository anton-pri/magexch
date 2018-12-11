{foreach key=tax_name item=tax from=$order.info.taxes}
{$tax.tax_display_name}{if $tax.rate_type eq "%"} {$tax.rate_value}%{/if}:<br />
{/foreach}
