{foreach key=tax_name item=tax from=$order.info.taxes}
{include file='common/currency.tpl' value=$tax.tax_cost}<br />
{/foreach}
