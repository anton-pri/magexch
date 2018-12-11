{strip}
{ldelim}
"region_name":"{$region_name}",
"regions_count":{count value=$regions},
"selected":"{$selected}",
"disabled":"{$disabled}",
"regions":{ldelim}
{foreach from=$regions item=region name="cod_types"}
    "{$region.region_id}":{ldelim}
        "region":"{$region.region|escape:"json"}"
    {rdelim}{if !$smarty.foreach.cod_types.last},{/if}
{/foreach}
{rdelim},
"state_name":"{$state_name}",
"states_count":{count value=$states},
"state_selected":"{$states_selected}"{if $states},
"states":{ldelim}
{foreach from=$states item=state name="cod_types"}
    "{$state.state_id}":{ldelim}
        "state":"{$state.state|escape:"json"}",
        "code":"{$state.state_code|escape:"json"}",
        "state_id":"{$state.state_id}"
    {rdelim}{if !$smarty.foreach.cod_types.last},{/if}
{/foreach}
{rdelim}
{/if}
{rdelim}
{/strip}
