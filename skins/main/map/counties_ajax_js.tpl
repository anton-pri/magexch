{strip}
{ldelim}
"county_name":"{$county_name}",
"counties_count":"{count value=$counties}",
"disabled":"{$disabled}",
"selected":"{$selected}",
"counties":{ldelim}
{foreach from=$counties item=county name="cod_types"}
    "{$county.county_id}":{ldelim}
        "county":"{$county.county|escape:"json"}"
    {rdelim}{if !$smarty.foreach.cod_types.last},{/if}
{/foreach}
{rdelim},
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
