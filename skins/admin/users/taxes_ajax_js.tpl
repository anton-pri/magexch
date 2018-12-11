{ldelim}
"selected":"{$user_selection}",
"taxes":{ldelim}
{foreach from=$taxes item=tax name="taxes"}
    "{$tax.tax_id}":{ldelim}
        "value":"{$tax.value|escape:"json"}",
        "title":"{$tax.title|escape:"json"}"
    {rdelim}{if !$smarty.foreach.taxes.last},{/if}
{/foreach}
{rdelim}
{rdelim}
