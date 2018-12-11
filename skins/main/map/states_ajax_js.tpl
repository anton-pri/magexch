{strip}
{ldelim}
"state_name":"{$state_name}",
"states_count":"{count value=$states}",
"selected":"{$selected}",
"disabled":"{$disabled}",
"states":{ldelim}
{foreach from=$states item=state name="cod_types" key=state_idx}
    "{$state_idx}":{ldelim}
        "state":"{$state.state|escape:"json"}{if $show_code} ({$state.state_code|escape:"json"}){/if}",
        "code":"{$state.state_code|escape:"json"}",
        "state_id":"{$state.state_id}"
    {rdelim}{if !$smarty.foreach.cod_types.last},{/if}
{/foreach}
{rdelim}
{rdelim}
{/strip}
