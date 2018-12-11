{strip}
{ldelim}
"cities_count":"{count value=$cities}",
"city_name":"{$city_name}",
"city_value":"{$city_value}",
"cities":{ldelim}
{foreach from=$cities item=city name="cod_types"}
    "{$city.city_id}":{ldelim}
        "city":"{$city.city|escape:"json"}"
    {rdelim}{if !$smarty.foreach.cod_types.last},{/if}
{/foreach}
{rdelim}
{rdelim}
{/strip}
