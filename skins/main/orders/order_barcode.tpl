{foreach from=$orders_data item=doc}
{assign var="products" value=$doc.products}

{foreach from=$products item=product}
    {section name=amount loop=$product.amount}
{eval var=$config.Bar_Code.bar_code_layout}
    {/section}
{/foreach}

{/foreach}
