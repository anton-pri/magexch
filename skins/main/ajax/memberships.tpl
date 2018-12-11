{strip}
{ldelim}
"el_membership":"{$el_membership|escape:"json"}",
"membership_count":"{count value=$memberships}",
"memberships":{ldelim}
{foreach from=$memberships item=membership name="cod_types"}
    "{$membership.membership_id}":{ldelim}
        "membership":"{$membership.membership|escape:"json"}"
    {rdelim}{if !$smarty.foreach.cod_types.last},{/if}
{/foreach}
{rdelim}
{rdelim}
{/strip}
